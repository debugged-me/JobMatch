<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Peso_model extends CI_Model
{
    public function mine($posterId)
    {
        return $this->db->from('jobs')
            ->where('poster_id', (int)$posterId)
            ->order_by('created_at','DESC')
            ->get()->result_array();
    }

    public function find($id, $posterId)
    {
        return $this->db->from('jobs')
            ->where('id', (int)$id)
            ->where('poster_id', (int)$posterId)
            ->get()->row_array();
    }

    public function create($posterId, $data, ?array $media = null)
    {
        $row = [
            'post_type'     => $data['post_type'] ?? 'hire',
            'poster_id'     => (int)$posterId,
            'title'         => $data['title'],
            'description'   => $data['description'] ?? null,
            'website_url'   => ($data['website_url'] ?? '') !== '' ? trim($data['website_url']) : null,
            'media_json'    => $media ? json_encode($media, JSON_UNESCAPED_UNICODE) : null,
            'location_text' => $data['location_text'] ?? null,
            'price_min'     => $data['price_min'] !== '' ? (float)$data['price_min'] : null,
            'price_max'     => $data['price_max'] !== '' ? (float)$data['price_max'] : null,
            'visibility'    => in_array(($data['visibility'] ?? 'public'), ['public','followers'], true) ? $data['visibility'] : 'public',
            'status'        => 'open',
        ];
        $this->db->insert('jobs', $row);
        return (int)$this->db->insert_id();
    }

    public function update_job($id, $posterId, $data, ?array $mediaAction = null)
    {
        $row = [
            'post_type'     => $data['post_type'] ?? 'hire',
            'title'         => $data['title'],
            'description'   => $data['description'] ?? null,
            'website_url'   => ($data['website_url'] ?? '') !== '' ? trim($data['website_url']) : null,
            'location_text' => $data['location_text'] ?? null,
            'price_min'     => $data['price_min'] !== '' ? (float)$data['price_min'] : null,
            'price_max'     => $data['price_max'] !== '' ? (float)$data['price_max'] : null,
            'visibility'    => in_array(($data['visibility'] ?? 'public'), ['public','followers'], true) ? $data['visibility'] : 'public',
        ];
        if ($mediaAction) {
            $mode = $mediaAction['mode'] ?? '';
            if ($mode === 'set' && isset($mediaAction['data']) && is_array($mediaAction['data'])) {
                $row['media_json'] = json_encode($mediaAction['data'], JSON_UNESCAPED_UNICODE);
            } elseif ($mode === 'remove') {
                $row['media_json'] = null;
            }
        }
        return $this->db->where('id', (int)$id)
            ->where('poster_id', (int)$posterId)
            ->update('jobs', $row);
    }

    public function toggle_status($id, $posterId)
    {
        $job = $this->find($id, $posterId);
        if (!$job) return false;
        $new = ($job['status'] === 'open') ? 'closed' : 'open';
        return $this->db->where('id',(int)$id)
            ->where('poster_id',(int)$posterId)
            ->update('jobs', ['status'=>$new]);
    }

    public function delete_job($id, $posterId)
    {
        return $this->db->where('id',(int)$id)
            ->where('poster_id',(int)$posterId)
            ->delete('jobs');
    }

    public function latest_public_open($limit = 10)
    {
        return $this->db->from('jobs')
            ->select('id,title,description,website_url,location_text,price_min,price_max,created_at,media_json')
            ->where('status','open')
            ->where('visibility','public')
            ->order_by('created_at','DESC')
            ->limit((int)$limit)
            ->get()->result_array();
    }

    /* ============================================================
       Employment reporting (hired workers)
       ============================================================ */

    /** Which timestamp column on personnel_hired represents the "hired" date. */
    private function hired_date_field(): string
    {
        if (!$this->db->table_exists('personnel_hired')) return '';
        if ($this->db->field_exists('created_at', 'personnel_hired')) return 'created_at';
        if ($this->db->field_exists('updated_at', 'personnel_hired')) return 'updated_at';
        return '';
    }

    /** Quick employment counts used by the dashboard + report header. */
    public function hired_summary(): array
    {
        $out = ['total' => 0, 'this_month' => 0, 'this_year' => 0];
        if (!$this->db->table_exists('personnel_hired')) return $out;

        $out['total'] = (int)$this->db->where('status', 'hired')
            ->count_all_results('personnel_hired');

        $field = $this->hired_date_field();
        if ($field !== '') {
            $out['this_month'] = (int)$this->db->where('status', 'hired')
                ->where($field . ' >=', date('Y-m-01 00:00:00'))
                ->where($field . ' <=', date('Y-m-t 23:59:59'))
                ->count_all_results('personnel_hired');

            $out['this_year'] = (int)$this->db->where('status', 'hired')
                ->where($field . ' >=', date('Y-01-01 00:00:00'))
                ->where($field . ' <=', date('Y-12-31 23:59:59'))
                ->count_all_results('personnel_hired');
        }

        return $out;
    }

    public function hired_workers_report(array $filters = []): array
    {
        if (!$this->db->table_exists('personnel_hired')) {
            return [];
        }

        $hasUsers = $this->db->table_exists('users');
        $hasClientProfile = $this->db->table_exists('client_profile');
        $hasClientProjects = $this->db->table_exists('client_projects');

        $this->db->from('personnel_hired ph');
        $this->db->select('ph.id, ph.client_id, ph.worker_id, ph.project_id, ph.status, ph.rate, ph.rate_unit, ph.created_at, ph.updated_at', false);

        if ($hasUsers) {
            $this->db->join('users wu', 'wu.id = ph.worker_id', 'left');
            $this->db->join('users cu', 'cu.id = ph.client_id', 'left');

            $this->db->select(
                "COALESCE(
                    NULLIF(CONCAT_WS(', ', NULLIF(TRIM(wu.last_name), ''), NULLIF(TRIM(wu.first_name), '')), ''),
                    NULLIF(TRIM(wu.email), ''),
                    CONCAT('Worker #', ph.worker_id)
                ) AS worker_name",
                false
            );
            $this->db->select("NULLIF(TRIM(wu.email), '') AS worker_email", false);
        } else {
            $this->db->select("CONCAT('Worker #', ph.worker_id) AS worker_name", false);
            $this->db->select("'' AS worker_email", false);
        }

        if ($hasClientProfile) {
            $this->db->join('client_profile cp', 'cp.clientID = ph.client_id', 'left');
        }

        if ($hasClientProjects) {
            $this->db->join('client_projects cpj', 'cpj.id = ph.project_id', 'left');
            $this->db->select('cpj.title AS project_title', false);
        } else {
            $this->db->select("'' AS project_title", false);
        }

        if ($hasUsers && $hasClientProfile) {
            $this->db->select(
                "COALESCE(
                    NULLIF(TRIM(cp.companyName), ''),
                    NULLIF(TRIM(cp.employer), ''),
                    NULLIF(CONCAT_WS(', ', NULLIF(TRIM(cu.last_name), ''), NULLIF(TRIM(cu.first_name), '')), ''),
                    NULLIF(TRIM(cu.email), ''),
                    CONCAT('Client #', ph.client_id)
                ) AS client_name",
                false
            );
        } elseif ($hasUsers) {
            $this->db->select(
                "COALESCE(
                    NULLIF(CONCAT_WS(', ', NULLIF(TRIM(cu.last_name), ''), NULLIF(TRIM(cu.first_name), '')), ''),
                    NULLIF(TRIM(cu.email), ''),
                    CONCAT('Client #', ph.client_id)
                ) AS client_name",
                false
            );
        } else {
            $this->db->select("CONCAT('Client #', ph.client_id) AS client_name", false);
        }

        $this->db->where('ph.status', 'hired');

        $workerId = isset($filters['worker_id']) ? (int)$filters['worker_id'] : 0;
        $clientId = isset($filters['client_id']) ? (int)$filters['client_id'] : 0;
        $projectId = isset($filters['project_id']) ? (int)$filters['project_id'] : 0;
        if ($workerId > 0) {
            $this->db->where('ph.worker_id', $workerId);
        }
        if ($clientId > 0) {
            $this->db->where('ph.client_id', $clientId);
        }
        if ($projectId > 0) {
            $this->db->where('ph.project_id', $projectId);
        }

        $dateField = $this->db->field_exists('updated_at', 'personnel_hired')
            ? 'ph.updated_at'
            : ($this->db->field_exists('created_at', 'personnel_hired') ? 'ph.created_at' : '');
        if ($dateField !== '') {
            $dateFrom = isset($filters['date_from']) ? trim((string)$filters['date_from']) : '';
            $dateTo = isset($filters['date_to']) ? trim((string)$filters['date_to']) : '';
            if ($dateFrom !== '') {
                $this->db->where($dateField . ' >=', $dateFrom . ' 00:00:00');
            }
            if ($dateTo !== '') {
                $this->db->where($dateField . ' <=', $dateTo . ' 23:59:59');
            }
            $this->db->order_by($dateField, 'DESC');
        } else {
            $this->db->order_by('ph.id', 'DESC');
        }

        return $this->db->get()->result_array();
    }

    public function hired_workers_filter_options(): array
    {
        if (!$this->db->table_exists('personnel_hired')) {
            return ['clients' => [], 'workers' => [], 'projects' => []];
        }

        $hasUsers = $this->db->table_exists('users');
        $hasClientProfile = $this->db->table_exists('client_profile');
        $hasClientProjects = $this->db->table_exists('client_projects');

        $clients = [];
        $workers = [];
        $projects = [];

        $this->db->from('personnel_hired ph')
            ->select('ph.client_id AS id', false)
            ->where('ph.status', 'hired')
            ->group_by('ph.client_id');
        if ($hasUsers) {
            $this->db->join('users cu', 'cu.id = ph.client_id', 'left');
        }
        if ($hasClientProfile) {
            $this->db->join('client_profile cp', 'cp.clientID = ph.client_id', 'left');
        }
        if ($hasUsers && $hasClientProfile) {
            $this->db->select(
                "COALESCE(
                    NULLIF(TRIM(cp.companyName), ''),
                    NULLIF(TRIM(cp.employer), ''),
                    NULLIF(CONCAT_WS(', ', NULLIF(TRIM(cu.last_name), ''), NULLIF(TRIM(cu.first_name), '')), ''),
                    NULLIF(TRIM(cu.email), ''),
                    CONCAT('Client #', ph.client_id)
                ) AS label",
                false
            );
        } elseif ($hasUsers) {
            $this->db->select(
                "COALESCE(
                    NULLIF(CONCAT_WS(', ', NULLIF(TRIM(cu.last_name), ''), NULLIF(TRIM(cu.first_name), '')), ''),
                    NULLIF(TRIM(cu.email), ''),
                    CONCAT('Client #', ph.client_id)
                ) AS label",
                false
            );
        } else {
            $this->db->select("CONCAT('Client #', ph.client_id) AS label", false);
        }
        $clientRows = $this->db->order_by('label', 'ASC')->get()->result_array();
        foreach ($clientRows as $row) {
            $id = (int)($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $clients[] = [
                'id' => $id,
                'label' => trim((string)($row['label'] ?? ('Client #' . $id))),
            ];
        }

        $this->db->from('personnel_hired ph')
            ->select('ph.worker_id AS id', false)
            ->where('ph.status', 'hired')
            ->group_by('ph.worker_id');
        if ($hasUsers) {
            $this->db->join('users wu', 'wu.id = ph.worker_id', 'left');
            $this->db->select(
                "COALESCE(
                    NULLIF(CONCAT_WS(', ', NULLIF(TRIM(wu.last_name), ''), NULLIF(TRIM(wu.first_name), '')), ''),
                    NULLIF(TRIM(wu.email), ''),
                    CONCAT('Worker #', ph.worker_id)
                ) AS label",
                false
            );
        } else {
            $this->db->select("CONCAT('Worker #', ph.worker_id) AS label", false);
        }
        $workerRows = $this->db->order_by('label', 'ASC')->get()->result_array();
        foreach ($workerRows as $row) {
            $id = (int)($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $workers[] = [
                'id' => $id,
                'label' => trim((string)($row['label'] ?? ('Worker #' . $id))),
            ];
        }

        if ($hasClientProjects) {
            $projectRows = $this->db->from('personnel_hired ph')
                ->select('ph.project_id AS id, cpj.title AS label', false)
                ->join('client_projects cpj', 'cpj.id = ph.project_id', 'left')
                ->where('ph.status', 'hired')
                ->where('ph.project_id IS NOT NULL', null, false)
                ->group_by('ph.project_id')
                ->order_by('cpj.title', 'ASC')
                ->get()
                ->result_array();

            foreach ($projectRows as $row) {
                $id = (int)($row['id'] ?? 0);
                if ($id <= 0) {
                    continue;
                }
                $label = trim((string)($row['label'] ?? ''));
                $projects[] = [
                    'id' => $id,
                    'label' => $label !== '' ? $label : ('Project #' . $id),
                ];
            }
        }

        return [
            'clients' => $clients,
            'workers' => $workers,
            'projects' => $projects,
        ];
    }
}
