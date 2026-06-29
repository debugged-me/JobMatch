<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TesdaTrainings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form', 'html']);
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->model('TesdaTraining_model', 'tesdaTraining');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        $role = $this->role_normalized();
        if (!in_array($role, ['tesda admin', 'admin'], true)) {
            show_error('Forbidden (TESDA only)', 403);
        }
    }

    private function role_normalized(): string
    {
        $raw = (string)($this->session->userdata('role') ?: $this->session->userdata('level') ?: '');
        $r = strtolower(trim($raw));
        $r = str_replace(['_', '-'], ' ', $r);
        $r = preg_replace('/\s+/', ' ', $r);
        return trim((string)$r);
    }

    private function me(): int
    {
        return (int)($this->session->userdata('user_id') ?: $this->session->userdata('id') ?: 0);
    }

    private function training_payload(): array
    {
        return [
            'title' => trim((string)$this->input->post('title', true)),
            'description' => trim((string)$this->input->post('description', true)),
            'website_url' => trim((string)$this->input->post('website_url', true)),
            'location_text' => trim((string)$this->input->post('location_text', true)),
            'address_id' => (int)$this->input->post('address_id'),
            'province' => trim((string)$this->input->post('province', true)),
            'city' => trim((string)$this->input->post('city', true)),
            'brgy' => trim((string)$this->input->post('brgy', true)),
            'visibility' => 'public',
        ];
    }

    private function upload_training_image(string $field = 'training_image'): array
    {
        if (
            !isset($_FILES[$field])
            || !is_array($_FILES[$field])
            || (int)($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
        ) {
            return ['ok' => true, 'uploaded' => false, 'path' => null];
        }

        $dir = FCPATH . 'uploads/trainings/';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return ['ok' => false, 'uploaded' => false, 'path' => null, 'error' => 'Unable to prepare training uploads directory.'];
        }

        $config = [
            'upload_path' => $dir,
            'allowed_types' => 'jpg|jpeg|png|webp|gif',
            'max_size' => 8192,
            'encrypt_name' => true,
            'remove_spaces' => true,
        ];
        $this->upload->initialize($config, true);

        if (!$this->upload->do_upload($field)) {
            $error = trim((string)$this->upload->display_errors('', ''));
            return [
                'ok' => false,
                'uploaded' => false,
                'path' => null,
                'error' => $error !== '' ? $error : 'Image upload failed.',
            ];
        }

        $up = $this->upload->data();
        $file = trim((string)($up['file_name'] ?? ''));
        if ($file === '') {
            return ['ok' => false, 'uploaded' => false, 'path' => null, 'error' => 'Image upload failed.'];
        }

        return [
            'ok' => true,
            'uploaded' => true,
            'path' => 'uploads/trainings/' . $file,
        ];
    }

    private function unlink_training_image(?string $relativePath): void
    {
        $path = trim((string)$relativePath);
        if ($path === '') {
            return;
        }

        $path = str_replace('\\', '/', $path);
        if (strpos($path, 'uploads/trainings/') !== 0) {
            return;
        }

        $full = FCPATH . ltrim($path, '/');
        if (is_file($full)) {
            @unlink($full);
        }
    }

    private function list_trainings(int $uid): array
    {
        return $this->tesdaTraining->mine($uid);
    }

    private function render_page(int $editId = 0): void
    {
        $uid = $this->me();
        $q = trim((string)$this->input->get('q', true));
        $list = $this->list_trainings($uid);

        if ($q !== '') {
            $needle = strtolower($q);
            $list = array_values(array_filter($list, static function ($row) use ($needle) {
                $title = strtolower((string)($row['title'] ?? ''));
                $loc = strtolower((string)($row['location_text'] ?? ''));
                return (strpos($title, $needle) !== false) || (strpos($loc, $needle) !== false);
            }));
        }

        $kOpen = 0;
        $kClosed = 0;
        $kPublic = 0;
        foreach ($list as $row) {
            $status = strtolower((string)($row['status'] ?? 'open'));
            $visibility = strtolower((string)($row['visibility'] ?? 'public'));
            if ($status === 'open') {
                $kOpen++;
            } else {
                $kClosed++;
            }
            if ($visibility === 'public') {
                $kPublic++;
            }
        }

        $edit = null;
        if ($editId > 0) {
            $candidate = $this->tesdaTraining->find($editId, $uid);
            if ($candidate) {
                $edit = $candidate;
            } else {
                $this->session->set_flashdata('danger', 'Training post not found.');
                redirect('tesda/trainings');
                return;
            }
        }

        $this->load->view('tesda_trainings', [
            'page_title' => 'TESDA Training Posts',
            'list' => $list,
            'edit' => $edit,
            'q' => $q,
            'k_open' => $kOpen,
            'k_closed' => $kClosed,
            'k_public' => $kPublic,
        ]);
    }

    public function index(): void
    {
        $this->render_page();
    }

    public function edit($id): void
    {
        $this->render_page((int)$id);
    }

    public function store(): void
    {
        $this->form_validation->set_rules('title', 'Training Title', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('website_url', 'Website Link', 'trim|valid_url');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            redirect('tesda/trainings');
            return;
        }

        $payload = $this->training_payload();
        $upload = $this->upload_training_image('training_image');
        if (!$upload['ok']) {
            $this->session->set_flashdata('danger', $upload['error'] ?? 'Image upload failed.');
            redirect('tesda/trainings');
            return;
        }
        if (!empty($upload['uploaded']) && !empty($upload['path'])) {
            $payload['image_path'] = (string)$upload['path'];
        }

        $newId = $this->tesdaTraining->create($this->me(), $payload);
        if (!$newId && !empty($upload['uploaded']) && !empty($upload['path'])) {
            $this->unlink_training_image((string)$upload['path']);
        }
        $this->session->set_flashdata($newId ? 'success' : 'danger', $newId ? 'Training posted.' : 'Unable to save training post.');
        redirect('tesda/trainings');
    }

    public function update($id): void
    {
        $id = (int)$id;
        $uid = $this->me();
        $row = $this->tesdaTraining->find($id, $uid);
        if (!$row) {
            $this->session->set_flashdata('danger', 'Training post not found.');
            redirect('tesda/trainings');
            return;
        }

        $this->form_validation->set_rules('title', 'Training Title', 'trim|required|max_length[200]');
        $this->form_validation->set_rules('website_url', 'Website Link', 'trim|valid_url');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('danger', validation_errors());
            redirect('tesda/trainings/edit/' . $id);
            return;
        }

        $payload = $this->training_payload();
        $upload = $this->upload_training_image('training_image');
        if (!$upload['ok']) {
            $this->session->set_flashdata('danger', $upload['error'] ?? 'Image upload failed.');
            redirect('tesda/trainings/edit/' . $id);
            return;
        }
        if (!empty($upload['uploaded']) && !empty($upload['path'])) {
            $payload['image_path'] = (string)$upload['path'];
        }

        $ok = $this->tesdaTraining->update_post($id, $uid, $payload);
        if ($ok && !empty($upload['uploaded']) && !empty($upload['path'])) {
            $this->unlink_training_image((string)($row['image_path'] ?? ''));
        }
        if (!$ok && !empty($upload['uploaded']) && !empty($upload['path'])) {
            $this->unlink_training_image((string)$upload['path']);
        }
        $this->session->set_flashdata($ok ? 'success' : 'danger', $ok ? 'Training updated.' : 'Unable to update training.');
        redirect('tesda/trainings');
    }

    public function toggle($id): void
    {
        $id = (int)$id;
        $uid = $this->me();
        $row = $this->tesdaTraining->find($id, $uid);
        if (!$row) {
            $this->session->set_flashdata('danger', 'Training post not found.');
            redirect('tesda/trainings');
            return;
        }

        $ok = $this->tesdaTraining->toggle_status($id, $uid);
        $this->session->set_flashdata($ok ? 'success' : 'danger', $ok ? 'Training status updated.' : 'Unable to update status.');
        redirect('tesda/trainings');
    }

    public function delete($id): void
    {
        $id = (int)$id;
        $uid = $this->me();
        $row = $this->tesdaTraining->find($id, $uid);
        if (!$row) {
            $this->session->set_flashdata('danger', 'Training post not found.');
            redirect('tesda/trainings');
            return;
        }

        $imagePath = (string)($row['image_path'] ?? '');
        $ok = $this->tesdaTraining->delete_post($id, $uid);
        if ($ok) {
            $this->unlink_training_image($imagePath);
        }
        $this->session->set_flashdata($ok ? 'success' : 'danger', $ok ? 'Training deleted.' : 'Unable to delete training.');
        redirect('tesda/trainings');
    }
}
