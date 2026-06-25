<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manage_User extends CI_Model
{
    private $table = 'users';

public function search(
    string $q = '',
    string $role = '',
    ?int $isActive = null,
    int $limit = 100,
    int $offset = 0,
    ?string $statusText = null,
    string $sort = 'created_at',
    string $dir = 'desc'
) {
    $this->db->from($this->table . ' u');
    // Join both profile tables
    $this->db->join('worker_profile wp', 'wp.workerID = u.id', 'left');
    $this->db->join('client_profile cp', 'cp.clientID = u.id', 'left');

    // Select with unified avatar
    $this->db->select("
        u.id, u.email, u.role, u.is_active, u.status,
        u.first_name, u.last_name, u.created_at,
        COALESCE(NULLIF(TRIM(wp.avatar), ''), NULLIF(TRIM(cp.avatar), '')) AS avatar
    ", false);

    if ($q !== '') {
        $this->db->group_start()
                 ->like('u.email', $q)
                 ->or_like('u.first_name', $q)
                 ->or_like('u.last_name', $q)
                 ->group_end();
    }

    if ($role !== '') {
        $this->db->where('LOWER(u.role) =', strtolower($role));
    }

    if ($isActive !== null) {
        $this->db->where('u.is_active', (int)$isActive);
    }

    if ($statusText !== null) {
        $this->db->where('LOWER(u.status) =', strtolower($statusText));
    }

    $this->db->group_by('u.id');
    $allowedSort = ['first_name' => 'u.first_name', 'email' => 'u.email', 'role' => 'u.role', 'created_at' => 'u.created_at'];
    $sortCol = $allowedSort[$sort] ?? 'u.created_at';
    $dir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
    return $this->db->order_by($sortCol, $dir)
                    ->limit($limit, $offset)
                    ->get()
                    ->result();
}


    public function set_active(int $id, int $active): bool
    {
        $data = [
            'is_active'  => $active,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($this->db->field_exists('status', $this->table)) {
            $data['status'] = $active ? 'active' : 'inactive';
        }
        return (bool)$this->db->where('id', $id)->update($this->table, $data);
    }

    public function count_search(
        string $q = '',
        string $role = '',
        ?int $isActive = null,
        ?string $statusText = null
    ): int {
        $this->db->from($this->table . ' u');
        $this->db->join('worker_profile wp', 'wp.workerID = u.id', 'left');
        $this->db->join('client_profile cp', 'cp.clientID = u.id', 'left');

        // Only need u.id for counting; avoids SELECT * pulling duplicate
        // column names (e.g. created_at) from the joined profile tables.
        $this->db->select('u.id', false);

        if ($q !== '') {
            $this->db->group_start()
                     ->like('u.email', $q)
                     ->or_like('u.first_name', $q)
                     ->or_like('u.last_name', $q)
                     ->group_end();
        }

        if ($role !== '') {
            $this->db->where('LOWER(u.role) =', strtolower($role));
        }

        if ($isActive !== null) {
            $this->db->where('u.is_active', (int)$isActive);
        }

        if ($statusText !== null) {
            $this->db->where('LOWER(u.status) =', strtolower($statusText));
        }

        $this->db->group_by('u.id');
        $sub = $this->db->get_compiled_select();
        $result = $this->db->query("SELECT COUNT(*) AS cnt FROM ($sub) AS sub")->row();
        return (int)($result->cnt ?? 0);
    }

}
