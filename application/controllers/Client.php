<?php defined('BASEPATH') or exit('No direct script access allowed');

class Client extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form', 'html', 'security']);
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->model('ClientProfile_model', 'cp');
        $this->load->database();

        $open = ['notifications_feed', 'notifications_count', 'notifications_mark_read'];
        $method = $this->router->method;

        if (!in_array($method, $open)) {
            if (!$this->session->userdata('logged_in')) {
                redirect('auth/login');
            }
            if ($this->session->userdata('role') !== 'client') {
                show_error('Unauthorized', 403);
            }
        } else {
            if (!$this->session->userdata('logged_in')) {
                redirect('auth/login');
            }
        }
    }


    public function edit()
    {
        $uid = (int)$this->session->userdata('user_id');
        $data['page_title'] = 'Edit Client Profile';
        $data['profile']    = $this->cp->get($uid);
        $this->load->view('client_edit', $data);
    }

    public function update()
    {
        $uid    = (int)$this->session->userdata('user_id');
        $next   = $this->input->post('next', true) ?: 'edit';
        $goto   = ($next === 'dashboard') ? 'dashboard/client' : 'client/edit';
        $partial = $this->input->post('__partial');

        $finish = function ($route) {
            redirect($route);
            exit;
        };

        if ($partial === 'avatar') {
            $isAjax = ($this->input->post('ajax') == '1') || $this->input->is_ajax_request();

            $this->load->library('upload');
            $current = $this->cp->get($uid);

            $base = FCPATH . 'uploads/clients';
            $dirAvatar = $base . '/avatars';
            if (!is_dir($dirAvatar)) @mkdir($dirAvatar, 0777, true);

            $single = function ($field, $path, $types, $maxmb = 8) {
                if (empty($_FILES[$field]['name'])) return null;

                $cfg = [
                    'upload_path'      => $path,
                    'allowed_types'    => $types,
                    'max_size'         => $maxmb * 1024,
                    'file_ext_tolower' => TRUE,
                    'remove_spaces'    => TRUE,
                    'encrypt_name'     => TRUE,
                    'detect_mime'      => TRUE,
                ];
                $this->upload->initialize($cfg);

                if (!$this->upload->do_upload($field)) {
                    throw new RuntimeException($this->upload->display_errors('', ''));
                }

                $d = $this->upload->data();
                $pathFull = $d['full_path'];
                $ext      = $d['file_ext'];

                if (function_exists('validate_uploaded_file_signature') && !validate_uploaded_file_signature($pathFull, $ext)) {
                    @unlink($pathFull);
                    throw new RuntimeException('Invalid or unsafe file.');
                }

                $imgExts = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
                if (in_array(strtolower($ext), $imgExts, true) && function_exists('safe_image_reencode')) {
                    if (!safe_image_reencode($pathFull, $ext)) {
                        @unlink($pathFull);
                        throw new RuntimeException('Failed to sanitize image.');
                    }
                }

                return 'uploads/clients/' . basename($path) . '/' . $d['file_name'];
            };

            try {
                $avatar_path = $single('avatar', $dirAvatar, 'jpg|jpeg|png|webp', 5);

                if (!$avatar_path) {
                    throw new RuntimeException('Please choose an image to upload.');
                }

                $this->cp->upsert($uid, [
                    'avatar'     => $avatar_path,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $this->session->set_userdata('avatar', $avatar_path);

                if ($isAjax) {
                    return $this->_json(true, 'OK', [
                        'avatar_url' => base_url($avatar_path),
                        'csrf_name'  => $this->security->get_csrf_token_name(),
                        'csrf_hash'  => $this->security->get_csrf_hash(),
                        'flash'      => ['type' => 'success', 'text' => 'Profile photo updated.'],
                    ]);
                }

                $this->session->set_flashdata('success', 'Profile photo updated.');
                return $finish($goto);
            } catch (RuntimeException $e) {

                if ($isAjax) {
                    $this->output->set_status_header(400);
                    return $this->_json(false, $e->getMessage(), [
                        'csrf_name' => $this->security->get_csrf_token_name(),
                        'csrf_hash' => $this->security->get_csrf_hash(),
                        'flash'     => ['type' => 'error', 'text' => $e->getMessage()],
                    ]);
                }

                $this->session->set_flashdata('error', $e->getMessage());
                return $finish($goto);
            }
        }

        $hasCompanyPosition = client_has_company_position_field();

        $this->form_validation->set_rules('fName', 'First name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('lName', 'Last name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('address', 'Address', 'trim|max_length[300]');
        $this->form_validation->set_rules('employer', 'Employer', 'trim|max_length[120]');
        $this->form_validation->set_rules('business_name', 'Business / Project', 'trim|max_length[160]');
        $this->form_validation->set_rules('business_location', 'Business Location', 'trim|max_length[160]');
        $this->form_validation->set_rules('phoneNo', 'Cellphone', 'trim|max_length[45]');
        $this->form_validation->set_rules('companyName', 'Company Name', 'trim|max_length[45]');
        if ($hasCompanyPosition) {
            $this->form_validation->set_rules('company_position', 'Position', 'trim|max_length[120]');
        }
        $this->form_validation->set_rules('city', 'City', 'trim|max_length[120]');
        $this->form_validation->set_rules('province', 'Province', 'trim|max_length[120]');
        $this->form_validation->set_rules('brgy', 'Barangay', 'trim|max_length[120]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors('', ''));
            return $finish($goto);
        }

        $current = $this->cp->get($uid);

        $base = FCPATH . 'uploads/clients';
        $dirs = [
            'avatar'      => $base . '/avatars',
            'id_image'    => $base . '/ids',
            'certs'       => $base . '/certificates',
            'permit'      => $base . '/permits',
        ];
        foreach ($dirs as $d) {
            if (!is_dir($d)) @mkdir($d, 0777, true);
        }

        $this->load->library('upload');

        // REPLACE your $single and $multi in the main update() with these:

        $single = function ($field, $path, $types, $maxmb = 8) {
            if (empty($_FILES[$field]['name'])) return null;

            $cfg = [
                'upload_path'      => $path,
                'allowed_types'    => $types,
                'max_size'         => $maxmb * 1024,
                'file_ext_tolower' => TRUE,
                'remove_spaces'    => TRUE,
                'encrypt_name'     => TRUE,
                'detect_mime'      => TRUE,
            ];
            $this->upload->initialize($cfg);

            if (!$this->upload->do_upload($field)) {
                throw new RuntimeException($this->upload->display_errors('', ''));
            }

            $d        = $this->upload->data();
            $pathFull = $d['full_path'];
            $ext      = $d['file_ext'];

            // Signature / magic-bytes validation (if available)
            if (function_exists('validate_uploaded_file_signature')) {
                if (!validate_uploaded_file_signature($pathFull, $ext)) {
                    @unlink($pathFull);
                    throw new RuntimeException('Invalid or unsafe file.');
                }
            }

            // Re-encode images (if available) to sanitize
            $imgExts = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
            if (in_array(strtolower($ext), $imgExts, true) && function_exists('safe_image_reencode')) {
                if (!safe_image_reencode($pathFull, $ext)) {
                    @unlink($pathFull);
                    throw new RuntimeException('Failed to sanitize image.');
                }
            }

            return 'uploads/clients/' . basename($path) . '/' . $d['file_name'];
        };

        $multi = function ($field, $path, $types, $maxmb = 8) {
            $out = [];
            if (empty($_FILES[$field]['name'][0])) return $out;

            $count = count($_FILES[$field]['name']);
            for ($i = 0; $i < $count; $i++) {
                if (empty($_FILES[$field]['name'][$i])) continue;

                $_FILES['one'] = [
                    'name'     => $_FILES[$field]['name'][$i],
                    'type'     => $_FILES[$field]['type'][$i],
                    'tmp_name' => $_FILES[$field]['tmp_name'][$i],
                    'error'    => $_FILES[$field]['error'][$i],
                    'size'     => $_FILES[$field]['size'][$i],
                ];

                $cfg = [
                    'upload_path'      => $path,
                    'allowed_types'    => $types,
                    'max_size'         => $maxmb * 1024,
                    'file_ext_tolower' => TRUE,
                    'remove_spaces'    => TRUE,
                    'encrypt_name'     => TRUE,
                    'detect_mime'      => TRUE,
                ];
                $this->upload->initialize($cfg);

                if (!$this->upload->do_upload('one')) {
                    throw new RuntimeException($this->upload->display_errors('', ''));
                }

                $d        = $this->upload->data();
                $pathFull = $d['full_path'];
                $ext      = $d['file_ext'];

                // Signature / magic-bytes validation (guarded)
                if (function_exists('validate_uploaded_file_signature')) {
                    if (!validate_uploaded_file_signature($pathFull, $ext)) {
                        @unlink($pathFull);
                        throw new RuntimeException('Invalid or unsafe file.');
                    }
                }

                // Re-encode images (if available)
                $imgExts = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
                if (in_array(strtolower($ext), $imgExts, true) && function_exists('safe_image_reencode')) {
                    if (!safe_image_reencode($pathFull, $ext)) {
                        @unlink($pathFull);
                        throw new RuntimeException('Failed to sanitize image.');
                    }
                }

                $out[] = 'uploads/clients/' . basename($path) . '/' . $d['file_name'];
            }

            return $out;
        };

        try {
            $avatar_path   = $single('avatar',     $dirs['avatar'], 'jpg|jpeg|png|webp', 5) ?: ($current->avatar ?? null);
            $id_image_path = $single('id_image',   $dirs['id_image'], 'jpg|jpeg|png|webp|pdf', 8) ?: ($current->id_image ?? null);
            $permit_path   = $single('business_permit', $dirs['permit'], 'jpg|jpeg|png|webp|pdf', 8) ?: ($current->business_permit ?? null);

            $cert_titles = (array) $this->input->post('cert_titles');

            $multi_with_titles = function ($field, array $titles, $path, $types, $maxmb = 8) {
                $out = [];
                if (empty($_FILES[$field]['name'][0])) return $out;

                $count = count($_FILES[$field]['name']);
                for ($i = 0; $i < $count; $i++) {
                    if (empty($_FILES[$field]['name'][$i])) continue;

                    $_FILES['one'] = [
                        'name'     => $_FILES[$field]['name'][$i],
                        'type'     => $_FILES[$field]['type'][$i],
                        'tmp_name' => $_FILES[$field]['tmp_name'][$i],
                        'error'    => $_FILES[$field]['error'][$i],
                        'size'     => $_FILES[$field]['size'][$i],
                    ];

                    $cfg = [
                        'upload_path'      => $path,
                        'allowed_types'    => $types,
                        'max_size'         => $maxmb * 1024,
                        'file_ext_tolower' => TRUE,
                        'remove_spaces'    => TRUE,
                        'encrypt_name'     => TRUE,
                        'detect_mime'      => TRUE,
                    ];
                    $this->upload->initialize($cfg);

                    if (!$this->upload->do_upload('one')) {
                        throw new RuntimeException($this->upload->display_errors('', ''));
                    }

                    $d        = $this->upload->data();
                    $pathFull = $d['full_path'];
                    $ext      = $d['file_ext'];

                    if (!function_exists('validate_uploaded_file_signature') || !validate_uploaded_file_signature($pathFull, $ext)) {
                        @unlink($pathFull);
                        throw new RuntimeException('Invalid or unsafe file.');
                    }

                    $imgExts = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
                    if (in_array(strtolower($ext), $imgExts, true) && function_exists('safe_image_reencode')) {
                        if (!safe_image_reencode($pathFull, $ext)) {
                            @unlink($pathFull);
                            throw new RuntimeException('Failed to sanitize image.');
                        }
                    }

                    $relPath = 'uploads/clients/' . basename($path) . '/' . $d['file_name'];
                    $title   = trim($titles[$i] ?? '');
                    if ($title === '') {
                        $title = pathinfo($_FILES[$field]['name'][$i], PATHINFO_FILENAME);
                    }

                    $out[] = ['path' => $relPath, 'title' => $title];
                }
                return $out;
            };

            $new_certs = $multi_with_titles('certificates', $cert_titles, $dirs['certs'], 'jpg|jpeg|png|webp|pdf', 8);

            $byPath = [];
            if (!empty($current->certificates)) {
                $decoded = json_decode($current->certificates, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $c) {
                        if (is_string($c)) {
                            $byPath[$c] = ['path' => $c, 'title' => pathinfo($c, PATHINFO_FILENAME)];
                        } elseif (is_array($c) && !empty($c['path'])) {
                            $byPath[$c['path']] = [
                                'path'  => $c['path'],
                                'title' => trim($c['title'] ?? pathinfo($c['path'], PATHINFO_FILENAME)),
                            ];
                        }
                    }
                }
            }
            foreach ($new_certs as $c) {
                $byPath[$c['path']] = $c;
            }
            $certs_all = array_values($byPath);


            // ID image is optional now; keep previous if not provided.

            // When selects are disabled (e.g. if API fails), the browser omits them from POST.
            // Keep existing DB values when a key is missing entirely.
            $posted = $this->input->post(NULL, true) ?: [];
            $province_val = array_key_exists('province', $posted) ? (string)$this->input->post('province', true) : ($current->province ?? '');
            $city_val     = array_key_exists('city', $posted)     ? (string)$this->input->post('city', true)     : ($current->city ?? '');
            $brgy_val     = array_key_exists('brgy', $posted)     ? (string)$this->input->post('brgy', true)     : ($current->brgy ?? '');

            $employer_val          = trim((string)$this->input->post('employer', true));
            $business_name_val     = trim((string)$this->input->post('business_name', true));
            $business_loc_val      = trim((string)$this->input->post('business_location', true));
            $phone_val             = trim((string)$this->input->post('phoneNo', true));
            $company_name_val      = trim((string)$this->input->post('companyName', true));
            $company_position_val  = null;
            if ($hasCompanyPosition) {
                $company_position_val = trim((string)$this->input->post('company_position', true));
                if ($company_name_val === '' || $employer_val === '' || $company_position_val === '') {
                    $company_position_val = null;
                }
            }

            $payload = [
                'fName'             => $this->input->post('fName', true),
                'mName'             => $this->input->post('mName', true),
                'lName'             => $this->input->post('lName', true),
                'address'           => $this->input->post('address', true),
                'employer'          => $employer_val,
                'business_name'     => $business_name_val,
                'business_location' => $business_loc_val,
                'phoneNo'           => $phone_val,
                'companyName'       => $company_name_val,
                'city'              => $city_val,
                'province'          => $province_val,
                'brgy'              => $brgy_val,
                'avatar'            => $avatar_path,
                'id_image'          => $id_image_path,
                'business_permit'   => $permit_path,
                'certificates' => json_encode($certs_all),
            ];
            if ($hasCompanyPosition) {
                $payload['company_position'] = $company_position_val;
            }

            $res = $this->cp->upsert($uid, $payload);

            if (!empty($res['ok'])) {
                // Keep the canonical users name + session in sync so the edited
                // name reflects on the dashboard, sidebar and top nav (which read
                // users.first_name/last_name), not just in client_profile.
                $this->db->where('id', $uid)->update('users', [
                    'first_name' => $payload['fName'],
                    'last_name'  => $payload['lName'],
                ]);
                $this->session->set_userdata('first_name', $payload['fName']);
                $this->session->set_userdata('last_name', $payload['lName']);

                if (!empty($res['changed'])) {
                    $this->session->set_flashdata('success', 'Client profile saved.');
                } else {
                    $this->session->set_flashdata('info', 'No changes were detected.');
                }
            } else {
                $msg = !empty($res['error']) ? $res['error'] : 'Could not save client profile.';
                $this->session->set_flashdata('error', $msg);
            }
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('error', $e->getMessage());
        }

        return $finish($goto);
    }

    private function _json($ok, $msg = 'OK', $extra = [])
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array_merge(['ok' => (bool)$ok, 'message' => $msg], (array)$extra)));
        return; // optional
    }

    private function _current_user_id()
    {
        foreach (['id', 'user_id', 'uid', 'account_id'] as $k) {
            $v = $this->session->userdata($k);
            if (is_numeric($v)) {
                return (int)$v;
            }
        }
        return 0;
    }

    private function _require_login()
    {
        if ($this->_current_user_id() > 0) return;
        $uri  = function_exists('uri_string') ? uri_string() : '';
        $base = function_exists('site_url') ? site_url($uri) : (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
        $qs   = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? ('?' . $_SERVER['QUERY_STRING']) : '';
        $next = $base . $qs;
        redirect('auth/login?next=' . rawurlencode($next));
        exit;
    }
    public function notifications_feed()
    {
        $uid = $this->_current_user_id();
        if ($uid <= 0) {
            return $this->_json(false, 'Unauthorized');
        }

        $this->load->model('ClientProfile_model', 'Profile');
        $limit = (int)($this->input->get('limit') ?: 10);
        $rows  = $this->Profile->get_notifications($uid, $limit, 0);

        $items = [];
        foreach ($rows as $r) {
            $actorName = trim(($r->actor_fname ?? '') . ' ' . ($r->actor_lname ?? ''));
            $seed      = $actorName !== '' ? $actorName : (string)$r->actor_id;
            $avatar    = 'https://api.dicebear.com/9.x/initials/svg?seed=' . rawurlencode($seed);
            $items[] = [
                'id'      => (int)$r->id,
                'type'    => (string)$r->type,
                'title'   => (string)$r->title,
                'body'    => (string)$r->body,
                'link'    => (string)$r->link,
                'is_read' => (int)$r->is_read,
                'created' => date('M d, Y h:i A', strtotime($r->created_at)),
                'actor'   => $actorName !== '' ? $actorName : (string)$r->actor_id,
                'avatar'  => $avatar,
            ];
        }
        return $this->_json(true, 'OK', ['items' => $items]);
    }

    public function notifications_count()
    {
        $uid = $this->_current_user_id();
        if ($uid <= 0) {
            return $this->_json(false, 'Unauthorized');
        }

        $this->load->model('ClientProfile_model', 'Profile');
        $unread = (int)$this->Profile->unread_count($uid);
        return $this->_json(true, 'OK', ['unread' => $unread]);
    }

    public function notifications_mark_read($id = null)
    {
        $uid = $this->_current_user_id();
        if ($uid <= 0) {
            return $this->_json(false, 'Unauthorized');
        }

        $this->load->model('ClientProfile_model', 'Profile');
        $ok = false;
        if ($id) {
            $ok = $this->Profile->mark_read((int)$id, $uid);
        }
        return $this->_json($ok, $ok ? 'Updated' : 'Failed');
    }
    public function notify_hire()
    {
        $uid = $this->_current_user_id();
        if ($uid <= 0) {
            return $this->_json(false, 'Unauthorized');
        }
        if ($this->session->userdata('role') !== 'client') {
            return $this->_json(false, 'Only clients can send hire requests');
        }

        $recipient = (int)($this->input->post('user_id') ?: $this->input->post('worker_id'));
        if ($recipient <= 0) {
            return $this->_json(false, 'Invalid recipient');
        }

        $exists = $this->db->select('id')->from('users')
            ->where(['id' => $recipient, 'role' => 'worker', 'is_active' => 1])
            ->limit(1)->get()->num_rows() > 0;
        if (!$exists) {
            return $this->_json(false, 'Worker not found or inactive');
        }

        $pid = (int)$this->input->post('project_id');
        $pTitle = '';
        if ($pid > 0) {
            if (method_exists($this->cpm, 'find_for_client')) {
                $pr = $this->cpm->find_for_client($uid, $pid);
            } else {
                $pr = $this->db->get_where('client_projects', ['id' => $pid, 'clientID' => $uid])->row();
            }
            if ($pr) {
                $pTitle = (string)$pr->title;
            } else {
                $pid = 0;
            } // invalid => ignore
        }

        $first = (string)($this->session->userdata('first_name') ?? '');
        $last  = (string)($this->session->userdata('last_name') ?? '');
        $clientName = trim($first . ' ' . $last);

        $title = 'Hire request';
        $bodyBase = $clientName ? ($clientName . ' wants to discuss hiring.') : 'A client wants to discuss hiring.';
        $body = $pid > 0 ? ($bodyBase . ' Project: “' . $pTitle . '”.') : $bodyBase;

        $link = site_url('message/start?to=' . $uid . ($pid > 0 ? ('&pid=' . $pid) : ''));

        $ok = $this->cp->add_notification($recipient, $uid, 'hire', $title, $body, $link);
        return $this->_json((bool)$ok, $ok ? 'Notified' : 'Failed', ['created' => (bool)$ok]);
    }


    public function payments()
    {
        if ($this->session->userdata('role') !== 'client') show_error('Unauthorized', 403);
        $this->load->model('Payment_model', 'pay');
        $uid = (int)$this->session->userdata('user_id');

        $data = [
            'page_title'   => 'Payments',
            'payments'     => $this->pay->list_for_client($uid, 50, 0),
            'spend_total'  => $this->pay->sum_for_client($uid),
        ];
        $this->load->view('payments_client', $data);
    }
    public function delete_doc()
    {
        if (!$this->session->userdata('user_id')) {
            $this->output->set_status_header(401);
            return $this->_json(false, 'Not authenticated');
        }

        $raw   = (string) $this->input->post('path', true);
        $field = trim((string) $this->input->post('field', true));

        if ($raw === '') {
            $this->output->set_status_header(400);
            return $this->_json(false, 'Missing path');
        }
        if (preg_match('#^https?://#i', $raw)) {
            $urlPath = parse_url($raw, PHP_URL_PATH);
            $base    = trim(parse_url(base_url(), PHP_URL_PATH), '/');
            $raw     = ltrim($base !== '' && strpos($urlPath, '/' . $base . '/') === 0
                ? substr($urlPath, strlen('/' . $base . '/'))
                : $urlPath, '/');
        }
        $rel = ltrim(str_replace('\\', '/', $raw), '/');
        if (!preg_match('#^uploads/#', $rel)) {
            $this->output->set_status_header(400);
            return $this->_json(false, 'Invalid file path');
        }
        $abs = FCPATH . $rel;
        if (is_file($abs)) @unlink($abs);

        $uid = (int) $this->session->userdata('user_id');
        $row = $this->db->select('certificates, id_image, business_permit')
            ->from('client_profile')
            ->where('clientID', $uid)
            ->get()->row();
        if (!$row) {
            $this->output->set_status_header(404);
            return $this->_json(false, 'Profile not found');
        }

        if ($field === 'id_image' || $field === 'business_permit') {
            $current = (string)($row->{$field} ?? '');
            $same    = ($current !== '' && ltrim($current, '/') === $rel);
            $this->db->where('clientID', $uid)->update('client_profile', [
                $field => $same ? null : $current
            ]);
        } else {
            $list = json_decode((string)$row->certificates, true);
            if (!is_array($list)) $list = [];
            $list = array_values(array_filter($list, function ($it) use ($rel) {
                $p = is_array($it) ? (string)($it['path'] ?? '') : (string)$it;
                return ltrim($p, '/') !== $rel;
            }));
            $this->db->where('clientID', $uid)->update('client_profile', [
                'certificates' => json_encode($list, JSON_UNESCAPED_SLASHES)
            ]);
        }

        $this->session->set_flashdata('success', 'Document deleted.');

        return $this->_json(true, 'Deleted', [
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash(),
            'flash'     => ['type' => 'success', 'text' => 'Document deleted.'],
        ]);
    }
}
