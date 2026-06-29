<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SchoolAdmin Controller (CI3)
 * - Dashboard (stats + recent table)
 * - Workers list (filter/search)
 * - Create (emails the user their email + password)
 * - Edit / Delete / Resend Email (new temp)
 * - CSV bulk (emails each)
 */
class SchoolAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form', 'security']);
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->model('SchoolAdminModel');


        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    /** Dashboard */
    public function index()
    {
        $data = [
            'title'  => 'School Admin Dashboard',
            'stats'  => $this->SchoolAdminModel->stats(),
            'recent' => $this->SchoolAdminModel->recent_users(10),
        ];
        $this->load->view('school_admin_dashboard', $data);
    }

    /** Workers list */
    public function workers()
    {
        $role   = trim((string)$this->input->get('role'));
        $active = $this->input->get('is_active');
        $q      = trim((string)$this->input->get('q'));

        if ($active === null || $active === '') $active = 'ALL';

        $data = [
            'title'     => 'Workers',
            'q'         => $q,
            'role'      => $role ?: 'ALL',
            'is_active' => $active,
            'rows'      => $this->SchoolAdminModel->get_users($role, $active, $q),
            'roles'     => ['worker'],
        ];
        $this->load->view('school_admin_workers', $data);
    }

    /** Show create form */
    public function create()
    {
        $data = [
            'title' => 'Create Worker',
            'roles' => ['worker'],
        ];
        $this->load->view('school_admin_form', $data);
    }

    /** Handle create + email */
    public function store()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');


        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            return redirect('school-admin/create');
        }


        $payload = [
            'email'       => strtolower(trim($this->input->post('email', TRUE))),
            'first_name'  => $this->input->post('first_name', TRUE),
            'last_name'   => $this->input->post('last_name', TRUE),
            'phone'       => $this->input->post('phone', TRUE),
            'role'        => 'worker',
            'is_active'   => $this->input->post('is_active', TRUE) === '0' ? 0 : 1,
            'status'      => $this->input->post('status', TRUE) ?: 'active',
            'visibility'  => $this->input->post('visibility', TRUE) ?: 'private',
            'password'    => $this->input->post('password', TRUE),
        ];

        $res = $this->SchoolAdminModel->create_user($payload);
        if (!$res['ok']) {
            $this->session->set_flashdata('danger', $res['message'] ?? 'Failed to create user.');
            return redirect('school-admin/create');
        }


        $passwordToSend = $res['temp_password'];

        $sent = $this->_send_welcome(
            $payload['email'],
            trim($payload['first_name'] . ' ' . $payload['last_name']),
            $passwordToSend,
            'worker'
        );

        $this->session->set_flashdata(
            $sent ? 'success' : 'danger',
            $sent ? 'Worker created and email sent.' : 'Worker created, but email failed to send. Check logs shown above.'
        );
        return redirect('school-admin/workers');
    }

    /** Show edit form */
    public function edit($id)
    {
        $id = (int)$id;
        $user = $this->SchoolAdminModel->get_user_by_id($id);
        if (!$user) {
            $this->session->set_flashdata('danger', 'User not found.');
            return redirect('school-admin/workers');
        }

        $data = [
            'title' => 'Edit Worker',
            'roles' => ['worker'],
            'user'  => $user
        ];
        $this->load->view('school_admin_form', $data);
    }

    /** Update */
    public function update($id)
    {
        $id = (int)$id;

        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        $pw = trim((string)$this->input->post('password', TRUE));
        if ($pw !== '') {
            $this->form_validation->set_rules('password', 'Password', 'min_length[8]');
            $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'matches[password]');
        }

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            return redirect('school-admin/edit/' . $id);
        }

        $payload = [
            'email'       => strtolower(trim($this->input->post('email', TRUE))),
            'first_name'  => $this->input->post('first_name', TRUE),
            'last_name'   => $this->input->post('last_name', TRUE),
            'phone'       => $this->input->post('phone', TRUE),
            'role'        => 'worker',
            'is_active'   => $this->input->post('is_active', TRUE) === '0' ? 0 : 1,
            'status'      => $this->input->post('status', TRUE) ?: 'active',
            'visibility'  => $this->input->post('visibility', TRUE) ?: 'private',
        ];
        if ($pw !== '') $payload['password'] = $pw;

        $res = $this->SchoolAdminModel->update_user($id, $payload);
        if (!$res['ok']) {
            $this->session->set_flashdata('danger', $res['message'] ?? 'Failed to update user.');
            return redirect('school-admin/edit/' . $id);
        }

        $this->session->set_flashdata('success', 'Worker updated.');
        return redirect('school-admin/workers');
    }

    /** Delete (hard delete) */
    public function delete($id)
    {
        $id = (int)$id;
        $res = $this->SchoolAdminModel->delete_user($id);

        if ($res['ok']) {
            $this->session->set_flashdata('success', 'Worker deleted.');
        } else {
            log_message('error', 'Delete user failed (id=' . $id . '): ' . $res['message']);
            $this->session->set_flashdata('danger', 'Unable to delete worker. ' . $res['message']);
        }
        return redirect('school-admin/workers');
    }

    /** Resend welcome email with NEW temp password (and update hash) */
    public function resend_email($id)
    {
        $id = (int)$id;
        $user = $this->SchoolAdminModel->get_user_by_id($id);
        if (!$user) {
            $this->session->set_flashdata('danger', 'User not found.');
            return redirect('school-admin/workers');
        }

        $set = $this->SchoolAdminModel->set_temp_password($id);
        if (!$set['ok']) {
            $this->session->set_flashdata('danger', 'Could not regenerate password. ' . $set['message']);
            return redirect('school-admin/workers');
        }

        $sent = $this->_send_welcome(
            $user->email,
            trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
            $set['temp_password'],
            'worker'
        );

        $this->session->set_flashdata(
            $sent ? 'success' : 'danger',
            $sent ? 'Welcome email re-sent.' : 'Re-send failed. See logs shown above.'
        );
        return redirect('school-admin/workers');
    }

    /** EXACTLY mirror Auth’s working email pattern */
    private function _send_welcome($to, $fullName, $plainPassword, $role)
    {

        $this->load->library('email');


        $fromEmail = $this->config->item('from_email') ?: ($this->config->item('smtp_user') ?: 'no-reply@jobmatch.local');
        $fromName  = $this->config->item('support_name') ?: 'JobMatch DavOr Support';
        $replyEmail = $this->config->item('reply_to_email') ?: $fromEmail;
        $replyName  = $this->config->item('reply_to_name') ?: ($this->config->item('from_name') ?: 'JobMatch DavOr');

        $this->email->from($fromEmail, $fromName);
        $this->email->reply_to($replyEmail, $replyName);
        $this->email->to($to);
        $this->email->subject('Your JobMatch DavOr account');
        $this->email->set_mailtype('html');
        $this->email->set_newline("\r\n");
        $this->email->set_crlf("\r\n");


        $bannerRel  = 'assets/images/logo-white.png';
        $logoRel    = 'assets/images/logo.png';
        $publicBase = rtrim(base_url(), '/');

        $bannerPath = FCPATH . $bannerRel;
        $logoPath   = FCPATH . $logoRel;

        $bannerCid = $logoCid = null;
        if (is_file($bannerPath)) {
            $this->email->attach($bannerPath, 'inline', basename($bannerPath));
            $bannerCid = $this->email->attachment_cid($bannerPath);
        }
        if (is_file($logoPath)) {
            $this->email->attach($logoPath, 'inline', basename($logoPath));
            $logoCid = $this->email->attachment_cid($logoPath);
        }

        $bannerSrc = $bannerCid ? "cid:{$bannerCid}" : rtrim($publicBase, '/') . '/' . ltrim($bannerRel, '/');
        $logoSrc   = $logoCid   ? "cid:{$logoCid}"   : rtrim($publicBase, '/') . '/' . ltrim($logoRel, '/');


        if ($this->load->view('email_worker_account_created', [], TRUE) !== '') {
            $message = $this->load->view('email_worker_account_created', [
                'full_name' => trim($fullName) !== '' ? $fullName : 'User',
                'email'     => $to,
                'password'  => $plainPassword,
                'role'      => $role,
                'bannerSrc' => $bannerSrc,
                'logoSrc'   => $logoSrc,
            ], TRUE);
        } else {

            $message = "
                <!doctype html><html><body style='font-family:Arial,sans-serif'>
                    <h2>Welcome to JobMatch DavOr</h2>
                    <p>Hi " . htmlspecialchars($fullName ?: 'User') . ",</p>
                    <p>Your account has been created.</p>
                    <p><strong>Login Email:</strong> " . htmlspecialchars($to) . "<br>
                       <strong>Password:</strong> " . htmlspecialchars($plainPassword) . "<br>
                       <strong>Role:</strong> " . htmlspecialchars($role) . "</p>
                    <p>Please change your password after first login.</p>
                </body></html>
            ";
        }

        $this->email->message($message);
        $ok = $this->email->send(false);

        if (!$ok) {

            $debug = $this->email->print_debugger(['headers']);
            log_message('error', 'Welcome email failed for ' . $to . ': ' . $debug);
            $this->session->set_flashdata(
                'danger',
                'Email failed to send. <br><pre style="white-space:pre-wrap">' .
                    htmlspecialchars($debug, ENT_QUOTES, 'UTF-8') . '</pre>'
            );
        }


        $this->email->clear(TRUE);
        return $ok;
    }

    /* ============================================================
       Student-worker reports (ported from trabawho) + CSV/PDF export
       ============================================================ */

    public function reports()
    {
        $ownerId = $this->_scope_owner_user_id();
        $scopeSchoolName = $this->_scope_school_name($this->_scope_school_id());
        $scopeSchoolNameShort = $this->_trim_school_label($scopeSchoolName, 54);
        $q = trim((string)$this->input->get('q', true));

        $this->load->view('school_admin_reports', [
            'title'                   => 'Student Worker Reports',
            'q'                       => $q,
            'summary'                 => $this->SchoolAdminModel->report_summary($ownerId),
            'rows'                    => $this->SchoolAdminModel->report_rows($q, $ownerId),
            'scope_school_name'       => $scopeSchoolName,
            'scope_school_name_short' => $scopeSchoolNameShort,
        ]);
    }

    public function reports_export_csv()
    {
        $ownerId = $this->_scope_owner_user_id();
        $q = trim((string)$this->input->get('q', true));
        $scopeSchoolName = $this->_scope_school_name($this->_scope_school_id());
        $rows = $this->SchoolAdminModel->report_rows($q, $ownerId);

        $filename = 'school_reports_' . date('Ymd_His') . '.csv';
        if (ob_get_length()) {
            @ob_end_clean();
        }
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        if (!$out) {
            exit;
        }

        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Student Worker Reports']);
        fputcsv($out, ['School', $scopeSchoolName !== '' ? $scopeSchoolName : 'N/A']);
        fputcsv($out, ['Generated At', date('Y-m-d H:i:s')]);
        if ($q !== '') {
            fputcsv($out, ['Filter', $q]);
        }
        fputcsv($out, []);
        fputcsv($out, ['Student', 'Email', 'Created', 'Hire Count', 'Latest Hired', 'Status']);

        foreach ($rows as $r) {
            $ln = trim((string)($r->last_name ?? ''));
            $fn = trim((string)($r->first_name ?? ''));
            $full = ($ln !== '' || $fn !== '') ? ($ln . ($ln && $fn ? ', ' : '') . $fn) : ('User #' . (int)($r->id ?? 0));
            $email = (string)($r->email ?? '');
            $createdAt = !empty($r->created_at) ? date('Y-m-d h:i A', strtotime($r->created_at)) : '-';
            $latestHired = !empty($r->latest_hired_at) ? date('Y-m-d h:i A', strtotime($r->latest_hired_at)) : '-';
            $hireCount = (int)($r->hire_count ?? 0);
            $statusText = $this->_report_status_label($r);

            fputcsv($out, [$full, $email, $createdAt, $hireCount, $latestHired, $statusText]);
        }

        fclose($out);
        exit;
    }

    public function reports_export_pdf()
    {
        if (!class_exists('FPDF')) {
            $autoload = FCPATH . 'vendor/autoload.php';
            if (is_file($autoload)) {
                require_once $autoload;
            }
            if (!class_exists('FPDF')) {
                $fpdf = FCPATH . 'vendor/setasign/fpdf/fpdf.php';
                if (is_file($fpdf)) {
                    require_once $fpdf;
                }
            }
            if (!class_exists('FPDF')) {
                show_error('PDF library (FPDF) is not available.', 500);
                return;
            }
        }

        $ownerId = $this->_scope_owner_user_id();
        $q = trim((string)$this->input->get('q', true));
        $scopeSchoolName = $this->_scope_school_name($this->_scope_school_id());
        $rows = $this->SchoolAdminModel->report_rows($q, $ownerId);

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetTitle($this->_pdf_text('Student Worker Reports'));
        $pdf->SetAuthor($this->_pdf_text('JobMatch DavOr'));
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, $this->_pdf_text('Student Worker Reports'), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, $this->_pdf_text('School: ' . ($scopeSchoolName !== '' ? $scopeSchoolName : 'N/A')), 0, 1, 'L');
        $meta = 'Generated: ' . date('M d, Y h:i A');
        if ($q !== '') {
            $meta .= '  |  Filter: ' . $q;
        }
        $pdf->Cell(0, 6, $this->_pdf_text($meta), 0, 1, 'L');
        $pdf->Ln(2);

        $headers = ['Student', 'Email', 'Created', 'Hire Count', 'Latest Hired', 'Status'];
        $widths  = [62, 84, 38, 24, 38, 26];

        $pdf->SetFont('Arial', 'B', 9);
        foreach ($headers as $i => $h) {
            $pdf->Cell($widths[$i], 8, $this->_pdf_text($h), 1, 0, 'C');
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);
        foreach ($rows as $r) {
            if ($pdf->GetY() > 188) {
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 9);
                foreach ($headers as $i => $h) {
                    $pdf->Cell($widths[$i], 8, $this->_pdf_text($h), 1, 0, 'C');
                }
                $pdf->Ln();
                $pdf->SetFont('Arial', '', 9);
            }

            $ln = trim((string)($r->last_name ?? ''));
            $fn = trim((string)($r->first_name ?? ''));
            $full = ($ln !== '' || $fn !== '') ? ($ln . ($ln && $fn ? ', ' : '') . $fn) : ('User #' . (int)($r->id ?? 0));
            $email = (string)($r->email ?? '');
            $createdAt = !empty($r->created_at) ? date('M d, Y h:i A', strtotime($r->created_at)) : '-';
            $latestHired = !empty($r->latest_hired_at) ? date('M d, Y h:i A', strtotime($r->latest_hired_at)) : '-';
            $hireCount = (int)($r->hire_count ?? 0);
            $statusText = $this->_report_status_label($r);

            $values = [
                $this->_trim_school_label($full, 34),
                $this->_trim_school_label($email, 44),
                $createdAt,
                (string)$hireCount,
                $latestHired,
                $statusText,
            ];

            foreach ($values as $i => $value) {
                $align = $i === 3 ? 'C' : 'L';
                $pdf->Cell($widths[$i], 7, $this->_pdf_text($value), 1, 0, $align);
            }
            $pdf->Ln();
        }

        $filename = 'school_reports_' . date('Ymd_His') . '.pdf';
        $pdf->Output('D', $filename);
        exit;
    }

    private function _current_role_normalized(): string
    {
        $role = strtolower(trim((string)$this->session->userdata('role')));
        $role = str_replace(['_', '-'], ' ', $role);
        $role = preg_replace('/\s+/', ' ', $role);
        return (string)$role;
    }

    private function _scope_owner_user_id(): ?int
    {
        if ($this->_current_role_normalized() !== 'school admin') {
            return null;
        }
        $id = (int)($this->session->userdata('id') ?: $this->session->userdata('user_id') ?: 0);
        return $id > 0 ? $id : -1;
    }

    private function _scope_school_id(): ?int
    {
        if ($this->_current_role_normalized() !== 'school admin') {
            return null;
        }

        $uid = (int)($this->session->userdata('id') ?: $this->session->userdata('user_id') ?: 0);
        if ($uid <= 0) {
            return null;
        }

        if ($this->db->field_exists('school_id', 'users')) {
            $u = $this->db->select('school_id')->from('users')->where('id', $uid)->limit(1)->get()->row();
            $sid = isset($u->school_id) ? (int)$u->school_id : 0;
            if ($sid > 0) {
                return $sid;
            }
        }

        if ($this->db->table_exists('school_admin_accounts')) {
            $a = $this->db->select('school_id')
                ->from('school_admin_accounts')
                ->where('user_id', $uid)
                ->limit(1)
                ->get()
                ->row();
            $sid = isset($a->school_id) ? (int)$a->school_id : 0;
            if ($sid > 0) {
                return $sid;
            }
        }

        return null;
    }

    private function _scope_school_name(?int $schoolId): string
    {
        $schoolId = (int)$schoolId;
        if ($schoolId <= 0 || !$this->db->table_exists('schools')) {
            return '';
        }
        $s = $this->db->select('school_name')->from('schools')->where('school_id', $schoolId)->limit(1)->get()->row();
        return $s ? trim((string)($s->school_name ?? '')) : '';
    }

    private function _trim_school_label(?string $schoolName, int $maxLength = 54): string
    {
        $schoolName = trim((string)$schoolName);
        if ($schoolName === '' || $maxLength < 4) {
            return $schoolName;
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($schoolName, 'UTF-8') <= $maxLength) {
                return $schoolName;
            }
            return rtrim(mb_substr($schoolName, 0, $maxLength - 3, 'UTF-8')) . '...';
        }

        if (strlen($schoolName) <= $maxLength) {
            return $schoolName;
        }
        return rtrim(substr($schoolName, 0, $maxLength - 3)) . '...';
    }

    private function _report_status_label($row): string
    {
        $statusLower = strtolower((string)($row->status ?? 'active'));
        $isActive = (int)($row->is_active ?? 0) === 1;

        if ($statusLower === 'pending') {
            return 'Pending';
        }
        if ($isActive) {
            return 'Active';
        }
        return 'Inactive';
    }

    private function _pdf_text(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }
        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT', $text);
        if ($converted !== false) {
            return $converted;
        }
        return preg_replace('/[^\x20-\x7E]/', '', $text);
    }
}
