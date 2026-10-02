<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!class_exists('PdfWithRotation')) {
    class PdfWithRotation extends \setasign\Fpdi\Fpdi
    {
        protected $angle = 0;
        protected $extgstates = [];

        public function __construct($orientation='P', $unit='mm', $size='A4')
        {
            parent::__construct($orientation, $unit, $size);
            $this->PDFVersion = '1.4';
        }

        public function Rotate($angle, $x=-1, $y=-1)
        {
            if ($x == -1) $x = $this->x;
            if ($y == -1) $y = $this->y;

            if ($this->angle != 0) {
                $this->angle = 0;
                $this->_out('Q');
            }
            if ($angle != 0) {
                $this->angle = $angle;
                $angle *= M_PI/180;
                $c = cos($angle); $s = sin($angle);
                $cx = $x * $this->k;
                $cy = ($this->h - $y) * $this->k;
                $this->_out(sprintf(
                    'q %.5F %.5F %.5F %.5F %.5F %.5F cm 1 0 0 1 %.5F %.5F cm',
                    $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy
                ));
            }
        }

        function _endpage()
        {
            if ($this->angle != 0) {
                $this->angle = 0;
                $this->_out('Q');
            }
            parent::_endpage();
        }

        // Soft-opacity
        public function SetAlpha($alpha, $bm='Normal')
        {
            $gs = $this->AddExtGState(['ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm]);
            $this->SetExtGState($gs);
        }
        protected function AddExtGState($parms)
        {
            $n = count($this->extgstates) + 1;
            $this->extgstates[$n]['parms'] = $parms;
            return $n;
        }
        protected function SetExtGState($gs)
        {
            $this->_out(sprintf('/GS%d gs', $gs));
        }
        function _putextgstates()
        {
            foreach ($this->extgstates as $i => $state) {
                $this->_newobj();
                $this->extgstates[$i]['n'] = $this->n;
                $this->_put('<</Type /ExtGState');
                $parms = $state['parms'];
                $this->_put('/ca ' . sprintf('%.3F', $parms['ca']));
                $this->_put('/CA ' . sprintf('%.3F', $parms['CA']));
                $this->_put('/BM ' . $parms['BM']);
                $this->_put('>>');
                $this->_put('endobj');
            }
        }
        function _putresourcedict()
        {
            parent::_putresourcedict();
            if (!empty($this->extgstates)) {
                $this->_put('/ExtGState <<');
                foreach ($this->extgstates as $i => $state) {
                    $this->_put('/GS' . $i . ' ' . $state['n'] . ' 0 R');
                }
                $this->_put('>>');
            }
        }
        function _putresources()
        {
            $this->_putextgstates();
            parent::_putresources();
        }
    }
}

class Media extends CI_Controller
{
    /** Dirs holding personal/credential documents — owner or staff only. */
    private const PRIVATE_DIRS = [
        'uploads/documents/',
        'uploads/clients/ids/',
        'uploads/clients/permits/',
        'uploads/clients/certificates/',
        'uploads/certificates/',
        'uploads/trainings/',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        if (!$this->session->userdata('logged_in')) {
            show_error('Authentication required', 401);
        }
    }

    /**
     * Authorization for a normalized 'uploads/…' path:
     * staff roles may view anything; other logged-in users may only fetch
     * files inside PRIVATE_DIRS that a DB row they own references.
     */
    private function _can_access(string $rel): bool
    {
        // Normalize so leading slashes / backslashes can't bypass the dir check.
        $rel = preg_replace('#[/\\\\]+#', '/', ltrim(str_replace('\\', '/', $rel), '/'));

        $role = strtolower(str_replace(['_', '-'], ' ',
            (string)($this->session->userdata('role') ?: $this->session->userdata('level'))));
        if (in_array($role, ['admin', 'peso', 'tesda admin', 'school admin'], true)) {
            return true;
        }
        foreach (self::PRIVATE_DIRS as $d) {
            if (strpos($rel, $d) === 0) return $this->_owns_file($rel);
        }
        return true; // published content dirs (posts, avatars, projects…)
    }

    /** True when a row owned by the current user references this path. */
    private function _owns_file(string $rel): bool
    {
        $uid = (int)($this->session->userdata('user_id') ?: $this->session->userdata('id'));
        if ($uid <= 0) return false;
        $this->load->database();

        $hit = function (string $table, string $ownerCol, array $cols) use ($uid, $rel): bool {
            if (!$this->db->table_exists($table)) return false;
            $q = $this->db->where($ownerCol, $uid)->group_start();
            foreach ($cols as $c) {
                if ($this->db->field_exists($c, $table)) $q->or_like($c, $rel);
            }
            return $q->group_end()->count_all_results($table) > 0;
        };

        return $hit('documents',      'user_id',   ['file_path'])
            || $hit('client_profile', 'clientID',  ['id_image', 'certificates', 'business_permit'])
            || $hit('worker_profile', 'workerID',  ['avatar', 'cert_files', 'tesda_certs', 'language_certs'])
            || $hit('users',          'id',        ['avatar', 'photo', 'image', 'profile_pic']);
    }

    /**
     * Normalize a user-supplied relative path and verify it resolves to a
     * real file strictly inside FCPATH/uploads. Returns absolute path or null.
     */
    private function _uploads_file($rel)
    {
        $rel = ltrim(str_replace('\\', '/', (string)$rel), '/');
        if ($rel === '' || strpos($rel, '..') !== false || strpos($rel, 'uploads/') !== 0) {
            return null;
        }
        $root = realpath(FCPATH . 'uploads');
        $abs  = $root ? realpath($root . DIRECTORY_SEPARATOR . substr($rel, 8)) : false;
        if (!$root || $abs === false || strpos($abs, $root . DIRECTORY_SEPARATOR) !== 0 || !is_file($abs)) {
            return null;
        }
        return $abs;
    }

    public function preview()
    {
        $f = (string) $this->input->get('f', true);
        if ($f === '') show_404();

        $f = ltrim($f, "/\\");
        $f = preg_replace('#[/\\\\]+#', '/', $f);
        if (preg_match('#\.\.#', $f)) show_error('Invalid path', 400);

        $uploadsRoot = realpath(FCPATH . 'uploads');
        $abs         = realpath(FCPATH . $f);
        if (!$uploadsRoot || !$abs || strpos($abs, $uploadsRoot) !== 0) show_error('Not allowed', 403);
        if (!is_file($abs)) show_404();
        if (!$this->_can_access($f)) show_error('Not allowed', 403);

        $ext  = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif','pdf'];
        if (!in_array($ext, $allowed, true)) show_error('Unsupported file type', 415);

        if (!validate_uploaded_file_signature($abs, $ext)) show_error('Corrupted or unsafe file', 415);

        $type = in_array($ext, ['jpg','jpeg','png','webp','gif']) ? 'image' : 'pdf';

        $data = [
            'title' => basename($abs),
            'type'  => $type,
        ];
        if ($type === 'image') {
            $data['url']     = base_url($f);
            $data['img_url'] = site_url('media/wm_image?f=' . rawurlencode($f));
        } else {
            $data['url']     = site_url('media/wm_pdf?f=' . rawurlencode($f));
            $data['img_url'] = null;
        }
        $this->load->view('media_viewer', $data);
    }

    // ---------------------------------------------------------------------

    public function wm_image()
    {
        $rel = (string) $this->input->get('f', true);
        $abs = $this->_uploads_file($rel);
        if ($abs === null) show_error('Invalid path', 400);
        if (!$this->_can_access($rel)) show_error('Not allowed', 403);

        $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
        $imgExts = ['jpg','jpeg','png','webp','gif'];
        if (!in_array($ext, $imgExts, true)) {
            redirect('media/preview?f=' . rawurlencode($rel));
            return;
        }

        // -------- font detection FIRST (so ETag can include it) --------
        $fontCandidates = [
            FCPATH.'assets/fonts/Inter/Inter-Bold.ttf',
            FCPATH.'assets/fonts/Inter/Inter-Bold.TTF',
            FCPATH.'assets/fonts/Inter/static/Inter-Bold.ttf',
            FCPATH.'assets/fonts/Inter/static/Inter-Bold.TTF',
            FCPATH.'assets/fonts/Roboto/Roboto-Bold.ttf',
            FCPATH.'assets/fonts/rubik/Rubik-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            'C:\Windows\Fonts\arialbd.ttf',
            '/Library/Fonts/Arial Bold.ttf',
        ];
        $fontPath = null;
        foreach ($fontCandidates as $c) {
            if (is_file($c) && is_readable($c)) { $fontPath = $c; break; }
        }
        $font_mtime = $fontPath ? (@filemtime($fontPath) ?: 0) : 0;

        // -------- Early ETag + Disk cache --------
        $wmver = 'wm4'; // bump if you change watermark look
        $src_mtime = @filemtime($abs) ?: 0;
        $etag = sprintf('W/"%s-%s-%d-%d"', $wmver, md5($rel), $src_mtime, $font_mtime);

        $this->output->set_header('Cache-Control: private, max-age=604800'); // 7d
        $this->output->set_header('ETag: ' . $etag);
        if (trim($this->input->server('HTTP_IF_NONE_MATCH') ?? '') === $etag) {
            $this->output->set_status_header(304);
            return;
        }

        $cacheDir = FCPATH . 'writable/wm-cache/img';
        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);

        $outExt = ($ext === 'png') ? 'png' : (($ext === 'webp') ? 'webp' : 'jpg');
        $cacheKey  = sha1($wmver.'|'.$rel.'|'.$src_mtime.'|'.$font_mtime.'|'.$outExt);
        $cacheFile = $cacheDir . '/' . $cacheKey . '.' . $outExt;

        if (is_file($cacheFile)) {
            $dl = $this->input->get('dl') ? 'attachment' : 'inline';
            header('Content-Disposition: ' . $dl . '; filename="' . basename($rel) . '"');
            header('Content-Type: image/' . ($outExt === 'jpg' ? 'jpeg' : $outExt));
            readfile($cacheFile);
            return;
        }

        // -------- Generate watermark (first time only) --------
        $im = null;
        switch ($ext) {
            case 'jpg':
            case 'jpeg': $im = @imagecreatefromjpeg($abs); break;
            case 'png':  $im = @imagecreatefrompng($abs);  break;
            case 'webp': $im = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($abs) : @imagecreatefromjpeg($abs); break;
            case 'gif':  $im = @imagecreatefromgif($abs);  break;
        }
        if (!$im) show_error('Unsupported or corrupted image', 415);

        imagealphablending($im, true);
        imagesavealpha($im, true);

        $w = imagesx($im);
        $h = imagesy($im);

        // keeps memory under control
        $maxDim = 2400;
        if (max($w,$h) > $maxDim) {
            $ratio = min($maxDim/$w, $maxDim/$h);
            $nw = max(1, (int)floor($w * $ratio));
            $nh = max(1, (int)floor($h * $ratio));
            $dst = imagecreatetruecolor($nw, $nh);
            imagealphablending($dst, true);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $im, 0,0, 0,0, $nw,$nh, $w,$h);
            imagedestroy($im);
            $im = $dst; $w = $nw; $h = $nh;
        }

        $stamp = 'DO NOT REUSE';
        $angle = -28;

        $hasFreetype = function_exists('gd_info') ? ((gd_info()['FreeType Support'] ?? false) ? true : false) : true;
        $useTTF = ($fontPath && function_exists('imagettftext') && $hasFreetype);
        if (!$useTTF) {
            log_message('debug', 'wm_image: TTF NOT used. fontPath=' . var_export($fontPath,true)
                . ' imagettftext='.(function_exists('imagettftext')?'yes':'no')
                . ' FreeType='.($hasFreetype?'yes':'no'));
        }

        // Colors (127 transparent … 0 opaque)
        $alphaMain   = 90;
        $alphaStroke = 110;
        $mainColor   = imagecolorallocatealpha($im, 220, 0, 0, $alphaMain);
        $strokeColor = imagecolorallocatealpha($im, 255,255,255, $alphaStroke);

        if ($useTTF) {
            $targetW = (int)round($w * 0.85);
            $size = max(32, (int)round(min($w, $h) * 0.22));

            $measure = function($sz) use ($stamp, $angle, $fontPath) {
                $b = imagettfbbox($sz, $angle, $fontPath, $stamp);
                $xs = [$b[0],$b[2],$b[4],$b[6]];
                $ys = [$b[1],$b[3],$b[5],$b[7]];
                $minX = min($xs); $maxX = max($xs);
                $minY = min($ys); $maxY = max($ys);
                return [$minX,$minY,$maxX,$maxY, $maxX-$minX, $maxY-$minY];
            };
            for ($i=0; $i<12; $i++) { [, , , , $tw, ] = $measure($size); if ($tw < $targetW) $size = (int)round($size * 1.18); else break; }
            for ($i=0; $i<8;  $i++) { [, , , , $tw, ] = $measure($size); if ($tw > $targetW) $size = (int)round(max(12, $size * 0.92)); else break; }

            [$minX,$minY,$maxX,$maxY,$textW,$textH] = $measure($size);
            $x = (int)(($w - $textW) / 2) - $minX;
            $y = (int)(($h - $textH) / 2) - $minY;

            @imagettftext($im, $size, $angle, $x+2, $y+2, $strokeColor, $fontPath, $stamp);
            @imagettftext($im, $size, $angle, $x,   $y,   $mainColor,   $fontPath, $stamp);
        } else {
            $strW = imagefontwidth(5) * strlen($stamp);
            $strH = imagefontheight(5);
            $x    = (int)(($w - $strW) / 2);
            $y    = (int)(($h - $strH) / 2);
            imagestring($im, 5, $x+1, $y+1, $stamp, $strokeColor);
            imagestring($im, 5, $x,   $y,   $stamp, $mainColor);
        }

        // save to cache then serve
        $dl = $this->input->get('dl') ? 'attachment' : 'inline';
        header('Content-Disposition: ' . $dl . '; filename="' . basename($rel) . '"');
        header('Content-Type: image/' . ($outExt === 'jpg' ? 'jpeg' : $outExt));

        if ($outExt === 'png') {
            imagepng($im, $cacheFile, 6);
        } elseif ($outExt === 'webp' && function_exists('imagewebp')) {
            imagewebp($im, $cacheFile, 85);
        } else {
            imagejpeg($im, $cacheFile, 85);
        }
        imagedestroy($im);

        readfile($cacheFile);
    }

    // ---------------------------------------------------------------------

    public function wm_pdf()
    {
        $rel = (string) $this->input->get('f', true);
        $abs = $this->_uploads_file($rel);
        if ($abs === null) show_error('Invalid path', 400);
        if (!$this->_can_access($rel)) show_error('Not allowed', 403);

        $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') show_error('Unsupported file type', 415);

        if (!class_exists('\setasign\Fpdi\Fpdi')) {
            show_error('FPDI not installed (composer require setasign/fpdi-fpdf)', 500);
        }

        // Early ETag + disk cache
        $wmver = 'wm4';
        $src_mtime = @filemtime($abs) ?: 0;
        $etag = sprintf('W/"%s-%s-%d"', $wmver, md5($rel), $src_mtime);
        $this->output->set_header('Cache-Control: private, max-age=604800');
        $this->output->set_header('ETag: ' . $etag);
        if (trim($this->input->server('HTTP_IF_NONE_MATCH') ?? '') === $etag) {
            $this->output->set_status_header(304);
            return;
        }

        $cacheDir = FCPATH . 'writable/wm-cache/pdf';
        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
        $cacheKey  = sha1($wmver.'|'.$rel.'|'.$src_mtime);
        $cacheFile = $cacheDir.'/'.$cacheKey.'.pdf';

        if (is_file($cacheFile)) {
            $dl = $this->input->get('dl') ? 'attachment' : 'inline';
            header('Content-Type: application/pdf');
            header('Content-Disposition: '.$dl.'; filename="'.basename($rel).'"');
            readfile($cacheFile);
            return;
        }

        if (ob_get_length()) { @ob_end_clean(); }
        @set_time_limit(60);

        $pdf = new PdfWithRotation();
        try {
            $pageCount = $pdf->setSourceFile($abs);
        } catch (\Throwable $e) {
            log_message('error', 'FPDI open error: '.$e->getMessage());
            show_error('Unable to open PDF', 415);
            return;
        }

        $stamp = 'DO NOT REUSE';

        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $size  = $pdf->getTemplateSize($tplId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height'], true);

            $pdf->SetTextColor(220, 0, 0);
            $pdf->SetAlpha(0.28);

            $fs = max(16, min($size['width'], $size['height']) * 0.18);
            $pdf->SetFont('Arial', 'B', $fs);
            $target = $size['width'] * 0.85;
            while ($pdf->GetStringWidth($stamp) < $target && $fs < 500) { $fs += 4; $pdf->SetFont('Arial', 'B', $fs); }
            while ($pdf->GetStringWidth($stamp) > $target && $fs > 12) { $fs -= 2; $pdf->SetFont('Arial', 'B', $fs); }
            $wText = $pdf->GetStringWidth($stamp);

            $x = ($size['width'] - $wText) / 2.0;
            $y = $size['height'] / 2.0;

            $pdf->Rotate(30, $size['width']/2.0, $size['height']/2.0);
            $pdf->Text($x, $y, $stamp);
            $pdf->Rotate(0);
            $pdf->SetAlpha(1);
        }

        // write once to cache then serve
        $bytes = $pdf->Output('S');
        file_put_contents($cacheFile, $bytes);

        $dl = $this->input->get('dl') ? 'attachment' : 'inline';
        header('Content-Type: application/pdf');
        header('Content-Disposition: '.$dl.'; filename="'.basename($rel).'"');
        echo $bytes;
        exit;
    }
}
