<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NSRP — National Skills Registration Program forms.
 *
 * Form 1 (Rev.3) — Jobseeker Registration.
 * Form 2 — Establishment Registration.
 *
 * These are official DOLE/PESO forms and are CONFIDENTIAL — access is
 * restricted to PESO (and admin) accounts only. PESO staff encode the
 * registrants, then may PRINT the form or SEND a copy to the concerned
 * worker/client account via the in-app message feature.
 */
class Nsrp extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url','form']);
        $this->load->library(['session','form_validation']);
        $this->load->model('WorkerProfile_model', 'wp');
        $this->load->model('ClientProfile_model', 'cp');
        $this->load->model('Peso_model', 'peso');
        $this->load->model('User_model');
        $this->load->model('Message_model');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        // CONFIDENTIAL: PESO / admin only.
        if (!$this->is_peso()) {
            show_error('Forbidden — NSRP forms are for PESO accounts only.', 403);
        }

        // Make sure the NSRP columns exist (auto-migrate; no manual SQL in prod).
        $this->ensure_schema();
    }

    /**
     * Idempotently apply the NSRP migrations. Guarded by sentinel columns so
     * the ALTERs only run when something is actually missing.
     */
    private function ensure_schema(): void
    {
        try {
            if (!$this->db->field_exists('nsrp_status', 'worker_profile')) {
                $this->run_sql_file(APPPATH . 'config/nsrp_form1_migration.sql');
            }
            if (!$this->db->field_exists('establishment_id', 'jobs')) {
                $this->run_sql_file(APPPATH . 'config/nsrp_form2_migration.sql');
            }
        } catch (\Throwable $e) {
            log_message('error', 'NSRP ensure_schema failed: ' . $e->getMessage());
        }
    }

    private function run_sql_file(string $path): void
    {
        if (!is_file($path)) return;
        $sql = file_get_contents($path);
        // Strip line comments, then run each statement separately.
        $sql = preg_replace('/^\s*--.*$/m', '', $sql);
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
            if ($stmt !== '') {
                $this->db->query($stmt);
            }
        }
    }

    /* ---------------- role helpers ---------------- */

    private function role(): string
    {
        $raw = (string)($this->session->userdata('role') ?: $this->session->userdata('level') ?: '');
        return strtolower(trim(str_replace(['_','-'], ' ', $raw)));
    }

    private function is_peso(): bool
    {
        $r = $this->role();
        return strpos($r, 'peso') !== false || $r === 'admin' || $r === 'tesda admin';
    }

    private function me(): int
    {
        return (int)($this->session->userdata('user_id') ?: $this->session->userdata('id') ?: 0);
    }

    /** PESO must always target a specific existing user; aborts otherwise. */
    private function resolve_target(int $user_id): int
    {
        if ($user_id <= 0) {
            show_error('A registrant must be specified.', 400);
        }
        if (!$this->User_model->get_by_id($user_id)) {
            show_error('Registrant not found.', 404);
        }
        return $user_id;
    }

    /* ---------------- Form 1: Jobseeker ---------------- */

    public function jobseeker($worker_id = 0)
    {
        $target = $this->resolve_target((int)$worker_id);

        $this->load->view('nsrp_form1', [
            'page_title' => 'NSRP Form 1 — Jobseeker Registration',
            'p'          => $this->wp->get_full($target),
            'target_id'  => $target,
        ]);
    }

    /** Save the jobseeker registration (PESO encoding on behalf of the worker). */
    public function save($worker_id = 0)
    {
        $target = $this->resolve_target((int)$worker_id);

        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|max_length[80]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|max_length[80]');
        $this->form_validation->set_rules('date_of_birth', 'Date of Birth', 'trim');
        $this->form_validation->set_rules('salary_expectation', 'Salary Expectation', 'trim|numeric');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            return redirect($this->form_url($target));
        }

        // Names live on the users table.
        $first = trim((string)$this->input->post('first_name', true));
        $last  = trim((string)$this->input->post('last_name', true));
        $this->db->where('id', $target)->update('users', [
            'first_name' => $first,
            'last_name'  => $last,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $ok = $this->wp->save_nsrp($target, $this->collect_payload());

        $this->session->set_flashdata($ok ? 'success' : 'danger',
            $ok ? 'NSRP Form 1 saved.' : 'Unable to save the form.');
        return redirect($this->form_url($target));
    }

    /** PESO-only: assessment block (eligibility / assessed by / status). */
    public function assess($worker_id = 0)
    {
        if (!$this->is_peso()) {
            show_error('Forbidden (PESO only).', 403);
        }
        $target = (int)$worker_id;
        if ($target <= 0 || !$this->User_model->get_by_id($target)) {
            show_error('Worker not found.', 404);
        }

        $elig = (array)$this->input->post('peso_eligibility');
        $payload = [
            'peso_eligibility' => implode(',', array_filter(array_map('trim', $elig))),
            'assessed_by'      => trim((string)$this->input->post('assessed_by', true)),
            'assessed_at'      => $this->date_or_null($this->input->post('assessed_at', true)),
            'nsrp_reference'   => trim((string)$this->input->post('nsrp_reference', true)),
            'nsrp_status'      => in_array($this->input->post('nsrp_status'), ['draft','submitted','assessed'], true)
                ? $this->input->post('nsrp_status') : 'assessed',
        ];

        $ok = $this->wp->assess_nsrp($target, $payload);
        $this->session->set_flashdata($ok ? 'success' : 'danger',
            $ok ? 'Assessment saved.' : 'Unable to save assessment.');
        return redirect($this->form_url($target));
    }

    /** PESO-only: create a walk-in worker account, then open their Form 1. */
    public function encode()
    {
        if (!$this->is_peso()) {
            show_error('Forbidden (PESO only).', 403);
        }

        // GET → show the lightweight "create walk-in" form.
        if (strtoupper($this->input->server('REQUEST_METHOD')) !== 'POST') {
            return $this->load->view('nsrp_encode', ['page_title' => 'Register Walk-in Jobseeker']);
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|max_length[80]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|max_length[80]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            return redirect('peso');
        }

        $email = strtolower(trim((string)$this->input->post('email', true)));
        $first = trim((string)$this->input->post('first_name', true));
        $last  = trim((string)$this->input->post('last_name', true));
        $phone = trim((string)$this->input->post('phone', true));

        $existing = $this->User_model->get_by_email($email);
        if ($existing) {
            // Re-use the existing account and jump straight to their form.
            return redirect($this->form_url((int)$existing->id));
        }

        $now = date('Y-m-d H:i:s');
        $user_id = $this->User_model->create([
            'email'         => $email,
            'password_hash' => password_hash(bin2hex(random_bytes(5)), PASSWORD_BCRYPT),
            'role'          => 'worker',
            'is_active'     => 1,
            'first_name'    => $first,
            'last_name'     => $last,
            'phone'         => $phone,
            'updated_at'    => $now,
        ]);

        if (!$user_id) {
            $this->session->set_flashdata('danger', 'Could not create the walk-in account.');
            return redirect('peso');
        }

        $this->User_model->approve_user((int)$user_id, $this->me());

        // Seed a worker_profile row.
        $this->db->insert('worker_profile', [
            'workerID'    => (int)$user_id,
            'phoneNo'     => $phone,
            'nsrp_status' => 'draft',
            'created_at'  => date('Y-m-d'),
            'updated_at'  => $now,
        ]);

        $this->session->set_flashdata('success', 'Walk-in account created. Continue the NSRP Form 1 below.');
        return redirect($this->form_url((int)$user_id));
    }

    /* ---------------- Form 2: Establishment ---------------- */

    public function establishment($client_id = 0)
    {
        $target = $this->resolve_target((int)$client_id);

        $editJob = null;
        $jobId = (int)$this->input->get('job');
        if ($jobId > 0) {
            $found = $this->peso->nsrp_find_vacancy($jobId);
            if ($found && (int)$found['establishment_id'] === $target) {
                $editJob = $found;
            }
        }

        $this->load->view('nsrp_form2', [
            'page_title' => 'NSRP Form 2 — Establishment Registration',
            'p'          => $this->cp->get_full($target),
            'vacancies'  => $this->peso->nsrp_vacancies($target),
            'edit_job'   => $editJob,
            'target_id'  => $target,
        ]);
    }

    /** Save establishment details + (optionally) one vacancy. */
    public function save_establishment($client_id = 0)
    {
        $target = $this->resolve_target((int)$client_id);

        $this->form_validation->set_rules('business_name', 'Business Name', 'trim|required|max_length[160]');
        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            return redirect($this->est_url($target));
        }

        $okEst = $this->cp->save_establishment($target, $this->collect_establishment());

        // Vacancy is optional — only when a Position Title is provided.
        $title = trim((string)$this->input->post('title', true));
        $jobId = (int)$this->input->post('job_id');
        if ($title !== '') {
            $vac = $this->collect_vacancy();
            if ($jobId > 0) {
                $existing = $this->peso->nsrp_find_vacancy($jobId);
                if ($existing && (int)$existing['establishment_id'] === $target) {
                    $this->peso->nsrp_update_vacancy($jobId, $vac);
                }
            } else {
                $this->peso->nsrp_create_vacancy($target, $this->me(), $vac);
            }
        }

        $this->session->set_flashdata($okEst ? 'success' : 'danger',
            $okEst ? 'NSRP Form 2 saved.' : 'Unable to save the form.');
        return redirect($this->est_url($target));
    }

    /** PESO-only: establishment status + per-vacancy assessment. */
    public function establishment_assess($client_id = 0)
    {
        if (!$this->is_peso()) {
            show_error('Forbidden (PESO only).', 403);
        }
        $target = (int)$client_id;
        if ($target <= 0 || !$this->User_model->get_by_id($target)) {
            show_error('Establishment not found.', 404);
        }

        $status = (string)$this->input->post('nsrp_status');
        if (in_array($status, ['draft','submitted','assessed'], true)) {
            $this->cp->set_nsrp_status($target, $status);
        }

        $jobId = (int)$this->input->post('job_id');
        if ($jobId > 0) {
            $existing = $this->peso->nsrp_find_vacancy($jobId);
            if ($existing && (int)$existing['establishment_id'] === $target) {
                $this->peso->nsrp_assess_vacancy($jobId, [
                    'assessed_by' => trim((string)$this->input->post('assessed_by', true)),
                    'encoded_by'  => trim((string)$this->input->post('encoded_by', true)),
                ]);
            }
        }

        $this->session->set_flashdata('success', 'Assessment saved.');
        return redirect($this->est_url($target) . ($jobId > 0 ? '?job=' . $jobId : ''));
    }

    /** PESO-only: create a walk-in establishment (client) account. */
    public function encode_establishment()
    {
        if (!$this->is_peso()) {
            show_error('Forbidden (PESO only).', 403);
        }

        if (strtoupper($this->input->server('REQUEST_METHOD')) !== 'POST') {
            return $this->load->view('nsrp_encode_establishment', ['page_title' => 'Register Walk-in Establishment']);
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('business_name', 'Business Name', 'trim|required|max_length[160]');
        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            return redirect('peso');
        }

        $email   = strtolower(trim((string)$this->input->post('email', true)));
        $biz     = trim((string)$this->input->post('business_name', true));
        $contact = trim((string)$this->input->post('contact_person', true));

        $existing = $this->User_model->get_by_email($email);
        if ($existing) {
            return redirect($this->est_url((int)$existing->id));
        }

        $now = date('Y-m-d H:i:s');
        $user_id = $this->User_model->create([
            'email'         => $email,
            'password_hash' => password_hash(bin2hex(random_bytes(5)), PASSWORD_BCRYPT),
            'role'          => 'client',
            'is_active'     => 1,
            'first_name'    => $contact ?: $biz,
            'last_name'     => '',
            'phone'         => trim((string)$this->input->post('phone', true)),
            'updated_at'    => $now,
        ]);

        if (!$user_id) {
            $this->session->set_flashdata('danger', 'Could not create the establishment account.');
            return redirect('peso');
        }

        $this->User_model->approve_user((int)$user_id, $this->me());
        $this->cp->ensure_row((int)$user_id, [
            'companyName'    => $biz,
            'business_name'  => $biz,
            'contact_person' => $contact,
        ]);

        $this->session->set_flashdata('success', 'Walk-in establishment created. Continue NSRP Form 2 below.');
        return redirect($this->est_url((int)$user_id));
    }

    private function est_url(int $target): string
    {
        return 'nsrp/establishment/' . $target;
    }

    /* ---------------- Print ---------------- */

    public function print_jobseeker($worker_id = 0)
    {
        $target = $this->resolve_target((int)$worker_id);
        $this->load->view('nsrp_form1_print', [
            'page_title' => 'NSRP Form 1',
            'p'          => $this->wp->get_full($target),
        ]);
    }

    public function print_establishment($client_id = 0)
    {
        $target = $this->resolve_target((int)$client_id);
        $this->load->view('nsrp_form2_print', [
            'page_title' => 'NSRP Form 2',
            'p'          => $this->cp->get_full($target),
            'vacancies'  => $this->peso->nsrp_vacancies($target),
        ]);
    }

    /* ---------------- Send to user via messages ---------------- */

    public function send_jobseeker($worker_id = 0)
    {
        $target = $this->resolve_target((int)$worker_id);
        $row    = $this->wp->get_full($target);
        $name   = trim((string)($row->first_name ?? '') . ' ' . (string)($row->last_name ?? ''));

        $body = "📄 NSRP Form 1 — Jobseeker Registration (from PESO Davao Oriental)\n\n"
              . $this->summarize_jobseeker($row)
              . "\n\nThis is a confidential copy of your NSRP registration on file with PESO. "
              . "Please review and inform our office of any corrections.";

        $ok = $this->send_to_user($target, $body);
        $this->session->set_flashdata($ok ? 'success' : 'danger',
            $ok ? ('NSRP Form 1 sent to ' . ($name ?: 'the worker') . ' via Messages.') : 'Unable to send the form.');
        return redirect($this->form_url($target));
    }

    public function send_establishment($client_id = 0)
    {
        $target = $this->resolve_target((int)$client_id);
        $row    = $this->cp->get_full($target);
        $vacs   = $this->peso->nsrp_vacancies($target);
        $biz    = (string)($row->business_name ?? $row->companyName ?? '');

        $body = "🏢 NSRP Form 2 — Establishment Registration (from PESO Davao Oriental)\n\n"
              . $this->summarize_establishment($row, $vacs)
              . "\n\nThis is a confidential copy of your establishment registration on file with PESO. "
              . "Please review and inform our office of any corrections.";

        $ok = $this->send_to_user($target, $body);
        $this->session->set_flashdata($ok ? 'success' : 'danger',
            $ok ? ('NSRP Form 2 sent to ' . ($biz ?: 'the establishment') . ' via Messages.') : 'Unable to send the form.');
        return redirect($this->est_url($target));
    }

    private function send_to_user(int $target, string $body): bool
    {
        $me = $this->me();
        if ($me <= 0 || $target <= 0 || $me === $target) return false;
        $thread = $this->Message_model->get_or_create_thread($me, $target);
        if (!$thread) return false;
        return (bool)$this->Message_model->add_message((int)$thread->id, $me, $body);
    }

    private function summarize_jobseeker($p): string
    {
        if (!$p) return '(no data on file)';
        $lines = [];
        $add = function ($label, $val) use (&$lines) {
            $val = is_array($val) ? implode(', ', array_filter($val)) : trim((string)$val);
            if ($val !== '') $lines[] = $label . ': ' . $val;
        };
        $add('Name', trim((string)($p->first_name ?? '') . ' ' . (string)($p->last_name ?? '')));
        $add('Sex', $p->sex ?? '');
        $add('Date of Birth', $p->date_of_birth ?? '');
        $add('Civil Status', $p->civil_status ?? '');
        $add('Mobile', $p->phoneNo ?? '');
        $add('Address', trim((string)($p->brgy ?? '') . ', ' . (string)($p->city ?? '') . ', ' . (string)($p->province ?? ''), ', '));
        $add('Education', $p->education_level ?? '');
        $add('Course', $p->course ?? '');
        $add('Skills', $p->skills ?? '');
        $add('Salary Expectation', $p->salary_expectation ?? '');
        $add('Registration Ref.', $p->nsrp_reference ?? '');
        $add('Status', $p->nsrp_status ?? '');
        return implode("\n", $lines) ?: '(no data on file)';
    }

    private function summarize_establishment($p, array $vacs): string
    {
        if (!$p) return '(no data on file)';
        $lines = [];
        $add = function ($label, $val) use (&$lines) {
            $val = trim((string)$val);
            if ($val !== '') $lines[] = $label . ': ' . $val;
        };
        $add('Business Name', $p->business_name ?? $p->companyName ?? '');
        $add('Trade Name', $p->trade_name ?? '');
        $add('TIN', $p->tin ?? '');
        $add('Employer Type', trim((string)($p->employer_type ?? '') . ' ' . (string)($p->employer_subtype ?? '')));
        $add('Line of Business', $p->line_of_business ?? '');
        $add('Contact Person', $p->contact_person ?? '');
        $add('Mobile', $p->phoneNo ?? '');
        $add('Status', $p->nsrp_status ?? '');
        if (!empty($vacs)) {
            $lines[] = "\nVacancies (" . count($vacs) . '):';
            foreach ($vacs as $vac) {
                $lines[] = '  • ' . trim((string)($vac['title'] ?? 'Untitled'))
                    . (!empty($vac['vacancy_count']) ? ' (x' . (int)$vac['vacancy_count'] . ')' : '')
                    . (!empty($vac['salary']) ? ' — ' . $vac['salary'] : '');
            }
        }
        return implode("\n", $lines) ?: '(no data on file)';
    }

    private function collect_establishment(): array
    {
        $in = $this->input;
        $biz = trim((string)$in->post('business_name', true));
        return [
            'business_name'    => $biz,
            'companyName'      => $biz, // keep legacy display column in sync
            'trade_name'       => trim((string)$in->post('trade_name', true)),
            'acronym'          => trim((string)$in->post('acronym', true)),
            'office_type'      => in_array($in->post('office_type'), ['main','branch'], true) ? $in->post('office_type') : null,
            'tin'              => trim((string)$in->post('tin', true)),
            'employer_type'    => in_array($in->post('employer_type'), ['public','private'], true) ? $in->post('employer_type') : null,
            'employer_subtype' => trim((string)$in->post('employer_subtype', true)),
            'workforce_size'   => in_array($in->post('workforce_size'), ['micro','small','medium','large'], true) ? $in->post('workforce_size') : null,
            'line_of_business' => trim((string)$in->post('line_of_business', true)),
            'street_village'   => trim((string)$in->post('street_village', true)),
            'brgy'             => trim((string)$in->post('brgy', true)),
            'city'             => trim((string)$in->post('city', true)),
            'province'         => trim((string)$in->post('province', true)),
            'owner_name'       => trim((string)$in->post('owner_name', true)),
            'contact_person'   => trim((string)$in->post('contact_person', true)),
            'contact_position' => trim((string)$in->post('contact_position', true)),
            'phoneNo'          => trim((string)$in->post('phoneNo', true)),
            'telephone'        => trim((string)$in->post('telephone', true)),
            'fax'              => trim((string)$in->post('fax', true)),
        ];
    }

    private function collect_vacancy(): array
    {
        $in = $this->input;
        return [
            'title'                  => trim((string)$in->post('title', true)),
            'description'            => trim((string)$in->post('description', true)),
            'nature_of_work'         => trim((string)$in->post('nature_of_work', true)),
            'place_of_work'          => trim((string)$in->post('place_of_work', true)),
            'salary'                 => trim((string)$in->post('salary', true)),
            'vacancy_count'          => $in->post('vacancy_count') !== '' ? (int)$in->post('vacancy_count') : null,
            'work_experience_months' => $in->post('work_experience_months') !== '' ? (int)$in->post('work_experience_months') : null,
            'other_qualifications'   => trim((string)$in->post('other_qualifications', true)),
            'accepts_pwd'            => $this->chk('accepts_pwd'),
            'pwd_types'              => implode(',', array_filter(array_map('trim', (array)$in->post('pwd_types')))),
            'accepts_ofw'            => $this->chk('accepts_ofw'),
            'educational_level'      => trim((string)$in->post('educational_level', true)),
            'course_strand'          => trim((string)$in->post('course_strand', true)),
            'license'                => trim((string)$in->post('license', true)),
            'eligibility'            => trim((string)$in->post('eligibility', true)),
            'certification'          => trim((string)$in->post('certification', true)),
            'language'               => trim((string)$in->post('language', true)),
            'posting_date'           => $this->date_or_null($in->post('posting_date')),
            'valid_until'            => $this->date_or_null($in->post('valid_until')),
            'location_text'          => trim((string)$in->post('place_of_work', true)),
            'visibility'             => in_array($in->post('visibility'), ['public','followers'], true) ? $in->post('visibility') : 'public',
        ];
    }

    /* ---------------- internals ---------------- */

    private function form_url(int $target): string
    {
        return 'nsrp/jobseeker/' . $target;
    }

    private function date_or_null($v): ?string
    {
        $v = trim((string)$v);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : null;
    }

    private function chk(string $field): int
    {
        return (int)($this->input->post($field) ? 1 : 0);
    }

    /** Map the posted form into the model's NSRP payload shape. */
    private function collect_payload(): array
    {
        $in = $this->input;

        // Repeaters arrive as parallel arrays; zip them into rows.
        $occ = $this->zip_rows([
            'occupation' => (array)$in->post('pref_occupation'),
            'industry'   => (array)$in->post('pref_industry'),
        ]);
        $elig = $this->zip_rows([
            'career_service' => (array)$in->post('elig_service'),
            'license_no'     => (array)$in->post('elig_license'),
            'expiry'         => (array)$in->post('elig_expiry'),
        ]);
        $langCerts = $this->zip_rows([
            'name'     => (array)$in->post('lang_cert_name'),
            'validity' => (array)$in->post('lang_cert_validity'),
        ]);
        $tesda = $this->zip_rows([
            'qualification' => (array)$in->post('tesda_qual'),
            'number'        => (array)$in->post('tesda_no'),
            'expiry'        => (array)$in->post('tesda_exp'),
        ]);
        $exp = $this->zip_rows([
            'company'  => (array)$in->post('exp_company'),
            'address'  => (array)$in->post('exp_address'),
            'position' => (array)$in->post('exp_position'),
            'dates'    => (array)$in->post('exp_dates'),
            'status'   => (array)$in->post('exp_status'),
        ]);

        $locLocal    = array_values(array_filter(array_map('trim', (array)$in->post('pref_local'))));
        $locOverseas = array_values(array_filter(array_map('trim', (array)$in->post('pref_overseas'))));

        return [
            // I. Personal
            'sex'              => $in->post('sex') ?: null,
            'date_of_birth'    => $this->date_or_null($in->post('date_of_birth')),
            'place_of_birth'   => trim((string)$in->post('place_of_birth', true)),
            'civil_status'     => trim((string)$in->post('civil_status', true)),
            'citizenship'      => trim((string)$in->post('citizenship', true)),
            'religion'         => trim((string)$in->post('religion', true)),
            'height_cm'        => $in->post('height_cm') !== '' ? $in->post('height_cm') : null,
            'weight_kg'        => $in->post('weight_kg') !== '' ? $in->post('weight_kg') : null,
            'phoneNo'          => trim((string)$in->post('phoneNo', true)),
            'landline'         => trim((string)$in->post('landline', true)),
            'mobile_secondary' => trim((string)$in->post('mobile_secondary', true)),
            'present_street'   => trim((string)$in->post('present_street', true)),
            'brgy'             => trim((string)$in->post('brgy', true)),
            'city'             => trim((string)$in->post('city', true)),
            'province'         => trim((string)$in->post('province', true)),
            'perm_same_as_present' => $this->chk('perm_same_as_present'),
            'perm_street'      => trim((string)$in->post('perm_street', true)),
            'perm_brgy'        => trim((string)$in->post('perm_brgy', true)),
            'perm_city'        => trim((string)$in->post('perm_city', true)),
            'perm_province'    => trim((string)$in->post('perm_province', true)),
            'disability'       => implode(',', array_filter(array_map('trim', (array)$in->post('disability')))),

            // Employment status & flags
            'employment_status'    => trim((string)$in->post('employment_status', true)),
            'employment_substatus' => trim((string)$in->post('employment_substatus', true)),
            'actively_looking'     => $this->chk('actively_looking'),
            'looking_duration'     => trim((string)$in->post('looking_duration', true)),
            'willing_immediate'    => $this->chk('willing_immediate'),
            'available_when'       => trim((string)$in->post('available_when', true)),
            'is_4ps'               => $this->chk('is_4ps'),
            'fourps_household_id'   => trim((string)$in->post('fourps_household_id', true)),
            'is_ofw'               => $this->chk('is_ofw'),
            'ofw_returning'        => $this->chk('ofw_returning'),

            // II. Job preference
            'pref_occupations'        => $occ,
            'pref_locations_local'    => $locLocal,
            'pref_locations_overseas' => $locOverseas,
            'salary_expectation'      => $in->post('salary_expectation') !== '' ? $in->post('salary_expectation') : null,

            // III. Education
            'education_level' => trim((string)$in->post('education_level', true)),
            'school'          => trim((string)$in->post('school', true)),
            'course'          => trim((string)$in->post('course', true)),
            'year_graduated'  => $in->post('year_graduated') !== '' ? $in->post('year_graduated') : null,

            // IV/V. Training, eligibility, languages
            'tesda_certs'    => $tesda,
            'eligibilities'  => $elig,
            'language_certs' => $langCerts,
            'languages'      => implode(',', array_filter(array_map('trim', (array)$in->post('languages')))),

            // VI. Work experience
            'exp' => $exp,

            // VII/IX. Skills self-assessment
            'century_skills'       => array_values(array_filter(array_map('trim', (array)$in->post('century_skills')))),
            'tech_skills_informal' => array_values(array_filter(array_map('trim', (array)$in->post('tech_skills_informal')))),
            'skills'               => trim((string)$in->post('skills', true)),
        ];
    }

    /** Turn parallel column arrays into a list of row objects, dropping blanks. */
    private function zip_rows(array $cols): array
    {
        $len = 0;
        foreach ($cols as $arr) { $len = max($len, count($arr)); }

        $rows = [];
        for ($i = 0; $i < $len; $i++) {
            $row = [];
            $hasValue = false;
            foreach ($cols as $key => $arr) {
                $v = isset($arr[$i]) ? trim((string)$arr[$i]) : '';
                $row[$key] = $v;
                if ($v !== '') $hasValue = true;
            }
            if ($hasValue) $rows[] = $row;
        }
        return $rows;
    }
}
