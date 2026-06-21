<?php defined('BASEPATH') OR exit('No direct script access allowed');

class WorkerFeedModel extends CI_Model
{
    public function get_feed_global(int $limit = 50)
    {
        return $this->db
            ->select('p.*, u.first_name, u.last_name', false)
            ->select("COALESCE(NULLIF(wp.avatar,''), NULLIF(cp.avatar,'')) AS author_avatar", false)
            ->from('worker_posts p')
            ->join('users u', 'u.id = p.worker_id')
            ->join('worker_profile wp', 'wp.workerID = u.id', 'left')
            ->join('client_profile cp', 'cp.clientID = u.id', 'left')
            ->where('p.visibility', 'public')
            ->order_by('p.created_at', 'DESC')
            ->order_by('p.id', 'DESC')
            ->limit($limit)
            ->get()->result();
    }
    public function get_feed_by_user(int $userId, int $limit = 50)
    {
        return $this->db
            ->select('p.*, u.first_name, u.last_name', false)
            ->select("COALESCE(NULLIF(wp.avatar,''), NULLIF(cp.avatar,'')) AS author_avatar", false)
            ->from('worker_posts p')
            ->join('users u', 'u.id = p.worker_id')
            ->join('worker_profile wp', 'wp.workerID = u.id', 'left')
            ->join('client_profile cp', 'cp.clientID = u.id', 'left')
            ->where('p.worker_id', $userId)
            ->order_by('p.created_at', 'DESC')
            ->order_by('p.id', 'DESC')
            ->limit($limit)
            ->get()->result();
    }
    public function get_feed_for_worker(int $meId, int $limit = 50)
    {
        return $this->get_feed_by_user($meId, $limit);
    }
    public function get_feed($workerId, $limit = 50)
    {
        return $this->get_feed_by_user((int)$workerId, $limit);
    }


    public function create_post($workerId, $data)
    {
        $row = [
            'worker_id'  => (int)$workerId,  
            'post_type'  => 'status',
            'title'      => null,
            'body'       => $data['body'] ?? null,
            'link_url'   => null,
            'media'      => !empty($data['media']) ? json_encode(array_values($data['media'])) : null,
            'visibility' => 'public',
'created_at' => gmdate('Y-m-d H:i:s'),
        ];
        $this->db->insert('worker_posts', $row);
        return $this->db->insert_id();
    }

    public function delete_post($workerId, $postId)
    {
        $this->db->where('id', $postId)->where('worker_id', $workerId)->delete('worker_posts');
        return $this->db->affected_rows() > 0;
    }

    public function get_open_projects_for_worker($workerId, $limit = 50)
    {
        $sql = "
            SELECT cp.*
            FROM client_projects cp
            WHERE cp.status = 'active' AND cp.visibility = 'public'
              AND NOT EXISTS (
                  SELECT 1 FROM project_applications pa
                  WHERE pa.project_id = cp.id AND pa.worker_id = ?
              )
            ORDER BY cp.created_at DESC
            LIMIT ?";
        return $this->db->query($sql, [$workerId, (int)$limit])->result();
    }

    /**
     * Open projects recommended for a worker, matched against their skills
     * (skill keyword appears in the job category, title, or description).
     * Falls back to all open projects when no skills are provided.
     */
    public function get_recommended_projects_for_worker($workerId, array $skills = [], $limit = 5)
    {
        $this->db->select('cp.*')
            ->from('client_projects cp')
            ->where('cp.status', 'active')
            ->where('cp.visibility', 'public')
            ->where('NOT EXISTS (SELECT 1 FROM project_applications pa WHERE pa.project_id = cp.id AND pa.worker_id = ' . (int)$workerId . ')', null, false);

        $skills = array_values(array_filter(array_map('trim', $skills)));
        if (!empty($skills)) {
            $this->db->group_start();
            foreach ($skills as $i => $s) {
                $like = $this->db->escape_like_str($s);
                $cond = "(cp.category LIKE '%{$like}%' OR cp.title LIKE '%{$like}%' OR cp.description LIKE '%{$like}%')";
                if ($i === 0) {
                    $this->db->where($cond, null, false);
                } else {
                    $this->db->or_where($cond, null, false);
                }
            }
            $this->db->group_end();
        }

        return $this->db->order_by('cp.created_at', 'DESC')
            ->limit((int)$limit)
            ->get()->result();
    }

    public function apply_to_project($workerId, $projectId, $payload)
    {
        $row = [
            'project_id'    => (int)$projectId,
            'worker_id'     => (int)$workerId,
            'pitch'         => $payload['pitch'] ?? null,
            'expected_rate' => $payload['expected_rate'] ?? null,
            'rate_unit'     => $payload['rate_unit'] ?? null,
            'status'        => 'submitted',
    'created_at'    => gmdate('Y-m-d H:i:s'),
        ];
     $sql = "INSERT INTO project_applications (project_id, worker_id, pitch, expected_rate, rate_unit, status, created_at)
        VALUES (?,?,?,?,?,'submitted',?)
        ON DUPLICATE KEY UPDATE
            pitch = VALUES(pitch),
            expected_rate = VALUES(expected_rate),
            rate_unit = VALUES(rate_unit),
            updated_at = UTC_TIMESTAMP()";
        $this->db->query($sql, [
            $row['project_id'], $row['worker_id'],
            $row['pitch'], $row['expected_rate'], $row['rate_unit'],
            $row['created_at']
        ]);

        $project = $this->db->select('clientID, title, id')->from('client_projects')->where('id', $projectId)->get()->row();
        if ($project) $this->notify_client_of_application($project->clientID, $workerId, $project);
        return true;
    }

    public function list_my_applications($workerId)
    {
        return $this->db->select('pa.*, cp.title, cp.city, cp.province, cp.category')
            ->from('project_applications pa')
            ->join('client_projects cp', 'cp.id = pa.project_id')
            ->where('pa.worker_id', $workerId)
            ->order_by('pa.created_at', 'DESC')->get()->result();
    }

    public function withdraw_application($workerId, $appId)
    {
        $this->db->where('id', $appId)->where('worker_id', $workerId)
                 ->update('project_applications', ['status' => 'withdrawn', 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->db->affected_rows() > 0;
    }

    private function notify_client_of_application($clientUserId, $workerId, $project)
    {
        $data = [
            'user_id'   => (string)$clientUserId,
            'actor_id'  => (string)$workerId,
            'type'      => 'system',
            'title'     => 'New application received',
            'body'      => 'A worker applied to your project: “' . ($project->title ?? 'Project') . '”.',
            'link'      => site_url('projects/view/'.$project->id),
            'is_read'   => 0,
'created_at' => gmdate('Y-m-d H:i:s'),
        ];
        $this->db->insert('tw_notifications', $data);
    }
    public function get_feed_since(int $meId, int $afterId, int $limit = 20)
{
    return $this->db
        ->select('p.*, u.first_name, u.last_name', false)
        ->select("COALESCE(NULLIF(wp.avatar,''), NULLIF(cp.avatar,'')) AS author_avatar", false)
        ->from('worker_posts p')
        ->join('users u', 'u.id = p.worker_id')
        ->join('worker_profile wp', 'wp.workerID = u.id', 'left')
        ->join('client_profile cp', 'cp.clientID = u.id', 'left')
        ->where('p.worker_id', $meId) 
        ->where('p.id >', $afterId)
        ->order_by('p.id', 'DESC')   
        ->limit($limit)
        ->get()->result();
}
public function get_feed_since_global(int $afterId, int $limit = 20)
{
    return $this->db
        ->select('p.*, u.first_name, u.last_name', false)
        ->select("COALESCE(NULLIF(wp.avatar,''), NULLIF(cp.avatar,'')) AS author_avatar", false)
        ->from('worker_posts p')
        ->join('users u', 'u.id = p.worker_id')
        ->join('worker_profile wp', 'wp.workerID = u.id', 'left')
        ->join('client_profile cp', 'cp.clientID = u.id', 'left')
        ->where('p.visibility', 'public')
        ->where('p.id >', $afterId)
        ->order_by('p.id', 'DESC') 
        ->limit($limit)
        ->get()->result();
}

public function get_feed_since_by_user(int $userId, int $afterId, int $limit = 20)
{
    return $this->db
        ->select('p.*, u.first_name, u.last_name', false)
        ->select("COALESCE(NULLIF(wp.avatar,''), NULLIF(cp.avatar,'')) AS author_avatar", false)
        ->from('worker_posts p')
        ->join('users u', 'u.id = p.worker_id')
        ->join('worker_profile wp', 'wp.workerID = u.id', 'left')
        ->join('client_profile cp', 'cp.clientID = u.id', 'left')
        ->where('p.worker_id', $userId)
        ->where('p.id >', $afterId)
        ->order_by('p.id', 'DESC')
        ->limit($limit)
        ->get()->result();
}
}
