<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SchoolAdminModel extends CI_Model
{
    private $table = 'users';

    public function stats()
    {
        $total = (int)$this->db->count_all($this->table);

        $q1 = $this->db->select('is_active, COUNT(*) cnt')->from($this->table)
              ->group_by('is_active')->get()->result();
        $byActive = ['1'=>0,'0'=>0];
        foreach ($q1 as $r) { $byActive[(string)$r->is_active] = (int)$r->cnt; }

        $q2 = $this->db->select('role, COUNT(*) cnt')->from($this->table)
              ->group_by('role')->order_by('cnt','DESC')->limit(10)->get()->result();
        $byRole = [];
        foreach ($q2 as $r) { $byRole[$r->role] = (int)$r->cnt; }

        return compact('total','byActive','byRole');
    }

    /** Provide recent users for dashboard */
    public function recent_users($limit = 10)
    {
        return $this->db->select('*')
            ->from($this->table)
            ->order_by('created_at','DESC')
            ->limit((int)$limit)
            ->get()->result();
    }

    /** /workers list */
    public function get_users($role='ALL', $active='ALL', $q=null)
    {
        if ($role && $role !== 'ALL')   $this->db->where('role', $role);
        if ($active !== 'ALL')          $this->db->where('is_active', (int)$active);
        if ($q) {
            $this->db->group_start()
                     ->like('email', $q)
                     ->or_like('first_name', $q)
                     ->or_like('last_name', $q)
                     ->or_like('phone', $q)
                     ->group_end();
        }
        $this->db->order_by('created_at','DESC');
        return $this->db->get($this->table)->result();
    }

    public function get_user_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => (int)$id], 1)->row();
    }

    public function update_user($id, array $p)
    {
        $id = (int)$id;

        $dupe = $this->db->select('id')->from($this->table)
                ->where('email', $p['email'])
                ->where('id <>', $id)->get()->row();
        if ($dupe) {
            return ['ok'=>false, 'message'=>'Email already exists on another user.'];
        }

        $row = [
            'email'      => $p['email'],
            'role'       => 'worker',
            'is_active'  => !empty($p['is_active']) ? 1 : 0,
            'visibility' => $p['visibility'] ?? 'private',
            'first_name' => $p['first_name'],
            'last_name'  => $p['last_name'],
            'phone'      => $p['phone'],
            'status'     => $p['status'] ?? 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($p['password'])) {
            $row['password_hash'] = password_hash($p['password'], PASSWORD_BCRYPT);
        }

        $this->db->update($this->table, $row, ['id' => $id]);
        if ($this->db->error()['code']) {
            return ['ok'=>false, 'message'=>$this->db->error()['message']];
        }
        return ['ok'=>true];
    }

    public function delete_user($id)
    {
        $id = (int)$id;
        $this->db->delete($this->table, ['id' => $id]);
        $err = $this->db->error();
        if ($err['code']) return ['ok'=>false, 'message'=>$err['message']];
        if ($this->db->affected_rows() < 1) return ['ok'=>false, 'message'=>'Nothing deleted (not found).'];
        return ['ok'=>true];
    }

    public function create_user(array $p)
    {
        $exists = $this->db->get_where($this->table, ['email'=>$p['email']], 1)->row();
        if ($exists) return ['ok'=>false,'message'=>'Email already exists.'];


        $temp = !empty($p['password']) ? $p['password'] : $this->_make_password(10);
        $hash = password_hash($temp, PASSWORD_BCRYPT);

        $row = [
            'email'              => $p['email'],
            'password_hash'      => $hash,
            'role'               => 'worker',
            'is_active'          => !empty($p['is_active']) ? 1 : 0,
            'visibility'         => $p['visibility'] ?? 'private',
            'first_name'         => $p['first_name'],
            'last_name'          => $p['last_name'],
            'phone'              => $p['phone'],
            'status'             => $p['status'] ?? 'active',
            'email_verified'     => 1,
            'email_verified_at'  => date('Y-m-d H:i:s'),
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table, $row);
        if ($this->db->error()['code']) {
            return ['ok'=>false,'message'=>$this->db->error()['message']];
        }

        return ['ok'=>true,'id'=>$this->db->insert_id(),'temp_password'=>$temp];
    }

    public function set_temp_password($id)
    {
        $id   = (int)$id;
        $temp = $this->_make_password(10);
        $hash = password_hash($temp, PASSWORD_BCRYPT);

        $this->db->update($this->table, [
            'password_hash' => $hash,
            'updated_at'    => date('Y-m-d H:i:s')
        ], ['id' => $id]);

        if ($this->db->error()['code']) {
            return ['ok'=>false, 'message'=>$this->db->error()['message']];
        }
        if ($this->db->affected_rows() < 1) {
            return ['ok'=>false, 'message'=>'User not found or unchanged.'];
        }
        return ['ok'=>true, 'temp_password'=>$temp];
    }

    public function parse_csv_users($filePath)
    {
        $rows = [];
        if (!file_exists($filePath)) return $rows;

        if (($h = fopen($filePath, 'r')) !== FALSE) {
            $header = fgetcsv($h);
            $map = $this->_map($header);

            while (($d = fgetcsv($h)) !== FALSE) {
                $email = strtolower(trim($this->_get($d,$map,'email')));
                if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

                $row = [
                    'email'      => $email,
                    'first_name' => $this->_get($d,$map,'first_name'),
                    'last_name'  => $this->_get($d,$map,'last_name'),
                    'phone'      => $this->_get($d,$map,'phone'),
                    'role'       => 'worker',
                    'is_active'  => $this->_get($d,$map,'is_active')==='0' ? 0 : 1,
                    'status'     => $this->_get($d,$map,'status') ?: 'active',
                    'visibility' => $this->_get($d,$map,'visibility') ?: 'private',
                ];
                $rows[] = $row;
            }
            fclose($h);
        }
        return $rows;
    }

    public function bulk_insert_users(array $rows, $emailCallback=null)
    {
        $created=0; $skipped=0;

        foreach ($rows as $p) {
            $p['email'] = strtolower(trim($p['email'] ?? ''));
            if (!$p['email'] || !filter_var($p['email'], FILTER_VALIDATE_EMAIL)) { $skipped++; continue; }

            $dupe = $this->db->get_where($this->table, ['email'=>$p['email']], 1)->row();
            if ($dupe) { $skipped++; continue; }

            $p['role'] = 'worker';

            $res = $this->create_user($p);
            if ($res['ok']) {
                $created++;
                if (is_callable($emailCallback)) {
                    $fullName = trim(($p['first_name'] ?? '').' '.($p['last_name'] ?? ''));
                    $emailCallback($p['email'], $fullName, $res['temp_password'], 'worker');
                }
            } else {
                $skipped++;
            }
        }
        return compact('created','skipped');
    }

    private function _make_password($len=10)
    {
        $chars='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        $s=''; for($i=0;$i<$len;$i++){ $s.=$chars[random_int(0,strlen($chars)-1)]; }
        return $s;
    }
    private function _map($header)
    {
        $map=[]; foreach ($header as $i=>$h){ $map[strtolower(trim($h))]=$i; } return $map;
    }
    private function _get($row,$map,$key)
    {
        return isset($map[$key]) ? trim((string)$row[$map[$key]]) : null;
    }

    /* ============================================================
       Student-worker reports (ported from trabawho)
       Owner-scoped by approved_by: a School Admin sees the workers
       they approved; admins (ownerUserId === null) see all.
       ============================================================ */

    private function report_roles(): array
    {
        return ['worker'];
    }

    private function apply_report_owner_scope(string $alias, ?int $ownerUserId): void
    {
        if ($ownerUserId === null) {
            return; // admin / global
        }
        $ownerUserId = (int)$ownerUserId;
        $prefix = trim($alias) !== '' ? rtrim($alias, '.') . '.' : '';
        if ($ownerUserId <= 0) {
            $this->db->where('1 = 0', null, false);
            return;
        }
        if ($this->db->field_exists('approved_by', $this->table)) {
            $this->db->where($prefix . 'approved_by', $ownerUserId);
        } else {
            $this->db->where('1 = 0', null, false);
        }
    }

    public function report_summary(?int $ownerUserId = null): array
    {
        $roles = $this->report_roles();

        $this->db->from($this->table)->where_in('role', $roles);
        $this->apply_report_owner_scope('', $ownerUserId);
        $totalCreated = (int)$this->db->count_all_results();

        $createdThisMonth = 0;
        if ($this->db->field_exists('created_at', $this->table)) {
            $this->db->from($this->table)
                ->where_in('role', $roles)
                ->where('created_at >=', date('Y-m-01 00:00:00'));
            $this->apply_report_owner_scope('', $ownerUserId);
            $createdThisMonth = (int)$this->db->count_all_results();
        }

        $hiredTotal = 0;
        $hiredThisMonth = 0;
        if ($this->db->table_exists('personnel_hired')) {
            $this->db->from('personnel_hired ph')
                ->join($this->table . ' u', 'u.id = ph.worker_id', 'inner')
                ->where_in('u.role', $roles);
            $this->apply_report_owner_scope('u', $ownerUserId);
            $hiredTotal = (int)$this->db->count_all_results();

            if ($this->db->field_exists('created_at', 'personnel_hired')) {
                $this->db->from('personnel_hired ph')
                    ->join($this->table . ' u', 'u.id = ph.worker_id', 'inner')
                    ->where_in('u.role', $roles)
                    ->where('ph.created_at >=', date('Y-m-01 00:00:00'));
                $this->apply_report_owner_scope('u', $ownerUserId);
                $hiredThisMonth = (int)$this->db->count_all_results();
            }
        }

        return [
            'total_created'      => $totalCreated,
            'created_this_month' => $createdThisMonth,
            'hired_total'        => $hiredTotal,
            'hired_this_month'   => $hiredThisMonth,
        ];
    }

    public function report_rows(?string $q = null, ?int $ownerUserId = null): array
    {
        $roles = $this->report_roles();
        $q = trim((string)$q);
        $hasPhone = $this->db->field_exists('phone', $this->table);
        $phoneExpr = $hasPhone ? 'u.phone' : 'NULL AS phone';

        $this->db->from($this->table . ' u')
            ->select("u.id, u.email, u.first_name, u.last_name, {$phoneExpr}, u.role, u.is_active, u.status, u.created_at", false)
            ->where_in('u.role', $roles);

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

        $this->apply_report_owner_scope('u', $ownerUserId);

        if ($this->db->table_exists('personnel_hired')) {
            $this->db->join('personnel_hired ph', 'ph.worker_id = u.id', 'left');
            if ($this->db->field_exists('created_at', 'personnel_hired')) {
                $this->db->select('COUNT(ph.id) AS hire_count, MAX(ph.created_at) AS latest_hired_at', false);
            } else {
                $this->db->select('COUNT(ph.id) AS hire_count, NULL AS latest_hired_at', false);
            }
        } else {
            $this->db->select('0 AS hire_count, NULL AS latest_hired_at', false);
        }

        $this->db->group_by('u.id');
        if ($this->db->field_exists('created_at', $this->table)) {
            $this->db->order_by('u.created_at', 'DESC');
        } else {
            $this->db->order_by('u.id', 'DESC');
        }

        return $this->db->limit(500)->get()->result();
    }
}
