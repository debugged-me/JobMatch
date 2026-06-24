<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    private $activeStatuses = array('active','open','ongoing','in_progress','invited');

    public function client_stats($clientId)
    {
        $clientId = (int)$clientId;

        $jobs_posted = (int) $this->db->from('client_projects')
            ->where('clientID', $clientId)
            ->count_all_results();

        $jobs_active = (int) $this->db->from('client_projects')
            ->where('clientID', $clientId)
            ->group_start()
                ->where_in('status', $this->activeStatuses)
                ->or_where('status IS NULL', null, false)
            ->group_end()
            ->count_all_results();

        $hires_total = 0;
        if ($this->db->table_exists('personnel_hired')) {
            $hires_total = (int) $this->db->from('personnel_hired')
                ->where('client_id', $clientId)
                ->where_in('status', array('hired','ended','onhold'))
                ->count_all_results();
        }

        if ($hires_total === 0 && $this->db->table_exists('transactions')) {
            $hires_total = (int) $this->db->distinct()
                ->select('workerID')
                ->from('transactions')
                ->where('clientID', $clientId)
                ->where_in('status', array('accepted','active','completed'))
                ->count_all_results();
        }

        $sum = $this->db->select_sum('t.rate_agreed', 's')
            ->from('transactions t')
            ->join('client_projects p', 'p.id = t.projectID', 'left')
            ->where('t.clientID', $clientId)
            ->where('t.rate_agreed IS NOT NULL', null, false)
            ->group_start()
                ->where('t.status', 'completed')
                ->or_where('p.status', 'closed')
            ->group_end()
            ->get()->row();
        $spend_total = (float)($sum && isset($sum->s) ? $sum->s : 0);

        return array(
            'jobs_posted' => (int)$jobs_posted,
            'jobs_active' => (int)$jobs_active,
            'hires_total' => (int)$hires_total,
            'spend_total' => $spend_total,
        );
    }

    public function client_active_projects($clientId, $limit = 6)
    {
        $clientId = (int)$clientId;
        $limit    = (int)$limit;

        return $this->db->select('id,title,status,created_at,budget_min,budget_max,rate_unit,city,province,brgy,visibility,files')
            ->from('client_projects')
            ->where('clientID', $clientId)
            ->group_start()
                ->where_in('status', $this->activeStatuses)
                ->or_where('status IS NULL', null, false)
            ->group_end()
            ->order_by('created_at','DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    public function client_recent_projects_any($clientId, $limit = 6)
    {
        $clientId = (int)$clientId;
        $limit    = (int)$limit;

        return $this->db->select('id,title,status,created_at,budget_min,budget_max,rate_unit,city,province,brgy,visibility,files')
            ->from('client_projects')
            ->where('clientID', $clientId)
            ->order_by('created_at','DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }
    /**
     * Hires per day for the last N days (for chart).
     * Returns ['labels' => [...], 'values' => [...]]
     */
    public function hires_chart_data($days = 30)
    {
        $days = max(1, (int)$days);
        $now  = new DateTime('now', new DateTimeZone('Asia/Manila'));
        $end  = clone $now;
        $start = (clone $now)->modify('-' . ($days - 1) . ' days');

        $labels = [];
        $values = [];
        for ($i = 0; $i < $days; $i++) {
            $d = (clone $start)->modify('+' . $i . ' days');
            $labels[] = $d->format('M j');
            $values[] = 0;
        }

        $startStr = $start->format('Y-m-d 00:00:00');
        $endStr   = $end->format('Y-m-d 23:59:59');

        $rows = [];
        if ($this->db->table_exists('personnel_hired') && $this->db->field_exists('created_at', 'personnel_hired')) {
            $rows = $this->db->select("DATE(created_at) AS d, COUNT(*) AS c", false)
                ->from('personnel_hired')
                ->where_in('status', ['hired', 'ended', 'onhold'])
                ->where('created_at >=', $startStr)
                ->where('created_at <=', $endStr)
                ->group_by('d')
                ->get()->result();
        } elseif ($this->db->table_exists('transactions') && $this->db->field_exists('created_at', 'transactions')) {
            $rows = $this->db->select("DATE(created_at) AS d, COUNT(DISTINCT workerID) AS c", false)
                ->from('transactions')
                ->where_in('status', ['accepted', 'active', 'completed'])
                ->where('created_at >=', $startStr)
                ->where('created_at <=', $endStr)
                ->group_by('d')
                ->get()->result();
        }

        if (!empty($rows)) {
            $map = [];
            foreach ($rows as $r) {
                $map[date('Y-m-d', strtotime($r->d))] = (int)$r->c;
            }
            for ($i = 0; $i < $days; $i++) {
                $d = (clone $start)->modify('+' . $i . ' days');
                $key = $d->format('Y-m-d');
                if (isset($map[$key])) {
                    $values[$i] = $map[$key];
                }
            }
        }

        // If all zeros, spread some sample data so chart isn't empty
        if (array_sum($values) === 0) {
            $values = [];
            for ($i = 0; $i < $days; $i++) {
                $values[] = 0;
            }
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Pending user verifications (is_active = 0).
     */
    public function pending_users_list($limit = 5)
    {
        $limit = max(1, (int)$limit);
        if (!$this->db->table_exists('users')) return [];

        $this->db->from('users')
            ->where_in('role', ['worker', 'client'])
            ->where('is_active', 0);

        if ($this->db->field_exists('created_at', 'users')) {
            $this->db->order_by('created_at', 'DESC');
        }

        return $this->db->limit($limit)->get()->result();
    }

    /**
     * Most recent registrations.
     */
    public function recent_registrations($limit = 5)
    {
        $limit = max(1, (int)$limit);
        if (!$this->db->table_exists('users')) return [];

        $select = 'id, email, role, is_active';
        if ($this->db->field_exists('first_name', 'users')) $select .= ', first_name';
        if ($this->db->field_exists('last_name', 'users'))  $select .= ', last_name';
        if ($this->db->field_exists('created_at', 'users')) $select .= ', created_at';

        $this->db->select($select, false)
            ->from('users')
            ->where_in('role', ['worker', 'client']);

        if ($this->db->field_exists('created_at', 'users')) {
            $this->db->order_by('created_at', 'DESC');
        } else {
            $this->db->order_by('id', 'DESC');
        }

        return $this->db->limit($limit)->get()->result();
    }

    /**
     * Top skills by worker count.
     */
    public function top_skills($limit = 8)
    {
        $limit = max(1, (int)$limit);
        if (!$this->db->table_exists('worker_skills') || !$this->db->table_exists('skills')) {
            return [];
        }

        return $this->db->select('s.Title AS title, COUNT(ws.workerID) AS cnt', false)
            ->from('worker_skills ws')
            ->join('skills s', 's.skillID = CAST(ws.skillsID AS UNSIGNED)', 'inner')
            ->where('ws.is_active', 1)
            ->or_where('ws.is_active IS NULL', null, false)
            ->group_by('s.skillID')
            ->order_by('cnt', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }

    /**
     * Count open complaints.
     */
    public function open_complaints_count()
    {
        if (!$this->db->table_exists('complaints')) return 0;
        return (int) $this->db->where('status', 'open')->count_all_results('complaints');
    }

    public function recent_activity_admin($limit = 8)
{
    $limit = (int) $limit;
    $items = [];

    // Windows
    $now       = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $last24h   = (clone $now)->modify('-24 hours')->format('Y-m-d H:i:s');
    $last7days = (clone $now)->modify('-7 days')->format('Y-m-d H:i:s');

    // Replace arrow functions with PHP 5.3+ compatible closures
    $tExists = function($t){ return $this->db->table_exists($t); };
    $cExists = function($t,$c){ return $this->db->field_exists($c, $t); };

    // 1) New projects posted (global)
    if ($tExists('client_projects') && $cExists('client_projects','created_at')) {
        $posted24 = (int) $this->db->from('client_projects')
            ->where('created_at >=', $last24h)
            ->count_all_results();

        if ($posted24 > 0) {
            $items[] = [
                'icon'  => 'mdi-briefcase-plus-outline',
                'title' => "{$posted24} new project".($posted24>1?'s':'')." posted",
                'meta'  => 'Last 24 hours',
            ];
        } else {
            $posted7 = (int) $this->db->from('client_projects')
                ->where('created_at >=', $last7days)
                ->count_all_results();
            if ($posted7 > 0) {
                $items[] = [
                    'icon'  => 'mdi-briefcase-plus-outline',
                    'title' => "{$posted7} new project".($posted7>1?'s':'')." posted",
                    'meta'  => 'Last 7 days',
                ];
            }
        }
    }

    // 2) Hires (global): prefer personnel_hired, else derive from transactions
    $hires7 = 0;
    if ($tExists('personnel_hired') && $cExists('personnel_hired','created_at')) {
        $hires7 = (int) $this->db->from('personnel_hired')
            ->where_in('status', ['hired','ended','onhold'])
            ->where('created_at >=', $last7days)
            ->count_all_results();
    } elseif ($tExists('transactions') && $cExists('transactions','created_at')) {
        $hires7 = (int) $this->db->distinct()
            ->select('workerID')
            ->from('transactions')
            ->where_in('status', ['accepted','active','completed'])
            ->where('created_at >=', $last7days)
            ->count_all_results();
    }
    if ($hires7 > 0) {
        $items[] = [
            'icon'  => 'mdi-account-plus-outline',
            'title' => "{$hires7} hire".($hires7>1?'s':'')." in progress",
            'meta'  => 'Last 7 days',
        ];
    }

    // 3) Worker verifications
    if ($tExists('worker_profiles') && $cExists('worker_profiles','verified_at')) {
        $v24 = (int) $this->db->from('worker_profiles')
            ->where('verified_at >=', $last24h)
            ->count_all_results();
        if ($v24 > 0) {
            $items[] = [
                'icon'  => 'mdi-shield-account-outline',
                'title' => "{$v24} worker".($v24>1?'s':'')." verified",
                'meta'  => 'Last 24 hours',
            ];
        }
    }

    // 4) Cancellations
    if ($tExists('transactions') && $cExists('transactions','updated_at')) {
        $cancel7 = (int) $this->db->from('transactions')
            ->where_in('status', ['cancelled','rejected'])
            ->where('updated_at >=', $last7days)
            ->count_all_results();
        if ($cancel7 > 0) {
            $items[] = [
                'icon'  => 'mdi-close-octagon-outline',
                'title' => "{$cancel7} cancelled engagement".($cancel7>1?'s':''),
                'meta'  => 'Last 7 days',
            ];
        }
    }

    // 5) Imports
    if ($tExists('import_logs') && $cExists('import_logs','created_at')) {
        $imports7 = (int) $this->db->from('import_logs')
            ->where('created_at >=', $last7days)
            ->count_all_results();
        if ($imports7 > 0) {
            $items[] = [
                'icon'  => 'mdi-database-import-outline',
                'title' => "Bulk upload completed ({$imports7})",
                'meta'  => 'Last 7 days',
            ];
        }
    }

    if (empty($items)) {
        $items[] = [
            'icon'  => 'mdi-information-outline',
            'title' => 'No recent activity',
            'meta'  => 'Past 7 days',
        ];
    }

    return array_slice($items, 0, max(1, $limit));
}


}
