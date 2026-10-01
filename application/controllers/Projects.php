<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url','html','security','form']);
        $this->load->library(['session','pagination','form_validation','upload']);
        $this->load->model('ClientProjects_model', 'cpm');
        $this->load->model('ClientProfile_model', 'cp');
    }
// Schema lives in database/migrations/001_schema_consolidation.sql —
// never ALTER at request time.

    private function _must_be_client()
    {
        if (!$this->session->userdata('logged_in')) return redirect('auth/login');
        if ((string)$this->session->userdata('role') !== 'client') {
            $this->session->set_flashdata('error', 'Clients only.');
            return redirect('dashboard/'.($this->session->userdata('role') ?: 'user'));
        }
    }

   public function active()
{
    if (!$this->session->userdata('logged_in')) return redirect('auth/login');

    $uid   = (int)$this->session->userdata('user_id');
    $role  = (string)$this->session->userdata('role');
    $page  = max(1, (int)$this->input->get('page'));
    $pp    = 12;
    $off   = ($page - 1) * $pp;

    $profile = $this->cp->get($uid);

    if ($role === 'client') {
        $rows  = $this->cpm->active_by_client($uid, $pp, $off);
        $total = $this->cpm->active_total($uid);

        $config = [
            'base_url'             => site_url('projects/active'),
            'total_rows'           => $total,
            'per_page'             => $pp,
            'page_query_string'    => true,
            'query_string_segment' => 'page',
            'use_page_numbers'     => true,
        ];
        $this->pagination->initialize($config);

        $data = [
            'page_title' => 'My Project/s',
            'mode'       => 'list',
            'items'      => $this->_map_rows($rows),
            'pagination' => $this->pagination->create_links(),
            'isClient'   => true,
            'tab'        => 'active',
            'profile'    => $profile,
        ];
        return $this->load->view('projects_active', $data);
    }

    if ($role === 'worker') {
        $rows = $this->db->select('p.*')
            ->from('transactions t')
            ->join('client_projects p', 'p.id = t.projectID', 'inner')
            ->where('t.workerID', $uid)
            ->where_in('t.status', ['accepted','active'])
            ->where('p.status', 'active')
            ->group_by('p.id')
            ->order_by('t.transactionID', 'DESC')
            ->limit($pp, $off)
            ->get()->result();

        $trow = $this->db->select('COUNT(DISTINCT p.id) AS c', false)
            ->from('transactions t')
            ->join('client_projects p', 'p.id = t.projectID', 'inner')
            ->where('t.workerID', $uid)
            ->where_in('t.status', ['accepted','active'])
            ->where('p.status', 'active')
            ->get()->row();
        $total = (int)($trow->c ?? 0);

        $config = [
            'base_url'             => site_url('projects/active'),
            'total_rows'           => $total,
            'per_page'             => $pp,
            'page_query_string'    => true,
            'query_string_segment' => 'page',
            'use_page_numbers'     => true,
        ];
        $this->pagination->initialize($config);

        $data = [
            'page_title' => 'My Project/s',
            'mode'       => 'list',
            'items'      => $this->_map_rows($rows),
            'pagination' => $this->pagination->create_links(),
            'isClient'   => false, 
            'tab'        => 'active',
            'profile'    => $profile,
        ];
        return $this->load->view('projects_active', $data);
    }

    $this->session->set_flashdata('error', 'Unauthorized.');
    return redirect('dashboard/'.($role ?: 'user'));
}

   public function history()
{
    if (!$this->session->userdata('logged_in')) return redirect('auth/login');

    $role = (string)$this->session->userdata('role');
    if ($role === 'worker') {
        return redirect('projects/worker_history');
    }

    $this->_must_be_client();

    $uid   = (int)$this->session->userdata('user_id');
    $page  = max(1, (int)$this->input->get('page'));
    $pp    = 12;
    $off   = ($page - 1) * $pp;

    $profile = $this->cp->get($uid);

    $this->db->start_cache();
    $this->db->from('client_projects cp');
    $this->db->join('client_project_hide h', 'h.projectID = cp.id AND h.clientID = '.$uid, 'left');
    $this->db->where('cp.clientID', $uid);
    $this->db->where('cp.status', 'closed');
    $this->db->where('h.projectID IS NULL', null, false);
    $this->db->stop_cache();

    $total = (int)$this->db->select('COUNT(*) AS c', false)->get()->row()->c;

    $rows = $this->db->select('cp.*')
        ->order_by('cp.updated_at', 'DESC')
        ->limit($pp, $off)
        ->get()->result();

    $this->db->flush_cache();

    $config = [
        'base_url'             => site_url('projects/history'),
        'total_rows'           => $total,
        'per_page'             => $pp,
        'page_query_string'    => true,
        'query_string_segment' => 'page',
        'use_page_numbers'     => true,
    ];
    $this->pagination->initialize($config);

    $data = [
        'page_title' => 'Project History',
        'mode'       => 'list',
        'items'      => $this->_map_rows($rows),
        'pagination' => $this->pagination->create_links(),
        'isClient'   => true,
        'tab'        => 'history',
        'profile'    => $profile,
    ];
    $this->load->view('projects_active', $data);
}
public function clear_history()
{
    $this->_must_be_client();
    if (strtoupper($this->input->method()) !== 'POST') {
        return redirect('projects/history');
    }

    $uid = (int)$this->session->userdata('user_id');

    $rows = $this->db->select('id')
        ->from('client_projects')
        ->where('clientID', $uid)
        ->where('status', 'closed')
        ->get()->result();

    if (!$rows) {
        $this->session->set_flashdata('success', 'Nothing to clear.');
        return redirect('projects/history');
    }

    $hidden = $this->db->select('projectID')->from('client_project_hide')->where('clientID', $uid)->get()->result_array();
    $already = array_map('intval', array_column($hidden, 'projectID'));

    $ins = [];
    foreach ($rows as $r) {
        $pid = (int)$r->id;
        if (!in_array($pid, $already, true)) {
            $ins[] = ['clientID' => $uid, 'projectID' => $pid, 'hidden_at' => date('Y-m-d H:i:s')];
        }
    }
    if (!empty($ins)) {
        $this->db->insert_batch('client_project_hide', $ins);
    }

    $this->session->set_flashdata('success', 'History cleared.');
    return redirect('projects/history');
}

public function restore_history()
{
    $this->_must_be_client();
    if (strtoupper($this->input->method()) !== 'POST') {
        return redirect('projects/history');
    }

    $uid = (int)$this->session->userdata('user_id');
    $this->db->where('clientID', $uid)->delete('client_project_hide');

    $this->session->set_flashdata('success', 'History restored.');
    return redirect('projects/history');
}

   public function create()
{
    $this->_must_be_client();

    $uid     = (int)$this->session->userdata('user_id');
    $profile = $this->cp->get($uid);

    $skills = $this->db->order_by('Title','ASC')->get('skills')->result();

    $data = [
        'page_title' => 'Post a Project',
        'mode'       => 'form',
        'isClient'   => true,
        'tab'        => 'active',
        'profile'    => $profile,
        'skills'     => $skills,
    ];
    $this->load->view('projects_active', $data);
}


  public function store()
{
    $this->_must_be_client();

    $uid = (int)$this->session->userdata('user_id');

    $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[150]');
    $this->form_validation->set_rules('description', 'Description', 'trim|max_length[5000]');
    $this->form_validation->set_rules('budget_min', 'Budget Min', 'trim|numeric');
    $this->form_validation->set_rules('budget_max', 'Budget Max', 'trim|numeric');
    $this->form_validation->set_rules('city', 'City', 'trim|max_length[120]');
    $this->form_validation->set_rules('province', 'Province', 'trim|max_length[120]');
    $this->form_validation->set_rules('brgy', 'Barangay', 'trim|max_length[120]');
    $this->form_validation->set_rules('visibility', 'Visibility', 'trim|in_list[public,private]');
$this->form_validation->set_rules('employment_term', 'Employment Term', 'trim|in_list[1_month,6_months,1_year,project_based]');
$this->form_validation->set_rules('payment_cycle', 'Payment', 'trim|in_list[monthly,yearly]');
$this->form_validation->set_rules('project_duration_value', 'Project Duration (Value)', 'trim|integer|greater_than_equal_to[1]');
$this->form_validation->set_rules('project_duration_unit', 'Project Duration (Unit)', 'trim|in_list[day,week,month,year]');

    if (!$this->form_validation->run()) {
        $this->session->set_flashdata('error', validation_errors('', ''));
        return redirect('projects/create');
    }

        $fileList = [];
        if (!empty($_FILES['files']['name'][0])) {
            $dir = FCPATH.'uploads/projects';
            if (!is_dir($dir)) @mkdir($dir, 0777, true);

            $files = $_FILES['files'];
            $MAX   = 12;
            $count = min(count($files['name']), $MAX);

           for ($i = 0; $i < $count; $i++) {
    if (empty($files['name'][$i])) continue;

    $_FILES['one'] = [
        'name'     => $files['name'][$i],
        'type'     => $files['type'][$i],
        'tmp_name' => $files['tmp_name'][$i],
        'error'    => $files['error'][$i],
        'size'     => $files['size'][$i],
    ];

    $cfg = [
        'upload_path'      => $dir,
        'allowed_types'    => 'pdf|jpg|jpeg|png|webp',
'max_size' => 2048, 
        'file_ext_tolower' => TRUE,
        'remove_spaces'    => TRUE,
        'encrypt_name'     => TRUE,
        'detect_mime'      => TRUE,
    ];
    $this->upload->initialize($cfg);

    if ($this->upload->do_upload('one')) {
        $u    = $this->upload->data();
        $path = $u['full_path'];
        $ext  = $u['file_ext'];

        if (!validate_uploaded_file_signature($path, $ext)) {
            @unlink($path);
            $this->session->set_flashdata('error', 'Invalid or unsafe file uploaded: '.$files['name'][$i]);
            return redirect('projects/create');
        }

        $imgExts = ['.jpg','.jpeg','.png','.gif','.webp'];
        if (in_array(strtolower($ext), $imgExts, true) && function_exists('safe_image_reencode')) {
            if (!safe_image_reencode($path, $ext)) {
                @unlink($path);
                $this->session->set_flashdata('error', 'Failed to sanitize image: '.$files['name'][$i]);
                return redirect('projects/create');
            }
        }

        $fileList[] = 'uploads/projects/'.$u['file_name'];
    } else {
        $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
        return redirect('projects/create');
    }
}

        }
    $budget_min = $this->input->post('budget_min', true);
    $budget_max = $this->input->post('budget_max', true);

$rawCats = $this->input->post('categories'); 
$catIds   = [];
$catNames = [];

if (is_array($rawCats)) {
    foreach ($rawCats as $v) {
        if (ctype_digit((string)$v)) {
            $catIds[] = (int)$v; 
        } else {
            $name = trim((string)$v);  
            if ($name !== '') $catNames[] = $name;
        }
    }
}

if (!empty($catIds)) {
    $rows = $this->db->select('Title')->from('skills')->where_in('skillID', $catIds)->get()->result();
    foreach ($rows as $r) { if (!empty($r->Title)) $catNames[] = $r->Title; }
}

$resolved_category = implode(', ', array_unique($catNames));

if ($resolved_category === '') {
    $category_id   = $this->input->post('category_id', true);
    $category_text = trim((string)$this->input->post('category_text', true));
    $legacy_text   = trim((string)$this->input->post('category', true));

    if ($category_id !== null && $category_id !== '') {
        $row = $this->db->select('Title')->from('skills')->where('skillID', (int)$category_id)->get()->row();
        if ($row && $row->Title) $resolved_category = $row->Title;
    }
    if ($resolved_category === '') {
        $resolved_category = $category_text !== '' ? $category_text : ($legacy_text !== '' ? $legacy_text : null);
    }
}


    $payload = [
        'title'       => $this->input->post('title', true),
        'description' => $this->input->post('description', true),
        'category'    => $resolved_category, 
        'budget_min'  => ($budget_min !== '') ? (float)$budget_min : null,
        'budget_max'  => ($budget_max !== '') ? (float)$budget_max : null,
        'city'        => $this->input->post('city', true) ?: null,
        'province'    => $this->input->post('province', true) ?: null,
        'brgy'        => $this->input->post('brgy', true) ?: null,
        'files'       => !empty($fileList) ? json_encode($fileList) : null,
        'visibility'  => $this->input->post('visibility', true) ?: 'public',
        'employment_term'        => $this->input->post('employment_term', true) ?: null,          // 1_month | 6_months | 1_year | project_based
'payment_cycle'          => $this->input->post('payment_cycle', true) ?: null,            // monthly | yearly
'project_duration_value' => $this->input->post('project_duration_value', true) !== '' ? (int)$this->input->post('project_duration_value', true) : null,
'project_duration_unit'  => $this->input->post('project_duration_unit', true) ?: null,    // day|week|month|year

        'status'      => 'active',
    ];

    $ok = $this->cpm->create($uid, $payload);
    if (!$ok) {
        $this->session->set_flashdata('error', 'Could not post project. Please try again.');
        return redirect('projects/create');
    }

    $this->session->set_flashdata('success', 'Project posted.');
    return redirect('projects/active');
}

  public function close($projectID)
{
    if ($this->input->method() !== 'post') {
        show_error('Method Not Allowed', 405);
    }
    $this->_must_be_client();
    $uid = (int)$this->session->userdata('user_id');
    $pid = (int)$projectID;

    if ($this->cpm->close($uid, $pid)) {
        $this->session->set_flashdata('success', 'Project closed.');
        return redirect('projects/history?review='.$pid);
    } else {
        $this->session->set_flashdata('error', 'Unable to close project.');
        return redirect('projects/active');
    }
}


    private function _map_rows(array $rows): array
    {
        $map = [];
        foreach ($rows as $r) {
            $files = [];
            if (!empty($r->files)) {
                $tmp = json_decode($r->files, true);
                if (is_array($tmp)) $files = $tmp;
            }
            $map[] = [
                'id'          => (int)$r->id,
                'title'       => (string)$r->title,
                'description' => (string)($r->description ?? ''),
                'category'    => (string)($r->category ?? ''),
                'files'       => $files,
                'visibility'  => (string)$r->visibility,
                'status'      => (string)$r->status,
                'city'        => (string)($r->city ?? ''),
                'province'    => (string)($r->province ?? ''),
                'brgy'        => (string)($r->brgy ?? ''),
                'budget_min'  => $r->budget_min !== null ? (float)$r->budget_min : null,
                'budget_max'  => $r->budget_max !== null ? (float)$r->budget_max : null,
                'rate_unit'   => (string)($r->rate_unit ?? ''),
                'created_at'  => (string)$r->created_at,
                'updated_at'  => (string)$r->updated_at,
                'employment_term'        => isset($r->employment_term) ? (string)$r->employment_term : '',
'payment_cycle'          => isset($r->payment_cycle) ? (string)$r->payment_cycle : '',
'project_duration_value' => isset($r->project_duration_value) ? (int)$r->project_duration_value : null,
'project_duration_unit'  => isset($r->project_duration_unit) ? (string)$r->project_duration_unit : '',

            ];
        }
        return $map;
    }

    private function _out($ok, $msg='OK', $extra=[])
    {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(array_merge(['ok'=>(bool)$ok,'message'=>$msg], (array)$extra)));
    }

    public function api_active_min()
{
    if (!$this->session->userdata('logged_in') || $this->session->userdata('role')!=='client') {
        return $this->_json(false, 'Unauthorized');
    }
    $uid = (int)$this->session->userdata('user_id');

    $rows = $this->cpm->active_by_client($uid, 100, 0);

    $items = [];
    foreach ($rows as $r) {
        $files = [];
        if (!empty($r->files)) {
            $tmp = json_decode($r->files, true);
            if (is_array($tmp)) $files = $tmp;
        }
        $cover = '';
        foreach ($files as $f) {
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $cover = base_url($f);
                break;
            }
        }
        $items[] = [
            'id'    => (int)$r->id,
            'title' => (string)$r->title,
            'cover' => $cover,
            'files_count' => count($files),
        ];
    }

    return $this->_json(true, 'OK', ['items'=>$items]);
}

public function api_one($project_id = null)
{
    if (!$this->session->userdata('logged_in') || $this->session->userdata('role')!=='client') {
        return $this->_json(false, 'Unauthorized');
    }
    $uid = (int)$this->session->userdata('user_id');
    $pid = (int)$project_id;
    if ($pid <= 0) return $this->_json(false, 'Invalid project');

    if (method_exists($this->cpm, 'find_for_client')) {
        $r = $this->cpm->find_for_client($uid, $pid);
    } else {
        $r = null;
        $rows = $this->cpm->active_by_client($uid, 500, 0);
        foreach ($rows as $row) { if ((int)$row->id === $pid) { $r = $row; break; } }
        if (!$r && method_exists($this->cpm, 'history_by_client')) {
            $rows = $this->cpm->history_by_client($uid, 500, 0);
            foreach ($rows as $row) { if ((int)$row->id === $pid) { $r = $row; break; } }
        }
    }

    if (!$r) return $this->_json(false, 'Not found');

    $files = [];
    if (!empty($r->files)) {
        $tmp = json_decode($r->files, true);
        if (is_array($tmp)) {
            foreach ($tmp as $f) {
                $ext  = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                $type = in_array($ext, ['jpg','jpeg','png','webp']) ? 'image' : ($ext==='pdf' ? 'pdf' : 'file');
                $files[] = ['url'=>base_url($f), 'name'=>basename($f), 'type'=>$type];
            }
        }
    }

    return $this->_json(true, 'OK', [
        'id'    => (int)$r->id,
        'title' => (string)$r->title,
        'files' => $files,
    ]);
}

private function _json($ok, $msg='OK', $extra=[])
{
    $this->output->set_content_type('application/json')
        ->set_output(json_encode(array_merge(['ok'=>(bool)$ok,'message'=>$msg], (array)$extra)));
}

public function find_workers($projectID = null)
{
    $this->_must_be_client();
    $uid = (int)$this->session->userdata('user_id');
    $pid = (int)$projectID;

    if ($pid <= 0) {
        $this->session->set_flashdata('error', 'Invalid project.');
        return redirect('projects/active');
    }

    $r = null;
    if (method_exists($this->cpm, 'find_for_client')) {
        $r = $this->cpm->find_for_client($uid, $pid);
    } else {
        $rows = $this->cpm->active_by_client($uid, 500, 0);
        foreach ($rows as $row) { if ((int)$row->id === $pid) { $r = $row; break; } }
        if (!$r && method_exists($this->cpm, 'history_by_client')) {
            $rows = $this->cpm->history_by_client($uid, 500, 0);
            foreach ($rows as $row) { if ((int)$row->id === $pid) { $r = $row; break; } }
        }
    }

    if (!$r) {
        $this->session->set_flashdata('error', 'Project not found.');
        return redirect('projects/active');
    }

    $category = trim((string)($r->category ?? ''));
    $title    = trim((string)($r->title ?? ''));

    $q = $category !== '' ? $category : $title;

    if ($q === '') {
        $this->session->set_flashdata('error', 'This project has no searchable category or title.');
        return redirect('projects/active');
    }

    $url = site_url('search?q=' . rawurlencode($q));
    return redirect($url);
}
public function save_review()
{
    if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'client') {
        return redirect('auth/login');
    }

    $uid     = (int)$this->session->userdata('user_id');
    $txid    = (int)$this->input->post('transactionID');
    $rating  = (int)$this->input->post('rating');
    $comment = trim((string)$this->input->post('comment', true));

    if ($txid <= 0 || $rating < 1 || $rating > 5) {
        $this->session->set_flashdata('error', 'Invalid review.');
        return redirect('projects/history');
    }

    $tx = $this->db->select('transactionID, clientID, workerID, projectID')
                   ->from('transactions')
                   ->where('transactionID', $txid)
                   ->get()->row();

    if (!$tx || (int)$tx->clientID !== $uid) {
        $this->session->set_flashdata('error', 'You cannot review this transaction.');
        return redirect('projects/history');
    }

    $exists = $this->db->select('reviewID')
                       ->from('reviews')
                       ->where('transactionID', $txid)
                       ->where('clientID', $uid)
                       ->get()->row();

    if ($exists) {
        $this->session->set_flashdata('error', 'You already submitted a review.');
        return redirect('projects/history');
    }

    $ok = $this->db->insert('reviews', [
        'transactionID' => (int)$tx->transactionID,
        'clientID'      => $uid,
        'workerID'      => (int)$tx->workerID,
        'rating'        => $rating,
        'comment'       => ($comment !== '' ? $comment : null),
        'created_at'    => date('Y-m-d H:i:s')
    ]);

    if ($ok) {
        $this->session->set_flashdata('success', 'Review submitted. Thank you!');
    } else {
        $this->session->set_flashdata('error', 'Could not save your review.');
    }

    return redirect('projects/history');
}
private function _must_be_worker()
{
    if (!$this->session->userdata('logged_in')) return redirect('auth/login');
    if ((string)$this->session->userdata('role') !== 'worker') {
        $this->session->set_flashdata('error', 'Workers only.');
        return redirect('dashboard/'.($this->session->userdata('role') ?: 'user'));
    }
}

public function worker_active()
{
    $this->_must_be_worker();

    $uid  = (int)$this->session->userdata('user_id');
    $page = max(1, (int)$this->input->get('page'));
    $pp   = 12;
    $off  = ($page - 1) * $pp;

    $rows = $this->db->select('cp.*')
        ->from('transactions t')
        ->join('client_projects cp', 'cp.id = t.projectID', 'inner')
        ->where('t.workerID', $uid)
        ->where_in('t.status', ['accepted','active'])
        ->where('cp.status', 'active')
        ->order_by('cp.updated_at', 'DESC')
        ->limit($pp, $off)
        ->get()->result();

    $total = (int)$this->db->select('COUNT(*) AS c', false)
        ->from('transactions t')
        ->join('client_projects cp', 'cp.id = t.projectID', 'inner')
        ->where('t.workerID', $uid)
        ->where_in('t.status', ['accepted','active'])
        ->where('cp.status', 'active')
        ->get()->row()->c;

    $this->pagination->initialize([
        'base_url'             => site_url('projects/worker_active'),
        'total_rows'           => $total,
        'per_page'             => $pp,
        'page_query_string'    => true,
        'query_string_segment' => 'page',
        'use_page_numbers'     => true,
    ]);

    $this->load->view('projects_active', [
        'page_title' => 'My Works',
        'mode'       => 'list',
        'items'      => $this->_map_rows($rows),
        'pagination' => $this->pagination->create_links(),
        'isClient'   => false,
        'tab'        => 'active',
    ]);
}

public function worker_history()
{
    $this->_must_be_worker();

    $uid  = (int)$this->session->userdata('user_id');
    $page = max(1, (int)$this->input->get('page'));
    $pp   = 12;
    $off  = ($page - 1) * $pp;

    $rows = $this->db->select('cp.*')
        ->from('transactions t')
        ->join('client_projects cp', 'cp.id = t.projectID', 'inner')
        ->where('t.workerID', $uid)
        ->where_in('t.status', ['accepted','active','completed'])
        ->where('cp.status', 'closed')
        ->group_by('cp.id')
        ->order_by('cp.updated_at', 'DESC')
        ->limit($pp, $off)
        ->get()->result();

    $total = (int)$this->db->select('COUNT(DISTINCT cp.id) AS c', false)
        ->from('transactions t')
        ->join('client_projects cp', 'cp.id = t.projectID', 'inner')
        ->where('t.workerID', $uid)
        ->where_in('t.status', ['accepted','active','completed'])
        ->where('cp.status', 'closed')
        ->get()->row()->c;

    $this->pagination->initialize([
        'base_url'             => site_url('projects/worker_history'),
        'total_rows'           => $total,
        'per_page'             => $pp,
        'page_query_string'    => true,
        'query_string_segment' => 'page',
        'use_page_numbers'     => true,
    ]);

    $this->load->view('projects_active', [
        'page_title' => 'Work History',
        'mode'       => 'list',
        'items'      => $this->_map_rows($rows),
        'pagination' => $this->pagination->create_links(),
        'isClient'   => false,
        'tab'        => 'history',
    ]);
}


}
