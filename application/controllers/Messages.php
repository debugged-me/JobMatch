<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url','html','security']);
        $this->load->library(['session']);
        $this->load->model('Message_model','mm');
        $this->load->model('ClientProfile_model','cp'); 
        $this->load->model('Presence_model','presence');

        $this->load->database();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login?next='.rawurlencode(current_url().($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'')));
        }
    }

    private function _uid(): int
    {
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
                            if ($rate === null && isset($j['rate']))       $rate = $this->money_to_float($j['rate']);
                            if ($unit === null && !empty($j['rate_unit'])) $unit = (string)$j['rate_unit'];
                            if (isset($j['invite']) && is_array($j['invite'])) {
                                if ($rate === null && isset($j['invite']['rate']))       $rate = $this->money_to_float($j['invite']['rate']);
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
                            if ($rate === null && isset($j['rate']))       $rate = $this->money_to_float($j['rate']);
                            if ($unit === null && !empty($j['rate_unit'])) $unit = (string)$j['rate_unit'];
                            if (isset($j['invite']) && is_array($j['invite'])) {
                                if ($rate === null && isset($j['invite']['rate']))       $rate = $this->money_to_float($j['invite']['rate']);
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

    private function _ago($datetime)
    {
        try {
            $t = new DateTime($datetime);
            $n = new DateTime('now');
            $d = $n->diff($t);
            foreach (['y'=>'year','m'=>'month','d'=>'day','h'=>'hour','i'=>'minute'] as $k=>$lbl) {
                if ($d->$k) {
                    $v = (int)$d->$k;
                    return $v.' '.$lbl.($v>1?'s':'').' ago';
                }
            }
            return 'just now';
        } catch (\Throwable $e) { return null; }
    }

    public function start()
    {
        $me = $this->_uid();
        if ($me <= 0) return redirect('auth/login');

        $to = (int)$this->input->get('to');
        if ($to <= 0) show_error('Invalid recipient', 400);

        if (method_exists($this->mm, 'get_or_create_thread')) {
            $t = $this->mm->get_or_create_thread($me, $to);
            $tid = (int)($t->id ?? 0);
        } elseif (method_exists($this->mm, 'ensure_thread')) {
            $tid = (int)$this->mm->ensure_thread($me, $to);
        } else {
            $tid = (int)($this->mm->thread_between($me, $to) ?? 0);
            if ($tid <= 0 && method_exists($this->mm, 'create_thread')) {
                $tid = (int)$this->mm->create_thread($me, $to);
            }
        }

        if ($tid <= 0) show_error('Could not start conversation', 500);

        $qs = [];
        $pid = (int)$this->input->get('pid');
        if ($pid > 0) $qs['pid'] = $pid;
        if ($this->input->get('invite') === '1') $qs['invite'] = '1';

        $url = site_url('messages/t/'.$tid . ($qs ? ('?'.http_build_query($qs)) : ''));
        redirect($url);
    }

    public function t($thread_id = null)
    {
        $me  = $this->_uid();
        $this->presence->ping($me);
        $tid = (int)$thread_id;
        if ($me <= 0) show_error('Unauthorized', 401);
        if ($tid <= 0) show_404();

        $thread = $this->mm->get_thread_for_user($tid, $me);

        if (!$thread) {
            $to = (int)$this->input->get('to');
            if ($to > 0 && $to !== $me && method_exists($this->mm, 'get_or_create_thread')) {
                $new = $this->mm->get_or_create_thread($me, $to);
                if ($new && !empty($new->id)) {
                    $qs  = [];
                    $pid = (int)$this->input->get('pid');
                    if ($pid > 0) $qs['pid'] = $pid;
                    if ($this->input->get('invite') === '1') $qs['invite'] = '1';
                    $ru     = $this->input->get('ru', true);
                    $amount = $this->input->get('amount', true);
                    if ($ru)     $qs['ru']     = $ru;
                    if ($amount) $qs['amount'] = $amount;

                    redirect('messages/t/'.$new->id . ($qs ? ('?'.http_build_query($qs)) : ''));
                    return;
                }
            }
            show_error('Thread not found', 404);
        }

        $other_id = ($thread->a_id == $me) ? (int)$thread->b_id : (int)$thread->a_id;

        $u = $this->db->select('id, email, first_name, last_name, role')
                      ->from('users')
                      ->where('id', $other_id)->get()->row();
        $other_name = $u ? trim(($u->first_name ?? '').' '.($u->last_name ?? '')) : ('User #'.$other_id);

        $wp = $this->db->get_where('worker_profile', ['workerID'=>$other_id])->row();
        $cp = $this->db->get_where('client_profile', ['clientID'=>$other_id])->row();

        $DEFAULT_AVATAR_REL = 'uploads/avatars/avatar.png';
        $norm = function($raw){
          $raw = trim((string)$raw);
          if ($raw !== '' && preg_match('#^https?://#i', $raw)) return $raw;
          if ($raw !== '') return base_url(str_replace('\\','/',$raw));
          return '';
        };

        $avatar = '';
        $wp = $this->db->get_where('worker_profile', ['workerID'=>$other_id])->row();
        $cp = $this->db->get_where('client_profile', ['clientID'=>$other_id])->row();

        if ($wp && !empty($wp->avatar))      $avatar = function_exists('avatar_url') ? avatar_url($wp->avatar) : $norm($wp->avatar);
        elseif ($cp && !empty($cp->avatar))  $avatar = function_exists('avatar_url') ? avatar_url($cp->avatar) : $norm($cp->avatar);

        if (!$avatar) {
          $cols = [];
          foreach (['avatar', 'photo', 'image', 'profile_pic'] as $col) {
            if ($this->db->field_exists($col, 'users')) {
              $cols[] = $col;
            }
          }
          if (!empty($cols)) {
            $urow = $this->db->select(implode(',', $cols), false)
                             ->from('users')
                             ->where('id', $other_id)
                             ->limit(1)
                             ->get()
                             ->row();
            if ($urow) {
              foreach ($cols as $col) {
                if (!empty($urow->$col)) {
                  $avatar = function_exists('avatar_url') ? avatar_url($urow->$col) : $norm($urow->$col);
                  break;
                }
              }
            }
          }
        }
        if (!$avatar) $avatar = base_url($DEFAULT_AVATAR_REL);

        $pid   = (int)$this->input->get('pid');
        $invOn = ($this->input->get('invite') === '1');

        $nb = $this->db->where('user_id', $me)
                       ->where('actor_id', $other_id)
                       ->where('is_read', 0)
                       ->where_in('type', ['hire','message']);
        if ($pid > 0 && $this->db->field_exists('link', 'tw_notifications')) {
            $nb->like('link', 'pid='.$pid);
        }
        $nb->update('tw_notifications', [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);

        $data = [
            'page_title'   => 'Messages',
            'thread'       => $thread,
            'me'           => $me,
            'other_id'     => $other_id,
            'other_name'   => $other_name,
            'other_avatar' => $avatar,
        ];

        $invite = null;

        $myRole   = (string)($this->session->userdata('role') ?? '');
        $clientId = ($myRole === 'worker') ? $other_id : $me;
        $workerId = ($myRole === 'worker') ? $me       : $other_id;

        $proposed_rate = null;
        $proposed_unit = null;
        $qAmount = $this->input->get('amount', true);
        $qRU     = $this->input->get('ru', true);
        if ($qAmount !== null && $qAmount !== '' && is_numeric($qAmount)) {
            $proposed_rate = (float)$qAmount;
        }
        if ($qRU) {
            $proposed_unit = strtolower($qRU);
            if (!in_array($proposed_unit, ['hour','day','project'], true)) $proposed_unit = null;
        }

        if ($proposed_rate === null || $proposed_unit === null) {
            $this->db->from('tw_notifications')
                     ->where('user_id', $me)
                     ->where('actor_id', $other_id)
                     ->where('type', 'hire');
            if ($pid > 0) {
                $this->db->like('link', 'pid='.$pid);
            }
            $n = $this->db->order_by('id', 'DESC')->limit(1)->get()->row();
            if ($n && !empty($n->link)) {
                $parts = @parse_url($n->link);
                if (!empty($parts['query'])) {
                    $qs = [];
                    parse_str($parts['query'], $qs);
                    if ($proposed_rate === null && isset($qs['amount']) && is_numeric($qs['amount'])) {
                        $proposed_rate = (float)$qs['amount'];
                    }
                    if ($proposed_unit === null && !empty($qs['ru'])) {
                        $u = strtolower($qs['ru']);
                        if (in_array($u, ['hour','day','project'], true)) $proposed_unit = $u;
                    }
                }
            }
        }

        if ($pid > 0) {
            $pr = $this->db->get_where('client_projects', ['id'=>$pid])->row();

            $files = [];
            if ($pr && !empty($pr->files)) {
                $tmp = json_decode($pr->files, true);
                if (is_array($tmp)) {
                    foreach ($tmp as $f) {
                        $ext  = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        $type = in_array($ext,['jpg','jpeg','png','webp','gif']) ? 'image' : ($ext==='pdf' ? 'pdf' : 'file');
                        $files[] = ['url'=>base_url($f), 'name'=>basename($f), 'type'=>$type];
                    }
                }
            }

            $canAct = $invOn && ($myRole === 'worker');
            $loc = trim(
                ($pr->brgy ? $pr->brgy.', ' : '')
              . ($pr->city ? $pr->city.', ' : '')
              . ($pr->province ?? '')
            );

            if ($pr) {
                $invite = [
                    'pid'         => (int)$pid,
                    'title'       => (string)$pr->title,
                    'files'       => $files, 
                    'rate'        => $proposed_rate, 
                    'budget_min'  => ($pr->budget_min !== null ? (float)$pr->budget_min : null),
                    'budget_max'  => ($pr->budget_max !== null ? (float)$pr->budget_max : null),
                    'loc'         => $loc,
                    'posted_at'   => $pr->created_at,
                    'posted_ago'  => $this->_ago($pr->created_at),
                    'category'    => $pr->category  ?? null,
                    'can_act'     => (bool)$canAct,
                ];
            }
        }

        $invite_status = null;

        if ($pid > 0) {
            $tx = $this->db->select('*')
                           ->from('transactions')
                           ->where('clientID', $clientId)
                           ->where('workerID', $workerId)
                           ->where('projectID', (int)$pid)
                           ->order_by('transactionID','DESC')
                           ->limit(1)->get()->row();

            if ($tx) {
                $st = strtolower((string)($tx->status ?? ''));
                $isAcceptedLike = in_array($st, ['accepted','active','completed'], true);
                if (!$invOn || $isAcceptedLike) {
                    $state = $isAcceptedLike ? 'accepted'
                           : (in_array($st, ['declined','rejected']) ? 'declined' : $st);
                    $at    = !empty($tx->confirmed_at) ? $tx->confirmed_at : ($tx->created_at ?? date('Y-m-d H:i:s'));
                    $invite_status = [
                        'state'       => $state,
                        'at'          => date('M d, Y h:i A', strtotime($at)),
                        'rate_agreed' => ($tx->rate_agreed !== null ? (float)$tx->rate_agreed : null),
                        'rate_unit'   => ($tx->rateUnit ?? null),
                    ];
                    if ($invite) {
                        $invite['agreed_rate']      = ($tx->rate_agreed !== null ? (float)$tx->rate_agreed : null);
                        $invite['agreed_rate_unit'] = ($tx->rateUnit ?? null);
                    }
                }
            }
        }

        if (!$invite_status && !$invOn && $pid <= 0) {
            $tx = $this->db->select('*')
                           ->from('transactions')
                           ->where('clientID', $clientId)
                           ->where('workerID', $workerId)
                           ->order_by('transactionID','DESC')
                           ->limit(1)->get()->row();

            if ($tx) {
                $st    = strtolower((string)($tx->status ?? ''));
                $state = in_array($st, ['accepted','active']) ? 'accepted'
                       : (in_array($st, ['declined','rejected']) ? 'declined' : $st);
                $at    = !empty($tx->confirmed_at) ? $tx->confirmed_at : ($tx->created_at ?? date('Y-m-d H:i:s'));
                $invite_status = [
                    'state'       => $state,
                    'at'          => date('M d, Y h:i A', strtotime($at)),
                    'rate_agreed' => ($tx->rate_agreed !== null ? (float)$tx->rate_agreed : null),
                    'rate_unit'   => ($tx->rateUnit ?? null),
                ];

                if (!$invite) {
                    $files = [];
                    $pr3 = $this->db->order_by('id','DESC')->limit(1)
                                    ->get_where('client_projects', ['clientID'=>$clientId, 'title'=>$tx->title])->row();
                    if ($pr3 && !empty($pr3->files)) {
                        $tmp = json_decode($pr3->files, true);
                        if (is_array($tmp)) {
                            foreach ($tmp as $f) {
                                $ext  = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                                $type = in_array($ext,['jpg','jpeg','png','webp']) ? 'image' : ($ext==='pdf' ? 'pdf' : 'file');
                                $files[] = ['url'=>base_url($f), 'name'=>basename($f), 'type'=>$type];
                            }
                        }
                    }
                    $invite = [
                        'pid'               => (int)($pr3->id ?? 0),
                        'title'             => (string)$tx->title,
                        'files'             => $files,
                        'can_act'           => false,
                        'agreed_rate'       => ($tx->rate_agreed !== null ? (float)$tx->rate_agreed : null),
                        'agreed_rate_unit'  => ($tx->rateUnit ?? null),
                        'rate'              => $proposed_rate,
                        'rate_unit'         => $proposed_unit ?: ($pr3->rate_unit ?? null),
                    ];
                } else {
                    $invite['agreed_rate']      = ($tx->rate_agreed !== null ? (float)$tx->rate_agreed : null);
                    $invite['agreed_rate_unit'] = ($tx->rateUnit ?? null);
                }
            }
        }

        if ($invite && $invite_status) {
            $invite['can_act'] = false;
        }

        $data['invite']        = $invite;
        $data['invite_status'] = $invite_status;

        $this->load->view('messages_thread', $data);
    }

    public function api_invite_action()
    {
        $json = function($ok,$msg='OK',$extra=[]){
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(array_merge(['ok'=>(bool)$ok,'message'=>$msg],(array)$extra)));
        };

        $me = $this->_uid();
        if ($me <= 0) return $json(false,'Unauthorized');

        $tid = (int)$this->input->post('thread_id');
        $pid = (int)($this->input->post('project_id') ?: $this->input->post('pid') ?: $this->input->get('pid'));
        $act = (string)$this->input->post('action');

        if ($act === 'accept') {
            $confirm = $this->input->post('confirm');
            if ($confirm !== '1') return $json(false,'Confirmation required');
        }

        if (!$tid) return $json(false,'Missing thread_id');
        if (!$pid) return $json(false,'Missing project_id');
        if (!in_array($act, ['accept','decline'], true)) return $json(false,'Invalid action');

        $thread = $this->mm->get_thread_for_user($tid, $me);
        if (!$thread) return $json(false,'Thread not found');

        if (strtolower((string)$this->session->userdata('role')) !== 'worker') {
            return $json(false,'Only workers can act on invitations');
        }

        $clientId = ($thread->a_id == $me) ? (int)$thread->b_id : (int)$thread->a_id;

        $pr = $this->db->get_where('client_projects', ['id'=>$pid, 'clientID'=>$clientId])->row();
        if (!$pr) return $json(false,'Project not found for this client');

        // An actual hire/invite from this client must exist — otherwise the
        // "action" is forged against an arbitrary project.
        $hasInvite = $this->db->order_by('id','DESC')->limit(1)
            ->get_where('tw_notifications', ['user_id'=>$me, 'actor_id'=>$clientId, 'type'=>'hire'])
            ->row();
        if (!$hasInvite) return $json(false,'No invitation exists for this project.');

        $amount = $this->input->post('rate');
        if ($amount === null || $amount === '') $amount = $this->input->post('amount');
        if ($amount === null || $amount === '') $amount = $this->input->get('amount');
        $amount = ($amount !== null && $amount !== '' && is_numeric($amount)) ? (float)$amount : null;

        $rate_unit = $this->input->post('rate_unit', true)
                  ?: $this->input->post('ru', true)
                  ?: $this->input->get('ru', true)
                  ?: ($pr->rate_unit ?? null);
        if ($rate_unit !== null) $rate_unit = strtolower((string)$rate_unit);
        if (!in_array($rate_unit, ['hour','day','project', null], true)) $rate_unit = null;

        $qb = $this->db->select('*')->from('transactions')
            ->where(['clientID'=>$clientId,'workerID'=>$me])
            ->order_by('transactionID','DESC')->limit(1);
        if ($this->db->field_exists('projectID','transactions')) {
            $qb->where('projectID', $pid);
        } else {
            $qb->where('title', (string)$pr->title);
        }
        $existing = $qb->get()->row();

        $now = date('Y-m-d H:i:s');
        $loc = trim(($pr->brgy ? $pr->brgy.', ' : '').($pr->city ? $pr->city.($pr->province ? ', ' : '') : '').($pr->province ?? ''));

        if ($act === 'decline') {
            if ($existing) {
                if (!in_array(strtolower((string)$existing->status), ['active','accepted'], true)) {
                    $upd = [
                        'status'       => 'declined',
                        'confirmed_by' => $me,
                        'confirmed_at' => $now
                    ];
                    if ($this->db->field_exists('projectID','transactions') && (int)$existing->projectID === 0) {
                        $upd['projectID'] = $pid;
                    }
                    $this->db->where('transactionID', (int)$existing->transactionID)->update('transactions', $upd);
                }
            } else {
                $ins = [
                    'clientID'      => $clientId,
                    'workerID'      => $me,
                    'skillsID'      => 0,
                    'title'         => (string)$pr->title,
                    'description'   => (string)$pr->description,
                    'status'        => 'declined',
                    'rate_agreed'   => null,
                    'rateUnit'      => $rate_unit,
                    'location_note' => $loc,
                    'confirmed_by'  => $me,
                    'confirmed_at'  => $now,
                    'created_at'    => $now,
                ];
                if ($this->db->field_exists('projectID','transactions')) $ins['projectID'] = $pid;
                $this->db->insert('transactions', $ins);
            }

            $this->cp->add_notification(
                $clientId, $me, 'hire',
                'Request declined',
                'Worker declined your hire request.',
                site_url('messages/start?to='.$me.'&pid='.$pid)
            );

            return $json(true,'Invitation declined');
        }

        if ($existing && !in_array(strtolower((string)$existing->status), ['active','accepted'], true)) {
            $upd = [
                'status'       => 'accepted',
                'rate_agreed'  => $amount,
                'rateUnit'     => $rate_unit,
                'confirmed_by' => $me,
                'confirmed_at' => $now,
                'started_at'   => $now,
            ];
            if ($this->db->field_exists('projectID','transactions') && (int)$existing->projectID === 0) {
                $upd['projectID'] = $pid;
            }
            $this->db->where('transactionID', (int)$existing->transactionID)->update('transactions', $upd);
        } elseif (!$existing) {
            $ins = [
                'clientID'      => $clientId,
                'workerID'      => $me,
                'skillsID'      => 0,
                'title'         => (string)$pr->title,
                'description'   => (string)$pr->description,
                'status'        => 'accepted',
                'rate_agreed'   => $amount,
                'rateUnit'      => $rate_unit,
                'location_note' => $loc,
                'confirmed_by'  => $me,
                'confirmed_at'  => $now,
                'started_at'    => $now,
                'created_at'    => $now,
            ];
            if ($this->db->field_exists('projectID','transactions')) $ins['projectID'] = $pid;
            $this->db->insert('transactions', $ins);
        }

        $this->load->model('Personnel_model','personnelm');
        $this->personnelm->ensure_hired((int)$clientId, (int)$me, (int)$pid ?: null, $amount, $rate_unit);

        $this->cp->add_notification(
            $clientId, $me, 'hire',
            'Request accepted',
            'Worker accepted your hire request.',
            site_url('messages/start?to='.$me.'&pid='.$pid)
        );

        return $json(true,'Invitation accepted');
    }

    public function api_thread($thread_id = null)
    {
        $me = $this->_uid();
        if ($me <= 0) return $this->_out(false, 'Unauthorized');

        $tid = (int)$thread_id;
        if ($tid <= 0) return $this->_out(false, 'Invalid thread');

        $thread = $this->mm->get_thread_for_user($tid, $me);
        if (!$thread) return $this->_out(false, 'Not found');

        $after = (int)$this->input->get('after_id');
        $limit = (int)($this->input->get('limit') ?: 50);
        $limit = max(1, min(200, $limit));

        $rows = $this->mm->list_messages_for_user($tid, $me, $limit, $after);

        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                'id'        => (int)$r->id,
                'sender_id' => (int)$r->sender_id,
                'body'      => (string)$r->body,
                'created_at'=> date('M d, Y h:i A', strtotime($r->created_at)),
                'is_me'     => ((int)$r->sender_id === $me),
            ];
        }

        $this->mm->mark_read($tid, $me);

        return $this->_out(true, 'OK', ['messages'=>$items]);
    }

    public function api_send()
    {
        $me = $this->_uid();
        if ($me <= 0) return $this->_out(false, 'Unauthorized');

        $tid  = (int)$this->input->post('thread_id');
        $text = trim((string)$this->input->post('body', true));

        if ($tid <= 0)   return $this->_out(false, 'Invalid thread');
        if ($text === '') return $this->_out(false, 'Message is empty');
        if (mb_strlen($text) > 2000) return $this->_out(false, 'Too long (max 2000 chars)');

        $thread = $this->mm->get_thread_for_user($tid, $me);
        if (!$thread) return $this->_out(false, 'Not found');

        $msg = $this->mm->add_message($tid, $me, $text);
        if (!$msg) {
            $err = $this->db->error();
            log_message('error', 'api_send failed. DB error: '.print_r($err, true).' | thread_id='.$tid.' sender='.$me);
            return $this->_out(false, 'Send failed');
        }

        return $this->_out(true, 'Sent', [
            'id'         => (int)$msg->id,
            'created_at' => date('M d, Y h:i A', strtotime($msg->created_at)),
        ]);
    }

    public function api_read($thread_id = null)
    {
        $me = $this->_uid();
        if ($me <= 0) return $this->_out(false, 'Unauthorized');

        $tid = (int)$thread_id;
        if ($tid <= 0) return $this->_out(false, 'Invalid thread');

        $thread = $this->mm->get_thread_for_user($tid, $me);
        if (!$thread) return $this->_out(false, 'Not found');

        $n = $this->mm->mark_read($tid, $me);
        return $this->_out(true, 'OK', ['updated'=>$n]);
    }

    public function api_start()
    {
        $me = $this->_uid();
        if ($me <= 0) return $this->_out(false, 'Unauthorized');

        $to = (int)$this->input->post('to');
        if ($to <= 0 || $to === $me) return $this->_out(false, 'Invalid recipient');

        $thread = $this->mm->get_or_create_thread($me, $to);
        if (!$thread) return $this->_out(false, 'Could not create thread');

        if ($this->db->field_exists('read_at', 'tw_notifications')) {
            $this->db->where('user_id', $me)
                     ->where('actor_id', $to)
                     ->where('is_read', 0)
                     ->where_in('type', ['hire','message'])
                     ->update('tw_notifications', ['is_read'=>1, 'read_at'=>date('Y-m-d H:i:s')]);
        } else {
            $this->db->where('user_id', $me)
                     ->where('actor_id', $to)
                     ->where('is_read', 0)
                     ->where_in('type', ['hire','message'])
                     ->update('tw_notifications', ['is_read'=>1]);
        }

        return $this->_out(true, 'OK', [
            'id'   => (int)$thread->id,
            'link' => site_url('messages/t/'.$thread->id),
        ]);
    }

    public function api_unread()
    {
        $me = $this->_uid();
        if ($me <= 0) return $this->_out(false, 'Unauthorized');
        $n = (int)$this->mm->unread_messages_count($me);
        return $this->_out(true, 'OK', ['unread' => $n]);
    }

    public function api_feed()
    {
        $me = $this->_uid();
        if ($me <= 0) return $this->_out(false, 'Unauthorized');

        $limit = (int)($this->input->get('limit') ?: 8);
        $rows  = $this->mm->latest_threads($me, $limit);

        $items = [];
        foreach ($rows as $r) {
            $other_id   = (int)$r->other_id;
            $name       = trim(($r->first_name ?? '').' '.($r->last_name ?? ''));
            $avatar = '';
            $DEFAULT_AVATAR_REL = 'uploads/avatars/avatar.png';
            $norm = function($raw){
                $raw = trim((string)$raw);
                if ($raw !== '' && preg_match('#^https?://#i', $raw)) return $raw;
                if ($raw !== '') return base_url(str_replace('\\','/',$raw));
                return '';
            };

            $wp = $this->db->get_where('worker_profile', ['workerID'=>$other_id])->row();
            $cp = $this->db->get_where('client_profile', ['clientID'=>$other_id])->row();
            if ($wp && !empty($wp->avatar))      $avatar = function_exists('avatar_url') ? avatar_url($wp->avatar) : $norm($wp->avatar);
            elseif ($cp && !empty($cp->avatar))  $avatar = function_exists('avatar_url') ? avatar_url($cp->avatar) : $norm($cp->avatar);
            if (!$avatar) $avatar = base_url($DEFAULT_AVATAR_REL);

            $items[] = [
                'thread_id' => (int)$r->thread_id,
                'other_id'  => $other_id,
                'name'      => $name !== '' ? $name : ('User #'.$other_id),
                'avatar'    => $avatar,
                'snippet'   => (string)$r->last_body,
                'time'      => date('M d, Y h:i A', strtotime($r->last_time ?? date('Y-m-d H:i:s'))),
                'unread'    => (int)$r->unread,
                'link'      => site_url('messages/t/'.$r->thread_id),
            ];
        }
        return $this->_out(true, 'OK', ['items'=>$items]);
    }

    private function _out($ok, $msg='OK', $extra=[])
    {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(array_merge(['ok'=>(bool)$ok,'message'=>$msg], (array)$extra)));
    }

    public function api_delete($message_id = null)
    {
        $me = $this->_uid();
        if ($me <= 0) return $this->_out(false, 'Unauthorized');

        $mid = (int)($message_id ?: $this->input->post('id'));
        if ($mid <= 0) return $this->_out(false, 'Invalid message id');

        [$ok, $msg] = $this->mm->delete_message_for_user($mid, $me);
        return $this->_out($ok, $msg);
    }
    public function api_presence_ping()
{
        $this->_nocache_headers();

    $me = $this->_uid();
    if ($me <= 0) return $this->_out(false, 'Unauthorized');
    $status = $this->input->post('status', true);
    $this->presence->ping($me, $status);
    return $this->_out(true, 'OK', ['server_time' => date('c')]);
}

public function api_presence_get($user_id = null)
{
        $this->_nocache_headers();

    $me  = $this->_uid();
    if ($me <= 0) return $this->_out(false, 'Unauthorized');

    $other = (int)($user_id ?: $this->input->get('user_id'));
    if ($other <= 0) return $this->_out(false, 'Invalid user');

    $info = $this->presence->get_presence($other);
    if (!$info) return $this->_out(true, 'OK', [
        'status' => 'offline',
        'last_seen' => null,
        'last_seen_human' => null
    ]);
    $ago = $info['last_seen'] ? $this->_ago($info['last_seen']) : null;

    return $this->_out(true, 'OK', [
        'status' => $info['status'],
        'last_seen' => $info['last_seen'],
        'last_seen_human' => $ago,
    ]);
}
public function presence_beacon()
{
        $this->_nocache_headers();

    $uid = (int)$this->session->userdata('id');
    if ($uid > 0) {
        $this->load->model('Presence_model','presence');
        $this->presence->mark_offline($uid);
    }
    $this->output->set_content_type('application/json')->set_output(json_encode(['ok'=>true]));
}
private function _nocache_headers() {
  $this->output
    ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
    ->set_header('Pragma: no-cache')
    ->set_header('Expires: 0');
}

}
