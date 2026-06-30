<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Complaints extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
if (!$this->session->userdata('logged_in')) {
    return redirect('auth/login');
}
        $this->load->model('ComplaintModel', 'complaints');
        $this->load->helper(['url','form','security']);
        $this->load->library(['session','form_validation','upload']);
        $this->load->database();
    }

    // ===== USER =====
    public function index()
    {
        $userId = (int) ($this->session->userdata('user_id') ?: 0);
        $role   = (string) $this->session->userdata('role');
        $isAdmin = ($role === 'admin');

        if ($isAdmin) {
            // Admin sees every report (with reporter info)
            $data['title'] = 'All Scam Reports';
            $data['items'] = $this->complaints->listAll();
        } else {
            // Worker / client see only their own reports
            $data['title'] = 'My Scam Reports';
            $data['items'] = $this->complaints->listByReporter($userId);
        }

        $data['isAdmin'] = $isAdmin;
        $this->load->view('complaints_index', $data);
    }

    public function create()
    {
        $data['title'] = 'Report a Scam';
        $data['users'] = $this->db->select('id, first_name, last_name, role')
                                  ->from('users')
                                  ->where_in('role', ['worker','client'])
                                  ->order_by('first_name')
                                  ->get()->result();
        $this->load->view('complaints_create', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[8]');
        $this->form_validation->set_rules('details', 'Details', 'required|min_length[20]');
        $this->form_validation->set_rules('complaint_type', 'Type', 'required|in_list[scam,abuse,spam,other]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
return redirect('complaints/create');
        }

    $uid  = (int) ($this->session->userdata('user_id') ?: 0);
$role = (string) $this->session->userdata('role'); // 'client' | 'worker' | 'admin'


        // Optional target user
        $against_user_id = $this->input->post('against_user_id', true);
        $against_role    = null;
        if ($against_user_id) {
            $u = $this->db->where('id', (int)$against_user_id)->get('users')->row();
            if ($u) $against_role = $u->role;
        }

        // Multiple evidence files -> /uploads (no subfolder)
        $evidence = [];
        if (!empty($_FILES['evidence']['name'][0])) {
            $files = $_FILES['evidence'];
            $count = count($files['name']);
            for ($i=0; $i<$count; $i++) {
                $_FILES['file']['name']     = $files['name'][$i];
                $_FILES['file']['type']     = $files['type'][$i];
                $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['file']['error']    = $files['error'][$i];
                $_FILES['file']['size']     = $files['size'][$i];

             $config = [
    'upload_path'   => FCPATH.'uploads/',
    'allowed_types' => 'jpg|jpeg|png|pdf',
    'max_size'      => 6144,
    'encrypt_name'  => true
];
                $this->upload->initialize($config);

                if ($this->upload->do_upload('file')) {
                    $up = $this->upload->data();
                    $evidence[] = [
                        'path'  => 'uploads/'.$up['file_name'],
                        'name'  => $up['orig_name'],
                        'type'  => $up['file_type'],
                        'size'  => $up['file_size']
                    ];
                }
            }
        }

   $payload = [
    'reporter_id'     => $uid,
    'reporter_role'   => ($role === 'client' ? 'client' : 'worker'),
    'against_user_id' => !empty($against_user_id) ? (int)$against_user_id : null,
    'against_role'    => $against_role,
    'complaint_type'  => $this->input->post('complaint_type', true),
    'title'           => trim($this->input->post('title', true)),
    'details'         => trim($this->input->post('details', true)),
    'evidence_files'  => !empty($evidence) ? json_encode($evidence) : null,
    'status'          => 'open',
    'created_at'      => gmdate('Y-m-d H:i:s') 
];


        $id = $this->complaints->create($payload);

        // Optional: notify admins (tw_notifications)
        if ($this->db->table_exists('tw_notifications')) {
            $admins = $this->db->where('role','admin')->get('users')->result();
            foreach ($admins as $a) {
                $this->db->insert('tw_notifications', [
                    'user_id'  => (string)$a->id,
                    'actor_id' => (string)$uid,
                    'type'     => 'system',
                    'title'    => 'New scam report submitted',
                    'body'     => 'Complaint #'.$id.' - '.$payload['title'],
                    'link'     => site_url('admin/complaints/'.$id)
                ]);
            }
        }

        // Optional: audit log
        if ($this->db->table_exists('audit_log')) {
            $this->db->insert('audit_log', [
                'actor_id' => $uid,
                'action'   => 'complaint_create',
                'meta'     => json_encode(['complaint_id'=>$id])
            ]);
        }

        $this->session->set_flashdata('success', 'Your report has been submitted. Our admin will review it soon.');
        return redirect('complaints');
    }

    public function show($id)
    {
$uid = (int) ($this->session->userdata('user_id') ?: 0);
        $role = (string) $this->session->userdata('role');

        $item = $this->complaints->find($id);
        if (!$item) show_404();

        // Access: owner or admin
        if ($item->reporter_id != $uid && $role !== 'admin') {
            show_error('Forbidden', 403);
        }

        $data['title'] = 'Complaint #'.$id;
        $data['item']  = $item;
        $this->load->view('complaints_show', $data);
    }

    // ===== ADMIN =====
    public function admin_index()
    {
        if ($this->session->userdata('role') !== 'admin') show_error('Forbidden', 403);

        $page    = max(1, (int)$this->input->get('page', true));
        $perPage = 15;

        $filters = [
            'status' => $this->input->get('status', true),
            'type'   => $this->input->get('type', true),
            'q'      => $this->input->get('q', true),
        ];

        $total    = $this->complaints->countAll($filters);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page     = min($page, $totalPages);
        $offset   = ($page - 1) * $perPage;
        $filters['limit']  = $perPage;
        $filters['offset'] = $offset;

        $data['title']    = 'Complaints (All)';
        $data['items']    = $this->complaints->listAll($filters);
        $data['filter']   = $filters;
        $data['total']    = $total;
        $data['summary']  = $this->complaints->statusSummary();
        $data['pagination'] = [
            'page'        => $page,
            'total_pages' => $totalPages,
            'total'       => $total,
            'from'        => $total > 0 ? $offset + 1 : 0,
            'to'          => min($offset + $perPage, $total),
        ];
        $this->load->view('complaints_admin_index', $data);
    }

    public function admin_show($id)
    {
        if ($this->session->userdata('role') !== 'admin') show_error('Forbidden', 403);

        $item = $this->complaints->find($id);
        if (!$item) show_404();

        $data['title'] = 'Complaint #'.$id;
        $data['item']  = $item;
        $this->load->view('complaints_admin_show', $data);
    }

  public function admin_update_status($id)
{
    if ($this->session->userdata('role') !== 'admin') show_error('Forbidden', 403);

    $status = $this->input->post('status', true); // open|under_review|resolved|dismissed
    $notes  = $this->input->post('admin_notes', true);

    $ok = $this->complaints->updateStatus($id, $status, $notes);

    if ($ok) {
        $item = $this->complaints->find($id);

        if ($this->db->table_exists('tw_notifications')) {
            $this->db->insert('tw_notifications', [
                'user_id'  => (string)$item->reporter_id,
                'actor_id' => (string) ($this->session->userdata('user_id') ?: 0),
                'type'     => 'system',
                'title'    => 'Complaint status updated',
                'body'     => 'Complaint #'.$id.' is now '.ucwords(str_replace('_',' ',$status)),
                'link'     => site_url('complaints/'.$id)
            ]);
        }

        if ($this->db->table_exists('audit_log')) {
            $this->db->insert('audit_log', [
                'actor_id' => (int) ($this->session->userdata('user_id') ?: 0),
                'action'   => 'complaint_status_update',
                'meta'     => json_encode(['complaint_id'=>$id,'status'=>$status])
            ]);
        }

        $this->session->set_flashdata('success', 'Status updated.');
    } else {
        $this->session->set_flashdata('danger', 'No changes made.');
    }

    // NEW: decide where to go
    $where = $this->input->post('redirect_to', true);
    if ($where === 'list') {
        return redirect('admin/complaints');          // go to admin list
    }
    // default: stay on detail
    return redirect('admin/complaints/'.$id);
}


public function edit($id)
{
    $uid  = (int) ($this->session->userdata('user_id') ?: 0);
    $role = (string) $this->session->userdata('role');

    $item = $this->complaints->find($id);
    if (!$item) show_404();

    // only owner or admin may edit
    if ($item->reporter_id != $uid && $role !== 'admin') show_error('Forbidden', 403);

    $data['title'] = 'Edit Report';
    $data['item']  = $item;

    // (optional) for "reported user" select, same as create()
    $data['users'] = $this->db->select('id, first_name, last_name, role')
                              ->from('users')
                              ->where_in('role', ['worker','client'])
                              ->order_by('first_name')
                              ->get()->result();

    $this->load->view('complaints_edit', $data);
}

public function update($id)
{
    $uid  = (int) ($this->session->userdata('user_id') ?: 0);
    $role = (string) $this->session->userdata('role');

    $item = $this->complaints->find($id);
    if (!$item) show_404();
    if ($item->reporter_id != $uid && $role !== 'admin') show_error('Forbidden', 403);

    $this->form_validation->set_rules('title', 'Title', 'required|min_length[8]');
    $this->form_validation->set_rules('details', 'Details', 'required|min_length[20]');
    $this->form_validation->set_rules('complaint_type', 'Type', 'required|in_list[scam,abuse,spam,other]');

    if (!$this->form_validation->run()) {
        $this->session->set_flashdata('error', validation_errors());
        return redirect('complaints/edit/'.$id);
    }

    // Optional target user (can be cleared)
    $against_user_id = $this->input->post('against_user_id', true);
    $against_role = null;
    if ($against_user_id) {
        $u = $this->db->where('id', (int)$against_user_id)->get('users')->row();
        if ($u) $against_role = $u->role;
    }

    $payload = [
        'complaint_type'  => $this->input->post('complaint_type', true),
        'title'           => trim($this->input->post('title', true)),
        'details'         => trim($this->input->post('details', true)),
        'against_user_id' => !empty($against_user_id) ? (int)$against_user_id : null,
        'against_role'    => $against_role,
        'updated_at'      => gmdate('Y-m-d H:i:s')
    ];

    $ok = $this->complaints->update($id, $payload);
    if ($ok) {
        $this->session->set_flashdata('success', 'Report updated.');
    } else {
        $this->session->set_flashdata('error', 'No changes saved.');
    }
    return redirect('complaints');
}

public function delete($id)
{
    if (strtoupper($this->input->method()) !== 'POST') show_error('Method Not Allowed', 405);

    $uid  = (int) ($this->session->userdata('user_id') ?: 0);
    $role = (string) $this->session->userdata('role');

    $item = $this->complaints->find($id);
    if (!$item) show_404();
    if ($item->reporter_id != $uid && $role !== 'admin') show_error('Forbidden', 403);

    // If you want to also remove evidence files, do it here (optional)

    $ok = ($role === 'admin')
        ? $this->complaints->deleteById($id)
        : $this->complaints->deleteByIdAndOwner($id, $uid);

    $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Report deleted.' : 'Delete failed.');
    return redirect('complaints');
}

}
