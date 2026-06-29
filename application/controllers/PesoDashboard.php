<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PesoDashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url','form','html','upload_safety']);
        $this->load->library(['session','form_validation','upload']);
        $this->load->model('Peso_model','peso');

        // Log what we actually have in session (helps diagnose prod)
        log_message(
            'debug',
            __CLASS__.'::__construct hit. logged_in='.(int)$this->session->userdata('logged_in')
            .' role=['.(string)$this->session->userdata('role').']'
            .' level=['.(string)$this->session->userdata('level').']'
        );

        // ⚠️ Allow public access to the JSON feed
        $method = strtolower($this->router->fetch_method());
        if (in_array($method, ['feed'], true)) {
            return; // no auth checks for the public feed
        }

        // Require login for everything else
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        if (!$this->is_peso_allowed()) {
            show_error('Forbidden (PESO only)', 403);
        }
    }

    /* ---------- Helpers ---------- */

    private function role_normalized(): string {
        $raw = (string)($this->session->userdata('role') ?: $this->session->userdata('level') ?: '');
        $r = strtolower(trim($raw));
        $r = str_replace(['_', '-'], ' ', $r);         // normalize underscores/dashes
        $r = preg_replace('/\s+/', ' ', $r);           // collapse extra spaces
        return $r;
    }

    private function is_peso_allowed(): bool {
        $r = $this->role_normalized();
        // TEMP: log normalized role
        log_message('debug', __CLASS__.' role normalized=['.$r.']');

        return (
            strpos($r, 'peso') !== false     // matches: "peso", "PESO_Officer", "peso-admin", etc.
            || $r === 'admin'
            || $r === 'tesda admin'
        );
    }

    private function should_force_public_visibility(): bool
    {
        $r = $this->role_normalized();
        return ($r === 'tesda admin' || $r === 'tesda');
    }

    private function me(): int
    {
        return (int) ($this->session->userdata('user_id')
            ?: $this->session->userdata('id')
            ?: 0);
    }

    private function decode_media_json($json): ?array
    {
        if (!$json) {
            return null;
        }
        if (is_string($json)) {
            $decoded = json_decode($json, true);
        } elseif (is_array($json)) {
            $decoded = $json;
        } else {
            $decoded = null;
        }
        if (!is_array($decoded)) {
            return null;
        }
        $rel = isset($decoded['rel_path']) ? (string)$decoded['rel_path'] : '';
        $rel = ltrim(str_replace('\\', '/', $rel), '/');
        if ($rel === '' || strpos($rel, 'uploads/') !== 0) {
            return null;
        }

        $type = isset($decoded['type']) ? strtolower((string)$decoded['type']) : '';
        $ext  = isset($decoded['ext']) ? strtolower((string)$decoded['ext']) : '';
        if ($ext === '') {
            $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
        }
        $imageExts = ['jpg','jpeg','png','webp','gif'];
        if (!in_array($type, ['image','pdf'], true)) {
            $type = in_array($ext, $imageExts, true) ? 'image' : 'pdf';
        }

        $decoded['rel_path'] = $rel;
        $decoded['type']     = $type;
        $decoded['ext']      = $ext;
        if (isset($decoded['size_bytes'])) {
            $decoded['size_bytes'] = (int)$decoded['size_bytes'];
        }
        return $decoded;
    }

    private function process_media_upload(?array $existing = null): array
    {
        $result = [
            'mode'     => 'keep',
            'media'    => $existing,
            'cleanup'  => null,
            'uploaded' => null,
        ];

        $removeRequested = (bool) $this->input->post('remove_media');
        $hasUpload = !empty($_FILES['attachment']['name']);

        if ($hasUpload) {
            $relDir = 'uploads/peso/' . date('Y') . '/' . date('m');
            $absDir = rtrim(FCPATH, '/\\') . '/' . $relDir;
            if (!is_dir($absDir)) {
                @mkdir($absDir, 0775, true);
            }

            $cfg = [
                'upload_path'      => $absDir,
                'allowed_types'    => 'jpg|jpeg|png|webp|gif|pdf',
                'max_size'         => 8 * 1024,
                'file_ext_tolower' => true,
                'remove_spaces'    => true,
                'encrypt_name'     => true,
                'detect_mime'      => true,
            ];
            $this->upload->initialize($cfg);

            if (!$this->upload->do_upload('attachment')) {
                $err = trim($this->upload->display_errors('', ''));
                throw new RuntimeException($err !== '' ? $err : 'Unable to upload the attachment.');
            }

            $data = $this->upload->data();
            $full = $data['full_path'];
            $ext  = ltrim(strtolower((string)$data['file_ext']), '.');

            if (!validate_uploaded_file_signature($full, $ext)) {
                @unlink($full);
                throw new RuntimeException('The uploaded file type is not allowed or appears to be corrupt.');
            }

            $imageExts = ['jpg','jpeg','png','webp','gif'];
            if (in_array($ext, $imageExts, true) && function_exists('safe_image_reencode')) {
                if (!safe_image_reencode($full, $ext)) {
                    @unlink($full);
                    throw new RuntimeException('Failed to process the uploaded image. Please try a different file.');
                }
            }

            $relPath = $relDir . '/' . $data['file_name'];
            $type    = in_array($ext, $imageExts, true) ? 'image' : 'pdf';
            $meta = [
                'rel_path'      => str_replace('\\', '/', $relPath),
                'type'          => $type,
                'ext'           => $ext,
                'mime'          => (string) ($data['file_type'] ?? ''),
                'size_bytes'    => isset($data['file_size']) ? (int) round(((float) $data['file_size']) * 1024) : null,
                'original_name' => (string) ($data['client_name'] ?? $data['orig_name'] ?? $data['file_name']),
                'uploaded_at'   => date('c'),
            ];

            $result['mode']     = 'set';
            $result['media']    = $meta;
            $result['uploaded'] = $meta;
            $result['cleanup']  = $existing;
            return $result;
        }

        if ($removeRequested && $existing) {
            $result['mode']    = 'remove';
            $result['media']   = null;
            $result['cleanup'] = $existing;
        }

        return $result;
    }

    private function delete_media_file(?array $media): void
    {
        if (!$media) {
            return;
        }
        $rel = isset($media['rel_path']) ? (string)$media['rel_path'] : '';
        $rel = ltrim(str_replace('\\', '/', $rel), '/');
        if ($rel === '' || strpos($rel, 'uploads/') !== 0) {
            return;
        }
        $abs = rtrim(FCPATH, '/\\') . '/' . $rel;
        if (is_file($abs)) {
            @unlink($abs);
        }
    }

    /** Public JSON feed for login screen (OPEN + PUBLIC, latest first) */
    public function feed()
    {
        // Explicitly JSON response
        $this->output->set_content_type('application/json');

        // Optional limit via ?limit=12, default 10
        $limit = (int) $this->input->get('limit');
        if ($limit <= 0 || $limit > 50) { $limit = 10; }

        try {
            $rows = $this->peso->latest_public_open($limit);

            // Normalize shape/types a bit for the frontend
            foreach ($rows as &$r) {
                $r['id']            = (int)($r['id'] ?? 0);
                $r['title']         = (string)($r['title'] ?? '');
                $r['description']   = (string)($r['description'] ?? '');
                $r['location_text'] = (string)($r['location_text'] ?? '');
                $r['price_min']     = isset($r['price_min']) && $r['price_min'] !== '' ? (float)$r['price_min'] : null;
                $r['price_max']     = isset($r['price_max']) && $r['price_max'] !== '' ? (float)$r['price_max'] : null;
                $r['created_at']    = (string)($r['created_at'] ?? '');
                $r['website_url']   = isset($r['website_url']) ? (string)$r['website_url'] : '';
                $media = $this->decode_media_json($r['media_json'] ?? null);
                unset($r['media_json']);
                if ($media) {
                    $rel = $media['rel_path'];
                    $viewer = site_url('media/preview?f=' . rawurlencode($rel));
                    $wm = $media['type'] === 'image'
                        ? site_url('media/wm_image?f=' . rawurlencode($rel))
                        : site_url('media/wm_pdf?f=' . rawurlencode($rel));
                    $r['media'] = [
                        'type'         => $media['type'],
                        'rel_path'     => $rel,
                        'viewer_url'   => $viewer,
                        'wm_url'       => $wm,
                        'public_url'   => base_url($rel),
                        'original'     => (string)($media['original_name'] ?? basename($rel)),
                        'size_bytes'   =>
                            isset($media['size_bytes']) ? (int)$media['size_bytes'] : null,
                        'uploaded_at'  => isset($media['uploaded_at']) ? (string)$media['uploaded_at'] : null,
                        'mime'         => isset($media['mime']) ? (string)$media['mime'] : null,
                    ];
                } else {
                    unset($r['media']);
                }
            }
            unset($r);

            $this->output->set_output(json_encode([
                'ok'   => true,
                'data' => $rows,
            ], JSON_UNESCAPED_UNICODE));
        } catch (Throwable $e) {
            // Don’t leak stack traces; keep it simple for the client
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'ok'      => false,
                    'error'   => 'server_error',
                    'message' => 'Unable to load feed.',
                ]));
        }
    }

    private function normalize_report_filters(): array
    {
        $dateFrom = trim((string)$this->input->get('date_from', true));
        $dateTo = trim((string)$this->input->get('date_to', true));
        $clientId = (int)$this->input->get('client_id', true);
        $workerId = (int)$this->input->get('worker_id', true);
        $projectId = (int)$this->input->get('project_id', true);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = '';
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = '';
        }

        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'client_id' => $clientId > 0 ? $clientId : 0,
            'worker_id' => $workerId > 0 ? $workerId : 0,
            'project_id' => $projectId > 0 ? $projectId : 0,
        ];
    }

    private function report_filter_labels(array $filters, array $options): array
    {
        $clientLabels = [];
        foreach ($options['clients'] ?? [] as $opt) {
            $clientLabels[(int)($opt['id'] ?? 0)] = (string)($opt['label'] ?? '');
        }

        $workerLabels = [];
        foreach ($options['workers'] ?? [] as $opt) {
            $workerLabels[(int)($opt['id'] ?? 0)] = (string)($opt['label'] ?? '');
        }

        $projectLabels = [];
        foreach ($options['projects'] ?? [] as $opt) {
            $projectLabels[(int)($opt['id'] ?? 0)] = (string)($opt['label'] ?? '');
        }

        $labels = [];
        if (!empty($filters['date_from'])) {
            $labels['date_from'] = 'From: ' . $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $labels['date_to'] = 'To: ' . $filters['date_to'];
        }
        if (!empty($filters['client_id'])) {
            $id = (int)$filters['client_id'];
            $labels['client_id'] = 'Client: ' . ($clientLabels[$id] ?? ('Client #' . $id));
        }
        if (!empty($filters['worker_id'])) {
            $id = (int)$filters['worker_id'];
            $labels['worker_id'] = 'Worker: ' . ($workerLabels[$id] ?? ('Worker #' . $id));
        }
        if (!empty($filters['project_id'])) {
            $id = (int)$filters['project_id'];
            $labels['project_id'] = 'Project: ' . ($projectLabels[$id] ?? ('Project #' . $id));
        }

        return $labels;
    }

    private function report_url_with_query(array $filters, bool $print = false): string
    {
        $query = [];
        if (!empty($filters['date_from'])) {
            $query['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $query['date_to'] = $filters['date_to'];
        }
        if (!empty($filters['client_id'])) {
            $query['client_id'] = (int)$filters['client_id'];
        }
        if (!empty($filters['worker_id'])) {
            $query['worker_id'] = (int)$filters['worker_id'];
        }
        if (!empty($filters['project_id'])) {
            $query['project_id'] = (int)$filters['project_id'];
        }
        if ($print) {
            $query['print'] = 1;
        }

        $base = site_url('peso/reports/hired-workers');
        return empty($query) ? $base : ($base . '?' . http_build_query($query));
    }

    public function index()
    {
        $uid = $this->me();
        $list = $this->peso->mine($uid);

        // Vacancy KPI counts derived from this PESO user's own postings.
        $kOpen = $kClosed = $kPublic = 0;
        foreach ($list as $r) {
            $st = strtolower((string)($r['status'] ?? ''));
            if ($st === 'open') {
                $kOpen++;
            } else {
                $kClosed++;
            }
            if (strtolower((string)($r['visibility'] ?? 'public')) === 'public') {
                $kPublic++;
            }
        }

        $this->load->view('dashboard_peso', [
            'page_title' => 'PESO Dashboard',
            'list'       => $list,
            'k_open'     => $kOpen,
            'k_closed'   => $kClosed,
            'k_public'   => $kPublic,
            'hired'      => $this->peso->hired_summary(),
            'force_public_visibility' => $this->should_force_public_visibility(),
        ]);
    }

    public function hired_workers_report()
    {
        $filters = $this->normalize_report_filters();
        $rows = $this->peso->hired_workers_report($filters);
        $options = $this->peso->hired_workers_filter_options();

        $workerIds = [];
        $clientIds = [];
        foreach ($rows as $row) {
            $wid = (int)($row['worker_id'] ?? 0);
            $cid = (int)($row['client_id'] ?? 0);
            if ($wid > 0) {
                $workerIds[$wid] = true;
            }
            if ($cid > 0) {
                $clientIds[$cid] = true;
            }
        }

        $summary = $this->peso->hired_summary();
        $activeFilterLabels = $this->report_filter_labels($filters, $options);
        $data = [
            'page_title' => 'PESO Report: Hired Workers',
            'rows' => $rows,
            'filters' => $filters,
            'filter_options' => $options,
            'active_filter_labels' => $activeFilterLabels,
            'print_url' => $this->report_url_with_query($filters, true),
            'reset_url' => site_url('peso/reports/hired-workers'),
            'stats' => [
                'total_hires' => count($rows),
                'total_workers' => count($workerIds),
                'total_clients' => count($clientIds),
                'hired_this_month' => (int)($summary['this_month'] ?? 0),
                'hired_this_year' => (int)($summary['this_year'] ?? 0),
            ],
        ];

        if ((int)$this->input->get('print') === 1) {
            $this->load->view('peso_hired_workers_report_print', $data);
            return;
        }

        $this->load->view('peso_hired_workers_report', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('title', 'Job Title', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('post_type', 'Post Type', 'trim');
        $this->form_validation->set_rules('visibility', 'Visibility', 'trim');
        $this->form_validation->set_rules('price_min', 'Min', 'trim');
        $this->form_validation->set_rules('price_max', 'Max', 'trim');
        $this->form_validation->set_rules('website_url', 'Website', 'trim|valid_url'); // keep or relax if you want

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            return redirect('dashboard/peso');
        }

        try {
            $mediaInfo = $this->process_media_upload(null);
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('danger', $e->getMessage());
            return redirect('dashboard/peso');
        }

        $payload = $this->input->post(null, true);
        if ($this->should_force_public_visibility()) {
            $payload['visibility'] = 'public';
        }
        $media   = ($mediaInfo['mode'] === 'set') ? $mediaInfo['media'] : null;
        $newId   = $this->peso->create($this->me(), $payload, $media);

        if (!$newId && $mediaInfo['mode'] === 'set') {
            $this->delete_media_file($mediaInfo['uploaded']);
        }

        $this->session->set_flashdata($newId ? 'success' : 'danger', $newId ? 'Job vacancy posted.' : 'Unable to save job vacancy.');
        return redirect('dashboard/peso');
    }

    public function edit($id)
    {
        $uid = $this->me();
        $job = $this->peso->find($id, $uid);
        if (!$job) {
            $this->session->set_flashdata('danger', 'Record not found.');
            return redirect('dashboard/peso');
        }
        $list = $this->peso->mine($uid);
        $this->load->view('dashboard_peso', [
            'page_title' => 'PESO Dashboard',
            'list'       => $list,
            'edit'       => $job,
            'force_public_visibility' => $this->should_force_public_visibility(),
        ]);
    }

    public function update($id)
    {
        $job = $this->peso->find($id, $this->me());
        if (!$job) {
            $this->session->set_flashdata('danger', 'Record not found.');
            return redirect('dashboard/peso');
        }

        $this->form_validation->set_rules('title', 'Job Title', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('website_url', 'Website', 'trim|valid_url'); // keep or relax if you want

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            return redirect('dashboard/peso');
        }

        $existingMedia = $this->decode_media_json($job['media_json'] ?? null);

        try {
            $mediaInfo = $this->process_media_upload($existingMedia);
        } catch (RuntimeException $e) {
            $this->session->set_flashdata('danger', $e->getMessage());
            return redirect('dashboard/peso');
        }

        $payload = $this->input->post(null, true);
        if ($this->should_force_public_visibility()) {
            $payload['visibility'] = 'public';
        }
        $mediaArg = null;
        if ($mediaInfo['mode'] === 'set') {
            $mediaArg = ['mode' => 'set', 'data' => $mediaInfo['media']];
        } elseif ($mediaInfo['mode'] === 'remove') {
            $mediaArg = ['mode' => 'remove'];
        }

        $ok = $this->peso->update_job($id, $this->me(), $payload, $mediaArg);
        if ($ok) {
            if (in_array($mediaInfo['mode'], ['set', 'remove'], true) && $mediaInfo['cleanup']) {
                $this->delete_media_file($mediaInfo['cleanup']);
            }
            $this->session->set_flashdata('success', 'Updated.');
        } else {
            if ($mediaInfo['mode'] === 'set' && $mediaInfo['uploaded']) {
                $this->delete_media_file($mediaInfo['uploaded']);
            }
            $this->session->set_flashdata('danger', 'Update failed.');
        }

        return redirect('dashboard/peso');
    }

    public function toggle($id)
    {
        $ok = $this->peso->toggle_status($id, $this->me());
        $this->session->set_flashdata($ok ? 'success' : 'danger', $ok ? 'Status changed.' : 'Could not change status.');
        return redirect('dashboard/peso');
    }

    public function delete($id)
    {
        $job = $this->peso->find($id, $this->me());
        if (!$job) {
            $this->session->set_flashdata('danger', 'Record not found.');
            return redirect('dashboard/peso');
        }

        $media = $this->decode_media_json($job['media_json'] ?? null);
        $ok    = $this->peso->delete_job($id, $this->me());
        if ($ok) {
            if ($media) {
                $this->delete_media_file($media);
            }
            $this->session->set_flashdata('success', 'Deleted.');
        } else {
            $this->session->set_flashdata('danger', 'Delete failed.');
        }
        return redirect('dashboard/peso');
    }
}
