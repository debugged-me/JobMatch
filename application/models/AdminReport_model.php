<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminReport_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* === KPI tiles === */
    public function totalJobs($from = '', $to = '') {
        if ($from !== '') $this->db->where('created_at >=', $from . ' 00:00:00');
        if ($to   !== '') $this->db->where('created_at <=', $to . ' 23:59:59');
        return (int)$this->db->select('COUNT(*) AS c')->get('jobs')->row()->c;
    }
    public function jobsWithApps($from = '', $to = '') {
        $sql = "SELECT COUNT(DISTINCT a.job_id) AS c FROM applications a JOIN jobs j ON j.id = a.job_id WHERE 1";
        $params = [];
        if ($from !== '') { $sql .= " AND a.created_at >= ?"; $params[] = $from . ' 00:00:00'; }
        if ($to   !== '') { $sql .= " AND a.created_at <= ?"; $params[] = $to . ' 23:59:59'; }
        return (int)$this->db->query($sql, $params)->row()->c;
    }
    public function totalClientProjects($from = '', $to = '') {
        if ($from !== '') $this->db->where('created_at >=', $from . ' 00:00:00');
        if ($to   !== '') $this->db->where('created_at <=', $to . ' 23:59:59');
        return (int)$this->db->select('COUNT(*) AS c')->get('client_projects')->row()->c;
    }
    public function projectsWithApps($from = '', $to = '') {
        $sql = "SELECT COUNT(DISTINCT pa.project_id) AS c FROM project_applications pa JOIN client_projects cp ON cp.id = pa.project_id WHERE 1";
        $params = [];
        if ($from !== '') { $sql .= " AND pa.created_at >= ?"; $params[] = $from . ' 00:00:00'; }
        if ($to   !== '') { $sql .= " AND pa.created_at <= ?"; $params[] = $to . ' 23:59:59'; }
        return (int)$this->db->query($sql, $params)->row()->c;
    }

    /* === Job lists === */
    // All jobs (including 0 applicants)
    public function allJobsWithApplicantTotals($from = '', $to = '') {
        $sql = "SELECT
                    j.id,
                    j.title,
                    j.post_type,
                    j.status,
                    j.created_at,
                    COALESCE(COUNT(a.id),0) AS applicant_count
                FROM jobs j
                LEFT JOIN applications a ON a.job_id = j.id";
        $params = [];
        $where = [];
        if ($from !== '') { $where[] = 'j.created_at >= ?'; $params[] = $from . ' 00:00:00'; }
        if ($to   !== '') { $where[] = 'j.created_at <= ?'; $params[] = $to . ' 23:59:59'; }
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= " GROUP BY j.id, j.title, j.post_type, j.status, j.created_at ORDER BY j.created_at DESC";
        return $this->db->query($sql, $params)->result_array();
    }

    // Only jobs that received applicants
    public function jobsWithApplicantsOnly($from = '', $to = '') {
        $sql = "SELECT
                    j.id,
                    j.title,
                    j.post_type,
                    j.status,
                    COUNT(a.id) AS applicant_count,
                    MIN(a.created_at) AS first_applied_at,
                    MAX(a.created_at) AS last_applied_at
                FROM jobs j
                JOIN applications a ON a.job_id = j.id";
        $params = [];
        $where = [];
        if ($from !== '') { $where[] = 'a.created_at >= ?'; $params[] = $from . ' 00:00:00'; }
        if ($to   !== '') { $where[] = 'a.created_at <= ?'; $params[] = $to . ' 23:59:59'; }
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= " GROUP BY j.id, j.title, j.post_type, j.status ORDER BY applicant_count DESC, j.title";
        return $this->db->query($sql, $params)->result_array();
    }

    /* === Client project summaries === */
    // Per client: total projects and count of projects that received apps
    public function clientProjectsSummary($from = '', $to = '') {
        $sql = "SELECT
                    cp.clientID,
                    COUNT(*) AS total_projects,
                    SUM(CASE WHEN x.project_id IS NOT NULL THEN 1 ELSE 0 END) AS projects_with_apps
                FROM client_projects cp
                LEFT JOIN (SELECT DISTINCT project_id FROM project_applications) x
                  ON x.project_id = cp.id";
        $params = [];
        $where = [];
        if ($from !== '') { $where[] = 'cp.created_at >= ?'; $params[] = $from . ' 00:00:00'; }
        if ($to   !== '') { $where[] = 'cp.created_at <= ?'; $params[] = $to . ' 23:59:59'; }
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= " GROUP BY cp.clientID ORDER BY total_projects DESC";
        return $this->db->query($sql, $params)->result_array();
    }

    // Drill-down list of projects for a single client (with applicant_count)
    public function projectsByClient($clientID) {
        $sql = "SELECT
                    cp.id,
                    cp.title,
                    cp.status,
                    cp.created_at,
                    COALESCE(COUNT(pa.id),0) AS applicant_count
                FROM client_projects cp
                LEFT JOIN project_applications pa ON pa.project_id = cp.id
                WHERE cp.clientID = ?
                GROUP BY cp.id, cp.title, cp.status, cp.created_at
                ORDER BY cp.created_at DESC";
        return $this->db->query($sql, [$clientID])->result_array();
    }

    // Optional helper to display client/company name in summaries
    public function clientLabelMap(array $clientIDs) {
        if (empty($clientIDs)) return [];
        $in = implode(',', array_fill(0, count($clientIDs), '?'));
        $sql = "SELECT u.id,
                       CONCAT(COALESCE(cp.companyName,''), 
                              CASE WHEN cp.companyName IS NOT NULL AND cp.companyName<>'' THEN '' 
                                   ELSE CONCAT(COALESCE(' ', cp.employer,'')) END
                       ) AS label
                FROM users u
                LEFT JOIN client_profile cp ON cp.clientID = u.id
                WHERE u.id IN ($in)";
        $rows = $this->db->query($sql, $clientIDs)->result();
        $map = [];
        foreach ($rows as $r) {
            $label = trim($r->label) !== '' ? $r->label : ('Client #'.$r->id);
            $map[(int)$r->id] = $label;
        }
        return $map;
    }
    /** For print: list applicants per job (names) */
public function applicantsByJobForPrint(): array {
$sql = "
    SELECT
        j.id    AS job_id,
        j.title AS job_title,
        COALESCE(
          NULLIF(CONCAT_WS(', ', NULLIF(TRIM(u.last_name),''), NULLIF(TRIM(u.first_name),'')), ''),
          u.email
        ) AS applicant_name
    FROM applications a
    JOIN jobs  j ON j.id = a.job_id
    JOIN users u ON u.id = a.user_id
    ORDER BY j.id, applicant_name
";

    $rows = $this->db->query($sql)->result_array();

    // Group by job_id → [ 'job_title' => ..., 'names' => [ ... ] ]
    $out = [];
    foreach ($rows as $r) {
        $jid = (int)$r['job_id'];
        if (!isset($out[$jid])) {
            $out[$jid] = ['job_title' => $r['job_title'], 'names' => []];
        }
        if (trim((string)$r['applicant_name']) !== '') {
            $out[$jid]['names'][] = $r['applicant_name'];
        }
    }
    return $out; // keyed by job_id
}

/** For print: list applicants per client project (names) */
public function applicantsByClientProjectForPrint(int $clientID): array {
 $sql = "
    SELECT
        cp.id    AS project_id,
        cp.title AS project_title,
        COALESCE(
          NULLIF(CONCAT_WS(', ', NULLIF(TRIM(u.last_name),''), NULLIF(TRIM(u.first_name),'')), ''),
          u.email
        ) AS applicant_name
    FROM client_projects cp
    LEFT JOIN project_applications pa ON pa.project_id = cp.id
    LEFT JOIN users u ON u.id = pa.worker_id
    WHERE cp.clientID = ?
    ORDER BY cp.id, applicant_name
";

    $rows = $this->db->query($sql, [$clientID])->result_array();

    $out = [];
    foreach ($rows as $r) {
        $pid = (int)$r['project_id'];
        if (!isset($out[$pid])) {
            $out[$pid] = ['project_title' => $r['project_title'], 'names' => []];
        }
        if (trim((string)$r['applicant_name']) !== '') {
            $out[$pid]['names'][] = $r['applicant_name'];
        }
    }
    return $out; // keyed by project_id
}

}
