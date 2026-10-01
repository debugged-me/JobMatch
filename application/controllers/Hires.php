<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hires extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url','security']);
        $this->load->library(['session']);
        $this->load->model('Personnel_model', 'personnelm');
    }

  
    public function accept()
    {
        if ($this->input->method() !== 'post') {
            show_error('Method Not Allowed', 405);
        }

        if (!$this->session->userdata('logged_in')) {
            return redirect('auth/login');
        }

        $role = (string)$this->session->userdata('role');
        $user_id = (int)$this->session->userdata('user_id');

        $client_id  = (int)$this->input->post('client_id', true);
        $worker_id  = (int)$this->input->post('worker_id', true);
        $project_id = (int)$this->input->post('project_id', true) ?: null;
        $rate       = $this->input->post('rate', true);
        $rate       = ($rate !== null && $rate !== '') ? (float)$rate : null;
        $rate_unit  = $this->input->post('rate_unit', true) ?: null;

        // Only the worker who was offered the hire may accept it.
        if ($role !== 'worker' || $user_id !== $worker_id || $worker_id <= 0 || $client_id <= 0) {
            $this->session->set_flashdata('error', 'You are not allowed to accept this hire.');
            return redirect('dashboard/worker');
        }

        // A hire request from that client must actually exist.
        if ($this->db->table_exists('tw_notifications')) {
            $invite = $this->db->order_by('id', 'DESC')->limit(1)
                ->get_where('tw_notifications', [
                    'user_id'  => $worker_id,
                    'actor_id' => $client_id,
                    'type'     => 'hire',
                ])->row();
            if (!$invite) {
                $this->session->set_flashdata('error', 'No hire request exists for this client.');
                return redirect('dashboard/worker');
            }
        }

        $id = $this->personnelm->ensure_hired($client_id, $worker_id, $project_id, $rate, $rate_unit);
        if ($id > 0) {
            $this->session->set_flashdata('success', 'Hire accepted. Added to your Personnel • Hired.');
        } else {
            $this->session->set_flashdata('error', 'Could not accept hire.');
        }

        return redirect('dashboard/worker');
    }
}
