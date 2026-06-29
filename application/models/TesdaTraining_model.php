<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TesdaTraining_model extends CI_Model
{
    private $table = 'tesda_trainings';

    public function __construct()
    {
        parent::__construct();
        $this->ensure_table();
    }

    public function ensure_table(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `poster_id` int(10) unsigned NOT NULL,
            `title` varchar(200) NOT NULL,
            `description` text DEFAULT NULL,
            `website_url` varchar(500) DEFAULT NULL,
            `image_path` varchar(255) DEFAULT NULL,
            `location_text` varchar(255) DEFAULT NULL,
            `address_id` int(10) unsigned DEFAULT NULL,
            `province` varchar(120) DEFAULT NULL,
            `city` varchar(120) DEFAULT NULL,
            `brgy` varchar(120) DEFAULT NULL,
            `visibility` enum('public','followers') NOT NULL DEFAULT 'public',
            `status` enum('open','closed') NOT NULL DEFAULT 'open',
            `created_at` datetime NOT NULL DEFAULT current_timestamp(),
            `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_tesda_trainings_poster` (`poster_id`),
            KEY `idx_tesda_trainings_status` (`status`),
            KEY `idx_tesda_trainings_visibility` (`visibility`),
            KEY `idx_tesda_trainings_address` (`address_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);

        if ($this->db->table_exists($this->table) && !$this->db->field_exists('image_path', $this->table)) {
            $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `image_path` varchar(255) DEFAULT NULL AFTER `website_url`");
        }
    }

    public function mine(int $posterId): array
    {
        return $this->db->from($this->table)
            ->where('poster_id', $posterId)
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->get()->result_array();
    }

    public function find(int $id, int $posterId): ?array
    {
        $row = $this->db->from($this->table)
            ->where('id', $id)
            ->where('poster_id', $posterId)
            ->get()->row_array();
        return $row ?: null;
    }

    public function create(int $posterId, array $data): int
    {
        $row = [
            'poster_id' => $posterId,
            'title' => (string)($data['title'] ?? ''),
            'description' => ($data['description'] ?? '') !== '' ? (string)$data['description'] : null,
            'website_url' => ($data['website_url'] ?? '') !== '' ? trim((string)$data['website_url']) : null,
            'image_path' => ($data['image_path'] ?? '') !== '' ? trim((string)$data['image_path']) : null,
            'location_text' => ($data['location_text'] ?? '') !== '' ? trim((string)$data['location_text']) : null,
            'province' => ($data['province'] ?? '') !== '' ? trim((string)$data['province']) : null,
            'city' => ($data['city'] ?? '') !== '' ? trim((string)$data['city']) : null,
            'brgy' => ($data['brgy'] ?? '') !== '' ? trim((string)$data['brgy']) : null,
            'visibility' => in_array((string)($data['visibility'] ?? 'public'), ['public', 'followers'], true)
                ? (string)$data['visibility']
                : 'public',
            'status' => 'open',
        ];

        $addressId = (int)($data['address_id'] ?? 0);
        if ($addressId > 0) {
            $row['address_id'] = $addressId;
        }

        $this->db->insert($this->table, $row);
        return (int)$this->db->insert_id();
    }

    public function update_post(int $id, int $posterId, array $data): bool
    {
        $row = [
            'title' => (string)($data['title'] ?? ''),
            'description' => ($data['description'] ?? '') !== '' ? (string)$data['description'] : null,
            'website_url' => ($data['website_url'] ?? '') !== '' ? trim((string)$data['website_url']) : null,
            'location_text' => ($data['location_text'] ?? '') !== '' ? trim((string)$data['location_text']) : null,
            'province' => ($data['province'] ?? '') !== '' ? trim((string)$data['province']) : null,
            'city' => ($data['city'] ?? '') !== '' ? trim((string)$data['city']) : null,
            'brgy' => ($data['brgy'] ?? '') !== '' ? trim((string)$data['brgy']) : null,
            'visibility' => in_array((string)($data['visibility'] ?? 'public'), ['public', 'followers'], true)
                ? (string)$data['visibility']
                : 'public',
        ];

        if (array_key_exists('image_path', $data)) {
            $row['image_path'] = ($data['image_path'] ?? '') !== '' ? trim((string)$data['image_path']) : null;
        }

        $addressId = (int)($data['address_id'] ?? 0);
        $row['address_id'] = $addressId > 0 ? $addressId : null;

        return (bool)$this->db->where('id', $id)
            ->where('poster_id', $posterId)
            ->update($this->table, $row);
    }

    public function toggle_status(int $id, int $posterId): bool
    {
        $row = $this->find($id, $posterId);
        if (!$row) {
            return false;
        }
        $next = strtolower((string)($row['status'] ?? 'open')) === 'open' ? 'closed' : 'open';
        return (bool)$this->db->where('id', $id)
            ->where('poster_id', $posterId)
            ->update($this->table, ['status' => $next]);
    }

    public function delete_post(int $id, int $posterId): bool
    {
        return (bool)$this->db->where('id', $id)
            ->where('poster_id', $posterId)
            ->delete($this->table);
    }

    public function latest_public_open(int $limit = 6): array
    {
        $limit = max(1, min(20, (int)$limit));
        return $this->db->from($this->table)
            ->select('id,title,description,website_url,image_path,location_text,created_at', false)
            ->where('status', 'open')
            ->where('visibility', 'public')
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->limit($limit)
            ->get()->result_array();
    }
}
