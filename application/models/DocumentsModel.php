<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentsModel extends CI_Model
{
    protected $table = 'documents';
    protected $typeTable = 'doc_type';

    public function create(array $data): int
    {
        $this->db->insert($this->table, $data);
        // Log DB errors if any
        if (method_exists($this->db, 'error')) {
            $err = $this->db->error();
            if (!empty($err['code'])) log_message('error', 'documents.create: '.$err['message']);
        }
        return (int)$this->db->insert_id();
    }

    public function update(int $id, array $data): bool
    {
        $this->db->where('id', $id)->update($this->table, $data);
        if (method_exists($this->db, 'error')) {
            $err = $this->db->error();
            if (!empty($err['code'])) log_message('error', 'documents.update: '.$err['message']);
        }
        return $this->db->affected_rows() >= 0;
    }

    public function delete(int $id): bool
    {
        $this->db->where('id', $id)->delete($this->table);
        if (method_exists($this->db, 'error')) {
            $err = $this->db->error();
            if (!empty($err['code'])) log_message('error', 'documents.delete: '.$err['message']);
        }
        return $this->db->affected_rows() > 0;
    }

    /** Owner-scoped variants — prevent cross-user document writes (IDOR). */
    public function belongs_to_user(int $id, int $user_id): bool
    {
        return (bool) $this->db
            ->where(['id' => $id, 'user_id' => $user_id])
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    public function update_for_user(int $id, int $user_id, array $data): bool
    {
        if (!$this->belongs_to_user($id, $user_id)) {
            return false;
        }
        unset($data['user_id'], $data['id']);
        $this->db->where(['id' => $id, 'user_id' => $user_id])->update($this->table, $data);
        return true;
    }

    public function delete_for_user(int $id, int $user_id): bool
    {
        $this->db->where(['id' => $id, 'user_id' => $user_id])->delete($this->table);
        if (method_exists($this->db, 'error')) {
            $err = $this->db->error();
            if (!empty($err['code'])) log_message('error', 'documents.delete_for_user: '.$err['message']);
        }
        return $this->db->affected_rows() > 0;
    }

public function list_by_user(int $user_id): array
{
    return $this->db
        ->select("
            d.id,
            d.doc_name,
            d.doc_type_id,
            d.skill_id,
            d.expiry_date,
            d.file_path,
            d.is_active,
            t.doc_type AS doc_type,
            t.doc_type AS type,
            t.code,
            s.Title AS skill,

            
            CASE
                WHEN LOWER(t.doc_type) REGEXP '(tesda|qualification|(^| )nc( |$))'
                     OR LOWER(t.code) IN ('tesda','tesda_cert','nc','qualification')
                THEN NULL
                WHEN LOWER(t.doc_type) LIKE '%certificate%' THEN 'certificate'
                ELSE 'document'
            END AS other_choice
        ", false)
        ->from($this->table.' d')
        ->join($this->typeTable.' t', 't.id = d.doc_type_id', 'left')
        ->join('skills s', 's.skillID = d.skill_id', 'left')
        ->where('d.user_id', $user_id)
        ->order_by('d.id', 'DESC')
        ->get()
        ->result_array();
}


    public function get_doc_types(): array
    {
        return $this->db
            ->select('id, doc_type, code')
            ->from($this->typeTable)
            ->where('is_active', 1)
            ->order_by('doc_type', 'ASC')
            ->get()->result_array();
    }
}
