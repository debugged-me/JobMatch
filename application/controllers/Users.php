<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'html', 'security']);
        $this->load->library(['session', 'form_validation', 'email']);


        $this->load->model('Manage_User', 'users');
        $this->load->model('User_model', 'User');

        $role = strtolower((string)$this->session->userdata('role'));
        $allowed = ['admin', 'tesda_admin', 'school_admin', 'worker', 'client', 'peso', 'other'];
        if (!in_array($role, $allowed, true)) {
            show_error('Forbidden', 403);
        }



        date_default_timezone_set('Asia/Manila');
    }

    public function index()
    {
        $q      = trim((string)$this->input->get('q', true));
        $role   = trim((string)$this->input->get('role', true));
        $status = trim((string)$this->input->get('status', true));
        $page   = max(1, (int)$this->input->get('page', true));
        $perPage = 20;
        $sort   = $this->input->get('sort', true);
        $dir    = $this->input->get('dir', true);
        $allowedSort = ['first_name', 'email', 'role', 'created_at'];
        $allowedDir  = ['asc', 'desc'];
        $sort = in_array($sort, $allowedSort) ? $sort : 'created_at';
        $dir  = in_array($dir, $allowedDir) ? $dir : 'desc';

        $statusInt = null;
        if ($status === 'active')   $statusInt = 1;
        if ($status === 'inactive') $statusInt = 0;

        $offset = ($page - 1) * $perPage;
        $users  = $this->users->search($q, $role, $statusInt, $perPage, $offset, $status === 'pending' ? 'pending' : null, $sort, $dir);

        $totalUsers = $this->users->count_search($q, $role, $statusInt, $status === 'pending' ? 'pending' : null);
        $totalPages = max(1, (int)ceil($totalUsers / $perPage));
        $page       = min($page, $totalPages);

        $data['page_title'] = 'Manage Users';
        $data['q']      = $q;
        $data['role']   = $role;
        $data['status'] = $status;
        $data['sort']   = $sort;
        $data['dir']    = $dir;
        $data['users']  = $users;
        $data['total_users'] = $totalUsers;
        $data['pagination'] = [
            'page'        => $page,
            'total_pages' => $totalPages,
            'total'       => $totalUsers,
            'from'        => $totalUsers > 0 ? $offset + 1 : 0,
            'to'          => min($offset + $perPage, $totalUsers),
        ];

        $this->load->view('users_list', $data);
    }

    public function toggle()
    {
        if (!$this->input->is_ajax_request()) {
            return $this->output->set_status_header(405)->set_output('Method Not Allowed');
        }

        $id     = (int)$this->input->post('id');
        $active = $this->input->post('active', true);

        if ($id <= 0 || !in_array($active, ['0', '1'], true)) {
            return $this->_out(false, 'Invalid payload', [], 400);
        }
        $me = (int)($this->session->userdata('id') ?: $this->session->userdata('user_id') ?: 0);
        if ($me === $id) {
            return $this->_out(false, "You can't deactivate yourself.", [], 400);
        }

        $ok = $this->users->set_active($id, (int)$active);
        if ($ok && (int)$active === 1 && $this->db->field_exists('status', 'users')) {
            $this->db->update('users', ['status' => 'active', 'updated_at' => date('Y-m-d H:i:s')], ['id' => $id]);
        }
        return $this->_out((bool)$ok, $ok ? 'OK' : 'Failed');
    }

    public function approve()
    {
        if (!$this->input->is_ajax_request()) {
            return $this->output->set_status_header(405)->set_output('Method Not Allowed');
        }
        $id = (int)$this->input->post('id');
        if ($id <= 0) return $this->_out(false, 'Missing id', [], 422);

        $adminId = (int)($this->session->userdata('id') ?: $this->session->userdata('user_id') ?: 0);
        $ok = $this->User->approve_user($id, $adminId);
        return $this->_out($ok, $ok ? 'User approved' : 'Approve failed', [], $ok ? 200 : 400);
    }

    public function resend()
    {
        if (!$this->input->is_ajax_request()) {
            return $this->output->set_status_header(405)->set_output('Method Not Allowed');
        }
        $id = (int)$this->input->post('id');
        if ($id <= 0) return $this->_out(false, 'Missing id', [], 422);

        $res = $this->User->resend_activation($id);
        $items = [];
        if (!empty($res['link'])) $items['link'] = $res['link'];

        $code = ($res['ok'] ?? false) ? 200 : 202;
        return $this->_out($res['ok'] ?? false, $res['msg'] ?? 'Failed', $items, $code);
    }

    private function _out($ok, $msg, $items = [], $status = 200)
    {
        $payload = ['ok' => (bool)$ok, 'msg' => (string)$msg, 'items' => $items];
        if ($this->config->item('csrf_protection')) {
            $payload['csrf_name'] = $this->security->get_csrf_token_name();
            $payload['csrf_hash'] = $this->security->get_csrf_hash();
        }
        return $this->output->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    public function admin_email_username($value)
    {
        $identifier = trim((string)$value);
        if ($identifier === '') {
            $this->form_validation->set_message('admin_email_username', 'Email / Username is required.');
            return false;
        }

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        $role = strtolower((string)$this->input->post('role', true));
        $usernameRoles = ['tesda_admin', 'school_admin', 'peso'];
        if (in_array($role, $usernameRoles, true)) {
            if (!preg_match('/^[A-Za-z0-9._-]+$/', $identifier)) {
                $this->form_validation->set_message(
                    'admin_email_username',
                    'Username may only contain letters, numbers, dots, underscores, or hyphens.'
                );
                return false;
            }
            return true;
        }

        $this->form_validation->set_message('admin_email_username', 'Please enter a valid email address.');
        return false;
    }


    public function create_admin()
    {
        if (!$this->input->is_ajax_request()) {
            return $this->output->set_status_header(405)->set_output('Method Not Allowed');
        }

        $adminId = (int)($this->session->userdata('user_id')
            ?: $this->session->userdata('id') ?: 0);

        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('last_name',  'Last Name',  'trim|required|min_length[2]');
        $this->form_validation->set_rules(
            'email',
            'Email / Username',
            'trim|required|min_length[3]|max_length[190]|callback_admin_email_username'
        );
        $this->form_validation->set_rules('role',       'Role', 'trim|required|in_list[admin,tesda_admin,school_admin,peso,other]');
        $this->form_validation->set_rules('password',   'Password',   'trim|required|min_length[6]');
        $this->form_validation->set_rules('confirm',    'Confirm',    'trim|required|matches[password]');

        if (!$this->form_validation->run()) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'ok' => false,
                    'msg' => strip_tags(validation_errors()),
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash(),
                ]));
        }

        $first = (string)$this->input->post('first_name', true);
        $last  = (string)$this->input->post('last_name',  true);
        $email = (string)$this->input->post('email',      true);
        $role  = (string)$this->input->post('role',       true);
        $pass  = (string)$this->input->post('password',   true);

        $allowed = ['admin', 'tesda_admin', 'school_admin', 'peso', 'other'];
        if (!in_array($role, $allowed, true)) {
            $role = 'admin';
        }

        if ($this->User->get_by_email($email)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'ok' => false,
                    'msg' => 'Email already exists.',
                    'csrf_name' => $this->security->get_csrf_token_name(),
                    'csrf_hash' => $this->security->get_csrf_hash(),
                ]));
        }

        $data = [
            'email'             => $email,
            'password_hash'     => password_hash($pass, PASSWORD_BCRYPT),
            'role'              => $role,
            'is_active'         => 1,
            'status'            => 'active',
            'visibility'        => 'private',
            'first_name'        => $first,
            'last_name'         => $last,
            'email_verified'    => 1,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'approved_by'       => $adminId ?: null,
            'approved_at'       => date('Y-m-d H:i:s'),
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $newId = $this->User->create($data);


        $resp = [
            'ok' => (bool)$newId,
            'msg' => $newId ? 'Admin account created.' : 'Failed to create admin.',
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash(),
        ];

        if (!$newId) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode($resp));
        }


        $full = trim($first . ' ' . $last) ?: $email;
        $sent = $this->_send_new_admin_email($email, $full, $email, $pass);

        if (!$sent) {

            $resp['msg'] = 'Admin created, but email failed to send. Check SMTP settings.';
        }

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($resp));
    }
    private function _send_new_admin_email(string $toEmail, string $fullname, string $username, string $plainPassword): bool
    {
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            log_message('info', 'Users::_send_new_admin_email skipped (no valid email provided): ' . $toEmail);
            return true;
        }

        $loginUrl = base_url('login');

        $message = $this->load->view('emails_new_admin_credentials', [
            'fullname'      => $fullname,
            'username'      => $username,
            'plainPassword' => $plainPassword,
            'loginUrl'      => $loginUrl
        ], true);


        $from = $this->config->item('from_email') ?: ($this->config->item('smtp_user') ?: 'no-reply@jobmatch.local');
        $fromName = $this->config->item('support_name') ?: 'JobMatch DavOr Support';


        $this->email->clear(true);
        $this->email->from($from, $fromName);
        $this->email->to($toEmail);
        $this->email->subject('Your Admin Account Credentials');
        $this->email->set_mailtype('html');
        $this->email->set_newline("\r\n");
        $this->email->set_crlf("\r\n");
        $this->email->message($message);

        $ok = $this->email->send();
        if (!$ok) {
            log_message('error', 'Users::_send_new_admin_email failed: ' . $this->email->print_debugger(['headers']));
        }
        return (bool)$ok;
    }


    public function delete()
    {
        $id = (int)$this->input->post('id');
        if ($id <= 0) {
            return $this->_json(false, 'Invalid user id.', 400);
        }

        $meId = (int)$this->session->userdata('user_id');
        if ($id === $meId) {
            return $this->_json(false, 'You cannot delete your own account.', 400);
        }


        $oldDebug = $this->db->db_debug;
        $this->db->db_debug = false;

        try {
            $result = $this->User->hard_delete($id);

            if (empty($result) || !isset($result['ok'])) {
                $this->db->db_debug = $oldDebug;
                return $this->_json(false, 'Delete failed (no result).', 500);
            }
            if (!$result['ok']) {
                if (!empty($result['db_error'])) {
                    log_message(
                        'error',
                        'Users::delete DB error: ' . $result['db_error']['message'] . ' (' . $result['db_error']['code'] . ')'
                    );
                }
                $this->db->db_debug = $oldDebug;
                return $this->_json(false, $result['msg'] ?? 'Delete failed.', 500, [
                    'db_error' => $result['db_error'] ?? null
                ]);
            }

            $this->db->db_debug = $oldDebug;
            return $this->_json(true, 'User and related records deleted permanently.');
        } catch (Throwable $e) {
            $this->db->db_debug = $oldDebug;
            log_message('error', 'Users::delete exception: ' . $e->getMessage());
            return $this->_json(false, 'Server error during delete.', 500, [
                'exception' => ENVIRONMENT !== 'production' ? $e->getMessage() : null
            ]);
        }
    }


    private function _json($ok, $msg, $httpCode = 200, $extra = [])
    {
        $resp = array_merge([
            'ok'        => (bool)$ok,
            'msg'       => (string)$msg,
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ], (array)$extra);

        $this->output->set_status_header($httpCode);
        $this->output->set_content_type('application/json')->set_output(json_encode($resp));
    }
}
