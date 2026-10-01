<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Experience_model extends CI_Model
{
    protected $table = 'worker_experience';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->_ensure_schema();
    }

    private function _ensure_schema(): void
    {
        if (!$this->db->table_exists($this->table)) {
            $sql = "
            CREATE TABLE `{$this->table}` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `user_id` INT UNSIGNED NOT NULL,
              `role` VARCHAR(150) NOT NULL,
              `employer` VARCHAR(180) DEFAULT NULL,
              `from` VARCHAR(7) DEFAULT NULL, -- 'YYYY' or 'YYYY-MM'
              `to`   VARCHAR(7) DEFAULT NULL, -- 'YYYY' or 'YYYY-MM'
              `to_present` TINYINT(1) NOT NULL DEFAULT 0,
              `desc` TEXT,
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `idx_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql);
        }
        // Column upgrades for legacy installs live in
        // database/migrations/001_schema_consolidation.sql — never ALTER here.
    }

    public function get_by_user(int $user_id): array
    {
        return $this->db->from($this->table)
            ->where('user_id', $user_id)
            ->order_by('to_present','DESC')
            ->order_by("COALESCE(NULLIF(`to`,''), `from`)", 'DESC', false)
            ->get()->result_array();
    }

    public function insert(array $row)
    {
        $row['created_at'] = date('Y-m-d H:i:s');
        $ok = $this->db->insert($this->table, $row);
        return $ok ? (int)$this->db->insert_id() : 0;
    }

    public function update(int $id, int $user_id, array $row): bool
    {
        $row['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->where('user_id', $user_id)->update($this->table, $row);
    }

    public function delete(int $id, int $user_id): bool
    {
        return $this->db->where('id', $id)->where('user_id', $user_id)->delete($this->table);
    }
}
