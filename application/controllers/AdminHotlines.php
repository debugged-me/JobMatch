<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminHotlines extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        // TODO: Ensure admin auth check here.
        $this->load->model('Hotline_model', 'Hotlines');
        $this->load->helper(['url','form','html']);
        $this->load->library('session');
    }

    public function index(){
        $data['page_title'] = 'Hotline Numbers';

        $page      = max(1, (int)$this->input->get('page', true));
        $perPage   = 15;

        $allRows = $this->Hotlines->all(false);

        $total     = count($allRows);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page      = min($page, $totalPages);
        $offset    = ($page - 1) * $perPage;
        $rows      = array_slice(array_values($allRows), $offset, $perPage);

        $data['rows']       = $rows;
        $data['total']      = $total;
        $data['pagination'] = [
            'page'        => $page,
            'total_pages' => $totalPages,
            'total'       => $total,
            'from'        => $total > 0 ? $offset + 1 : 0,
            'to'          => min($offset + $perPage, $total),
        ];

        $this->load->view('admin_hotlines_index', $data);
    }

    public function create(){
        if ($this->input->method() === 'post'){
            $data = $this->_sanitize();
            if ($this->_validate($data)){
                $this->Hotlines->create($data);
                $this->session->set_flashdata('success','Hotline added.');
                redirect('admin/hotlines');
                return;
            }
            $this->session->set_flashdata('error','Please fill required fields.');
            $form = (object)$data;
        } else {
            $form = (object)['title'=>'','agency'=>'','phone'=>'','notes'=>'','audience'=>'all','is_active'=>1,'sort_order'=>0];
        }
        $data['page_title'] = 'Add Hotline';
        $data['form'] = $form;
        $this->load->view('admin_hotlines_form', $data);
    }

    public function edit($id = null){
        $id = (int)$id;
        if ($this->input->method() === 'post'){
            $data = $this->_sanitize();
            if ($this->_validate($data)){
                $this->Hotlines->update($id, $data);
                $this->session->set_flashdata('success','Hotline updated.');
                redirect('admin/hotlines');
                return;
            }
            $this->session->set_flashdata('error','Please fill required fields.');
            $form = (object)$data;
        } else {
            $row = $this->Hotlines->get($id);
            if (!$row) show_404();
            $form = $row;
        }
        $data['page_title'] = 'Edit Hotline';
        $data['form'] = $form;
        $data['id'] = $id;
        $this->load->view('admin_hotlines_form', $data);
    }

    public function delete($id = null){
        if ($this->input->method() !== 'post') show_error('Method Not Allowed', 405);
        $row = $this->Hotlines->get((int)$id);
        if ($row){ $this->Hotlines->delete((int)$id); $this->session->set_flashdata('success','Hotline deleted.'); }
        redirect('admin/hotlines');
    }

    public function toggle($id = null){
        if ($this->input->method() !== 'post') show_error('Method Not Allowed', 405);
        if ($this->Hotlines->toggle((int)$id)){
            $this->session->set_flashdata('success','Status updated.');
        }
        redirect('admin/hotlines');
    }

    private function _sanitize(){
        return [
            'title'     => trim((string)$this->input->post('title')),
            'agency'    => trim((string)$this->input->post('agency')),
            'phone'     => trim((string)$this->input->post('phone')),
            'notes'     => trim((string)$this->input->post('notes')),
            'audience'  => in_array($this->input->post('audience'), ['all','worker','client','admin'], true) ? $this->input->post('audience') : 'all',
            'is_active' => $this->input->post('is_active') ? 1 : 0,
            'sort_order'=> (int)$this->input->post('sort_order'),
        ];
    }

    private function _validate($d){
        return ($d['title'] !== '' && $d['phone'] !== '');
    }
}
