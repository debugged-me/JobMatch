<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->helper(['url','html','form']);
        $this->load->library(['session','form_validation']);
        $this->load->model('User_model', 'user');
        $this->load->model('WorkerProfile_model', 'wp'); 
        $this->load->model('Reviews_model','reviews');
        $this->load->model('Dashboard_model', 'dash'); 
        $this->load->model('ClientProfile_model', 'cp'); 
        $this->load->model('Peso_model','peso');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        log_message(
            'debug',
            __CLASS__.'::__construct hit. logged_in='.(int)$this->session->userdata('logged_in')
            .' role=['.(string)$this->session->userdata('role').']'
            .' level=['.(string)$this->session->userdata('level').']'
        );
    }

    /* ---------- Helpers ---------- */

    private function get_counts()
    {
        $projects = 0;
        if (file_exists(APPPATH.'models/Project_model.php')) {
            $this->load->model('Project_model','project');
            if (method_exists($this->project, 'count_ongoing')) {
                $projects = (int) $this->project->count_ongoing();
            }
        }

        return [
            'workers'   => (int) $this->user->count_workers(),
            'employers' => (int) $this->user->count_employers(),
            'projects'  => $projects,
        ];
    }

    private function me(): int
    {
        return (int) ($this->session->userdata('user_id')
            ?: $this->session->userdata('id')
            ?: 0);
    }

    private function role_normalized(): string {
        $raw = (string)($this->session->userdata('role') ?: $this->session->userdata('level') ?: '');
        $r = strtolower(trim($raw));
        $r = str_replace(['_', '-'], ' ', $r);         // normalize underscores/dashes
        $r = preg_replace('/\s+/', ' ', $r);           // collapse extra spaces
        return $r;
    }

    private function is_peso_allowed(): bool {
        $r = $this->role_normalized();
        log_message('debug', __CLASS__.' role normalized=['.$r.']');
        return (
            strpos($r, 'peso') !== false     // "peso", "peso officer", "peso-admin", etc.
            || $r === 'admin'
            || $r === 'tesda admin'
        );
    }

    /* ---------- Admin ---------- */

    public function admin()
    {
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Unauthorized access', 403);
        }

        $stats = [
            'total_workers'         => (int) $this->user->count_workers(),
            'total_clients'         => (int) $this->user->count_employers(),
            'active_projects'       => 0,
            'pending_verifications' => 0,
            'completed'             => 0,
            'ongoing'               => 0,
            'cancelled'             => 0,
        ];

        if ($this->db->table_exists('projects')) {
            $this->db->from('projects')->where_in('status', ['active','in_progress','ongoing']);
            $stats['active_projects'] = (int) $this->db->count_all_results();

            $this->db->from('projects')->where_in('status', ['completed','complete','done']);
            $stats['completed'] = (int) $this->db->count_all_results();

            $this->db->from('projects')->where_in('status', ['ongoing','in_progress']);
            $stats['ongoing'] = (int) $this->db->count_all_results();

            $this->db->from('projects')->where_in('status', ['cancelled','canceled']);
            $stats['cancelled'] = (int) $this->db->count_all_results();
        }

        $this->db->from('users')->where_in('role', ['worker','client'])->where('is_active', 0);
        $stats['pending_verifications'] = (int) $this->db->count_all_results();

        $activity = $this->dash->recent_activity_admin(6); 

        $data = [
            'page_title' => 'Admin Dashboard',
            'stats'      => $stats,
            'activity'   => $activity,  
        ];

        $this->load->view('dashboard_admin', $data);
    }

    /* ---------- Worker ---------- */

    public function worker()
    {
        if ($this->session->userdata('role') !== 'worker') {
            show_error('Unauthorized access', 403);
        }

        $uid = (int) $this->session->userdata('user_id');
        $p   = $this->wp->get($uid);
        $incomplete = !$this->wp->is_complete($p);

        $docs = [];
        try {
            if (file_exists(APPPATH.'models/Documents_model.php')) {
                $this->load->model('Documents_model', 'docs_m');
                if (method_exists($this->docs_m, 'list_by_user')) {
                    $docs = $this->docs_m->list_by_user($uid);
                }
            }
            if (empty($docs) && $this->db->table_exists('documents')) {
                $userCol = $this->db->field_exists('user_id', 'documents') ? 'user_id' : 'userID';
                $docs = $this->db->from('documents')->where($userCol, $uid)->get()->result();
            }
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard->worker(): docs load failed: '.$e->getMessage());
        }
        $data['docs'] = $docs;
        $p->docs_count = is_array($docs) ? count($docs) : (is_object($docs) ? count((array)$docs) : 0);

        $this->load->model('Experience_model','exp');
        $xpRows = $this->exp->get_by_user($uid);

        if (is_array($xpRows) && !empty($xpRows)) {
            $norm = [];
            foreach ($xpRows as $r) {
                $role       = is_array($r) ? ($r['role'] ?? '')       : ($r->role ?? '');
                $employer   = is_array($r) ? ($r['employer'] ?? '')   : ($r->employer ?? '');
                $from       = is_array($r) ? ($r['from'] ?? '')       : ($r->from ?? '');
                $to         = is_array($r) ? ($r['to'] ?? '')         : ($r->to ?? '');
                $present    = is_array($r) ? ($r['to_present'] ?? 0)  : ($r->to_present ?? 0);
                $desc       = is_array($r) ? ($r['desc'] ?? '')       : ($r->desc ?? '');

                $norm[] = [
                    'role'     => trim((string)$role),
                    'employer' => trim((string)$employer),
                    'from'     => trim((string)$from),
                    'to'       => $present ? 'Present' : trim((string)$to),
                    'desc'     => trim((string)$desc),
                ];
            }
            $p->exp = json_encode($norm, JSON_UNESCAPED_UNICODE);
        }

        $c = $this->wp->completion($p);
        $stats = $this->reviews->stats($uid);

        $this->load->model('DocumentsModel', 'docs');
        $docs = $this->docs->list_by_user($uid);

        $data = [
            'page_title'     => 'Worker Dashboard',
            'counts'         => $this->get_counts(),
            'profile'        => $p,
            'completion'     => ['percent' => $c['percent'] ?? 0, 'missing' => $c['missing'] ?? []],
            'times_hired'    => $this->reviews->times_hired($uid),
            'reviews'        => $stats,
            'latest_reviews' => $this->reviews->latest($uid, 3),
            'incomplete'     => $incomplete,
            'docs'           => $docs,
        ];

        // Job-discovery data (JobStreet-style): open work + my applications
        $recommended_jobs = [];
        $my_applications  = [];
        try {
            $this->load->model('WorkerFeedModel', 'wfeed');
            $wskills = array_filter(array_map('trim', explode(',', (string)($p->skills ?? ''))));
            $recommended_jobs = $this->wfeed->get_recommended_projects_for_worker($uid, $wskills, 5);
            // Fallback to recent open jobs if no skill matches (so the section isn't empty)
            if (empty($recommended_jobs)) {
                $recommended_jobs = $this->wfeed->get_open_projects_for_worker($uid, 5);
            }
            $my_applications  = $this->wfeed->list_my_applications($uid);
        } catch (\Throwable $e) {
            log_message('error', 'Dashboard->worker(): jobs load failed: '.$e->getMessage());
        }
        $data['recommended_jobs'] = $recommended_jobs;
        $data['my_applications']  = $my_applications;

        // Application status summary
        $app_counts = ['total' => 0, 'submitted' => 0, 'accepted' => 0, 'withdrawn' => 0];
        foreach ((array)$my_applications as $a) {
            $st = strtolower((string)($a->status ?? ''));
            $app_counts['total']++;
            if (isset($app_counts[$st])) $app_counts[$st]++;
        }
        $data['app_counts'] = $app_counts;

        $this->load->view('dashboard_worker', $data);
    }

    /* ---------- Client ---------- */

    public function client()
    {
        if ($this->session->userdata('role') !== 'client') {
            show_error('Unauthorized access', 403);
        }

        $uid = (int) $this->session->userdata('user_id');
        $p   = $this->cp->get($uid);
        $incomplete = !$this->cp->is_complete($p);

        $this->load->model('Dashboard_model', 'dash');
        $stats  = $this->dash->client_stats($uid);
        $recent = $this->dash->client_active_projects($uid, 6);

        if (empty($recent)) {
            $recent = $this->dash->client_recent_projects_any($uid, 6);
        }

        $data = [
            'page_title' => 'Client Dashboard',
            'counts'     => $this->get_counts(),
            'profile'    => $p,
            'stats'      => $stats,
            'recent'     => $recent, 
            'incomplete' => $incomplete,
        ];

        $this->load->helper('date');

        $recent_jobs = $this->db->select('id,title,status,created_at')
            ->from('client_projects')
            ->where('clientID', $uid)
            ->order_by('created_at','DESC')
            ->limit(6)
            ->get()->result();

        foreach ($recent_jobs as &$j) {
            $j->posted_ago = timespan(strtotime($j->created_at), time()).' ago';
            $j->applicants = (int)$this->db->from('transactions')->where('projectID',$j->id)->count_all_results();
        }
        unset($j);

        $data['recent_jobs'] = $recent_jobs;

        $this->load->view('dashboard_client', $data);
    }

    /* ---------- TESDA ---------- */

    public function tesda()
    {
        $role = $this->role_normalized();
        if (!in_array($role, ['tesda admin','admin'], true)) {
            show_error('Forbidden', 403);
        }

        $this->load->model('Tesda_model');

        $data = [
            'page_title' => 'TESDA Dashboard',
            'stats'      => $this->Tesda_model->metrics(),
        ];

        $this->load->view('dashboard_tesda', $data);
    }

    /* ---------- PESO ---------- */

    public function peso()
    {
        if (!$this->is_peso_allowed()) {
            show_error('Forbidden (PESO only)', 403);
        }

        $uid  = $this->me();
        $list = $this->peso->mine($uid);

        $data = [
            'page_title' => 'PESO Dashboard',
            'list'       => $list,
        ];
        $this->load->view('dashboard_peso', $data);
    }

    public function peso_feed()
    {
        // public: no role check
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'ok'=>true,
                'data'=>$this->peso->latest_public_open(10)
            ]));
    }
public function user()
{
    // Normalize role/level exactly the same way you do elsewhere
    $r = $this->role_normalized();

    // If not logged in, punt
    if (!$this->session->userdata('logged_in')) {
        redirect('auth/login');
        return;
    }

    // Route to the correct dashboard
    if ($r === 'admin')        { redirect('dashboard/admin');  return; }
    if ($r === 'worker')       { redirect('dashboard/worker'); return; }
    if ($r === 'client')       { redirect('dashboard/client'); return; }
    if ($r === 'tesda admin')  { redirect('dashboard/tesda');  return; }

    // NEW: School Admin → School Admin module
    if ($r === 'school admin') { redirect('school-admin');     return; }

    // All PESO variants go to PESO dashboard
    if (in_array($r, ['peso','peso officer','peso admin'], true)) {
        redirect('dashboard/peso');
        return;
    }

    // Unknown role
    show_error('Your account role "'.$r.'" is not recognized. Please contact an administrator to assign a proper role.', 403);
}

}
