<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TesdaReports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form', 'html']);
        $this->load->library('session');
        $this->load->model('TesdaReports_model', 'tesdaReports');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        $role = $this->role_normalized();
        if (!in_array($role, ['tesda admin', 'admin'], true)) {
            show_error('Forbidden (TESDA only)', 403);
        }
    }

    private function role_normalized(): string
    {
        $raw = (string)($this->session->userdata('role') ?: $this->session->userdata('level') ?: '');
        $r = strtolower(trim($raw));
        $r = str_replace(['_', '-'], ' ', $r);
        $r = preg_replace('/\s+/', ' ', $r);
        return trim((string)$r);
    }

    private function me(): int
    {
        return (int)($this->session->userdata('user_id') ?: $this->session->userdata('id') ?: 0);
    }

    public function index(): void
    {
        $ownerId = $this->me();
        $q = trim((string)$this->input->get('q', true));

        $this->load->view('tesda_reports', [
            'page_title' => 'TESDA Worker Reports',
            'q' => $q,
            'summary' => $this->tesdaReports->summary($ownerId),
            'rows' => $this->tesdaReports->rows($q, $ownerId),
        ]);
    }
}
