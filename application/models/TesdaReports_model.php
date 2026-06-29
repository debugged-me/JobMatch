<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TesdaReports_model extends CI_Model
{
    private $usersTable = 'users';

    private function worker_roles(): array
    {
        // TESDA reports should reflect skilled workers only.
        return ['worker'];
    }

    private function apply_owner_scope(string $alias, int $ownerUserId): void
    {
        $prefix = trim($alias) !== '' ? rtrim($alias, '.') . '.' : '';

        if ($ownerUserId <= 0) {
            $this->db->where('1 = 0', null, false);
            return;
        }

        $hasCreatedBy = $this->db->field_exists('created_by', $this->usersTable);
        $hasApprovedBy = $this->db->field_exists('approved_by', $this->usersTable);
        if ($hasCreatedBy && $hasApprovedBy) {
            $this->db->group_start()
                ->where($prefix . 'created_by', $ownerUserId)
                ->or_where($prefix . 'approved_by', $ownerUserId)
            ->group_end();
            return;
        }
        if ($hasCreatedBy) {
            $this->db->where($prefix . 'created_by', $ownerUserId);
            return;
        }
        if ($hasApprovedBy) {
            $this->db->where($prefix . 'approved_by', $ownerUserId);
            return;
        }

        // No ownership column available; avoid leaking global data.
        $this->db->where('1 = 0', null, false);
    }

    public function summary(int $ownerUserId): array
    {
        $roles = $this->worker_roles();

        $this->db->from($this->usersTable)->where_in('role', $roles);
        $this->apply_owner_scope('', $ownerUserId);
        $totalCreated = (int)$this->db->count_all_results();

        $createdThisMonth = 0;
        if ($this->db->field_exists('created_at', $this->usersTable)) {
            $this->db->from($this->usersTable)
                ->where_in('role', $roles)
                ->where('created_at >=', date('Y-m-01 00:00:00'));
            $this->apply_owner_scope('', $ownerUserId);
            $createdThisMonth = (int)$this->db->count_all_results();
        }

        $hiredTotal = 0;
        $hiredThisMonth = 0;
        if ($this->db->table_exists('personnel_hired')) {
            $this->db->from('personnel_hired ph')
                ->join($this->usersTable . ' u', 'u.id = ph.worker_id', 'inner')
                ->where_in('u.role', $roles);
            if ($this->db->field_exists('status', 'personnel_hired')) {
                $this->db->where_in('ph.status', ['hired', 'ended', 'onhold', 'completed']);
            }
            $this->apply_owner_scope('u', $ownerUserId);
            $hiredTotal = (int)$this->db->count_all_results();

            if ($this->db->field_exists('created_at', 'personnel_hired')) {
                $this->db->from('personnel_hired ph')
                    ->join($this->usersTable . ' u', 'u.id = ph.worker_id', 'inner')
                    ->where_in('u.role', $roles)
                    ->where('ph.created_at >=', date('Y-m-01 00:00:00'));
                if ($this->db->field_exists('status', 'personnel_hired')) {
                    $this->db->where_in('ph.status', ['hired', 'ended', 'onhold', 'completed']);
                }
                $this->apply_owner_scope('u', $ownerUserId);
                $hiredThisMonth = (int)$this->db->count_all_results();
            }
        }

        return [
            'total_created' => $totalCreated,
            'created_this_month' => $createdThisMonth,
            'hired_total' => $hiredTotal,
            'hired_this_month' => $hiredThisMonth,
        ];
    }

    public function rows(?string $q, int $ownerUserId): array
    {
        $roles = $this->worker_roles();
        $q = trim((string)$q);
        $hasPhone = $this->db->field_exists('phone', $this->usersTable);
        $hasCreatedAt = $this->db->field_exists('created_at', $this->usersTable);
        $hasStatus = $this->db->field_exists('status', $this->usersTable);

        $this->db->from($this->usersTable . ' u')
            ->where_in('u.role', $roles)
            ->select('u.id, u.email, u.first_name, u.last_name, u.is_active', false);

        if ($hasStatus) {
            $this->db->select('u.status', false);
        } else {
            $this->db->select('NULL AS status', false);
        }

        if ($hasPhone) {
            $this->db->select('u.phone', false);
        } else {
            $this->db->select('NULL AS phone', false);
        }

        if ($hasCreatedAt) {
            $this->db->select('u.created_at', false);
        } else {
            $this->db->select('NULL AS created_at', false);
        }

        if ($this->db->table_exists('personnel_hired')) {
            $joinCond = 'ph.worker_id = u.id';
            if ($this->db->field_exists('status', 'personnel_hired')) {
                $joinCond .= " AND ph.status IN ('hired','ended','onhold','completed')";
            }
            $this->db->join('personnel_hired ph', $joinCond, 'left');
            if ($this->db->field_exists('created_at', 'personnel_hired')) {
                $this->db->select('COUNT(ph.id) AS hire_count, MAX(ph.created_at) AS latest_hired_at', false);
            } else {
                $this->db->select('COUNT(ph.id) AS hire_count, NULL AS latest_hired_at', false);
            }
        } else {
            $this->db->select('0 AS hire_count, NULL AS latest_hired_at', false);
        }

        if ($q !== '') {
            $this->db->group_start()
                ->like('u.email', $q)
                ->or_like('u.first_name', $q)
                ->or_like('u.last_name', $q);
            if ($hasPhone) {
                $this->db->or_like('u.phone', $q);
            }
            $this->db->group_end();
        }

        $this->apply_owner_scope('u', $ownerUserId);
        $this->db->group_by('u.id');

        if ($hasCreatedAt) {
            $this->db->order_by('u.created_at', 'DESC');
        } else {
            $this->db->order_by('u.id', 'DESC');
        }

        return $this->db->limit(500)->get()->result();
    }
}
