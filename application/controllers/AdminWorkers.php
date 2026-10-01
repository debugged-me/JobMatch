<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminWorkers extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        $role    = strtolower((string)$this->session->userdata('role'));
        $level   = strtolower((string)$this->session->userdata('level'));
        $isAdmin = (int)$this->session->userdata('isAdmin');

        // Allow classic admins AND these staff roles:
        $staffRoles = ['admin', 'tesda_admin', 'school_admin', 'peso', 'other'];
        if (!(in_array($role, $staffRoles, true) || $level === 'admin' || $isAdmin === 1)) {
            show_error('Forbidden', 403);
        }

        $this->load->helper(['url', 'form', 'string']);
        $this->load->library(['upload']);
        $this->load->model('WorkerImport_model');
    }

    public function index()
    {
        // Determine which prefix to use in links (admin vs tesda) based on URL or role
        $uri = trim((string)uri_string(), '/');
        $isTesdaPath = (strpos($uri, 'tesda/') === 0);
        $role  = strtolower((string)$this->session->userdata('role'));
        $route_base = $isTesdaPath || $role === 'tesda_admin' ? 'tesda' : 'admin';

        $this->load->view('workers_bulk_upload', [
            'page_title' => 'Bulk Upload: Skilled Workers',
            'route_base' => $route_base
        ]);
    }

    public function template()
    {
        $headers = [
            'email',
            'first_name',
            'last_name',
            'phone',
            'province',
            'city',
            'brgy',
            'bio',
            'headline',
            'birthday',
            'years_experience',
            'skills',
            'tesda_cert_no',
            'tesda_expiry'
        ];
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=JobMatch.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, $headers);
        fclose($out);
    }

    public function preview()
    {
        $config = [
            'upload_path'   => FCPATH . 'uploads/',
            'allowed_types' => 'csv|xls|xlsx',
            'max_size'      => 8192,
            'encrypt_name'  => TRUE,
        ];
        $this->upload->initialize($config);

        // Figure out route base again for the view
        $uri = trim((string)uri_string(), '/');
        $isTesdaPath = (strpos($uri, 'tesda/') === 0);
        $role  = strtolower((string)$this->session->userdata('role'));
        $route_base = $isTesdaPath || $role === 'tesda_admin' ? 'tesda' : 'admin';

        if (!$this->upload->do_upload('file')) {
            return $this->load->view('workers_bulk_upload', [
                'page_title' => 'Bulk Upload: Skilled Workers',
                'error'      => $this->upload->display_errors(),
                'route_base' => $route_base
            ]);
        }

        $file = $this->upload->data();
        $full = $file['full_path'];
        $ext  = strtolower(pathinfo($full, PATHINFO_EXTENSION));

        try {
            $rows = ($ext === 'csv') ? $this->parse_csv($full) : $this->parse_xlsx($full);
        } catch (Exception $e) {
            return $this->load->view('workers_bulk_upload', [
                'page_title' => 'Bulk Upload: Skilled Workers',
                'error'      => $e->getMessage(),
                'route_base' => $route_base
            ]);
        }

        if (empty($rows)) {
            return $this->load->view('workers_bulk_upload', [
                'page_title' => 'Bulk Upload: Skilled Workers',
                'error'      => 'No rows found in file.',
                'route_base' => $route_base
            ]);
        }

        $preview = $this->WorkerImport_model->build_preview($rows);

        $this->load->view('admin_workers_bulk_upload_preview', [
            'page_title' => 'Preview Skilled Workers',
            'file'       => $file['file_name'],
            'rows'       => $preview['rows'],
            'errors'     => $preview['errors'],
            'dupes'      => $preview['dupes'],
            'can_import' => (($preview['valid_count'] ?? 0) > 0),
            'summary'    => $preview,
            'route_base' => $route_base
        ]);
    }

    public function import()
    {
        if ($this->input->method() !== 'post') show_error('Method Not Allowed', 405);

        $filename = basename((string)$this->input->post('filename'));
        if ($filename === '' || $filename === '.' || $filename === '..') show_error('Missing file', 400);

        // Canonicalize: resolved path must be a file directly inside uploads/.
        $uploadsRoot = realpath(FCPATH . 'uploads');
        $full        = realpath($uploadsRoot ? $uploadsRoot . DIRECTORY_SEPARATOR . $filename : '');
        if (!$uploadsRoot || $full === false || dirname($full) !== $uploadsRoot || !is_file($full)) {
            show_error('File not found', 404);
        }

        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'xls', 'xlsx'], true)) {
            show_error('Invalid file type', 400);
        }

        try {
            $rows = ($ext === 'csv') ? $this->parse_csv($full) : $this->parse_xlsx($full);
        } catch (Exception $e) {
            show_error($e->getMessage(), 400);
        }

        $result = $this->WorkerImport_model->import_rows($rows);

        // Remove the uploaded PII-bearing import file once processed.
        @unlink($full);

        // Determine route base for “Back” links in result view too (if you have them)
        $uri = trim((string)uri_string(), '/');
        $isTesdaPath = (strpos($uri, 'tesda/') === 0);
        $role  = strtolower((string)$this->session->userdata('role'));
        $route_base = $isTesdaPath || $role === 'tesda_admin' ? 'tesda' : 'admin';

        $this->load->view('admin_workers_bulk_upload_result', [
            'page_title' => 'Import Result',
            'result'     => $result,
            'route_base' => $route_base
        ]);
    }

    private function parse_csv($path)
    {
        $out = [];
        if (($h = fopen($path, 'r')) === FALSE) throw new Exception('Unable to read CSV file.');
        $headers = fgetcsv($h);
        if (!$headers) throw new Exception('CSV has no header row.');
        $headers = array_map(function ($x) {
            return strtolower(trim($x));
        }, $headers);

        while (($row = fgetcsv($h)) !== FALSE) {
            if (count(array_filter($row)) === 0) continue;
            $row = array_slice(array_pad($row, count($headers), ''), 0, count($headers));
            $out[] = array_combine($headers, $row);
        }
        fclose($h);
        return $out;
    }

    private function parse_xlsx($path)
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            throw new Exception('XLS/XLSX parsing requires PhpSpreadsheet. Please install it or upload CSV instead.');
        }
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $ss     = $reader->load($path);
        $sheet  = $ss->getActiveSheet();
        $rows   = $sheet->toArray(null, true, true, true);

        if (!$rows || !isset($rows[1])) throw new Exception('Invalid spreadsheet: missing header row.');
        $headers = [];
        foreach ($rows[1] as $val) $headers[] = strtolower(trim($val));

        $out = [];
        for ($i = 2; $i <= count($rows); $i++) {
            $vals = array_values($rows[$i]);
            if (count(array_filter($vals, function ($v) {
                return $v !== null && $v !== '';
            })) === 0) continue;
            $vals = array_slice(array_pad($vals, count($headers), ''), 0, count($headers));
            $out[] = array_combine($headers, $vals);
        }
        return $out;
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Invalid request', 400);
        }

        $this->load->model('User_model');
        $this->load->database();
        $this->load->helper(['security', 'string']);

        $first  = trim((string)$this->input->post('first_name', true));
        $middle = trim((string)$this->input->post('middle_name', true));
        $last   = trim((string)$this->input->post('last_name', true));
        $email  = trim((string)$this->input->post('email', true));
        $phone  = trim((string)$this->input->post('phone', true));

        $province = trim((string)$this->input->post('province', true));
        $city     = trim((string)$this->input->post('city', true));
        $brgy     = trim((string)$this->input->post('brgy', true));

        $pw_override = trim((string)$this->input->post('password_override', true));

        if ($first === '' || $last === '' || $email === '') {
            return $this->_json(false, 'First name, Last name, and Email are required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->_json(false, 'Invalid email format.');
        }

        if ($pw_override !== '') {
            $raw    = $pw_override;
            $source = 'custom';
        } else {
            $base = strtolower(preg_replace('/[^a-z0-9]/i', '', $last));
            if ($base === '') {
                $raw    = bin2hex(random_bytes(4));
                $source = 'random (no/invalid last_name)';
            } else {
                $raw    = $base;
                $source = 'last name';
            }
        }
        $hash = password_hash($raw, PASSWORD_BCRYPT);

        $now  = date('Y-m-d H:i:s');
        $user = $this->User_model->get_by_email($email);

        $this->db->trans_start();

        $created = false;

        if (!$user) {
            $user_id = $this->User_model->create([
                'email'         => $email,
                'password_hash' => $hash,
                'role'          => 'worker',
                'is_active'     => 1,
                'first_name'    => $first,
                'last_name'     => $last,
                'phone'         => $phone ?: '',
                'updated_at'    => $now
            ]);
            if (!$user_id) {
                $this->db->trans_complete();
                return $this->_json(false, 'Failed to create user.');
            }
            $created = true;

            $admin_id = (int)($this->session->userdata('user_id') ?? 0);
            $this->User_model->approve_user((int)$user_id, $admin_id);
        } else {
            $user_id = (int)$user->id;

            $upd = ['updated_at' => $now];
            if ($first !== '') $upd['first_name'] = $first;
            if ($last  !== '') $upd['last_name']  = $last;
            if ($phone !== '') $upd['phone']      = $phone;
            if (count($upd) > 1) {
                $this->db->update('users', $upd, ['id' => $user_id]);
            }

            if ((int)($user->is_active ?? 0) !== 1 || strtolower((string)($user->status ?? '')) !== 'active') {
                $admin_id = (int)($this->session->userdata('user_id') ?? 0);
                $this->User_model->approve_user($user_id, $admin_id);
            }
        }

        $exists = $this->db->get_where('worker_profile', ['workerID' => $user_id])->row();
        $wp = [
            'province'   => $province ?: null,
            'city'       => $city ?: null,
            'brgy'       => $brgy ?: null,
            'phoneNo'    => $phone ?: null,
            'updated_at' => $now
        ];
        if ($exists) {
            $this->db->update('worker_profile', $wp, ['workerID' => $user_id]);
        } else {
            $wp['workerID']   = $user_id;
            $wp['created_at'] = date('Y-m-d');
            $this->db->insert('worker_profile', $wp);
        }

        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            return $this->_json(false, 'Database error. Please try again.');
        }

        return $this->_json(true, 'Saved', [
            'temp_password' => $created ? $raw : '(existing user — password not changed)',
            'source'        => $created ? $source : null,
            'activated'     => true
        ]);
    }

    private function _json(bool $ok, string $message = '', array $data = [], int $status = 200)
    {
        $payload = array_merge(['ok' => $ok, 'message' => $message], $data);

        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();

        exit;
    }
}
