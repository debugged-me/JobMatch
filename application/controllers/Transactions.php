<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url','security']);
        $this->load->library(['session']);
        $this->load->database();
        $this->load->model('ClientProfile_model','cp');
        $this->load->model('Personnel_model','personnelm'); 
    }

    private function _json($ok, $msg='OK', $extra=[]){
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(array_merge(['ok'=>(bool)$ok,'message'=>$msg], (array)$extra)));
    }

    private function _uid(): int {
        foreach (['id','user_id','uid','account_id'] as $k) {
            $v = $this->session->userdata($k);
            if (is_numeric($v)) return (int)$v;
        }
        return 0;
    }

    private function money_to_float($v): ?float
    {
        if ($v === null) return null;
        if (is_float($v) || is_int($v)) return (float)$v;

        $s = trim((string)$v);
        if ($s === '') return null;

        if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
            $s = str_replace(',', '', $s);
        } elseif (strpos($s, ',') !== false && strpos($s, '.') === false) {
            $s = str_replace(',', '.', $s);
        }
        $s = preg_replace('/[^\d.]/', '', $s);

        return ($s === '' ? null : (float)$s);
    }

  
    private function find_invite_rate_unit(int $thread_id, ?int $project_id = null): array
    {
        $rate = null; $unit = null;

        if ($this->db->table_exists('tw_messages')) {
            $qb = $this->db->select('*')->from('tw_messages')->where('thread_id', $thread_id);
            if ($this->db->field_exists('type','tw_messages')) $qb->where('type','hire');
            if ($project_id && $this->db->field_exists('project_id','tw_messages')) $qb->where('project_id', $project_id);
            if ($project_id && $this->db->field_exists('pid','tw_messages'))        $qb->where('pid', $project_id);
            $m = $qb->order_by('id','DESC')->limit(1)->get()->row();
            if ($m) {
                foreach (['meta','payload','data'] as $col) {
                    if (!empty($m->$col)) {
                        $j = json_decode((string)$m->$col, true);
                        if (is_array($j)) {
                            if ($rate === null && isset($j['rate']))      $rate = $this->money_to_float($j['rate']);
                            if ($unit === null && !empty($j['rate_unit'])) $unit = (string)$j['rate_unit'];
                            if (isset($j['invite']) && is_array($j['invite'])) {
                                if ($rate === null && isset($j['invite']['rate']))      $rate = $this->money_to_float($j['invite']['rate']);
                                if ($unit === null && !empty($j['invite']['rate_unit'])) $unit = (string)$j['invite']['rate_unit'];
                            }
                        }
                    }
                }
            }
        }

        if (($rate === null || $unit === null) && $this->db->table_exists('tw_notifications')) {
            $qb = $this->db->select('*')->from('tw_notifications')->where_in('type',['hire','message']);
            if ($this->db->field_exists('thread_id','tw_notifications')) $qb->where('thread_id', $thread_id);
            if ($project_id && $this->db->field_exists('project_id','tw_notifications')) $qb->where('project_id', $project_id);
            if ($project_id && $this->db->field_exists('pid','tw_notifications'))        $qb->where('pid', $project_id);
            $n = $qb->order_by('id','DESC')->limit(1)->get()->row();
            if ($n) {
                foreach (['meta','payload','data'] as $col) {
                    if (!empty($n->$col)) {
                        $j = json_decode((string)$n->$col, true);
                        if (is_array($j)) {
                            if ($rate === null && isset($j['rate']))      $rate = $this->money_to_float($j['rate']);
                            if ($unit === null && !empty($j['rate_unit'])) $unit = (string)$j['rate_unit'];
                            if (isset($j['invite']) && is_array($j['invite'])) {
                                if ($rate === null && isset($j['invite']['rate']))      $rate = $this->money_to_float($j['invite']['rate']);
                                if ($unit === null && !empty($j['invite']['rate_unit'])) $unit = (string)$j['invite']['rate_unit'];
                            }
                        }
                    }
                }
            }
        }

        if ($unit && !in_array($unit, ['hour','day','project'], true)) $unit = 'project';
        return [$rate, $unit];
    }

   public function api_accept()
{
    $wid = $this->_uid();
    if ($wid <= 0 || ($this->session->userdata('role') ?? '') !== 'worker') {
        return $this->_json(false, 'Unauthorized');
    }

    $thread_id  = (int)$this->input->post('thread_id');
    $project_id = (int)$this->input->post('project_id');
    if ($thread_id <= 0 || $project_id <= 0) {
        return $this->_json(false, 'Bad request');
    }

    $offered_rate = $this->input->post('rate', true);
    $offered_rate = ($offered_rate !== null && $offered_rate !== '' && is_numeric($offered_rate)) ? (float)$offered_rate : null;
    $offered_unit = $this->input->post('rate_unit', true) ?: null;

    $t = $this->db->get_where('tw_threads', ['id'=>$thread_id])->row();
    // The caller must actually be a participant in the thread.
    if (!$t || ((int)$t->a_id !== $wid && (int)$t->b_id !== $wid)) {
        return $this->_json(false, 'Thread not found');
    }
    $cid = ($t->a_id == $wid) ? (int)$t->b_id : (int)$t->a_id;

    $p = $this->db->get_where('client_projects', ['id'=>$project_id,'clientID'=>$cid])->row();
    if (!$p) $p = $this->db->get_where('client_projects', ['id'=>$project_id,'client_id'=>$cid])->row();
    if (!$p) return $this->_json(false, 'Project not found for this client');

    // A hire/invite notification from this client must exist — otherwise the
    // "acceptance" is forged. Reuse the same lookup that infers the rate.
    $inviteRows = $this->db->order_by('id','DESC')->limit(5)
             ->get_where('tw_notifications', ['user_id'=>$wid, 'actor_id'=>$cid, 'type'=>'hire'])->result();
    if (empty($inviteRows)) {
        return $this->_json(false, 'No hire request exists for this client.');
    }

    if ($offered_rate === null) {
        $rows = $inviteRows;
        foreach ($rows as $n) {
            if (!empty($n->link)) {
                $query = parse_url($n->link, PHP_URL_QUERY);
                if ($query) {
                    parse_str($query, $q);
                    if (!empty($q['amount']) && is_numeric($q['amount'])) {
                        $offered_rate = (float)$q['amount'];
                        break;
                    }
                }
            }
        }
    }

    $skillId = 0;
    if (isset($p->skillsID))      $skillId = (int)$p->skillsID;
    elseif (isset($p->skills_id)) $skillId = (int)$p->skills_id;

    $title = trim((string)($p->title ?? ''));
    if ($title === '') $title = 'Hired Project';

    $final_unit = $offered_unit ?: ($p->rate_unit ?? null);

    $loc = trim(
        ($p->brgy ? $p->brgy.', ' : '').
        ($p->city ? $p->city.($p->province ? ', ' : '') : '').
        ($p->province ?? '')
    );

    $dup = $this->db->where([
        'clientID'  => $cid,
        'workerID'  => $wid,
        'projectID' => $project_id
    ])->from('transactions')->count_all_results();

    $now = date('Y-m-d H:i:s');

    if ($dup) {
        $this->db->where([
            'clientID'  => $cid,
            'workerID'  => $wid,
            'projectID' => $project_id
        ])->update('transactions', [
            'status'       => 'accepted',
            'rate_agreed'  => $offered_rate,  
            'rateUnit'     => $final_unit,
            'confirmed_by' => $wid,
            'confirmed_at' => $now,
            'started_at'   => $now,
        ]);

        $this->personnelm->ensure_hired($cid, $wid, $project_id, $offered_rate, $final_unit);

        $this->cp->add_notification(
            $cid, $wid, 'hire', 'Request accepted',
            'Worker accepted your hire request.',
            site_url('messages/start?to='.$wid.'&pid='.$project_id.'&invite=1')
        );

        return $this->_json(true, 'Accepted');
    }

    $this->db->db_debug = false;
    $fields = array_flip($this->db->list_fields('transactions'));

    $candidate = [
        'projectID'     => $project_id,
        'clientID'      => $cid,
        'workerID'      => $wid,
        'skillsID'      => $skillId,
        'title'         => $title,
        'description'   => (string)($p->description ?? ''),
        'status'        => 'accepted',
        'rate_agreed'   => $offered_rate, 
        'rateUnit'      => $final_unit,
        'location_note' => $loc ?: null,
        'confirmed_by'  => $wid,
        'confirmed_at'  => $now,
        'started_at'    => $now,
        'completed_at'  => null,
        'cancelled_at'  => null,
        'created_at'    => $now,
    ];
    $payload = array_intersect_key($candidate, $fields);

    $ok = $this->db->insert('transactions', $payload);
    if (!$ok) {
        $err = $this->db->error();
        log_message('error', 'Transactions::api_accept insert failed: ' . ($err['message'] ?? 'unknown') . ' payload=' . json_encode($payload));
        return $this->_json(false, 'Could not accept. Please try again.');
    }

    $this->personnelm->ensure_hired($cid, $wid, $project_id, $offered_rate, $final_unit);

    $this->cp->add_notification(
        $cid, $wid, 'hire', 'Request accepted',
        'Worker accepted your hire request.',
        site_url('messages/start?to='.$wid.'&pid='.$project_id.'&invite=1')
    );

    return $this->_json(true, 'Accepted');
}


    public function api_decline()
    {
        $wid = $this->_uid();
        if ($wid <= 0 || ($this->session->userdata('role') ?? '') !== 'worker') {
            return $this->_json(false, 'Unauthorized');
        }
        return $this->_json(true, 'Declined');
    }
}
