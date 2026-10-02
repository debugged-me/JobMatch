<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{

     private function _fix_image_orientation(string $fullPath): void
    {
        // Must exist and be a regular file
        if (!is_file($fullPath)) return;

        // Only meaningful for JPEGs with EXIF orientation
        $lower = strtolower($fullPath);
        if (!preg_match('/\.(jpe?g)$/i', $lower)) return;

        // EXIF extension might not be installed/enabled — fail gracefully
        if (!function_exists('exif_read_data')) return;

        $exif = @exif_read_data($fullPath);
        if (!$exif || empty($exif['Orientation'])) return;

        $orientation = (int)$exif['Orientation'];
        if ($orientation === 1) return; // already correct

        if (!function_exists('imagecreatefromjpeg') || !function_exists('imagerotate')) return;

        $img = @imagecreatefromjpeg($fullPath);
        if (!$img) return;

        $rotated = null;
        switch ($orientation) {
            case 3: // 180°
                $rotated = @imagerotate($img, 180, 0);
                break;
            case 6: // 90° CW
                $rotated = @imagerotate($img, -90, 0);
                break;
            case 8: // 90° CCW
                $rotated = @imagerotate($img, 90, 0);
                break;
            default:
                // Other EXIF orientations (mirror) are uncommon for camera phones; ignore safely
                break;
        }

        if ($rotated) {
            // Overwrite the file. Use quality ~85 to keep size reasonable.
            @imagejpeg($rotated, $fullPath, 85);
            @imagedestroy($rotated);
        }

        @imagedestroy($img);
    }
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url','form','text']);
        $this->load->library(['form_validation','session','upload']);
        $this->load->model('WorkerProfile_model','wp');

      // Profile::__construct()
if (!$this->session->userdata('logged_in')) {
    if ($this->input->is_ajax_request()) {
        $this->output
            ->set_status_header(401)
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok'=>false,'error'=>'auth']));
        exit; // IMPORTANT: stop execution for AJAX
    }
    redirect('auth/login');
}
    }
    public function edit()
    {
        if (!$this->session->userdata('logged_in')) {
            return redirect('auth/login');
        }

        $uid      = (int) $this->session->userdata('user_id');
        $data['p'] = $this->wp->get($uid);

        $this->load->model('User_model', 'um');
        $data['ps'] = $this->um->philsys_brief($uid);

        // normalize JSON fields we persist as text
        if (!empty($data['p']->exp) && is_string($data['p']->exp)) {
            $tmp = json_decode($data['p']->exp, true);
            if (is_array($tmp)) $data['p']->exp = $tmp;
        }
        if (!empty($data['p']->cert_files) && is_string($data['p']->cert_files)) {
            $tmp = json_decode($data['p']->cert_files, true);
            if (is_array($tmp)) $data['p']->cert_files = $tmp;
        }
        if (!empty($data['p']->tesda_certs) && is_string($data['p']->tesda_certs)) {
            $tmp = json_decode($data['p']->tesda_certs, true);
            if (is_array($tmp)) $data['p']->tesda_certs = $tmp;
        }

        // skill CSV default pulled from pivot
        $this->load->model('Skills_model', 'skills_model');
        $workerKey    = (string) $uid;
        $skill_titles = $this->skills_model->get_titles_by_worker($workerKey);
        $csv          = implode(', ', $skill_titles);

        if (!isset($data['p']->skills) || !is_string($data['p']->skills) || $data['p']->skills === '') {
            $data['p']->skills = $csv;
        }

        // rates for the view (if used)
        $data['skill_rates'] = $this->skills_model->get_worker_skill_rates($workerKey);

        // default 1 blank experience row
        if (empty($data['p']->exp) || !is_array($data['p']->exp)) {
            $data['p']->exp = [['role'=>'','employer'=>'','from'=>'','to'=>'','desc'=>'']];
        }

        // -------- Re-populate repeater data (when validation failed last request) --------
        $nc_post    = $this->session->flashdata('nc_post');
        $exp_post   = $this->session->flashdata('exp_post');
        $skills_post= $this->session->flashdata('skills_post');

        if (is_array($nc_post)) {
            // same structure the view already expects
            $data['p']->tesda_certs = $nc_post;
        }
        if (is_array($exp_post) && !empty($exp_post)) {
            $data['p']->exp = $exp_post;
        }
        if (is_string($skills_post) && $skills_post !== '') {
            $data['p']->skills = $skills_post;
        }
        // -------------------------------------------------------------------------------

        $data['page_title'] = 'Edit Worker Profile';
        $data['ncOptions'] = $this->db
            ->select('skillID AS id, Title AS text', false)
            ->from('skills')
            ->like('Title', 'NC')        // same as WHERE Title LIKE '%NC%'
            ->order_by('Title', 'ASC')
            ->get()
            ->result_array();

        $this->load->view('profile_edit', $data);
    }

    public function update()
    {
        if (!$this->session->userdata('logged_in')) {
            return redirect('auth/login');
        }

        $this->load->library(['form_validation','upload']);

        $uid        = (int) $this->session->userdata('user_id');
        $current    = $this->wp->get($uid);
        $payload    = [];
        $madeChange = false;
        $next = (string) $this->input->post('next', true) ?: 'dashboard';

        // Schema lives in database/migrations/001_schema_consolidation.sql —
        // never ALTER at request time.

        // ---- avatar-only path
        if ($this->input->post('__partial') === 'avatar') {
            $dir = FCPATH . 'uploads/avatars';
            if (!is_dir($dir)) @mkdir($dir, 0755, true);

            $config = [
                'upload_path'      => $dir,
                'allowed_types'    => 'jpg|jpeg|png|webp',
                'max_size'         => 2048,
                'file_ext_tolower' => TRUE,
                'remove_spaces'    => TRUE,
                'encrypt_name'     => TRUE,
                'detect_mime'      => TRUE,
            ];
            $this->upload->initialize($config);

            $is_ajax = (string)$this->input->post('ajax') === '1';

            if (!$this->upload->do_upload('avatar')) {
                $err = $this->upload->display_errors('', '');
                if ($is_ajax) {
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'ok'        => false,
                            'error'     => $err,
                            'csrf_name' => $this->security->get_csrf_token_name(),
                            'csrf_hash' => $this->security->get_csrf_hash(),
                        ]));
                }
                $this->session->set_flashdata('error', $err);
                return redirect('profile/edit');
            }

            $u    = $this->upload->data();
            $path = $u['full_path'];
            $ext  = $u['file_ext'];

            if (!function_exists('validate_uploaded_file_signature') || !validate_uploaded_file_signature($path, $ext)) {
                @unlink($path);
                if ($is_ajax) {
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'ok' => false,
                            'error' => 'Invalid or unsafe file.',
                            'csrf_name' => $this->security->get_csrf_token_name(),
                            'csrf_hash' => $this->security->get_csrf_hash(),
                        ]));
                }
                $this->session->set_flashdata('error', 'Invalid or unsafe file.');
                return redirect('profile/edit');
            }

            if (function_exists('safe_image_reencode')) {
                if (!safe_image_reencode($path, $ext)) {
                    @unlink($path);
                    if ($is_ajax) {
                        return $this->output
                            ->set_content_type('application/json')
                            ->set_output(json_encode([
                                'ok' => false,
                                'error' => 'Failed to sanitize image.',
                                'csrf_name' => $this->security->get_csrf_token_name(),
                                'csrf_hash' => $this->security->get_csrf_hash(),
                            ]));
                    }
                    $this->session->set_flashdata('error', 'Failed to sanitize image.');
                    return redirect('profile/edit');
                }
            }
$this->_fix_image_orientation($path);

            $avatarPath = 'uploads/avatars/' . $u['file_name'];
            $ok = $this->wp->update_fields($uid, ['avatar' => $avatarPath]);
            
            // Log the update attempt for debugging
            if (!$ok) {
                log_message('error', 'Avatar update failed for user '.$uid.' - path: '.$avatarPath);
            } else {
                log_message('info', 'Avatar updated for user '.$uid.' - path: '.$avatarPath);
            }

            if (!$ok) {
                if ($is_ajax) {
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'ok' => false,
                            'error' => 'Could not save avatar.',
                            'csrf_name' => $this->security->get_csrf_token_name(),
                            'csrf_hash' => $this->security->get_csrf_hash(),
                        ]));
                }
                $this->session->set_flashdata('error', 'Could not save avatar.');
                return redirect('profile/edit');
            }

            if ($is_ajax) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'ok'         => true,
                        'avatar_url' => base_url($avatarPath),
                        'csrf_name'  => $this->security->get_csrf_token_name(),
                        'csrf_hash'  => $this->security->get_csrf_hash(),
                    ]));
            }

            $this->session->set_flashdata('success', 'Photo uploaded.');
            $next = $this->input->post('next') ?: 'edit';
            return $next === 'dashboard' ? redirect('dashboard/worker') : redirect('profile/edit');
        }

        // ---- names (users table)
        $first = trim((string)$this->input->post('first_name', true));
        $last  = trim((string)$this->input->post('last_name',  true));
        if ($first !== '' || $last !== '') {
            $uData = [];
            if ($first !== '') $uData['first_name'] = $first;
            if ($last  !== '') $uData['last_name']  = $last;

            if ($this->db->field_exists('updated_at', 'users')) {
                $uData['updated_at'] = date('Y-m-d H:i:s');
            }
            $this->db->where('id', $uid)->update('users', $uData);
            $this->session->set_userdata($uData);
            $madeChange = true;
        }

        // ---- do we have profile details?
        $hasDetails =
        ($this->input->post('headline') !== null) ||
($this->input->post('bio') !== null) ||
            ($this->input->post('years_experience') !== null) ||
            ($this->input->post('skills') !== null) ||
            ($this->input->post('credentials') !== null) ||
            ($this->input->post('brgy') !== null) ||
            ($this->input->post('city') !== null) ||
            ($this->input->post('province') !== null) ||
            ($this->input->post('phoneNo') !== null) ||
            ($this->input->post('availability_days') !== null) ||
            ($this->input->post('availability_start') !== null) ||
            ($this->input->post('availability_end') !== null) ||
            ($this->input->post('expected_rate') !== null) ||
            ($this->input->post('rate_unit') !== null) ||
            ($this->input->post('rate_negotiable') !== null) ||
            ($this->input->post('education_level') !== null) ||
            ($this->input->post('course') !== null) ||
            ($this->input->post('school') !== null) ||
            ($this->input->post('year_graduated') !== null) ||
            ($this->input->post('tesda_qualification') !== null) ||
            ($this->input->post('tesda_cert_no') !== null) ||
            ($this->input->post('tesda_expiry') !== null) ||
            ($this->input->post('tesda_certs') !== null) ||
            (!empty($_FILES['cert_files']['name'][0])) ||
            ($this->input->post('exp') !== null) ||
            ($this->input->post('languages') !== null) ||
            ($this->input->post('portfolio_url') !== null) ||
            ($this->input->post('facebook_url') !== null);

        $skillsArr = [];

        if ($hasDetails) {
            // ---- validation
            $this->form_validation->set_rules('headline', 'Headline', 'trim|max_length[160]');
$this->form_validation->set_rules('bio', 'Bio', 'trim|max_length[600]');
            $this->form_validation->set_rules('years_experience', 'Years of Experience', 'integer|greater_than[-1]');
            $this->form_validation->set_rules('skills', 'Skills', 'trim|max_length[255]');
            $this->form_validation->set_rules('credentials', 'Credentials', 'trim');
            $this->form_validation->set_rules('brgy', 'Barangay', 'trim');
            $this->form_validation->set_rules('city', 'City', 'trim');
            $this->form_validation->set_rules('province', 'Province', 'trim');
            $this->form_validation->set_rules('phoneNo', 'Phone', 'trim');
            $this->form_validation->set_rules('expected_rate', 'Expected Rate', 'trim|numeric');
            $this->form_validation->set_rules('rate_unit', 'Rate Unit', 'trim|in_list[hour,day,project]');
            $this->form_validation->set_rules('education_level', 'Highest Education', 'trim|max_length[50]');
            $this->form_validation->set_rules('course', 'Course', 'trim|max_length[120]');
            $this->form_validation->set_rules('school', 'School', 'trim|max_length[120]');
            $this->form_validation->set_rules('year_graduated', 'Year Graduated', 'trim|integer');
            $this->form_validation->set_rules('tesda_qualification', 'TESDA Qualification', 'trim|max_length[150]');

            if (!$this->form_validation->run()) {
                // Build a $p object that merges the DB profile + posted values
                $p = clone $current;

                // Simple text/number fields
                $p->headline = $this->input->post('headline', true) ?? ($p->headline ?? '');
$p->bio      = $this->input->post('bio', true)      ?? ($p->bio ?? '');
                $p->first_name        = $this->input->post('first_name', true) ?: ($p->first_name ?? '');
                $p->last_name         = $this->input->post('last_name',  true) ?: ($p->last_name  ?? '');
                $p->years_experience  = $this->input->post('years_experience', true) ?: ($p->years_experience ?? 0);
                $p->phoneNo           = $this->input->post('phoneNo',    true) ?: ($p->phoneNo    ?? '');
                $p->credentials       = $this->input->post('credentials',true) ?: ($p->credentials?? '');
                $p->education_level   = $this->input->post('education_level', true) ?: ($p->education_level ?? '');
                $p->course            = $this->input->post('course', true) ?: ($p->course ?? '');
                $p->school            = $this->input->post('school', true) ?: ($p->school ?? '');
                $p->year_graduated    = $this->input->post('year_graduated', true) ?: ($p->year_graduated ?? '');

                // Address cascade
                $p->province          = $this->input->post('province', true) ?: ($p->province ?? '');
                $p->city              = $this->input->post('city',     true) ?: ($p->city     ?? '');
                $p->brgy              = $this->input->post('brgy',     true) ?: ($p->brgy     ?? '');

                // Availability (checkbox array → CSV)
                $p->availability_days = implode(',', (array)($this->input->post('availability_days') ?: []));
                $p->availability_start= $this->input->post('availability_start', true) ?: ($p->availability_start ?? null);
                $p->availability_end  = $this->input->post('availability_end',   true) ?: ($p->availability_end   ?? null);

                // Skills
                $p->skills            = $this->input->post('skills', true) ?: ($p->skills ?? '');

                // Experience repeater
                $expIn                = $this->input->post('exp');
                if (is_array($expIn)) $p->exp = $expIn;

                // TESDA NC repeater
                $ncIn                 = $this->input->post('tesda_certs');
                if (is_array($ncIn))  $p->tesda_certs = $ncIn;

                // Links
                $p->portfolio_url     = $this->input->post('portfolio_url', true) ?: ($p->portfolio_url ?? '');
                $p->facebook_url      = $this->input->post('facebook_url',  true) ?: ($p->facebook_url  ?? '');

                $data = [
                    'page_title' => 'Edit Worker Profile',
                    'p'          => $p,
                ];

                // If your view needs these:
                $this->load->model('Skills_model', 'skills_model');
                $workerKey            = (string) $uid;
                $data['skill_rates']  = $this->skills_model->get_worker_skill_rates($workerKey);

                $this->session->set_flashdata('error', validation_errors('', ''));
                return $this->load->view('profile_edit', $data); // <- re-render, no redirect
            }
            $skillsRaw = (string) $this->input->post('skills', true);
            $skillsArr = array_filter(array_map(function ($s) {
                return preg_replace('/[^A-Za-z0-9 \-\+\.#]/', '', trim($s));
            }, explode(',', $skillsRaw)));
            $skillsArr = array_slice($skillsArr, 0, 30);
            $skills    = implode(', ', $skillsArr);

            $ratesIn = $this->input->post('skill_rates');
            $rates   = [];
            if (is_array($ratesIn)) {
                foreach ($ratesIn as $title => $rm) {
                    $title = preg_replace('/[^A-Za-z0-9 \-\+\.#]/', '', trim($title));
                    if ($title === '') continue;
                    $min = isset($rm['min']) && $rm['min'] !== '' ? (float)$rm['min'] : null;
                    $max = isset($rm['max']) && $rm['max'] !== '' ? (float)$rm['max'] : null;
                    if ($min !== null || $max !== null) {
                        $rates[$title] = ['min' => $min ?? 0.00, 'max' => $max ?? 0.00];
                    }
                }
            }

            $langRaw = (string) $this->input->post('languages', true);
            $langArr = array_filter(array_map(function ($s) {
                return preg_replace('/[^A-Za-z0-9 \-\+\.#]/', '', trim($s));
            }, explode(',', $langRaw)));
            $languages = implode(', ', array_slice($langArr, 0, 10));
            $daysArr = (array) ($this->input->post('availability_days') ?: []);
            $daysArr = array_values(array_unique(array_map('trim', $daysArr)));
            $daysStr = implode(',', $daysArr);
            $start   = $this->input->post('availability_start', true) ?: null;
            $end     = $this->input->post('availability_end', true)   ?: null;

            if ($start && $end && $start >= $end) {
                $p = clone $current;
                $fields = [
                    'first_name','last_name','years_experience','phoneNo','credentials',
                    'education_level','course','school','year_graduated',
                    'province','city','brgy','portfolio_url','facebook_url','skills'
                ];
                foreach ($fields as $f) { $p->$f = $this->input->post($f, true) ?? ($p->$f ?? ''); }

                $p->availability_days = implode(',', (array)($this->input->post('availability_days') ?: []));
                $p->availability_start= $start;
                $p->availability_end  = $end;

                $expIn = $this->input->post('exp');            if (is_array($expIn)) $p->exp = $expIn;
                $ncIn  = $this->input->post('tesda_certs');    if (is_array($ncIn))  $p->tesda_certs = $ncIn;

                $data = [
                    'page_title' => 'Edit Worker Profile',
                    'p'          => $p,
                ];
                $this->load->model('Skills_model', 'skills_model');
                $workerKey = (string) $uid;
                $data['skill_rates'] = $this->skills_model->get_worker_skill_rates($workerKey);

                $this->session->set_flashdata('error', 'End time must be later than start time.');
                return $this->load->view('profile_edit', $data); // no redirect
            }
            $newCerts = [];
            if (!empty($_FILES['cert_files']['name'][0])) {
                $dir = FCPATH . 'uploads/certificates';
                if (!is_dir($dir)) @mkdir($dir, 0755, true);

                $files  = $_FILES['cert_files'];
                $titles = (array) $this->input->post('cert_titles');

                $MAX_PER_REQUEST = 12;
                $count = min(count($files['name']), $MAX_PER_REQUEST);

                for ($i = 0; $i < $count; $i++) {
                    if (empty($files['name'][$i])) continue;

                    if (!empty($files['error'][$i]) && $files['error'][$i] !== UPLOAD_ERR_OK) {
                        $this->session->set_flashdata('error', 'Upload failed for '.$files['name'][$i].' (code '.$files['error'][$i].').');
                        return redirect('profile/edit');
                    }

                    $_FILES['one_cert'] = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];

                    $cfg = [
                        'upload_path'      => $dir,
                        'allowed_types'    => 'pdf|jpg|jpeg|png|webp',
                        'max_size'         => 2048,
                        'file_ext_tolower' => TRUE,
                        'remove_spaces'    => TRUE,
                        'encrypt_name'     => TRUE,
                        'detect_mime'      => TRUE,
                    ];
                    $this->upload->initialize($cfg);

                    if ($this->upload->do_upload('one_cert')) {
                        $u    = $this->upload->data();
                        $path = $u['full_path'];
                        $ext  = $u['file_ext'];

                        if (!function_exists('validate_uploaded_file_signature') || !validate_uploaded_file_signature($path, $ext)) {
                            @unlink($path);
                            $this->session->set_flashdata('error', 'Invalid or unsafe file.');
                            return redirect('profile/edit');
                        }

                        $imgExts = ['.jpg','.jpeg','.png','.gif','.webp'];
                        if (in_array(strtolower($ext), $imgExts, true) && function_exists('safe_image_reencode')) {
                            if (!safe_image_reencode($path, $ext)) {
                                @unlink($path);
                                $this->session->set_flashdata('error', 'Failed to sanitize image.');
                                return redirect('profile/edit');
                            }
                        }
                        $relPath = 'uploads/certificates/'.$u['file_name'];
                        $given   = isset($titles[$i]) ? trim((string)$titles[$i]) : '';
                        $fallbackTitle = pathinfo($files['name'][$i], PATHINFO_FILENAME);
                        $title   = $given !== '' ? $given : $fallbackTitle;

                        $newCerts[] = ['path' => $relPath, 'title' => $title];
                    } else {
                        $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                        return redirect('profile/edit');
                    }
                }
            }
            $expIn   = $this->input->post('exp');
            $expRows = [];
            if (is_array($expIn)) {
                foreach ($expIn as $row) {
                    $r = [
                        'role'     => trim((string)($row['role'] ?? '')),
                        'employer' => trim((string)($row['employer'] ?? '')),
                        'from'     => trim((string)($row['from'] ?? '')),
                        'to'       => trim((string)($row['to'] ?? '')),
                        'desc'     => trim((string)($row['desc'] ?? '')),
                    ];
                    if (implode('', $r) !== '') $expRows[] = $r;
                }
            }
            $ncIn   = $this->input->post('tesda_certs');
            $ncRows = [];
            if (is_array($ncIn)) {
                foreach ($ncIn as $row) {
                    $qualification = trim((string)($row['qualification'] ?? ''));
                    $number        = trim((string)($row['number'] ?? ''));
                    $expiry        = trim((string)($row['expiry'] ?? ''));
                    if ($qualification !== '' || $number !== '' || $expiry !== '') {
                        $qualification = mb_substr($qualification, 0, 150);
                        $number        = mb_substr($number,        0, 100);
                        $expiry        = $expiry ? substr($expiry, 0, 10) : null;
                        $ncRows[] = [
                            'qualification' => $qualification,
                            'number'        => $number,
                            'expiry'        => $expiry,
                        ];
                    }
                }
            }
            if (empty($ncRows)) {
                $ncListJson = (string) $this->input->post('nc_list', true);
                if ($ncListJson !== '') {
                    $picked = json_decode($ncListJson, true);
                    if (is_array($picked) && !empty($picked)) {
                        $ids = array_values(array_unique(array_map('intval', $picked)));
                        if (!empty($ids)) {
                            $rows = $this->db->select('skillID AS id, Title')
                                             ->from('skills')
                                             ->where_in('skillID', $ids)
                                             ->order_by('Title','ASC')
                                             ->get()->result_array();
                            foreach ($rows as $r) {
                                $q = trim((string)$r['Title']);
                                if ($q !== '') {
                                    $ncRows[] = ['qualification' => $q, 'number' => '', 'expiry' => null];
                                }
                            }
                        }
                    }
                }
            }

            $ncUploads = []; 
            if (!empty($_FILES['nc_files']['name']) && is_array($_FILES['nc_files']['name'])) {

                $dir = FCPATH . 'uploads/certificates';
                if (!is_dir($dir)) @mkdir($dir, 0755, true);

                $MAX = min(count($_FILES['nc_files']['name']), 30);
                $docTitles = $this->input->post('nc_doc_titles', true); 

                for ($i=0; $i<$MAX; $i++) {
                    $fname = $_FILES['nc_files']['name'][$i];
                    if ($fname === '' || (!empty($_FILES['nc_files']['error'][$i]) && $_FILES['nc_files']['error'][$i] !== UPLOAD_ERR_OK)) {
                        continue;
                    }
                    $_FILES['one_nc_file'] = [
                        'name'     => $_FILES['nc_files']['name'][$i],
                        'type'     => $_FILES['nc_files']['type'][$i],
                        'tmp_name' => $_FILES['nc_files']['tmp_name'][$i],
                        'error'    => $_FILES['nc_files']['error'][$i],
                        'size'     => $_FILES['nc_files']['size'][$i],
                    ];

                    $cfg = [
                        'upload_path'      => $dir,
                        'allowed_types'    => 'pdf|jpg|jpeg|png|webp',
                        'max_size'         => 2048, 
                        'file_ext_tolower' => TRUE,
                        'remove_spaces'    => TRUE,
                        'encrypt_name'     => TRUE,
                        'detect_mime'      => TRUE,
                    ];
                    $this->upload->initialize($cfg);

                    if (!$this->upload->do_upload('one_nc_file')) {
                        log_message('error', 'NC file upload failed: '.$this->upload->display_errors('', ''));
                        continue;
                    }

                    $u    = $this->upload->data();
                    $path = $u['full_path'];
                    $ext  = $u['file_ext'];

                    if (!function_exists('validate_uploaded_file_signature') || !validate_uploaded_file_signature($path, $ext)) {
                        @unlink($path);
                        log_message('error','Invalid/unsafe NC file dropped: '.$u['file_name']);
                        continue;
                    }

                    $imgExts = ['.jpg','.jpeg','.png','.gif','.webp'];
                    if (in_array(strtolower($ext), $imgExts, true) && function_exists('safe_image_reencode')) {
                        if (!safe_image_reencode($path, $ext)) {
                            @unlink($path);
                            log_message('error','Failed to sanitize NC image: '.$u['file_name']);
                            continue;
                        }
                    }

                    if (is_array($docTitles) && isset($docTitles[$i]) && trim($docTitles[$i]) !== '') {
                        $desiredBase = trim($docTitles[$i]);
                    } elseif (isset($ncRows[$i]['qualification']) && trim((string)$ncRows[$i]['qualification']) !== '') {
                        $desiredBase = trim((string)$ncRows[$i]['qualification']);
                    } else {
                        $desiredBase = pathinfo($fname, PATHINFO_FILENAME);
                    }

                    $safeBase = strtolower(preg_replace('/[^A-Za-z0-9\-_]+/', '-', $desiredBase));
                    $safeBase = trim($safeBase, '-_');
                    if ($safeBase === '') $safeBase = 'document';
                    $newFile = $safeBase . $ext;
                    $target  = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newFile;
                    $ctr     = 1;
                    while (file_exists($target)) {
                        $newFile = $safeBase . '-' . $ctr . $ext;
                        $target  = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newFile;
                        $ctr++;
                    }
                    @rename($path, $target);

                    $relPath = 'uploads/certificates/'.$newFile;
                    $title = '';
                    if (is_array($docTitles) && isset($docTitles[$i]) && trim($docTitles[$i]) !== '') {
                        $title = trim($docTitles[$i]);
                    } elseif (isset($ncRows[$i]['qualification']) && trim((string)$ncRows[$i]['qualification']) !== '') {
                        $title = trim((string)$ncRows[$i]['qualification']);
                    } else {
                        $title = pathinfo($newFile, PATHINFO_FILENAME);
                    }

                    $ncUploads[] = ['path' => $relPath, 'title' => $title];
                }
            }

            $mirroredFirstNC = false;
            if (!empty($ncRows)) {
                $payload['tesda_certs'] = json_encode(array_values($ncRows));
                $first = $ncRows[0];
                $payload['tesda_qualification'] = $first['qualification'] ?: null;
                $payload['tesda_cert_no']       = $first['number'] ?: null;
                $payload['tesda_expiry']        = $first['expiry'] ?: null;
                $mirroredFirstNC = true;
            }

            if (!empty($ncUploads)) {
                $existing = [];
                if (!empty($current->cert_files)) {
                    $tmp = json_decode($current->cert_files, true);
                    if (is_array($tmp)) $existing = $tmp;
                }
                $map = [];
                foreach ((array)$existing as $it) {
                    if (is_array($it)) {
                        $p = (string)($it['path'] ?? ''); if ($p==='') continue;
                        $t = trim((string)($it['title'] ?? '')); if ($t==='') $t = pathinfo($p, PATHINFO_FILENAME);
                        $map[$p] = ['path'=>$p,'title'=>$t];
                    } elseif (is_string($it) && $it!=='') {
                        $p = $it; $t = pathinfo($p, PATHINFO_FILENAME);
                        $map[$p] = ['path'=>$p,'title'=>$t];
                    }
                }
                foreach ($ncUploads as $it) {
                    $p = $it['path']; if ($p==='') continue;
                    $t = trim($it['title']) ?: pathinfo($p, PATHINFO_FILENAME);
                    $map[$p] = ['path'=>$p,'title'=>$t];
                }
                $all = array_values($map);
                if (count($all) > 30) $all = array_slice($all, 0, 30);
                $payload['cert_files'] = json_encode($all, JSON_UNESCAPED_SLASHES);
            }
            $headline = trim((string)$this->input->post('headline', true));
$bio      = trim((string)$this->input->post('bio', true));
$payload['headline'] = ($headline !== '') ? $headline : null;
$payload['bio']      = ($bio      !== '') ? $bio      : null;
            $payload['years_experience']    = (int) $this->input->post('years_experience');
            $payload['skills']              = $skills;
            $payload['credentials']         = $this->input->post('credentials', true);
            $payload['brgy']                = $this->input->post('brgy', true);
            $payload['city']                = $this->input->post('city', true);
            $payload['province']            = $this->input->post('province', true);
            $payload['phoneNo']             = $this->input->post('phoneNo', true);
            $payload['availability_days']   = $daysStr ?: null;
            $payload['availability_start']  = $start ?: null;
            $payload['availability_end']    = $end ?: null;
            $payload['expected_rate']       = $this->input->post('expected_rate', true) !== '' ? (float)$this->input->post('expected_rate') : null;
            $payload['rate_unit']           = $this->input->post('rate_unit', true) ?: null;
            $payload['rate_negotiable']     = $this->input->post('rate_negotiable') ? 1 : 0;
            $payload['education_level']     = $this->input->post('education_level', true) ?: null;
            $payload['course']              = $this->input->post('course', true) ?: null;
            $payload['school']              = $this->input->post('school', true) ?: null;
            $payload['year_graduated']      = $this->input->post('year_graduated', true) ?: null;

            if (!$mirroredFirstNC) {
                $payload['tesda_qualification'] = $this->input->post('tesda_qualification', true) ?: null;
                $payload['tesda_cert_no']       = $this->input->post('tesda_cert_no', true) ?: null;
                $payload['tesda_expiry']        = $this->input->post('tesda_expiry', true) ?: null;
            }
            if (!empty($newCerts)) {
                $existing = [];
                if (!empty($current->cert_files)) {
                    $tmp = json_decode($current->cert_files, true);
                    if (is_array($tmp)) $existing = $tmp;
                }
                $map = [];
                foreach ((array)$existing as $it) {
                    if (is_string($it)) {
                        $p = $it;
                        if ($p === '') continue;
                        $t = pathinfo($p, PATHINFO_FILENAME);
                        $map[$p] = ['path' => $p, 'title' => $t];
                    } elseif (is_array($it)) {
                        $p = (string)($it['path'] ?? '');
                        if ($p === '') continue;
                        $t = trim((string)($it['title'] ?? ''));
                        if ($t === '') $t = pathinfo($p, PATHINFO_FILENAME);
                        $map[$p] = ['path' => $p, 'title' => $t];
                    }
                }
                foreach ($newCerts as $it) {
                    $p = (string)$it['path'];
                    if ($p === '') continue;
                    $t = trim((string)$it['title']);
                    if ($t === '') $t = pathinfo($p, PATHINFO_FILENAME);
                    $map[$p] = ['path' => $p, 'title' => $t];
                }
                $all = array_values($map);
                if (count($all) > 30) $all = array_slice($all, 0, 30);
                $payload['cert_files'] = json_encode($all);
            }

            if (!empty($expRows)) {
                $payload['exp'] = json_encode($expRows);
            }
            $payload['languages'] = $languages ?: null;

            $portfolio = trim($this->input->post('portfolio_url', true));
            $facebook  = trim($this->input->post('facebook_url', true));
            $payload['portfolio_url'] = ($portfolio !== '') ? $portfolio : null;
            $payload['facebook_url']  = ($facebook  !== '') ? $facebook  : null;
            $this->load->model('Skills_model', 'skills_model');
            $workerKey = (string) ($current->workerID ?? $uid);
            $syncOk = $this->skills_model->replace_worker_skills($workerKey, $skillsArr, $rates ?? []);
            if (!$syncOk) {
                log_message('error', 'Skills sync failed for workerID='.$workerKey);
            }
        }

        if (!empty($payload)) {
         $nullableKeep = ['headline','bio']; 
foreach ($payload as $k => $v) {
    if (in_array($k, $nullableKeep, true)) {
        continue;
    }
    if ($v === null || (is_string($v) && trim($v) === '')) {
        unset($payload[$k]);
    }
}

        }

        if (!empty($payload)) {
            $ok = $this->wp->upsert($uid, $payload);
            if (!$ok) {
                $this->session->set_flashdata('error', 'Could not save profile. Please try again.');
                return redirect('profile/edit');
            }
            $madeChange = true;
        }

        $this->session->set_flashdata('success', $madeChange ? 'Profile saved.' : 'Nothing to update.');
        $updated = $this->wp->get($uid);
        $role    = $this->session->userdata('role') ?: 'worker';
        $target  = ($role === 'admin') ? 'dashboard/admin'
                  : (($role === 'worker') ? 'dashboard/worker' : 'dashboard/user');

        if ($role === 'worker') {
            if ($next === 'dashboard') {
                return redirect($target);
            }
            if (method_exists($this->wp, 'is_complete') && method_exists($this->wp, 'completion')) {
                if ($this->wp->is_complete($updated)) {
                    return redirect($target);
                }
                $c = $this->wp->completion($updated);
                $this->session->set_flashdata('info', 'Saved, but still missing: '.implode(', ', $c['missing']));
                return redirect('profile/edit');
            }

         $isComplete =
    !empty($updated->avatar) &&
    !empty($updated->years_experience) &&
    !empty($updated->brgy) &&
    !empty($updated->city) &&
    !empty($updated->province) &&
    !empty($updated->phoneNo) &&
    !empty($updated->skills) &&
    !empty($updated->credentials);

            if ($isComplete) {
                return redirect($target);
            } else {
                $this->session->set_flashdata('info', 'Saved, but profile is still incomplete. Please fill out all required fields.');
                return redirect('profile/edit');
            }
        }

        return redirect($target);
    }

   public function worker($user_id = 0)
{
    if (!$this->session->userdata('logged_in')) {
        return redirect('auth/login');
    }

    $user_id = (int)$user_id;
    if ($user_id <= 0) return show_404();
    $w = $this->wp->get($user_id);
    if (!$w || (string)$w->role !== 'worker') return show_404();

    $rates = $this->db->select('s.Title AS title, ws.minRate AS min, ws.maxRate AS max')
                      ->from('worker_skills ws')
                      ->join('skills s','s.skillID = ws.skillsID','left')
                      ->where('ws.workerID', $user_id)
                      ->where('(ws.is_active = 1 OR ws.is_active IS NULL)', null, false)
                      ->order_by('s.Title','ASC')
                      ->get()->result_array();

    $aggMin = null; $aggMax = null;
    foreach ($rates as $r) {
        if ($r['min'] !== null && $r['min'] !== '') $aggMin = ($aggMin === null) ? (float)$r['min'] : min($aggMin, (float)$r['min']);
        if ($r['max'] !== null && $r['max'] !== '') $aggMax = ($aggMax === null) ? (float)$r['max'] : max($aggMax, (float)$r['max']);
    }

    $items = $this->wp->list_portfolio($user_id, false);
// --- decode cert_files (normalize to ['path'=>..., 'name'=>...])
$decode_files = function($json) {
    if (!is_string($json) || $json === '') return [];
    $arr = json_decode($json, true);
    if (!is_array($arr)) return [];
    $out = [];
    foreach ($arr as $it) {
        if (is_string($it) && trim($it) !== '') {
            // plain string path
            $out[] = ['path' => trim($it), 'name' => ''];
        } elseif (is_array($it) && !empty($it['path'])) {
            // object with path and maybe a name/title
            $out[] = [
                'path' => trim((string)$it['path']),
                'name' => trim((string)($it['name'] ?? $it['title'] ?? $it['doc_name'] ?? '')),
            ];
        }
    }
    return $out;
};

// pull from worker + fallback table
$certs = $decode_files($w->cert_files ?? null);
if (empty($certs)) {
    $row = $this->db->select('cert_files')
                    ->from('worker_profile')
                    ->where('workerID', $user_id)
                    ->get()->row();
    if ($row && isset($row->cert_files)) {
        $certs = $decode_files($row->cert_files);
    }
}

// --- helper to normalize paths so keys match even with/without domain/base path
$normalize = function($raw) {
    $p = trim((string)$raw);
    if ($p === '') return '';
    // strip domain if a full URL was saved
    $host = rtrim(parse_url(base_url(), PHP_URL_HOST) ?: '', '/');
    if (stripos($p, 'http') === 0) {
        $urlPath = parse_url($p, PHP_URL_PATH);
        if ($urlPath !== null) $p = $urlPath;
    }
    // remove the app base path prefix (e.g. /myapp/)
    $basePath = trim(parse_url(base_url(), PHP_URL_PATH) ?: '', '/');
    $p = ltrim($p, '/');
    if ($basePath !== '' && stripos($p, $basePath.'/') === 0) {
        $p = substr($p, strlen($basePath) + 1);
    }
    // normalize slashes
    return ltrim(str_replace('\\','/',$p), '/');
};

// Build a map by path; prefer a non-empty name when available.
$byPath = [];
// seed from cert_files (already normalized to ['path','name'])
foreach ($certs as $c) {
    $p = $normalize($c['path'] ?? '');
    if ($p === '') continue;
    $n = trim($c['name'] ?? '');
    if (!isset($byPath[$p])) {
        $byPath[$p] = ['path' => $p, 'name' => $n];
    } elseif ($byPath[$p]['name'] === '' && $n !== '') {
        $byPath[$p]['name'] = $n;
    }
}

// merge Saved Documents (objects OR arrays, and broader name fields)
$this->load->model('DocumentsModel','docs');
$docs = $this->docs->list_by_user($user_id);
foreach ($docs as $d) {
    // Support both object and array payloads
    $get = function($k) use ($d) {
        if (is_array($d)) return $d[$k] ?? null;
        if (is_object($d)) return $d->$k ?? null;
        return null;
    };

    $p  = $normalize($get('file_path') ?? $get('path') ?? $get('filepath') ?? $get('file') ?? '');
    if ($p === '') continue;

    // Try multiple possible columns for the human name
    $n = trim((string)(
        $get('document') ??
        $get('document_name') ??
        $get('doc_name') ??
        $get('label') ??
        $get('title') ??
        $get('name') ??
        ''
    ));

    if (!isset($byPath[$p])) {
        $byPath[$p] = ['path' => $p, 'name' => $n];
    } elseif ($byPath[$p]['name'] === '' && $n !== '') {
        $byPath[$p]['name'] = $n; // upgrade empty -> real name
    }
}

// flatten back to array
$certs = array_values($byPath);



    $reviews         = $this->wp->reviews_summary($user_id);
    $latest_reviews  = $this->wp->latest_reviews($user_id, 5);

    $viewer_role    = (string)($this->session->userdata('role') ?? '');
    $viewer_profile = null;
    if ($viewer_role === 'client') {
        $this->load->model('ClientProfile_model','cp');
        $viewer_profile = $this->cp->get((int)$this->session->userdata('user_id'));
    } elseif ($viewer_role === 'worker') {
        $viewer_profile = $this->wp->get((int)$this->session->userdata('user_id'));
    }

   $this->load->model('Experience_model','exp');
    $experiences = $this->exp->get_by_user((int)$user_id); // returns array of rows

    $data = [
        'page_title'     => 'Worker Profile',
        'w'              => $w,
        'skill_rates'    => $rates,
        'aggMin'         => $aggMin,
        'aggMax'         => $aggMax,
        'items'          => $items,
        'certs'          => $certs,
        
        'reviews'        => $reviews,
        'latest_reviews' => $latest_reviews,
        'profile'        => $viewer_profile,

        // ⬇️ IMPORTANT
        'experiences'    => is_array($experiences) ? $experiences : [],
    ];

    $this->load->view('profile_worker', $data);
}
  

    public function client($user_id = 0)
    {
        if (!$this->session->userdata('logged_in')) {
            return redirect('auth/login');
        }

        $user_id = (int)$user_id;
        if ($user_id <= 0) return show_404();

        $this->load->model('ClientProfile_model','cp');
        $this->load->model('ClientProjects_model','cpm');

        $c = $this->cp->get($user_id);
        if (!$c) return show_404();

        $projects = $this->db->order_by('created_at','DESC')
                             ->get_where('client_projects', [
                                 'clientID'   => $user_id,
                                 'status'     => 'active',
                                 'visibility' => 'public'
                             ])->result();

        $viewer_role    = (string)($this->session->userdata('role') ?? '');
        $viewer_profile = null;
        if ($viewer_role === 'client') {
            $viewer_profile = $this->cp->get((int)$this->session->userdata('user_id'));
        } else {
            $viewer_profile = $this->wp->get((int)$this->session->userdata('user_id'));
        }

        $data = [
            'page_title' => 'Client Profile',
            'c'          => $c,
            'projects'   => $projects,
            'profile'    => $viewer_profile,
        ];
        $this->load->view('profile_client', $data);
    }

    public function delete_doc()
    {
        try {
            if (!$this->session->userdata('user_id')) {
                return $this->_json(['ok' => false, 'msg' => 'Not authenticated'], 401);
            }

            $raw = (string) $this->input->post('path', true);
            if ($raw === '') {
                return $this->_json(['ok' => false, 'msg' => 'Missing path'], 400);
            }

            if (preg_match('#^https?://#i', $raw)) {
                $urlPath = parse_url($raw, PHP_URL_PATH);
                $base    = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                $raw     = ltrim($base !== '' && strpos($urlPath, '/'.$base.'/') === 0
                            ? substr($urlPath, strlen('/'.$base.'/'))
                            : $urlPath, '/');
            }
            $rel = ltrim(str_replace('\\','/',$raw), '/');
            if (!preg_match('#^uploads/#', $rel) || strpos($rel, '..') !== false) {
                return $this->_json(['ok' => false, 'msg' => 'Invalid file path'], 400);
            }

            // Canonicalize: resolved path must stay inside the real uploads dir.
            $uploadsRoot = realpath(FCPATH . 'uploads');
            $abs         = realpath(FCPATH . $rel);
            if (!$uploadsRoot || $abs === false || strpos($abs, $uploadsRoot . DIRECTORY_SEPARATOR) !== 0) {
                return $this->_json(['ok' => false, 'msg' => 'Invalid file path'], 400);
            }

            $uid = (int) $this->session->userdata('user_id');

            $this->db->db_debug = false; 
            $row = $this->db->select('cert_files')
                            ->from('worker_profile')
                            ->where('workerID', $uid)
                            ->get()->row();

            // Ownership check: only unlink files referenced by this worker's profile.
            $owns = false;
            if ($row && isset($row->cert_files)) {
                $ownList = json_decode((string)$row->cert_files, true);
                if (is_array($ownList)) {
                    foreach ($ownList as $it) {
                        $p = is_array($it) ? (string)($it['path'] ?? '') : (string)$it;
                        if (ltrim($p, '/') === $rel) { $owns = true; break; }
                    }
                }
            }
            if (!$owns) {
                return $this->_json(['ok' => false, 'msg' => 'File does not belong to your profile'], 403);
            }

            if (is_file($abs)) @unlink($abs);

            if ($row && isset($row->cert_files)) {
                $list = json_decode((string)$row->cert_files, true);
                if (!is_array($list)) { $list = []; }

                $list = array_values(array_filter($list, function($it) use ($rel) {
                    $p = is_array($it) ? (string)($it['path'] ?? '') : (string)$it;
                    return ltrim($p, '/') !== $rel;
                }));

                $ok = $this->db->where('workerID', $uid)
                               ->update('worker_profile', [
                                   'cert_files' => json_encode($list, JSON_UNESCAPED_SLASHES)
                               ]);

                if (method_exists($this->db, 'error')) {
                    $err = $this->db->error();
                    if (!$ok || !empty($err['code'])) {
                        log_message('error', 'delete_doc update failed: '.$err['message']);
                        return $this->_json(['ok' => false, 'msg' => 'Database error while updating list'], 500);
                    }
                }
            }

            return $this->_json([
                'ok'        => true,
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'delete_doc fatal: '.$e->getMessage().' @ '.$e->getFile().':'.$e->getLine());
            return $this->_json(['ok' => false, 'msg' => 'Server error: '.$e->getMessage()], 500);
        }
    }

    private function _json($payload, $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json','utf-8')
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }
    
public function documents_json()
{
    // ✅ load the correct model
    $this->load->model('DocumentsModel', 'docs');

    $user_id = (int) $this->session->userdata('user_id');
    $rows = $this->docs->list_by_user($user_id);

    // optional but nice: set JSON content-type
    $this->output->set_content_type('application/json')
                 ->set_output(json_encode(['data' => $rows]));
}


public function experience_json()
{
    if (!$this->session->userdata('logged_in')) {
        return $this->_json(['ok'=>false,'error'=>'auth'], 401);
    }

    $this->load->model('Experience_model','exp');
    $user_id = (int)$this->session->userdata('user_id');

    $rows = $this->exp->get_by_user($user_id);
    // DataTables expects an array under "data"
    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['data'=>$rows]));
}

public function save_experience()
{
    if (!$this->session->userdata('logged_in')) {
        return $this->_json(['ok'=>false,'error'=>'auth'], 401);
    }
    if (!$this->input->is_ajax_request()) {
        return $this->_json(['ok'=>false,'error'=>'bad_request'], 400);
    }

    $this->output->set_content_type('application/json');

    $this->load->model('Experience_model','exp');
    $user_id   = (int)$this->session->userdata('user_id');

    $id        = (int)$this->input->post('id');
    $role      = trim((string)$this->input->post('role'));
    $employer  = trim((string)$this->input->post('employer'));
    $from      = trim((string)$this->input->post('from')); // YYYY or YYYY-MM
    $to        = trim((string)$this->input->post('to'));   // YYYY or YYYY-MM
    $present   = $this->input->post('to_present') ? 1 : 0;
    $desc      = trim((string)$this->input->post('desc'));

    if ($role === '' && $employer === '' && $from === '' && $to === '' && $desc === '') {
        return $this->output->set_status_header(422)
            ->set_output(json_encode(['ok'=>false,'error'=>'Nothing to save']));
    }

    $from = $this->_ym_normalize($from); // 'YYYY' or 'YYYY-MM' only; leave original if invalid
    $to   = $present ? '' : $this->_ym_normalize($to);

    $payload = [
        'user_id'    => $user_id,
        'role'       => mb_substr($role, 0, 150),
        'employer'   => mb_substr($employer, 0, 180),
        '`from`'     => $from,     // backticks avoid reserved-word issues
        '`to`'       => $to,
        'to_present' => $present,
        'desc'       => $desc,
    ];

    if ($id > 0) {
        $ok = $this->exp->update($id, $user_id, $payload);
        return $this->output->set_output(json_encode($ok ? ['ok'=>true,'mode'=>'updated'] : ['ok'=>false,'error'=>'DB error']));
    } else {
        $new_id = $this->exp->insert($payload);
        return $this->output->set_output(json_encode($new_id ? ['ok'=>true,'mode'=>'created','id'=>$new_id] : ['ok'=>false,'error'=>'DB error']));
    }
}

public function delete_experience($id)
{
    if (!$this->session->userdata('logged_in')) {
        return $this->_json(['ok'=>false,'error'=>'auth'], 401);
    }
    if (!$this->input->is_ajax_request()) {
        return $this->_json(['ok'=>false,'error'=>'bad_request'], 400);
    }

    $this->load->model('Experience_model','exp');
    $user_id = (int)$this->session->userdata('user_id');
    $ok = $this->exp->delete((int)$id, $user_id);

    return $this->_json($ok ? ['ok'=>true] : ['ok'=>false,'error'=>'Delete failed'], $ok ? 200 : 500);
}

/**
 * Normalize a string to either "YYYY" or "YYYY-MM". Returns original if not matching.
 */
private function _ym_normalize(string $v): string
{
    $v = trim($v);
    if ($v === '') return '';
    if (preg_match('/^\d{4}$/', $v)) return $v;                 // YYYY
    if (preg_match('/^\d{4}-\d{2}$/', $v)) return $v;           // YYYY-MM
    // try to coerce from HTML5 month "YYYY-MM-DD" (some browsers can pass weird values)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return substr($v, 0, 7);
    return $v; // leave as-is; frontend will render it raw if not a valid YM
}

public function doc_types_json()
{
    $this->load->model('Profile_model', 'docs');
    $types = $this->docs->list_doc_types();

    $this->output->set_content_type('application/json')
                 ->set_output(json_encode($types));
}

public function skills_json()
{
    $this->output->set_content_type('application/json');

    $q     = trim((string)$this->input->get('q', true));         // Select2 search term
    $page  = max(1, (int)$this->input->get('page'));             // Select2 paging
    $limit = 50;                                                 // results per page
    $off   = ($page - 1) * $limit;

    $this->load->model('Profile_model', 'pm');
    [$rows, $total] = $this->pm->search_skills($q, $off, $limit);

    // Select2 format
    $results = array_map(function($r){
        return ['id' => (string)$r->skillID, 'text' => (string)$r->Title];
    }, $rows);

    echo json_encode([
        'results'    => $results,
        'pagination' => ['more' => ($off + $limit) < $total],
    ]);
}

public function save_document()
{
    $this->load->model('DocumentsModel', 'docs');
    $this->output->set_content_type('application/json');

    // Basic inputs
    $id          = (int)$this->input->post('id');
    $user_id     = (int)$this->session->userdata('user_id');
    $doc_name    = trim((string)$this->input->post('doc_name'));
    $doc_type_id = (int)$this->input->post('doc_type_id');
    $skill_id    = $this->input->post('skill_id') ? (int)$this->input->post('skill_id') : null;
    $expiry_date = $this->input->post('expiry_date') ?: null;
    $is_active   = (int)$this->input->post('is_active');

    // Quick validation (optional but helpful)
    if ($doc_name === '' || $doc_type_id <= 0) {
        return $this->output->set_status_header(422)->set_output(json_encode([
            'ok' => false,
            'error' => 'Please provide Document Name and Document Type.'
        ]));
    }
// (Optional) handle file upload
$file_path = null;
if (!empty($_FILES['doc_file']['name'])) {

$webRoot = rtrim(FCPATH, "/\\"); 
$dir = $webRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR;


    // Ensure it exists
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    // Normalize (Windows-friendly) and final sanity checks
    $dirReal = realpath($dir);
    if ($dirReal === false) { $dirReal = $dir; }          // keep going even if realpath fails

    // If the folder exists but isn't writable, try to relax perms once
    if (is_dir($dirReal) && !is_writable($dirReal)) {
        @chmod($dirReal, 0777);
        clearstatcache(true, $dirReal);
    }

    if (!is_dir($dirReal) || !is_writable($dirReal)) {
        log_message('error', 'Upload dir missing/unwritable. FCPATH=' . FCPATH . ' | resolved=' . $dirReal);
        return $this->output
            ->set_status_header(500)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'ok'    => false,
                'error' => 'Upload directory missing or not writable: ' . $dirReal
            ]));
    }

    $config = [
        'upload_path'      => rtrim($dirReal, "/\\") . DIRECTORY_SEPARATOR, // ABSOLUTE dir
        'allowed_types'    => 'pdf|jpg|jpeg|png',
        'encrypt_name'     => true,
        'file_ext_tolower' => true,
        'remove_spaces'    => true,
        'detect_mime'      => true,
        'max_size'         => 4096,  // 4MB
    ];

    // IMPORTANT: (re)initialize the Upload lib with our config
    $this->load->library('upload');
    $this->upload->initialize($config, true);

    if (!$this->upload->do_upload('doc_file')) {
        $err = $this->upload->display_errors('', '');
        log_message('error', 'doc_file upload failed: ' . $err . ' | path=' . $config['upload_path']);
        return $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => false, 'error' => $err]));
    }

    $u = $this->upload->data();
    // Store a RELATIVE web path in DB (for <a href>)
    $file_path = 'uploads/documents/' . $u['file_name'];
    $absPath = rtrim(FCPATH, "/\\") . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file_path);
$this->_fix_image_orientation($absPath);
}

// After you have $doc_type_id from POST, determine its code
$docTypeCode = null;
$dtRow = $this->db->select('code, doc_type')
                  ->from('doc_type')
                  ->where('id', $doc_type_id)
                  ->get()->row();
if ($dtRow) {
    $docTypeCode = strtolower(trim((string)$dtRow->code));
}

// If type is OTHERS, map the tiny dropdown to skill_id = ID of 'Certificate' or 'Document'
if ($docTypeCode === 'others') {
    $other_choice = strtolower(trim((string)$this->input->post('other_choice')));
    $choiceTitle  = $other_choice === 'certificate' ? 'Certificate'
                  : ($other_choice === 'document'   ? 'Document'   : null);

    if ($choiceTitle) {
        // fetch the skill id (the seed SQL above ensures they exist)
        $rowSkill = $this->db->select('skillID')->from('skills')->where('Title', $choiceTitle)->get()->row();
        if ($rowSkill) {
            $skill_id = (int)$rowSkill->skillID;
        } else {
            // safety: create if somehow missing
            $this->db->insert('skills', ['Title'=>$choiceTitle, 'Description'=>'Generic']);
            $skill_id = (int)$this->db->insert_id();
        }
    } else {
        $skill_id = null; // no selection
    }
}

    // Build payload for DB
    $payload = [
        'user_id'     => $user_id,
        'doc_name'    => $doc_name,
        'doc_type_id' => $doc_type_id,
        'skill_id'    => $skill_id ?: null,
        'expiry_date' => $expiry_date ?: null,
        'is_active'   => $is_active,
    ];
    if ($file_path) {
        $payload['file_path'] = $file_path;
    }

    // Create / Update (update is owner-scoped)
    if ($id > 0) {
        $ok = $this->docs->update_for_user($id, $user_id, $payload);
        if (!$ok) {
            return $this->output->set_status_header(403)->set_output(json_encode([
                'ok' => false, 'error' => 'Document not found.'
            ]));
        }
        return $this->output->set_output(json_encode(['ok' => true, 'mode' => 'updated']));
    } else {
        $new_id = $this->docs->create($payload);
        return $this->output->set_output(json_encode(['ok' => true, 'mode' => 'created', 'id' => $new_id]));
    }
}


public function delete_document($id)
{
    if ($this->input->method() !== 'post') {
        $this->output->set_status_header(405);
        echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
        return;
    }
    $this->load->model('DocumentsModel', 'docs');
    $user_id = (int)$this->session->userdata('user_id');
    $ok = $this->docs->delete_for_user((int)$id, $user_id);
    $this->output->set_content_type('application/json');
    echo json_encode(['ok' => (bool)$ok]);
}

}
