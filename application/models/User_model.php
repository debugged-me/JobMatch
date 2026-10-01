<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $table = 'users';

    public function get_by_email($email)
    {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function create($data)
    {
        $this->db->insert('users', $data);
        return $this->db->affected_rows() > 0 ? $this->db->insert_id() : false;
    }

    public function get_by_identifier($identifier)
    {
        $id = strtolower(trim((string)$identifier));
        if ($id === '') return null;

        $this->db->from($this->table);
        $this->db->group_start()
            ->where('LOWER(email)=', $id);
        if ($this->db->field_exists('username', $this->table)) {
            $this->db->or_where('LOWER(username)=', $id);
        }
        $this->db->group_end();

        return $this->db->limit(1)->get()->row();
    }

    public function verify_credentials($identifier, $password)
    {
        $user = $this->get_by_identifier($identifier);
        if (!$user) {
            // Uniform error — do not reveal whether the account exists.
            // Run a dummy verify to keep timing comparable.
            password_verify($password, '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG');
            return [false, "Invalid email/username or password."];
        }

        // --- Lockout check (before any password work) ---
        if (!empty($user->locked_until)) {
            $until = strtotime((string)$user->locked_until);
            if ($until !== false && $until > time()) {
                $mins = max(1, (int)ceil(($until - time()) / 60));
                return [false, "Account temporarily locked. Try again in {$mins} minute(s)."];
            }
        }

        if (!password_verify($password, (string)$user->password_hash)) {
            $this->_register_failed_attempt((int)$user->id, (int)($user->failed_attempts ?? 0));
            return [false, "Invalid email/username or password."];
        }

        // Successful auth: clear counters.
        $this->_clear_failed_attempts((int)$user->id);

        $role = $this->normalize_role($user->role ?? 'user');

        // --- Everyone: verified + active checks. No role bypasses these. ---
        $hasEmailVerifiedCol = $this->db->field_exists('email_verified', $this->table);
        $emailVerified = $hasEmailVerifiedCol ? (int)($user->email_verified ?? 0) : 1;

        $hasStatusCol    = $this->db->field_exists('status', $this->table);
        $hasIsActiveCol  = $this->db->field_exists('is_active', $this->table);
        $status     = $hasStatusCol ? strtolower((string)($user->status ?? '')) : 'active';
        $isActive   = $hasIsActiveCol ? (int)($user->is_active ?? 0) : 1;
        $approvedAt = (string)($user->approved_at ?? '');

        $isStaff = $this->is_staff_role($role);
        $requiresApproval  = in_array($role, ['client', 'employer'], true);
        $hasApprovedAtCol  = $this->db->field_exists('approved_at', $this->table);

        if ($emailVerified !== 1) {
            return [false, "Please verify your email first. We’ve sent you an activation link."];
        }

        if ($status === 'suspended') {
            return [false, "This account has been suspended. Contact the administrator."];
        }

        if ($status !== 'active' || $isActive !== 1) {
            return [false, $requiresApproval ? "Your account is pending admin approval." : "Your account isn’t active yet."];
        }

        if ($requiresApproval && $hasApprovedAtCol && $approvedAt === '' && !$isStaff) {
            return [false, "Your account is pending admin approval."];
        }

        return [true, $user];
    }

    /**
     * Increment failed_attempts; lock the account for 15 minutes after
     * 5 consecutive failures. Columns exist in the users table.
     */
    private function _register_failed_attempt(int $userId, int $current): void
    {
        if (!$this->db->field_exists('failed_attempts', $this->table)) {
            return;
        }
        $attempts = $current + 1;
        $data = [
            'failed_attempts' => $attempts,
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        if ($attempts >= 5 && $this->db->field_exists('locked_until', $this->table)) {
            $data['locked_until']    = date('Y-m-d H:i:s', time() + 900);
            $data['failed_attempts'] = 0;
        }
        $this->db->update($this->table, $data, ['id' => $userId]);
    }

    private function _clear_failed_attempts(int $userId): void
    {
        if (!$this->db->field_exists('failed_attempts', $this->table)) {
            return;
        }
        $data = ['failed_attempts' => 0];
        if ($this->db->field_exists('locked_until', $this->table)) {
            $data['locked_until'] = null;
        }
        $this->db->update($this->table, $data, ['id' => $userId]);
    }

    public function update_last_login($id)
    {
        $now  = date('Y-m-d H:i:s');
        $data = ['updated_at' => $now];

        if ($this->db->field_exists('last_login_at', $this->table)) {
            $data['last_login_at'] = $now;
        }

        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function get_by_token($token)
    {
        $now = date('Y-m-d H:i:s');

        $this->db->from($this->table);
        $this->db->where('activation_token', $token);
        $this->db->group_start()
            ->where('email_token_expires >=', $now)
            ->or_where('email_token_expires IS NULL', null, false)
            ->group_end();

        return $this->db->get()->row();
    }

    public function set_activation_token($id, $token, $expires = null)
    {
        return $this->db->update($this->table, [
            'activation_token'    => $token,
            'email_token_expires' => $expires,
            'updated_at'          => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    public function activate($id)
    {
        return $this->db->update($this->table, [
            'is_active'           => 1,
            'activation_token'    => null,
            'email_token_expires' => null,
            'failed_attempts'     => 0,
            'locked_until'        => null,
            'updated_at'          => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    public function get_pending_users()
    {
        return $this->db->get_where($this->table, ['is_active' => 0])->result();
    }

    public function activate_user($id)
    {
        return $this->db->update($this->table, [
            'is_active'  => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    public function count_by_role($role, $only_active = true)
    {
        $this->db->from($this->table);
        $this->db->where('role', $role);
        if ($only_active) {
            $this->db->where('is_active', 1);
        }
        return (int) $this->db->count_all_results();
    }

    public function count_workers()
    {
        return $this->count_by_role('worker', true);
    }

    public function count_employers()
    {
        return $this->count_by_role('client', true);
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => (int)$id])->row();
    }

    public function update_password($id, string $plain_password): bool
    {
        return $this->db->update($this->table, [
            'password_hash' => password_hash($plain_password, PASSWORD_BCRYPT),
            'updated_at'    => date('Y-m-d H:i:s')
        ], ['id' => (int)$id]);
    }

    public function approve_user(int $user_id, ?int $admin_id = null): bool
    {
        $data = [
            'is_active'           => 1,
            'activation_token'    => null,
            'email_token_expires' => null,
            'failed_attempts'     => 0,
            'locked_until'        => null,
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        if ($this->db->field_exists('email_verified', 'users')) {
            $data['email_verified'] = 1;
        }
        if ($this->db->field_exists('email_verified_at', 'users')) {
            $data['email_verified_at'] = date('Y-m-d H:i:s');
        }
        if ($this->db->field_exists('status', 'users')) {
            $data['status'] = 'active';
        }
        if ($this->db->field_exists('approved_by', 'users')) {
            $data['approved_by'] = $admin_id ?: null;
        }
        if ($this->db->field_exists('approved_at', 'users')) {
            $data['approved_at'] = date('Y-m-d H:i:s');
        }

        $this->db->update('users', $data, ['id' => $user_id]);
        return $this->db->affected_rows() > 0;
    }

    public function resend_activation(int $user_id): array
    {
        $user = $this->get_by_id($user_id);
        if (!$user) return ['ok' => false, 'msg' => 'User not found'];

        if ((int)$user->is_active === 1) {
            return ['ok' => false, 'msg' => 'User already active'];
        }
        $token   = bin2hex(random_bytes(24));
        $expires = date('Y-m-d H:i:s', time() + 48 * 3600);

        $this->set_activation_token($user->id, $token, $expires);

        if ($this->db->field_exists('activation_sent_at', 'users')) {
            $this->db->update('users', ['activation_sent_at' => date('Y-m-d H:i:s')], ['id' => $user->id]);
        }

        $link = site_url('auth/activate/' . rawurlencode($token));
        $sent = $this->send_activation_email(
            $user->email,
            trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
            $link
        );
        return [
            'ok'  => (bool)$sent,
            'msg' => $sent ? 'Activation email sent' : 'Email sending failed',
            'link' => $link
        ];
    }

    private function send_activation_email(string $to, string $name, string $link): bool
    {
        $this->load->library('email');
        $from = $this->config->item('from_email') ?: ($this->config->item('smtp_user') ?: 'no-reply@jobmatch.local');
        $fromName = $this->config->item('support_name') ?: 'JobMatch DavOr Support';

        $this->email->clear(true);
        $this->email->from($from, $fromName);
        $this->email->to($to);
        $this->email->subject('Activate your account');

        $name = trim($name) ?: 'there';
        $body = "Hi {$name},\n\nPlease activate your account by clicking the link below:\n{$link}\n\nIf you didn't request this, please ignore this email.";
        $this->email->message($body);

        return @$this->email->send();
    }

    public function activate_from_token(string $token, ?string $email = null): array
    {
        $user = $this->get_by_token($token);
        if (!$user) {
            return ['ok' => false, 'msg' => 'Invalid or expired activation link.'];
        }
        if ($email && strcasecmp($user->email, $email) !== 0) {
            return ['ok' => false, 'msg' => 'Activation link does not match this email.'];
        }

        $data = [
            'activation_token'     => null,
            'email_token_expires'  => null,
            'updated_at'           => date('Y-m-d H:i:s'),
        ];
        if ($this->db->field_exists('email_verified', $this->table)) {
            $data['email_verified'] = 1;
        }
        if ($this->db->field_exists('email_verified_at', $this->table)) {
            $data['email_verified_at'] = date('Y-m-d H:i:s');
        }

        $this->db->update($this->table, $data, ['id' => (int)$user->id]);
        return ['ok' => true, 'user' => $this->get_by_id((int)$user->id)];
    }

    public function mark_email_verified(int $id): bool
    {
        $data = [
            'activation_token'    => null,
            'email_token_expires' => null,
            'updated_at'          => date('Y-m-d H:i:s'),
        ];
        if ($this->db->field_exists('email_verified', $this->table)) {
            $data['email_verified'] = 1;
        }
        if ($this->db->field_exists('email_verified_at', $this->table)) {
            $data['email_verified_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function set_pending(int $id): bool
    {
        $data = ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')];
        if ($this->db->field_exists('status', $this->table)) {
            $data['status'] = 'pending';
        }
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    private function is_staff_role($role): bool
    {
        $role = $this->normalize_role($role);
        return in_array($role, ['admin', 'tesda_admin', 'school_admin', 'peso', 'other'], true);
    }

    private function normalize_role($role): string
    {
        $raw = strtolower(trim((string)$role));
        $raw = str_replace(['_', '-'], ' ', $raw);
        $raw = preg_replace('/\s+/', ' ', $raw);

        $map = [
            'admin' => 'admin',
            'administrator' => 'admin',
            'admins' => 'admin',
            'super admin' => 'admin',
            'superadmin' => 'admin',

            'tesda admin' => 'tesda_admin',
            'tesda' => 'tesda_admin',

            'school admin' => 'school_admin',
            'schooladmin' => 'school_admin',

            'peso admin' => 'peso',
            'peso officer' => 'peso',
            'peso' => 'peso',

            'worker' => 'worker',
            'workers' => 'worker',

            'client' => 'client',
            'clients' => 'client',
            'employer' => 'employer',
            'employers' => 'employer',
            'customer' => 'client',
            'customers' => 'client',

            'other' => 'other',
        ];

        return $map[$raw] ?? $raw;
    }

    public function hard_delete(int $userId): array
    {
        if ($userId <= 0) {
            return ['ok' => false, 'msg' => 'Invalid user id.', 'db_error' => null];
        }

        $this->db->trans_begin();

        // Helper: safe delete (guard table existence)
        $del = function (string $table, array $where) {
            if ($this->db->table_exists($table)) {
                $this->db->where($where)->delete($table);
            }
        };

        // Worker side
        $del('worker_skills',   ['workerID'  => $userId]);
        $del('worker_posts',    ['worker_id' => $userId]);
        $del('worker_profile',  ['workerID'  => $userId]);

        // Client side
        $del('client_projects', ['clientID'  => $userId]);
        $del('client_profile',  ['clientID'  => $userId]);

        // Documents
        $del('documents',       ['user_id'   => $userId]);

        // Personnel / Hires (either role)
        if ($this->db->table_exists('personnel_hired')) {
            $this->db->group_start()
                ->where('worker_id', $userId)
                ->or_where('client_id', $userId)
                ->group_end()
                ->delete('personnel_hired');
        }

        // Transactions (either side)
        if ($this->db->table_exists('transactions')) {
            $this->db->group_start()
                ->where('workerID', $userId)
                ->or_where('clientID', $userId)
                ->group_end()
                ->delete('transactions');
        }

        // Threads (user could be either a_id or b_id)
        if ($this->db->table_exists('tw_threads')) {
            $this->db->group_start()
                ->where('a_id', $userId)
                ->or_where('b_id', $userId)
                ->group_end()
                ->delete('tw_threads');
        }

        // Finally: users
        if ($this->db->table_exists('users')) {
            $this->db->where('id', $userId)->delete('users');
        } else {
            $this->db->trans_rollback();
            return ['ok' => false, 'msg' => 'Table "users" not found.', 'db_error' => null];
        }

        if ($this->db->trans_status() === false) {
            $dbErr = $this->db->error();
            $this->db->trans_rollback();
            return [
                'ok'       => false,
                'msg'      => 'Database error during delete.',
                'db_error' => $dbErr ?: null
            ];
        }

        if ($this->db->affected_rows() < 1) {
            $this->db->trans_rollback();
            return ['ok' => false, 'msg' => 'User not found or already deleted.', 'db_error' => null];
        }

        $this->db->trans_commit();
        return ['ok' => true, 'msg' => 'Deleted', 'db_error' => null];
    }
}
