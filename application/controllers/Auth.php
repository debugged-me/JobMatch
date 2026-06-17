<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'session']);
        $this->load->model('User_model', 'user');
        $this->load->model('ClientProfile_model', 'cp');
    }

    public function index()
    {
        redirect('auth/login', 'location', 301);
    }

    public function signup()
    {
        $data = [];

        if ($this->input->method() === 'post') {

            $this->form_validation->set_error_delimiters('<div class="text-red-600 text-sm mt-2">', '</div>');

            $this->form_validation->set_rules('first_name', 'First Name', 'required');
            $this->form_validation->set_rules('last_name',  'Last Name',  'required');

            $this->form_validation->set_rules(
                'email',
                'Email',
                'required|valid_email|is_unique[users.email]',
                [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique'   => 'This email is already registered. Please log in or use another email.'
                ]
            );

            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[worker,client,employer]');
            $this->form_validation->set_rules(
                'accept_privacy',
                'Privacy Policy',
                'required',
                ['required' => 'You must agree to the Privacy Policy to proceed.']
            );
            $this->form_validation->set_rules(
                'g-recaptcha-response',
                'reCAPTCHA',
                'required|callback__recaptcha_check',
                ['required' => 'Please complete the reCAPTCHA.']
            );
            $this->form_validation->set_rules('website', 'Website', 'callback__honeypot_clear');

            if ($this->form_validation->run()) {
                $now = date('Y-m-d H:i:s');
                $dataInsert = [
                    'first_name'    => $this->input->post('first_name', true),
                    'last_name'     => $this->input->post('last_name', true),
                    'email'         => $this->input->post('email', true),
                    'password_hash' => password_hash((string)$this->input->post('password', true), PASSWORD_BCRYPT),
                    'role'          => $this->input->post('role', true),
                    'status'        => 'active',
                    'is_active'     => 1,
                ];

                if ($this->db->field_exists('activation_token', 'users')) {
                    $dataInsert['activation_token'] = null;
                }
                if ($this->db->field_exists('email_token_expires', 'users')) {
                    $dataInsert['email_token_expires'] = null;
                }
                if ($this->db->field_exists('email_verified', 'users')) {
                    $dataInsert['email_verified'] = 1;
                }
                if ($this->db->field_exists('email_verified_at', 'users')) {
                    $dataInsert['email_verified_at'] = $now;
                }
                if ($this->db->field_exists('approved_at', 'users')) {
                    $dataInsert['approved_at'] = $now;
                }
                if ($this->db->field_exists('failed_attempts', 'users')) {
                    $dataInsert['failed_attempts'] = 0;
                }
                if ($this->db->field_exists('locked_until', 'users')) {
                    $dataInsert['locked_until'] = null;
                }
                if ($this->db->field_exists('updated_at', 'users')) {
                    $dataInsert['updated_at'] = $now;
                }
                if ($this->db->field_exists('created_at', 'users')) {
                    $dataInsert['created_at'] = $now;
                }

                $ok = $this->user->create($dataInsert);
                if (!$ok) {
                    $err = $this->db->error();
                    if (($err['code'] ?? 0) === 1062) {
                        $this->session->set_flashdata('error', 'This email is already registered. Please log in or use another email.');
                    } else {
                        $this->session->set_flashdata('error', 'We couldn’t create your account. Please try again.');
                    }
                    return redirect('auth/signup');
                }

                $uid = (int) $this->db->insert_id();
                if (($dataInsert['role'] ?? '') === 'client') {
                    $this->cp->ensure_row($uid, [
                        'fName' => $dataInsert['first_name'] ?? '',
                        'lName' => $dataInsert['last_name']  ?? '',
                    ]);
                }

                $this->session->set_flashdata('msg', 'Signup successful! Your account is now active. You can log in.');
                return redirect('auth/login');
            }

            $cap = $this->_start_local_captcha();
            $data['captcha_q'] = $cap['q'];
        } else {
            $cap = $this->_start_local_captcha();
            $data['captcha_q'] = $cap['q'];
        }

        $this->load->view('auth_signup', $data);
    }


    public function email_available()
    {
        $email = strtolower(trim((string)$this->input->get('email', true)));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->_json(['ok' => false, 'available' => false, 'msg' => 'Enter a valid email.'], 200);
        }

        $exists = (bool) $this->user->get_by_email($email);
        return $this->_json(['ok' => true, 'available' => !$exists]);
    }

    private function _json(array $payload, int $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }
    private function _send_activation_email($email, $token)
    {
        $this->load->library('email');

        $fromEmail = $this->config->item('from_email') ?: ($this->config->item('smtp_user') ?: 'no-reply@jobmatch.local');
        $fromName  = $this->config->item('from_name') ?: 'JobMatch DavOr';
        $replyEmail = $this->config->item('reply_to_email') ?: $fromEmail;
        $replyName  = $this->config->item('reply_to_name') ?: $fromName;

        $this->email->from($fromEmail, $fromName);
        $this->email->reply_to($replyEmail, $replyName);
        $this->email->to($email);
        $this->email->subject('Confirm your JobMatch account');
        $this->email->set_mailtype('html');
        $this->email->set_newline("\r\n");
        $this->email->set_crlf("\r\n");

        $link = base_url("auth/activate/{$token}");
        $blue   = '#0b61ff';
        $yellow = '#ffd200';
        $ink    = '#111827';
        $muted  = '#6b7280';
        $bannerRel = 'assets/images/logo-white.png';
        $logoRel   = 'assets/images/logo.png';
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

        $message = "<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <meta name='x-apple-disable-message-reformatting'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <title>JobMatch DavOr — Confirm your email</title>
  <style>
    body,table,td,a{ -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
    table,td{ mso-table-lspace:0pt; mso-table-rspace:0pt; }
    img{ -ms-interpolation-mode:bicubic; border:0; height:auto; line-height:100%; outline:none; text-decoration:none; display:block; }
    table{ border-collapse:collapse!important; }
    body{ margin:0!important; padding:0!important; width:100%!important; background:#f3f5f7; }
    .btn:hover{ filter:brightness(.95); }
    .nd { pointer-events:none; user-select:none; -webkit-user-drag:none; -webkit-touch-callout:none; }
    @media (prefers-color-scheme: dark){
      .bg{ background:#0b1220!important; }
      .card{ background:#121926!important; }
      .muted{ color:#b5c0d0!important; }
      .heading,.text{ color:#e6edf6!important; }
    }
  </style>
</head>
<body style='margin:0;padding:0;'>
  <div style='display:none;font-size:1px;color:#fff;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;'>
    Confirm your email to start using JobMatch DavOr.
  </div>

  <table role='presentation' width='100%' cellpadding='0' cellspacing='0' class='bg'
         style=\"background-image:linear-gradient(180deg,#f6fafe 0%,#f3f5f7 60%,#eef1f4 100%);\">
    <tr>
      <td align='center' style='padding:32px 16px;'>

        <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='max-width:620px;' class='card'>

          <!-- HERO (short, background image so no obvious download UI) -->
          <tr>
            <td style='padding:0; border-radius:12px 12px 0 0; overflow:hidden;'>
              <!--[if gte mso 9]>
              <v:rect xmlns:v=\"urn:schemas-microsoft-com:vml\" fill=\"true\" stroke=\"false\" style=\"width:620px;height:112px;\">
                <v:fill type=\"frame\" src=\"{$bannerSrc}\" color=\"{$blue}\" />
                <v:textbox inset=\"0,0,0,0\">
              <![endif]-->
              <div style=\"
                   background: {$blue} url('{$bannerSrc}') no-repeat center/280px auto;
                   height:112px; text-align:center; position:relative;\">
               
              </div>
              <!--[if gte mso 9]></v:textbox></v:rect><![endif]-->
            </td>
          </tr>

          <tr>
            <td style='background:#ffffff; padding:28px 24px; border-radius:0 0 12px 12px; box-shadow:0 6px 18px rgba(16,24,40,.08);'>

           
              <p style='margin:0 0 10px; font-family:Arial,Helvetica,sans-serif; font-size:18px; font-weight:bold;'>
                <span style='color:{$blue};'>JobMatch</span><span style='color:{$yellow};'>DavOr?</span>
              </p>
              <div style='width:56px; height:3px; background:{$yellow}; border-radius:2px; margin:4px 0 18px;'></div>

              <p class='text' style='margin:0 0 14px; font-family:Arial,Helvetica,sans-serif; font-size:16px; color:{$ink};'>Hi there,</p>
              <p class='text' style='margin:0 0 20px; font-family:Arial,Helvetica,sans-serif; font-size:16px; color:#374151;'>
                Thanks for signing up! Please confirm your email address by clicking the button below.
              </p>

              <table role='presentation' cellspacing='0' cellpadding='0' border='0' align='left'>
                <tr>
                  <td align='center' bgcolor='{$blue}' style='border-radius:8px;'>
                    <a href='{$link}' class='btn'
                       style='font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:1; color:#ffffff; text-decoration:none; padding:14px 22px; display:block; border-radius:8px;'>
                      Confirm My Email
                    </a>
                  </td>
                </tr>
              </table>

              <div style='height:16px; line-height:16px; clear:both;'></div>

              <p class='muted' style='margin:0; font-family:Arial,Helvetica,sans-serif; font-size:13px; color:{$muted};'>
                If the button doesn't work, copy and paste this link into your browser:<br>
                <span style='word-break:break-all;'>" . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . "</span>
              </p>

              <hr style='border:none; height:1px; background:#eef2f7; margin:24px 0;'>

              <!-- Footer line with tiny mark (non-interactive) -->
              <table role='presentation' width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                  <td width='32' valign='top'>
                    <img src='{$logoSrc}' width='28' height='28' alt='' class='nd' style='border-radius:6px;'>
                  </td>
                  <td style='padding-left:8px;'>
                    <p class='muted' style='margin:0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:{$muted};'>
                      You’re receiving this email because you created a JobMatch DavOr account with this address.
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td align='center' style='padding:16px;'>
              <p class='muted' style='margin:0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#94a3b8;'>
                © " . date('Y') . " City of Mati • JobMatch DavOr
              </p>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>
</body>
</html>";

        $this->email->message($message);

        if (!$this->email->send(false)) {
            log_message('error', $this->email->print_debugger(['headers']));
            echo "<div style='margin:16px;padding:14px;background:#fff3cd;border:1px solid #ffeeba;border-radius:6px'>
                <strong>DEV ONLY:</strong> Activation link —
                <a href='" . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . "</a>
              </div>";
        }
    }

    public function resend_activation()
    {
        $email = $this->input->post('email', true);
        $user  = $this->user->get_by_email($email);

        if (!$user) {
            $this->session->set_flashdata('error', 'No account found for that email.');
            return redirect('auth/login');
        }

        if ((int)$user->is_active === 1) {
            $this->session->set_flashdata('msg', 'This account is already activated.');
            return redirect('auth/login');
        }

        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->user->set_activation_token($user->id, $token, $expires);
        $this->_send_activation_email($email, $token);

        $this->session->set_flashdata('msg', 'We sent a new activation link.');
        return redirect('auth/login');
    }
    public function activate($token)
    {
        $user = $this->user->get_by_token($token);

        if (!$user) {
            $this->session->set_flashdata('error', 'Invalid or expired activation link.');
            return redirect('auth/login');
        }

        $role = strtolower((string)$user->role);

        if (in_array($role, ['client', 'employer'], true)) {
            $this->user->mark_email_verified($user->id);
            if ($this->db->field_exists('status', 'users')) {
                $this->db->update('users', [
                    'status'     => 'pending',
                    'updated_at' => date('Y-m-d H:i:s')
                ], ['id' => $user->id]);
            }
            $this->session->set_flashdata('msg', 'Email confirmed. Your account is pending admin approval.');
        } else {
            $this->user->approve_user((int)$user->id, null);
            $this->session->set_flashdata('msg', 'Your account has been activated. You can now log in.');
        }

        return redirect('auth/login');
    }
    public function login()
    {
        if ($this->input->method() === 'post') {
            $identifier = trim((string)$this->input->post('email', true));
            $password   = (string)$this->input->post('password', true);

            if (strpos($identifier, '@') !== false) {
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            } else {
                $this->form_validation->set_rules('email', 'Username', 'required|min_length[3]');
            }
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run()) {
                list($ok, $res) = $this->user->verify_credentials($identifier, $password);

                if (!$ok) {
                    $this->session->set_flashdata('error', (string)$res);
                    return redirect('auth/login');
                }

                $user = $res;

                if (!empty($user->locked_until)) {
                    $lockedUntilTs = strtotime((string)$user->locked_until);
                    if ($lockedUntilTs !== false && $lockedUntilTs > time()) {
                        $this->session->set_flashdata('error', 'Your account is temporarily locked. Please try again later.');
                        return redirect('auth/login');
                    }
                }

                $role = strtolower($user->role ?? 'user');

                $this->session->set_userdata([
                    'user_id'        => (int)$user->id,
                    'email'          => (string)$user->email,
                    'first_name'     => (string)($user->first_name ?? ''),
                    'last_name'      => (string)($user->last_name ?? ''),
                    'role'           => $role,
                    'logged_in'      => true,
                    'status'         => isset($user->status) ? (string)$user->status : '',
                    'email_verified' => isset($user->email_verified) ? (int)$user->email_verified : 1,
                    'is_active'      => isset($user->is_active) ? (int)$user->is_active : 1,
                ]);
                $this->load->model('Presence_model', 'presence');
                $this->presence->ping((int)$user->id, 'online');

                $this->user->update_last_login((int)$user->id);
                if ($role === 'admin')  return redirect('dashboard/admin');
                if ($role === 'worker') return redirect('dashboard/worker');
                if ($role === 'client') return redirect('dashboard/client');
                if ($role === 'tesda_admin') return redirect('dashboard/tesda');
                return redirect('dashboard/user');
            }
        }
        $data['error'] = $this->session->flashdata('error');
        $data['info']  = $this->session->flashdata('info');
        $this->load->view('auth_login', $data);
    }


    public function logout()
    {
        $uid = 0;
        foreach (['id', 'user_id', 'uid', 'account_id'] as $k) {
            $v = $this->session->userdata($k);
            if (is_numeric($v)) {
                $uid = (int)$v;
                break;
            }
        }
        $this->load->model('Presence_model', 'presence');
        if ($uid > 0) {
            $this->presence->ping($uid, 'offline');
        }
        $this->session->set_flashdata('success', 'You have been logged out successfully.');
        $this->session->unset_userdata([
            'id',
            'user_id',
            'email',
            'first_name',
            'last_name',
            'role',
            'logged_in'
        ]);
        $this->session->sess_regenerate(true);

        return redirect('auth/login');
    }


    private function _start_local_captcha(): array
    {
        $a = random_int(10, 99);
        $b = random_int(1, 9);
        $ans = $a + $b;

        $this->session->set_userdata([
            'captcha_answer'     => $ans,
            'captcha_expires_at' => time() + 600,
            'form_started_at'    => time(),
        ]);

        return ['q' => "{$a} + {$b} = ?"];
    }

    public function _honeypot_clear($val): bool
    {
        if (!empty($val)) {
            $this->form_validation->set_message('_honeypot_clear', 'Form verification failed.');
            return false;
        }
        return true;
    }
    // ---------------------------------------------------------------
    // reCAPTCHA verification helpers
    // ---------------------------------------------------------------
    private function _verify_recaptcha(): bool
    {
        $token = (string) $this->input->post('g-recaptcha-response', true);
        if ($token === '') return false;

        $secret = (string) $this->config->item('recaptcha_secret');
        if ($secret === '') return false;

        $payload = http_build_query([
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $this->config->item('recaptcha_check_remoteip') ? $this->input->ip_address() : null,
        ]);

        if (function_exists('curl_init')) {
            $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            ]);
            $res = curl_exec($ch);
            curl_close($ch);
        } else {
            $ctx = stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
                        "Content-Length: " . strlen($payload) . "\r\n",
                    'content' => $payload,
                    'timeout' => 5,
                ]
            ]);
            $res = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $ctx);
        }

        if ($res === false) return false;
        $json = json_decode($res, true);
        return is_array($json) && !empty($json['success']);
    }

    public function _recaptcha_check(): bool
    {
        if ($this->_verify_recaptcha()) return true;
        $this->form_validation->set_message('_recaptcha_check', 'Please complete the reCAPTCHA.');
        return false;
    }

    public function _nocaptcha_check($val): bool
    {
        $expires = (int) $this->session->userdata('captcha_expires_at');
        $answer  = (int) $this->session->userdata('captcha_answer');
        $this->session->unset_userdata(['captcha_answer', 'captcha_expires_at']);

        if ($expires < time()) {
            $this->form_validation->set_message('_nocaptcha_check', 'The verification expired. Please try again.');
            return false;
        }
        if (!preg_match('/^\d+$/', (string)$val)) {
            $this->form_validation->set_message('_nocaptcha_check', 'Enter the correct answer.');
            return false;
        }

        if ((int)$val !== (int)$answer) {
            $this->form_validation->set_message('_nocaptcha_check', 'Wrong answer. Please try again.');
            return false;
        }
        $started = (int) $this->session->userdata('form_started_at');
        $this->session->unset_userdata('form_started_at');
        if ($started && (time() - $started) < 2) {
            $this->form_validation->set_message('_nocaptcha_check', 'Please take a moment before submitting.');
            return false;
        }

        return true;
    }

    public function forgot()
    {
        $this->load->library(['form_validation', 'email']);
        $this->load->helper(['url', 'security']);

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[190]');

            if ($this->form_validation->run()) {
                $email = strtolower(trim($this->input->post('email', true)));
                $genericMsg = 'If that email exists in our system, we’ve sent a reset link. Please check your inbox.';
                $recent = $this->db->where('ip', $this->input->ip_address())
                    ->where('created_at >=', date('Y-m-d H:i:s', time() - 120))
                    ->count_all_results('password_resets');
                if ($recent > 5) {
                    $this->session->set_flashdata('info', $genericMsg);
                    return redirect('auth/login');
                }

                $user = $this->db->get_where('users', ['email' => $email])->row();

                if ($user) {
                    $this->db->where('user_id', (int)$user->id)
                        ->where('used_at IS NULL', null, false)
                        ->delete('password_resets');
                    $selector  = bin2hex(random_bytes(9));
                    $validator = bin2hex(random_bytes(32));
                    $hashed    = hash('sha256', $validator, true);
                    $expires   = date('Y-m-d H:i:s', time() + 3600);

                    $this->db->insert('password_resets', [
                        'user_id'          => (int)$user->id,
                        'selector'         => $selector,
                        'hashed_validator' => $hashed,
                        'expires_at'       => $expires,
                        'used_at'          => null,
                        'created_at'       => date('Y-m-d H:i:s'),
                        'ip'               => $this->input->ip_address(),
                        'ua'               => substr((string)$this->input->user_agent(), 0, 255),
                    ]);

                    $token    = $selector . '.' . $validator;
                    $resetUrl = site_url('auth/reset/' . $token);
                    $fromEmail = $this->config->item('from_email') ?: ($this->config->item('smtp_user') ?: 'no-reply@jobmatch.local');
                    $fromName  = $this->config->item('support_name') ?: 'JobMatch DavOr Support';
                    $replyEmail = $this->config->item('reply_to_email') ?: $fromEmail;
                    $replyName  = $this->config->item('reply_to_name') ?: ($this->config->item('from_name') ?: 'JobMatch DavOr');

                    $this->email->from($fromEmail, $fromName);
                    $this->email->reply_to($replyEmail, $replyName);
                    $this->email->to($email);
                    $this->email->subject('Reset your JobMatch DavOr password');
                    $this->email->set_mailtype('html');
                    $this->email->set_newline("\r\n");
                    $this->email->set_crlf("\r\n");

                    $blue   = '#0b61ff';
                    $yellow = '#ffd200';
                    $ink    = '#111827';
                    $muted  = '#6b7280';

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

                    $msg = "<!doctype html>
<html>
<head>
  <meta charset='utf-8'>
  <meta name='x-apple-disable-message-reformatting'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <title>JobMatch DavOr — Confirm your email</title>
  <style>
    body,table,td,a{ -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
    table,td{ mso-table-lspace:0pt; mso-table-rspace:0pt; }
    img{ -ms-interpolation-mode:bicubic; border:0; height:auto; line-height:100%; outline:none; text-decoration:none; display:block; }
    table{ border-collapse:collapse!important; }
    body{ margin:0!important; padding:0!important; width:100%!important; background:#f3f5f7; }
    .btn:hover{ filter:brightness(.95); }
    .nd { pointer-events:none; user-select:none; -webkit-user-drag:none; -webkit-touch-callout:none; }
    @media (prefers-color-scheme: dark){
      .bg{ background:#0b1220!important; }
      .card{ background:#121926!important; }
      .muted{ color:#b5c0d0!important; }
      .heading,.text{ color:#e6edf6!important; }
    }
  </style>
</head>
<body>
  <table role='presentation' width='100%' ...>
    <tr>
      <td align='center' style='padding:32px 16px;'>
        <table role='presentation' width='100%' style='max-width:620px;' class='card'>

          <!-- HERO (identical to activation) -->
          <tr>
            <td style='padding:0; border-radius:12px 12px 0 0; overflow:hidden;'>
              <!--[if gte mso 9]>
              <v:rect xmlns:v=\"urn:schemas-microsoft-com:vml\" fill=\"true\" stroke=\"false\" style=\"width:620px;height:112px;\">
                <v:fill type=\"frame\" src=\"{$bannerSrc}\" color=\"{$blue}\" />
                <v:textbox inset=\"0,0,0,0\">
              <![endif]-->
              <div style=\"background: {$blue} url('{$bannerSrc}') no-repeat center/280px auto; height:112px;\"></div>
              <!--[if gte mso 9]></v:textbox></v:rect><![endif]-->
            </td>
          </tr>

          <!-- BODY -->
          <tr>
            <td style='background:#ffffff; padding:28px 24px; border-radius:0 0 12px 12px; box-shadow:0 6px 18px rgba(16,24,40,.08);'>
              <p style='margin:0 0 6px; font-family:Arial,Helvetica,sans-serif; font-size:18px; font-weight:bold;'>
                <span style='color:{$blue};'>JobMatch</span><span style='color:{$yellow};'DavOr?</span>
              </p>
              <div style='width:56px; height:3px; background:{$yellow}; border-radius:2px; margin:4px 0 16px;'></div>

              <h2 style='margin:0 0 12px; font-family:Arial,Helvetica,sans-serif; font-size:20px; color:#111827;'>Reset your password.</h2>
              <p style='margin:0 0 14px; font-family:Arial,Helvetica,sans-serif; font-size:16px; color:#111827;'>We received a request to reset your password.</p>
              <p style='margin:0 0 20px; font-family:Arial,Helvetica,sans-serif; font-size:16px; color:#374151;'>Click the button below to choose a new password.</p>

              <table role='presentation' cellspacing='0' cellpadding='0' border='0' align='left'>
                <tr>
                  <td align='center' bgcolor='{$blue}' style='border-radius:8px;'>
                    <a href='" . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . "' style='font-family:Arial,Helvetica,sans-serif; font-size:16px; line-height:1; color:#fff; text-decoration:none; padding:14px 22px; display:block; border-radius:8px;'>Reset Password</a>
                  </td>
                </tr>
              </table>

              <div style='height:16px; line-height:16px; clear:both;'></div>

              <p style='margin:0; font-family:Arial,Helvetica,sans-serif; font-size:13px; color:#6b7280;'>
                If the button doesn't work, copy and paste this link into your browser:<br>
                <span style='word-break:break-all;'>" . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . "</span>
              </p>

              <hr style='border:none; height:1px; background:#eef2f7; margin:24px 0;'>

              <p style='margin:0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#6b7280;'>If you didn’t request this, you can safely ignore this email—your password won’t change.</p>

              <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='margin-top:14px;'>
                <tr>
                  <td width='32' valign='top'><img src='{$logoSrc}' width='28' height='28' alt='' style='border-radius:6px; pointer-events:none; user-select:none;'></td>
                  <td style='padding-left:8px;'><p style='margin:0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#6b7280;'>You’re receiving this because a password reset was requested for your JobMatch DavOr account.</p></td>
                </tr>
              </table>
            </td>
          </tr>

          <tr><td align='center' style='padding:16px;'><p style='margin:0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#94a3b8;'>© " . date('Y') . " City of Mati • JobMatch DavOr</p></td></tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>";

                    $this->email->message($msg);

                    if (!$this->email->send(false)) {
                        log_message(
                            'error',
                            'Password reset email failed for user ' . $user->id . ' : ' . $this->email->print_debugger(['headers'])
                        );
                    }
                }

                $this->session->set_flashdata('success', $genericMsg);
                return redirect('auth/login');
            }
        }

        $this->load->view('auth_forgot', ['page_title' => 'Forgot Password']);
    }

    public function reset($token = null)
    {
        $this->load->library('form_validation');
        $selector = $validator = null;
        if (is_string($token) && strpos($token, '.') !== false) {
            list($selector, $validator) = explode('.', $token, 2);
        }
        if ($this->input->method() === 'post') {
            $selector  = $this->input->post('selector', true);
            $validator = $this->input->post('validator', true);
        }

        if (!$selector || !$validator || strlen($selector) > 24 || strlen($validator) > 128) {
            $this->session->set_flashdata('error', 'Invalid or expired reset link.');
            return redirect('auth/login');
        }

        $row = $this->db->get_where('password_resets', ['selector' => $selector])->row();
        if (!$row || $row->used_at !== null || strtotime($row->expires_at) < time()) {
            $this->session->set_flashdata('error', 'Invalid or expired reset link.');
            return redirect('auth/login');
        }
        $calc = hash('sha256', $validator, true);
        if (!hash_equals($row->hashed_validator, $calc)) {
            $this->session->set_flashdata('error', 'Invalid or expired reset link.');
            return redirect('auth/login');
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('password',  'New Password',    'required|min_length[8]|max_length[128]');
            $this->form_validation->set_rules('password2', 'Confirm Password', 'required|matches[password]');

            if ($this->form_validation->run()) {
                $hash = password_hash($this->input->post('password'), PASSWORD_DEFAULT);

                $this->db->where('id', (int)$row->user_id)
                    ->update('users', ['password_hash' => $hash, 'updated_at' => date('Y-m-d H:i:s')]);
                $this->db->where('id', (int)$row->id)->update('password_resets', ['used_at' => date('Y-m-d H:i:s')]);
                $this->db->where('user_id', (int)$row->user_id)->where('used_at IS NULL', null, false)->delete('password_resets');

                $this->session->set_flashdata('success', 'Password has been reset. You can now log in.');
                return redirect('auth/login');
            }
        }

        $data = [
            'page_title' => 'Reset Password',
            'selector'   => $selector,
            'validator'  => $validator
        ];
        $this->load->view('auth_reset', $data);
    }
}
