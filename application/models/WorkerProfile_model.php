<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WorkerProfile_model extends CI_Model
{
    private $table = 'worker_profile';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get($user_id)
    {
        $select = "
            u.id as user_id, u.email, u.first_name, u.last_name, u.role,
            w.workerID, w.bio, w.brgy, w.city, w.province, w.phoneNo,
            w.created_at, w.updated_at, w.avatar, w.headline, w.years_experience,
            w.skills, w.credentials,
            w.availability_days, w.availability_start, w.availability_end,
            w.expected_rate, w.rate_unit, w.rate_negotiable,
            w.education_level, w.school, w.course, w.year_graduated,
            w.tesda_qualification,
            w.tesda_cert_no, w.tesda_expiry,
            w.tesda_certs,
            w.cert_files,
         
   w.portfolio_url, w.facebook_url,

            COALESCE(w.avgRating, 0) AS profileAvgRating,
            COALESCE( (SELECT AVG(r.rating) FROM reviews r WHERE r.workerID = u.id), 0 ) AS avgRating,
            COALESCE( (SELECT COUNT(*)      FROM reviews r WHERE r.workerID = u.id), 0 ) AS reviewCount
        ";

        $this->db->select($select, false);
        $this->db->from('users u');
        $this->db->join($this->table . ' w', 'w.workerID = u.id', 'left');
        $this->db->where('u.id', (int)$user_id);

        return $this->db->get()->row();
    }

    /* ===================================================================
     * NSRP Form 1 (Rev.3) — Jobseeker Registration
     * =================================================================== */

    /** Columns on worker_profile that the NSRP Form 1 jobseeker form owns. */
    private $nsrp_fields = [
        // I. Personal information
        'sex','date_of_birth','place_of_birth','civil_status','citizenship',
        'religion','height_cm','weight_kg','phoneNo','landline','mobile_secondary',
        'brgy','city','province','present_street',
        'perm_same_as_present','perm_street','perm_brgy','perm_city','perm_province',
        'disability',
        // Employment status & flags
        'employment_status','employment_substatus','actively_looking','looking_duration',
        'willing_immediate','available_when','is_4ps','fourps_household_id','is_ofw','ofw_returning',
        // II. Job preference
        'pref_occupations','pref_locations_local','pref_locations_overseas','salary_expectation',
        // III. Education (reuse existing columns)
        'education_level','school','course','year_graduated',
        // IV/V. Training, eligibility, languages
        'tesda_certs','eligibilities','language_certs','languages',
        // VI. Work experience (reuse exp JSON)
        'exp',
        // VII/IX. Skills self-assessment
        'century_skills','tech_skills_informal','skills',
    ];

    /** JSON-stored NSRP fields (arrays in PHP, text in DB). */
    private $nsrp_json_fields = [
        'pref_occupations','pref_locations_local','pref_locations_overseas',
        'tesda_certs','eligibilities','language_certs','century_skills',
        'tech_skills_informal','exp',
    ];

    private function apply_nsrp_filters(string $q = '', string $status = ''): void
    {
        if ($status !== '' && in_array($status, ['draft','submitted','assessed'], true)) {
            $this->db->where('w.nsrp_status', $status);
        }
        if ($q !== '') {
            $this->db->group_start()
                ->like('u.first_name', $q)->or_like('u.last_name', $q)->or_like('u.email', $q)
                ->or_like('w.nsrp_reference', $q)
                ->group_end();
        }
    }

    /** List jobseeker NSRP registrations for the PESO records report. */
    public function nsrp_list(string $q = '', string $status = '', int $limit = 25, int $offset = 0): array
    {
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);

        $this->db->select("u.id, u.email, u.first_name, u.last_name,
                w.nsrp_status, w.assessed_by, w.assessed_at, w.nsrp_reference, w.updated_at", false)
            ->from('users u')
            ->join($this->table . ' w', 'w.workerID = u.id', 'inner')
            ->where('u.role', 'worker');

        $this->apply_nsrp_filters($q, $status);
        return $this->db->order_by('w.updated_at', 'DESC')->limit($limit, $offset)->get()->result_array();
    }

    public function nsrp_total(string $q = '', string $status = ''): int
    {
        $this->db->from('users u')
            ->join($this->table . ' w', 'w.workerID = u.id', 'inner')
            ->where('u.role', 'worker');

        $this->apply_nsrp_filters($q, $status);
        return (int)$this->db->count_all_results();
    }

    /** Status counts for the records report header. */
    public function nsrp_counts(): array
    {
        $out = ['total' => 0, 'draft' => 0, 'submitted' => 0, 'assessed' => 0];
        $rows = $this->db->select('w.nsrp_status AS s, COUNT(*) AS c', false)
            ->from('users u')
            ->join($this->table . ' w', 'w.workerID = u.id', 'inner')
            ->where('u.role', 'worker')
            ->group_by('w.nsrp_status')
            ->get()->result_array();
        foreach ($rows as $r) {
            $s = (string)($r['s'] ?? 'draft');
            $c = (int)($r['c'] ?? 0);
            if (isset($out[$s])) $out[$s] += $c;
            $out['total'] += $c;
        }
        return $out;
    }

    /** Full joined row (users + worker_profile, all columns) for the NSRP form. */
    public function get_full($user_id)
    {
        $this->db->select('u.id as user_id, u.email, u.first_name, u.last_name, u.role, w.*', false);
        $this->db->from('users u');
        $this->db->join($this->table . ' w', 'w.workerID = u.id', 'left');
        $this->db->where('u.id', (int)$user_id);
        $row = $this->db->get()->row();

        if ($row) {
            foreach ($this->nsrp_json_fields as $f) {
                if (isset($row->$f) && is_string($row->$f) && $row->$f !== '') {
                    $tmp = json_decode($row->$f, true);
                    if (is_array($tmp)) $row->$f = $tmp;
                }
            }
        }
        return $row;
    }

    /**
     * Save the jobseeker-editable portion of NSRP Form 1.
     * $data may contain arrays for JSON fields; they are encoded here.
     * Only known NSRP columns are persisted.
     */
    public function save_nsrp($user_id, array $data): bool
    {
        $clean = array_intersect_key($data, array_flip($this->nsrp_fields));

        foreach ($this->nsrp_json_fields as $f) {
            if (array_key_exists($f, $clean)) {
                $val = $clean[$f];
                if (is_array($val)) {
                    $val = array_values(array_filter($val, function ($x) {
                        if (is_array($x)) return count(array_filter($x, fn($y) => $y !== '' && $y !== null)) > 0;
                        return $x !== '' && $x !== null;
                    }));
                    $clean[$f] = !empty($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : null;
                } elseif ($val === '' || $val === null) {
                    $clean[$f] = null;
                }
            }
        }

        // update_fields() drops nulls, so map intentional "clear" to empty string.
        foreach ($clean as $k => $v) {
            if ($v === null) $clean[$k] = '';
        }

        if (empty($clean)) return true;
        return (bool) $this->update_fields($user_id, $clean);
    }

    /** PESO-only assessment block (FOR USE OF PESO ONLY). */
    public function assess_nsrp($user_id, array $data): bool
    {
        $allowed = ['peso_eligibility','assessed_by','assessed_at','nsrp_reference','nsrp_status'];
        $clean = array_intersect_key($data, array_flip($allowed));
        foreach ($clean as $k => $v) {
            if ($v === null) $clean[$k] = '';
        }
        if (empty($clean)) return true;
        return (bool) $this->update_fields($user_id, $clean);
    }

    public function upsert($user_id, $data)
    {
        // You can optionally whitelist here; leaving open to keep all fields (incl. tesda_certs).
        $row = $this->db->get_where($this->table, ['workerID' => (int)$user_id])->row();
        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($row) {
            $this->db->where('workerID', (int)$user_id);
            return $this->db->update($this->table, $data);
        } else {
            $data['workerID']   = (int)$user_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert($this->table, $data);
        }
    }

    public function has_uploaded_documents($p): bool
    {
        return count($this->extract_doc_paths($p)) > 0;
    }
    private function has_documents($p): bool
    {
        // 0) If the controller already knows how many docs there are, trust it.
        if (isset($p->docs_count) && (int)$p->docs_count > 0) {
            return true;
        }
        if (isset($p->documents) && is_array($p->documents) && count($p->documents) > 0) {
            return true;
        }

        // 1) Try checking common "Saved Documents" tables
        $uid = (int)($p->userID ?? $p->user_id ?? $this->session->userdata('user_id') ?? 0);
        if ($uid > 0) {
            $candidateTables = ['documents', 'saved_documents', 'worker_documents'];
            $userCols        = ['user_id', 'userID', 'worker_id', 'workerID'];

            foreach ($candidateTables as $tbl) {
                try {
                    if (!$this->db->table_exists($tbl)) continue;

                    // choose the first user id column that exists
                    $userCol = null;
                    foreach ($userCols as $uc) {
                        if ($this->db->field_exists($uc, $tbl)) {
                            $userCol = $uc;
                            break;
                        }
                    }
                    if (!$userCol) continue;

                    $qb = $this->db->from($tbl)->where($userCol, $uid);

                    // ignore soft-deleted and empty files when columns exist
                    if ($this->db->field_exists('is_deleted', $tbl)) {
                        $qb->where('is_deleted', 0);
                    }
                    if ($this->db->field_exists('file_path', $tbl)) {
                        $qb->where("COALESCE(NULLIF(TRIM(file_path), ''), '') !=", '');
                    }

                    if ((int)$qb->count_all_results() > 0) {
                        return true; // ✅ at least one saved doc
                    }
                } catch (\Throwable $e) {
                    log_message('error', "has_documents(): $tbl check failed: " . $e->getMessage());
                    // continue to next table/fallback
                }
            }
        }

        // 2) Fallback (legacy JSON in profile: cert_files / certificates)
        $fields = ['cert_files', 'certificates'];
        foreach ($fields as $f) {
            if (!isset($p->$f) || $p->$f === null) continue;

            $val = $p->$f;
            if (is_string($val)) {
                $trim = trim($val);
                if ($trim === '' || $trim === '[]' || $trim === '{}') continue;
                $decoded = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE) $val = $decoded;
            }

            if (is_array($val)) {
                foreach ($val as $item) {
                    if (is_string($item) && trim($item) !== '') return true;
                    if (is_array($item) && !empty($item['path'])) return true;
                }
            }
        }

        return false;
    }

    public function completion($row): array
    {
        $p = (object)($row ?: []);

        $checks = [
            'Photo'               => !empty($p->avatar),
            // 'Bio (>=10 chars)'    => !empty($p->bio) && strlen(trim($p->bio)) >= 10,
            'Years of experience' => isset($p->years_experience) && (int)$p->years_experience > 0,
            'Barangay'            => !empty($p->brgy),
            'City'                => !empty($p->city),
            'Province'            => !empty($p->province),
            'Phone'               => !empty($p->phoneNo),
            'Skills'              => !empty($p->skills),
            'Credentials'         => !empty($p->credentials),
            'Documents'           => $this->has_documents($p),
        ];

        $done = 0;
        foreach ($checks as $v) {
            $done += $v ? 1 : 0;
        }

        $total   = count($checks);
        $percent = (int)round(($done / max(1, $total)) * 100);
        $missing = array_keys(array_filter($checks, static function ($v) {
            return !$v;
        }));

        return compact('percent', 'missing', 'checks');
    }

    public function is_complete($row): bool
    {
        $c = $this->completion($row);
        return $c['percent'] >= 100;
    }

    public function is_complete_by_user(int $user_id): bool
    {
        $row = $this->get($user_id);
        if (!$row) return false;
        return $this->is_complete($row);
    }

    public function add_portfolio_item(int $user_id, array $item): bool
    {
        $row = $this->get($user_id);
        $existing = [];
        if (!empty($row->exp)) {
            $tmp = json_decode($row->exp, true);
            if (is_array($tmp)) $existing = $tmp;
        }

        $item['title']       = trim((string)($item['title'] ?? 'Untitled'));
        $item['description'] = trim((string)($item['description'] ?? ''));
        $item['files']       = array_values(array_filter((array)($item['files'] ?? [])));
        $item['visibility']  = in_array(($item['visibility'] ?? 'public'), ['public', 'private'], true) ? $item['visibility'] : 'public';
        $item['created_at']  = date('Y-m-d H:i:s');

        $existing[] = $item;

        $payload = ['exp' => json_encode($existing)];
        return $this->upsert($user_id, $payload);
    }

    public function list_portfolio(int $user_id, bool $include_private = true): array
    {
        $row = $this->get($user_id);
        if (empty($row->exp)) return [];
        $items = json_decode($row->exp, true);
        if (!is_array($items)) return [];

        $out = [];
        foreach ($items as $it) {
            $title = $it['title'] ?? ($it['role'] ?? 'Untitled');
            $desc  = $it['description'] ?? ($it['desc'] ?? '');
            $files = isset($it['files']) && is_array($it['files']) ? $it['files'] : [];
            $vis   = $it['visibility'] ?? 'public';
            if (!$include_private && $vis === 'private') continue;

            $out[] = [
                'title'       => $title,
                'description' => $desc,
                'files'       => $files,
                'visibility'  => $vis,
                'created_at'  => $it['created_at'] ?? null,
            ];
        }
        usort($out, function ($a, $b) {
            return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        });
        return $out;
    }

    public function times_hired(int $user_id): int
    {
        $this->db->from('transactions');
        $this->db->where('workerID', $user_id);
        $this->db->where_in('status', ['ongoing', 'completed', 'hired', 'paid']);
        return (int) $this->db->count_all_results();
    }

    public function reviews_summary(int $user_id): array
    {
        $row = $this->db->select('COUNT(*) AS cnt, AVG(rating) AS avg_rating')
            ->get_where('reviews', ['workerID' => $user_id])
            ->row();
        return [
            'count' => (int)($row->cnt ?? 0),
            'avg'   => $row && $row->cnt ? round((float)$row->avg_rating, 2) : 0.0,
        ];
    }

    public function latest_reviews(int $workerID, int $limit = 5)
    {
        $rows = $this->db->select("
                r.reviewID, r.transactionID, r.clientID, r.workerID,
                r.rating, r.comment, r.created_at,

                COALESCE(
                  NULLIF(CONCAT(TRIM(cp.fName),' ',TRIM(cp.lName)), ''),
                  NULLIF(CONCAT(TRIM(u.first_name),' ',TRIM(u.last_name)), ''),
                  u.email
                ) AS client_name,

                COALESCE(p.title,'') AS job_title
            ", false)
            ->from('reviews r')
            ->join('transactions t', 't.transactionID = r.transactionID', 'left')
            ->join('client_projects p', 'p.id = t.projectID', 'left')
            ->join('users u', 'u.id = r.clientID', 'left')
            ->join('client_profile cp', 'cp.clientID = u.id', 'left')
            ->where('r.workerID', $workerID)
            ->order_by('r.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result();

        foreach ($rows as $row) {
            $ts   = strtotime($row->created_at);
            $diff = time() - ($ts ?: time());
            if ($diff < 60)        $row->time_ago = $diff . ' secs ago';
            elseif ($diff < 3600)  $row->time_ago = floor($diff / 60) . ' mins ago';
            elseif ($diff < 86400) $row->time_ago = floor($diff / 3600) . ' hrs ago';
            else                   $row->time_ago = floor($diff / 86400) . ' days ago';
        }

        return $rows;
    }

    public function update_fields($user_id, array $data)
    {
        if (empty($data)) return true;

        foreach ($data as $k => $v) {
            if ($v === null) unset($data[$k]);
        }
        if (empty($data)) return true;

        $user_id = (int)$user_id;

        $exists = $this->db->where('workerID', $user_id)
            ->count_all_results($this->table) > 0;

        $data['updated_at'] = date('Y-m-d H:i:s');

        if ($exists) {
            $this->db->where('workerID', $user_id);
            $result = $this->db->update($this->table, $data);
            // In CodeIgniter, update() returns affected_rows (could be 0 even on success)
            // Return true if no database error occurred
            return $result !== false;
        } else {
            $data['workerID']   = $user_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            $result = $this->db->insert($this->table, $data);
            // insert() returns true on success, false on failure
            return $result;
        }
    }

    private function extract_doc_paths($p): array
    {
        $out = [];
        $fields = ['cert_files', 'certificates'];
        foreach ($fields as $f) {
            if (!isset($p->$f) || $p->$f === null) continue;
            $val = $p->$f;
            if (is_string($val)) {
                $tmp = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE) $val = $tmp;
            }
            if (is_array($val)) {
                foreach ($val as $item) {
                    if (is_string($item) && $item !== '') $out[] = $item;
                    if (is_array($item) && !empty($item['path'])) $out[] = (string)$item['path'];
                }
            }
        }
        return $out;
    }
    public function get_experiences($userId)
    {
        $row = $this->db->from('worker_profiles')->where('user_id', $userId)->get()->row();
        $arr = [];
        if ($row && !empty($row->exp)) {
            $tmp = json_decode($row->exp, true);
            if (is_array($tmp)) $arr = $tmp;
        }
        return $arr;
    }

    public function put_experiences($userId, array $items)
    {
        $this->db->where('user_id', $userId)->update('worker_profiles', [
            'exp' => json_encode(array_values($items), JSON_UNESCAPED_UNICODE)
        ]);
        return $this->db->affected_rows() >= 0;
    }
}
