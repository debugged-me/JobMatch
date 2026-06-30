<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ComplaintModel extends CI_Model
{
    protected $table = 'complaints';

    public function create(array $data): int
    {
        $this->db->insert($this->table, $data);
        return (int) $this->db->insert_id();
    }
public function find($id)
{
    $this->db->from($this->table.' c');
    $this->db->select("
        c.*,
        ur.first_name AS r_first, ur.last_name AS r_last, ur.role AS r_role,
        ua.first_name AS a_first, ua.last_name AS a_last, ua.role AS a_role,
        CONCAT_WS(' ', ur.first_name, ur.last_name) AS reporter_name,
        CONCAT_WS(' ', ua.first_name, ua.last_name) AS against_user_name
    ", false);
    $this->db->join('users ur', 'ur.id = c.reporter_id', 'left');
    $this->db->join('users ua', 'ua.id = c.against_user_id', 'left');
    $this->db->where('c.id', (int)$id);
    return $this->db->get()->row();
}

public function listByReporter($userId)
{
    return $this->db
        ->from($this->table.' c')
        ->select("
            c.*,
            ua.role AS a_role,
            CONCAT_WS(' ', ua.first_name, ua.last_name) AS against_user_name
        ", false)
        ->join('users ua', 'ua.id = c.against_user_id', 'left')
        ->where('c.reporter_id', (int)$userId)
        ->order_by('c.created_at','DESC')
        ->get()
        ->result();
}

    public function listAll($filters = [])
    {
        $this->db->from($this->table.' c');
        $this->db->select("
            c.*,
            ur.first_name AS r_first, ur.last_name AS r_last, ur.role AS r_role,
            ua.first_name AS a_first, ua.last_name AS a_last, ua.role AS a_role,
            CONCAT_WS(' ', ur.first_name, ur.last_name) AS reporter_name,
            CONCAT_WS(' ', ua.first_name, ua.last_name) AS against_user_name
        ", false);
        $this->db->join('users ur', 'ur.id = c.reporter_id', 'left');
        $this->db->join('users ua', 'ua.id = c.against_user_id', 'left');

        if (!empty($filters['status'])) {
            $this->db->where('c.status', $filters['status']);
        }
        if (!empty($filters['type'])) {
            $this->db->where('c.complaint_type', $filters['type']);
        }
        if (!empty($filters['q'])) {
            $this->db->group_start()
                     ->like('c.title', $filters['q'])
                     ->or_like('c.details', $filters['q'])
                     ->group_end();
        }

        $limit  = $filters['limit'] ?? 0;
        $offset = $filters['offset'] ?? 0;
        if ($limit > 0) $this->db->limit($limit, $offset);

        return $this->db->order_by('c.created_at','DESC')->get()->result();
    }

    public function countAll($filters = [])
    {
        $this->db->from($this->table.' c');
        $this->db->join('users ur', 'ur.id = c.reporter_id', 'left');
        $this->db->join('users ua', 'ua.id = c.against_user_id', 'left');

        if (!empty($filters['status'])) {
            $this->db->where('c.status', $filters['status']);
        }
        if (!empty($filters['type'])) {
            $this->db->where('c.complaint_type', $filters['type']);
        }
        if (!empty($filters['q'])) {
            $this->db->group_start()
                     ->like('c.title', $filters['q'])
                     ->or_like('c.details', $filters['q'])
                     ->group_end();
        }

        return (int)$this->db->count_all_results();
    }

    public function statusSummary()
    {
        $this->db->select('status, COUNT(*) AS cnt');
        $this->db->from($this->table);
        $this->db->group_by('status');
        $rows = $this->db->get()->result();
        $out = ['open' => 0, 'under_review' => 0, 'resolved' => 0, 'dismissed' => 0];
        foreach ($rows as $r) {
            $out[$r->status] = (int)$r->cnt;
        }
        return $out;
    }


    public function updateStatus($id, $status, $admin_notes = null): bool
    {
        $this->db->where('id', (int)$id)->update($this->table, [
            'status'      => $status,
            'admin_notes' => $admin_notes,
            'updated_at'  => date('Y-m-d H:i:s')
        ]);
        return $this->db->affected_rows() > 0;
    }
    public function update(int $id, array $data): bool
{
    return $this->db->where('id', $id)->update('complaints', $data);
}

public function deleteById(int $id): bool
{
    return $this->db->where('id', $id)->delete('complaints');
}

public function deleteByIdAndOwner(int $id, int $ownerId): bool
{
    return $this->db->where('id', $id)->where('reporter_id', $ownerId)->delete('complaints');
}

}
