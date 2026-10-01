<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Controller — shared base for all application controllers.
 *
 * Provides the single source of truth for authentication and role guards.
 * Controllers should call:
 *
 *   $this->require_login();          // redirect anonymous users to login
 *   $this->require_role('admin');    // 403 (or JSON 403) for wrong roles
 *   $this->require_post();           // 405 for non-POST mutations
 *
 * For API/AJAX endpoints, use the *_json variants which emit JSON errors
 * instead of redirects/HTML error pages.
 */
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    /* --------------------------------------------------------------- *
     *  Identity helpers
     * --------------------------------------------------------------- */

    protected function current_user_id(): int
    {
        return (int)($this->session->userdata('user_id')
            ?: $this->session->userdata('id') ?: 0);
    }

    protected function current_role(): string
    {
        $r = (string)($this->session->userdata('role') ?: $this->session->userdata('level') ?: '');
        $r = strtolower(trim(str_replace(['_', '-'], ' ', $r)));
        return preg_replace('/\s+/', ' ', $r);
    }

    protected function is_logged_in(): bool
    {
        return (bool)$this->session->userdata('logged_in') && $this->current_user_id() > 0;
    }

    /* --------------------------------------------------------------- *
     *  Guards — HTML pages (redirect/403 on failure)
     * --------------------------------------------------------------- */

    /** Redirect anonymous users to the login page. */
    protected function require_login(): void
    {
        if (!$this->is_logged_in()) {
            redirect('auth/login');
            exit;
        }
    }

    /**
     * Require the user's normalized role to be in $roles.
     * Pass a single role string or an array, e.g. ['admin','school_admin'].
     * Emits a 403 page on failure.
     */
    protected function require_role($roles): void
    {
        $this->require_login();
        $roles = is_array($roles) ? $roles : [$roles];
        if (!in_array($this->current_role(), $roles, true)) {
            show_error('You do not have permission to access this page.', 403, 'Forbidden');
        }
    }

    /** Only allow POST requests — use on every state-changing endpoint. */
    protected function require_post(): void
    {
        if ($this->input->method() !== 'post') {
            show_error('Method Not Allowed', 405);
        }
    }

    /* --------------------------------------------------------------- *
     *  Guards — JSON/API endpoints
     * --------------------------------------------------------------- */

    protected function json_out(array $data, int $status = 200): void
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    protected function require_login_json(): void
    {
        if (!$this->is_logged_in()) {
            $this->json_out(['ok' => false, 'error' => 'auth'], 401);
            exit;
        }
    }

    protected function require_role_json($roles): void
    {
        $this->require_login_json();
        $roles = is_array($roles) ? $roles : [$roles];
        if (!in_array($this->current_role(), $roles, true)) {
            $this->json_out(['ok' => false, 'error' => 'forbidden'], 403);
            exit;
        }
    }

    protected function require_post_json(): void
    {
        if ($this->input->method() !== 'post') {
            $this->json_out(['ok' => false, 'error' => 'method_not_allowed'], 405);
            exit;
        }
    }
}
