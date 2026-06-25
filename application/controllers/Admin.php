<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');  
        $this->load->database();     
        $this->load->helper(['url','form']); 
        $this->load->model('User_model', 'user');

        $role = strtolower((string)$this->session->userdata('role'));
        if (!$this->session->userdata('logged_in') || $role !== 'admin') {
            redirect('auth/login');
        }
    }

    public function pending_users()
    {
        $data['users'] = $this->user->get_pending_users();
        $this->load->view('admin_pending_users', $data);
    }

    public function approve($id)
    {
        $this->user->activate_user($id);
        redirect('admin/pending_users');
    }

    public function skills()
    {
        if (strtolower((string)$this->session->userdata('role')) !== 'admin') {
            show_error('Access Denied', 403);
            return;
        }

        $data['skills'] = $this->db->order_by('Title','ASC')->get('skills')->result();
        $this->load->view('skills', $data); 
    }

    public function saveSkill()
    {
        if (strtolower((string)$this->session->userdata('role')) !== 'admin') {
            show_error('Access Denied', 403);
            return;
        }

        $title = trim((string)$this->input->post('Title', TRUE));
        $desc  = trim((string)$this->input->post('Description', TRUE));

        if ($title === '') {
            $this->session->set_flashdata('error', 'Skill Title is required.');
            redirect('admin/skills?add=1');
            return;
        }

        $ok = $this->db->insert('skills', [
            'Title'       => $title,
            'Description' => $desc
        ]);

        $this->session->set_flashdata($ok ? 'success' : 'error',
            $ok ? 'Skill added successfully.' : 'Failed to add skill.');

        redirect('admin/skills');
    }
    public function updateSkill($id = null)
    {
        if (strtolower((string)$this->session->userdata('role')) !== 'admin') {
            show_error('Access Denied', 403);
            return;
        }

        if ($this->input->method() !== 'post') {
            show_error('Method Not Allowed', 405);
            return;
        }

        $id    = (int)$id;
        $title = trim((string)$this->input->post('Title', TRUE));
        $desc  = trim((string)$this->input->post('Description', TRUE));

        if ($title === '') {
            $this->session->set_flashdata('error', 'Skill Title is required.');
            redirect('admin/skills');
            return;
        }

        $row = $this->db->select('skillID')->get_where('skills', ['skillID' => $id])->row();
        if (!$row) {
            $this->session->set_flashdata('error', 'Skill not found.');
            redirect('admin/skills');
            return;
        }

        $ok = $this->db->where('skillID', $id)->update('skills', [
            'Title'       => $title,
            'Description' => $desc
        ]);

        $this->session->set_flashdata($ok ? 'success' : 'error',
            $ok ? 'Skill updated successfully.' : 'Failed to update skill.');

        redirect('admin/skills');
    }

public function deleteSkill($id = null)
{
    if (strtolower((string)$this->session->userdata('role')) !== 'admin') {
        show_error('Access Denied', 403);
        return;
    }

    if ($this->input->method() !== 'post') {
        show_error('Method Not Allowed', 405);
        return;
    }

    $id = (int)$id;
    if ($id <= 0) {
        $this->session->set_flashdata('error', 'Invalid skill ID.');
        redirect('admin/skills');
        return;
    }

    $row = $this->db->select('skillID')->get_where('skills', ['skillID' => $id])->row();
    if (!$row) {
        $this->session->set_flashdata('error', 'Skill not found.');
        redirect('admin/skills');
        return;
    }

    $ok = $this->db->delete('skills', ['skillID' => $id]);

    if ($ok && $this->db->affected_rows() > 0) {
        $this->session->set_flashdata('success', 'Skill deleted.');
    } else {
        $err = $this->db->error();
        $msg = !empty($err['message']) ? $err['message'] : 'Failed to delete skill.';
        $this->session->set_flashdata('error', $msg);
    }

    redirect('admin/skills');
}
public function change_password()
{
    if (strtolower((string)$this->session->userdata('role')) !== 'admin') {
        show_error('Access Denied', 403);
        return;
    }

    if ($this->input->method() === 'post') {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('current_password', 'Current Password', 'required|min_length[6]');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

        if ($this->form_validation->run()) {
            $uid = (int)$this->session->userdata('user_id');
            $user = $this->user->get_by_id($uid);

            if (!$user) {
                $this->session->set_flashdata('error', 'Account not found.');
                return redirect('admin/change_password');
            }

            if (!password_verify((string)$this->input->post('current_password', true), (string)$user->password_hash)) {
                $this->session->set_flashdata('error', 'Current password is incorrect.');
                return redirect('admin/change_password');
            }

            $ok = $this->user->update_password($uid, (string)$this->input->post('new_password', true));

            if ($ok) {
                $this->session->set_flashdata('success', 'Password updated successfully. Please log in again.');
                $this->session->unset_userdata(['user_id','email','first_name','last_name','role','logged_in']);
                $this->session->sess_regenerate(true);
                return redirect('auth/login');
            }

            $this->session->set_flashdata('error', 'Failed to update password. Please try again.');
            return redirect('admin/change_password');
        }
    }

    $data = [
        'page_title' => 'Change Password',
    ];
    $this->load->view('admin_change_password', $data);
}

}
