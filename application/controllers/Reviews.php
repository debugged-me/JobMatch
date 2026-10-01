<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reviews extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['session','form_validation']);
        $this->load->model('Reviews_model','reviews');
    }

    public function store()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method Not Allowed', 405);
        }

        $clientID = (int) $this->session->userdata('user_id'); 
        if (!$clientID) return redirect('auth/login');

        $this->form_validation->set_rules('transactionID', 'Transaction', 'required|integer');
        $this->form_validation->set_rules('workerID',      'Worker',      'required|integer');
        $this->form_validation->set_rules('rating',        'Rating',      'required|integer|greater_than[0]|less_than[6]');
        $this->form_validation->set_rules('comment',       'Comment',     'trim|max_length[2000]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            return redirect($this->_safe_back());
        }

        list($ok, $msg) = $this->reviews->add(
            (int)$this->input->post('transactionID'),
            $clientID,
            (int)$this->input->post('workerID'),
            (int)$this->input->post('rating'),
            (string)$this->input->post('comment')
        );

        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Thanks for your review!' : $msg);
        return redirect($this->_safe_back());
    }

    /**
     * Only allow same-site relative URLs in the `back` param —
     * prevents open-redirect abuse (phishing via redirect chains).
     */
    private function _safe_back(): string
    {
        $back = (string) $this->input->post('back');
        if ($back === '') {
            return 'dashboard/user';
        }
        // Same-site absolute path (/x — but not //host) or a URL under our base_url.
        if (preg_match('#^/(?!/)#', $back)) {
            return $back;
        }
        $base = rtrim((string) base_url(), '/');
        if ($base !== '' && stripos($back, $base . '/') === 0) {
            return $back;
        }
        return 'dashboard/user';
    }
}
