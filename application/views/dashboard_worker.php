<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'JobMatch DavOr', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.0.7') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/universal.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-worker.css?v=3.1.0') ?>">

  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

</head>

<body>
  <?php $this->load->view('partials_translate_banner'); ?>

  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="wd">

            <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success" role="alert"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>

            <?php
            $p = isset($profile) ? $profile : null;
            $first_name = $p->first_name ?? ($this->session->userdata('first_name') ?: '');
            $last_name  = $p->last_name  ?? '';
            $full_name  = trim($last_name . ', ' . $first_name);
            $seed       = $full_name !== '' ? $full_name : ($this->session->userdata('first_name') ?: 'Worker');
            $avatarUrl = function_exists('avatar_url')
              ? avatar_url($p->avatar ?? '')
              : (function ($raw) {
                $raw = trim((string)$raw);
                if ($raw !== '' && preg_match('#^https?://#i', $raw)) return $raw;
                if ($raw !== '') return base_url(str_replace('\\', '/', $raw));
                return base_url('uploads/avatars/avatar.png');
              })($p->avatar ?? '');
            $headline   = $p->headline ?? '';
            $bio        = $p->bio ?? '';
            $loc        = trim(($p->brgy ?? '') . (($p->brgy ?? '') && ($p->city ?? '') ? ', ' : '') . ($p->city ?? '') . ((($p->brgy ?? '') || ($p->city ?? '')) && ($p->province ?? '') ? ', ' : '') . ($p->province ?? ''));
            $phoneNo    = $p->phoneNo ?? '';
            $skills     = array_filter(array_map('trim', explode(',', $p->skills ?? '')));
            $creds      = preg_split('/\r\n|\r|\n/', $p->credentials ?? '', -1, PREG_SPLIT_NO_EMPTY);
            $avg        = (float)($p->avgRating ?? 0);
            $days       = array_filter(array_map('trim', explode(',', $p->availability_days ?? '')));
            $edu        = $p->education_level ?? '';
            $school     = $p->school ?? '';
            $yr         = $p->year_graduated ?? '';
            $course     = $p->course ?? '';
            $tesda      = $p->tesda_cert_no ?? '';
            $texp       = $p->tesda_expiry ?? '';
            $tesda_qual = trim($p->tesda_qualification ?? '');

            $ncList = [];
            if (!empty($p->tesda_certs)) {
              $tmp = is_string($p->tesda_certs) ? json_decode($p->tesda_certs, true) : (array)$p->tesda_certs;
              if (is_array($tmp)) {
                foreach ($tmp as $row) {
                  $q = trim((string)($row['qualification'] ?? ''));
                  $n = trim((string)($row['number'] ?? ''));
                  $e = trim((string)($row['expiry'] ?? ''));
                  if ($q !== '' || $n !== '' || $e !== '') {
                    $ncList[] = ['qualification' => $q, 'number' => $n, 'expiry' => $e];
                  }
                }
              }
            }
            if (empty($ncList) && ($tesda_qual || $tesda || $texp)) {
              $ncList[] = ['qualification' => $tesda_qual, 'number' => $tesda, 'expiry' => $texp];
            }

            if (!function_exists('nc_status_badge')) {
              function nc_status_badge($date)
              {
                if (!$date) return '';
                try {
                  $today = new DateTime(date('Y-m-d'));
                  $exp   = new DateTime(substr($date, 0, 10));
                  $diff  = (int)$today->diff($exp)->format('%r%a');
                  if ($diff < 0)   return '<span class="badge-soft" style="border-color:#fecaca;color:#b91c1c;background:#fff1f2">Expired</span>';
                  if ($diff <= 30) return '<span class="badge-soft" style="border-color:#fde68a;color:#b45309;background:#fffbeb">Expiring soon</span>';
                  return '<span class="badge-soft" style="border-color:#bbf7d0;color:#065f46;background:#ecfdf5">Active</span>';
                } catch (\Throwable $e) {
                  return '';
                }
              }
            }


            if (!function_exists('viewer_url_from_abs')) {
              function viewer_url_from_abs($absUrl)
              {
                if (!$absUrl) return null;
                $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                if (!$pathOnly) return null;
                $fParam   = ltrim($pathOnly, '/');
                $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                  $fParam = substr($fParam, strlen($basePath) + 1);
                }
                return site_url('media/preview') . '?f=' . rawurlencode($fParam);
              }
            }

            $certs = [];
            if (!empty($p->certificates)) {
              $tmp = is_string($p->certificates) ? json_decode($p->certificates, true) : (array)$p->certificates;
              if (is_array($tmp)) $certs = array_values(array_filter($tmp));
            } elseif (!empty($p->cert_files)) {
              $tmp = is_string($p->cert_files) ? json_decode($p->cert_files, true) : (array)$p->cert_files;
              if (is_array($tmp)) $certs = array_values(array_filter($tmp));
            }

            $exp        = [];
            if (!empty($p->exp)) {
              $tmp = json_decode($p->exp, true);
              if (is_array($tmp)) $exp = $tmp;
            }
            $langs      = array_filter(array_map('trim', explode(',', $p->languages ?? '')));

            if (!function_exists('is_image_path')) {
              function is_image_path($path)
              {
                $ext = strtolower(pathinfo(is_string($path) ? $path : '', PATHINFO_EXTENSION));
                return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
              }
            }
            if (!function_exists('is_pdf_path')) {
              function is_pdf_path($path)
              {
                $ext = strtolower(pathinfo(is_string($path) ? $path : '', PATHINFO_EXTENSION));
                return $ext === 'pdf';
              }
            }
            $proofByTitle = [];
            if (!empty($certs)) {
              foreach ($certs as $c) {
                $path  = is_array($c) ? (string)($c['path'] ?? '') : (string)$c;
                if ($path === '') continue;

                $title = is_array($c) ? trim((string)($c['title'] ?? '')) : '';
                if ($title === '') {
                  $title = pathinfo($path, PATHINFO_FILENAME);
                }

                $abs   = preg_match('#^https?://#i', $path) ? $path : base_url($path);
                $view  = function_exists('viewer_url_from_abs') ? (viewer_url_from_abs($abs) ?: $abs) : $abs;

                $proofByTitle[mb_strtolower($title)] = $view;
              }
            }

            $tesdaKeys = [];
            if (!empty($ncList)) {
              foreach ($ncList as $nc) {
                $q = trim((string)($nc['qualification'] ?? ''));
                $n = trim((string)($nc['number'] ?? ''));
                if ($q !== '') $tesdaKeys[] = mb_strtolower($q);
                if ($n !== '') $tesdaKeys[] = mb_strtolower($n);
              }
              $tesdaKeys = array_values(array_unique($tesdaKeys));
            }

            // ---- helpers for job listings ----
            if (!function_exists('wd_time_ago')) {
              function wd_time_ago($datetime)
              {
                if (!$datetime) return '';
                $ts = strtotime((string)$datetime);
                if (!$ts) return '';
                $diff = time() - $ts;
                if ($diff < 60)        return 'just now';
                if ($diff < 3600)      return floor($diff / 60) . 'm ago';
                if ($diff < 86400)     return floor($diff / 3600) . 'h ago';
                if ($diff < 2592000)   return floor($diff / 86400) . 'd ago';
                return date('M j, Y', $ts);
              }
            }
            if (!function_exists('wd_budget')) {
              function wd_budget($min, $max, $unit)
              {
                $min = $min !== null && $min !== '' ? (float)$min : null;
                $max = $max !== null && $max !== '' ? (float)$max : null;
                $u   = $unit ? ('/ ' . htmlspecialchars((string)$unit, ENT_QUOTES, 'UTF-8')) : '';
                $fmt = function ($n) {
                  return '₱' . number_format((float)$n, ($n == (int)$n) ? 0 : 2);
                };
                if ($min !== null && $max !== null) {
                  if ($min == $max) return $fmt($min) . ' ' . $u;
                  return $fmt($min) . ' – ' . $fmt($max) . ' ' . $u;
                }
                if ($min !== null) return 'From ' . $fmt($min) . ' ' . $u;
                if ($max !== null) return 'Up to ' . $fmt($max) . ' ' . $u;
                return 'Rate negotiable';
              }
            }

            $recommended_jobs = $recommended_jobs ?? [];
            $my_applications  = $my_applications ?? [];
            $app_counts       = $app_counts ?? ['total' => 0, 'submitted' => 0, 'accepted' => 0, 'withdrawn' => 0];
            $greetName = $first_name !== '' ? $first_name : 'there';
            $pc        = (int)($completion['percent'] ?? 0);
            $missing   = (array)($completion['missing'] ?? []);

            // Experience (parsed once, used below)
            $xp = [];
            if (!empty($p->exp)) {
              $items = json_decode($p->exp, true);
              if (is_array($items)) {
                foreach ($items as $it) {
                  $xp[] = [
                    'role'     => trim($it['role'] ?? ($it['title'] ?? '')),
                    'employer' => trim($it['employer'] ?? ''),
                    'from'     => trim($it['from'] ?? (!empty($it['created_at']) ? date('M Y', strtotime($it['created_at'])) : '')),
                    'to'       => trim($it['to'] ?? ''),
                    'desc'     => trim($it['desc'] ?? ($it['description'] ?? '')),
                  ];
                }
              }
            }
            ?>

            <!-- ============ HERO — core identity first ============ -->
            <section class="wd-hero">
              <div class="wd-hero-id">
                <?php $defaultEsc = htmlspecialchars(base_url('uploads/avatars/avatar.png'), ENT_QUOTES, 'UTF-8'); ?>
                <img class="wd-hero-avatar"
                  src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                  alt="Avatar"
                  onerror="this.onerror=null;this.src='<?= $defaultEsc ?>';">
                <div class="wd-hero-text">
                  <div class="wd-hero-greet">Welcome back 👋</div>
                  <h1 class="wd-hero-name"><?= htmlspecialchars($full_name !== '' ? $full_name : $greetName, ENT_QUOTES, 'UTF-8') ?></h1>
                  <?php if ($headline): ?>
                    <div class="wd-hero-headline"><?= htmlspecialchars($headline, ENT_QUOTES, 'UTF-8') ?></div>
                  <?php endif; ?>
                  <div class="wd-hero-meta">
                    <?php if ($loc): ?><span><i class="mdi mdi-map-marker"></i> <?= htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    <?php if ($phoneNo): ?><span><i class="mdi mdi-phone"></i> <?= htmlspecialchars($phoneNo, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                  </div>
                  <div class="wd-hero-actions">
                    <a href="<?= site_url('worker/feed') ?>" class="wd-btn wd-btn-primary"><i class="mdi mdi-briefcase-search-outline"></i> Browse Open Jobs</a>
                    <a href="<?= site_url('profile/edit') ?>" class="wd-btn wd-btn-ghost"><i class="mdi mdi-account-edit-outline"></i> Edit Profile</a>
                  </div>
                </div>
              </div>
              <div class="wd-hero-stats">
                <div class="wd-hstat">
                  <div class="wd-hstat-ico"><i class="mdi mdi-briefcase-check"></i></div>
                  <div>
                    <div class="wd-hstat-val"><?= (int)($times_hired ?? 0) ?></div>
                    <div class="wd-hstat-lbl">Times Hired</div>
                  </div>
                </div>
                <div class="wd-hstat">
                  <div class="wd-hstat-ico"><i class="mdi mdi-star"></i></div>
                  <div>
                    <div class="wd-hstat-val"><?= number_format((float)($p->avgRating ?? 0), 1) ?></div>
                    <div class="wd-hstat-lbl">Avg. Rating</div>
                  </div>
                </div>
                <div class="wd-hstat">
                  <div class="wd-hstat-ico"><i class="mdi mdi-send-check-outline"></i></div>
                  <div>
                    <div class="wd-hstat-val"><?= (int)($app_counts['total'] ?? 0) ?></div>
                    <div class="wd-hstat-lbl">Applications</div>
                  </div>
                </div>
              </div>
            </section>

            <div class="wd-stack">

              <!-- ===== CORE PROFILE: strength / skills / availability ===== -->
              <div class="wd-row wd-row-3">
                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-shield-check-outline"></i> Profile Strength</h2></div>
                  <div class="wd-completion">
                    <div class="progress-ring" style="--val: <?= $pc ?>; --accent: <?= $pc >= 100 ? '#16a34a' : '#e23b41' ?>;">
                      <span><?= $pc ?>%</span>
                    </div>
                    <div>
                      <div class="wd-completion-title"><?= $pc >= 100 ? 'All set — great work!' : 'Complete your profile' ?></div>
                      <div class="wd-muted"><?= $pc >= 100 ? 'You appear higher in client searches.' : 'A complete profile gets more job offers.' ?></div>
                    </div>
                  </div>
                  <?php if ($pc < 100 && !empty($missing)): ?>
                    <div class="wd-missing">
                      <?php foreach (array_slice($missing, 0, 4) as $m): ?>
                        <span class="wd-miss-chip"><i class="mdi mdi-alert-circle-outline"></i><?= htmlspecialchars($m, ENT_QUOTES, 'UTF-8') ?></span>
                      <?php endforeach; ?>
                      <?php if (count($missing) > 4): ?><span class="wd-muted">+<?= count($missing) - 4 ?> more</span><?php endif; ?>
                    </div>
                    <a href="<?= site_url('profile/edit') ?>" class="wd-btn wd-btn-ghost wd-btn-block wd-btn-sm mt-2"><i class="mdi mdi-pencil"></i> Complete now</a>
                  <?php endif; ?>
                </section>

                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-lightbulb-on-outline"></i> Skills</h2></div>
                  <?php if (!empty($skills)): ?>
                    <div class="wd-chips">
                      <?php foreach ($skills as $s): ?>
                        <span class="wd-chip wd-chip-accent"><?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?><div class="wd-empty wd-empty-sm">No skills added.</div><?php endif; ?>
                </section>

                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-calendar-clock"></i> Availability</h2></div>
                  <?php if (!empty($days)): ?>
                    <div class="wd-chips">
                      <?php foreach ($days as $d): ?>
                        <span class="wd-chip"><?= htmlspecialchars($d, ENT_QUOTES, 'UTF-8') ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?><div class="wd-empty wd-empty-sm">No availability set.</div><?php endif; ?>
                </section>
              </div>

              <!-- ===== About + Experience ===== -->
              <div class="wd-row wd-row-2">
                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-information-outline"></i> About</h2></div>
                  <?php if ($bio): ?>
                    <div class="wd-about"><?= nl2br(htmlspecialchars($bio, ENT_QUOTES, 'UTF-8')) ?></div>
                  <?php else: ?><div class="wd-empty wd-empty-sm">No bio yet. Add a short summary about your background and strengths.</div><?php endif; ?>
                </section>

                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-briefcase-outline"></i> Work Experience</h2></div>
                  <?php if (empty($xp)): ?>
                    <div class="wd-empty wd-empty-sm">No experience added yet. Adding past work boosts your hire rate.</div>
                  <?php else: ?>
                    <div class="wd-xp">
                      <?php foreach ($xp as $row): ?>
                        <div class="wd-xp-item">
                          <div class="wd-xp-role">
                            <?= htmlspecialchars($row['role'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($row['employer'])): ?><span class="wd-xp-meta"> • <?= htmlspecialchars($row['employer'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                          </div>
                          <div class="wd-xp-meta"><?= htmlspecialchars(trim(($row['from'] ?? '') . ' - ' . ($row['to'] ?? '')), ENT_QUOTES, 'UTF-8') ?></div>
                          <?php if (!empty($row['desc'])): ?><div class="wd-xp-desc"><?= nl2br(htmlspecialchars($row['desc'], ENT_QUOTES, 'UTF-8')) ?></div><?php endif; ?>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </section>
              </div>

              <!-- ===== Education + Documents ===== -->
              <div class="wd-row wd-row-2">
                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-school-outline"></i> Education &amp; Links</h2></div>
                  <?php
                  $eduPrimary = trim((string)$course) !== '' ? trim((string)$course) : trim((string)$edu);
                  $eduParts = array_filter([$eduPrimary, trim((string)$school), trim((string)$yr)], function ($v) {
                    return $v !== '';
                  });
                  $eduText = implode(' • ', $eduParts);
                  $hasRight = ($eduText !== '') || (!empty($p->portfolio_url) || !empty($p->facebook_url));
                  ?>
                  <?php if (!$hasRight): ?>
                    <div class="wd-empty wd-empty-sm">No education or links yet.</div>
                  <?php else: ?>
                    <?php if ($eduText !== ''): ?>
                      <div class="wd-kv"><i class="mdi mdi-school-outline"></i> <?= htmlspecialchars($eduText, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <?php if (!empty($p->portfolio_url)): ?>
                      <div class="wd-kv"><i class="mdi mdi-link-variant"></i> <a href="<?= htmlspecialchars($p->portfolio_url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Portfolio</a></div>
                    <?php endif; ?>
                    <?php if (!empty($p->facebook_url)): ?>
                      <div class="wd-kv"><i class="mdi mdi-facebook"></i> <a href="<?= htmlspecialchars($p->facebook_url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Facebook</a></div>
                    <?php endif; ?>
                  <?php endif; ?>
                </section>

                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-folder-outline"></i> Saved Documents</h2></div>
                  <?php
                  $docs = $docs ?? [];
                  $extOf = function ($path) {
                    return strtolower(pathinfo((string)$path, PATHINFO_EXTENSION));
                  };
                  $makeFileHref = function ($pth) {
                    $rel = ltrim((string)$pth, '/');
                    $isAbs     = preg_match('#^https?://#i', $rel);
                    $isUploads = preg_match('#^uploads/#i',  $rel);
                    if ($isUploads) return site_url('media/preview') . '?f=' . rawurlencode($rel);
                    return $isAbs ? $rel : base_url($rel);
                  };
                  ?>
                  <?php if (empty($docs)): ?>
                    <div class="wd-empty wd-empty-sm">No saved documents yet.</div>
                  <?php else: ?>
                    <div class="wd-docs">
                      <?php foreach ($docs as $r):
                        $name  = trim((string)($r->doc_name    ?? $r['doc_name']    ?? ''));
                        $type  = trim((string)($r->doc_type    ?? $r['doc_type']    ?? ''));
                        $expd  = trim((string)($r->expiry_date ?? $r['expiry_date'] ?? ''));
                        $file  = (string)($r->file_path ?? $r['file_path'] ?? '');
                        $href  = $file !== '' ? $makeFileHref($file) : '';
                        $ext   = $extOf($file);
                        $badge = ($expd !== '' && function_exists('nc_status_badge')) ? nc_status_badge($expd) : '';
                      ?>
                        <div class="wd-doc">
                          <i class="mdi <?= $ext === 'pdf' ? 'mdi-file-pdf-box' : 'mdi-file-document-outline' ?>"></i>
                          <div class="wd-doc-info">
                            <div class="wd-doc-name"><?= htmlspecialchars($name ?: '(Untitled)', ENT_QUOTES, 'UTF-8') ?></div>
                            <?php if ($type): ?><div class="wd-muted"><?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                            <?php if ($badge): ?><div class="mt-1"><?= $badge ?></div><?php endif; ?>
                          </div>
                          <?php if ($href): ?>
                            <a class="wd-doc-link" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= $ext ? strtoupper($ext) : 'View' ?></a>
                          <?php endif; ?>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </section>
              </div>

              <!-- ===== FIND WORK (below the core profile) ===== -->
              <section class="wd-card">
                <div class="wd-card-head">
                  <h2><i class="mdi mdi-briefcase-search"></i> Recommended Work</h2>
                  <a href="<?= site_url('worker/feed') ?>" class="wd-link">Browse all <i class="mdi mdi-arrow-right"></i></a>
                </div>

                <?php if (empty($recommended_jobs)): ?>
                  <div class="wd-empty">
                    <i class="mdi mdi-briefcase-outline"></i>
                    <div>No open jobs right now. Check the job board for new postings.</div>
                    <a href="<?= site_url('worker/feed') ?>" class="wd-btn wd-btn-primary wd-btn-sm mt-2"><i class="mdi mdi-refresh"></i> Open Job Board</a>
                  </div>
                <?php else: ?>
                  <div class="wd-jobs">
                    <?php foreach ($recommended_jobs as $job):
                      $jLoc = trim(implode(', ', array_filter([
                        trim((string)($job->brgy ?? '')),
                        trim((string)($job->city ?? '')),
                        trim((string)($job->province ?? '')),
                      ])));
                      $jDesc = trim((string)($job->description ?? ''));
                    ?>
                      <article class="wd-job">
                        <div class="wd-job-top">
                          <div class="wd-job-logo"><i class="mdi mdi-briefcase-variant-outline"></i></div>
                          <div class="wd-job-headings">
                            <h3 class="wd-job-title"><?= htmlspecialchars((string)($job->title ?? 'Untitled job'), ENT_QUOTES, 'UTF-8') ?></h3>
                            <div class="wd-job-meta">
                              <?php if (!empty($job->category)): ?>
                                <span><i class="mdi mdi-shape-outline"></i> <?= htmlspecialchars((string)$job->category, ENT_QUOTES, 'UTF-8') ?></span>
                              <?php endif; ?>
                              <?php if ($jLoc !== ''): ?>
                                <span><i class="mdi mdi-map-marker-outline"></i> <?= htmlspecialchars($jLoc, ENT_QUOTES, 'UTF-8') ?></span>
                              <?php endif; ?>
                              <span><i class="mdi mdi-clock-outline"></i> <?= htmlspecialchars(wd_time_ago($job->created_at ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                          </div>
                          <div class="wd-job-pay"><?= wd_budget($job->budget_min ?? null, $job->budget_max ?? null, $job->rate_unit ?? null) ?></div>
                        </div>

                        <?php if ($jDesc !== ''): ?>
                          <p class="wd-job-desc"><?= htmlspecialchars(mb_strimwidth($jDesc, 0, 200, '…'), ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>

                        <div class="wd-job-foot">
                          <div class="wd-job-tags">
                            <?php if (!empty($job->employment_term)): ?>
                              <span class="wd-tag"><i class="mdi mdi-briefcase-clock-outline"></i> <?= htmlspecialchars((string)$job->employment_term, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <?php if (!empty($job->project_duration_value) && !empty($job->project_duration_unit)): ?>
                              <span class="wd-tag"><i class="mdi mdi-calendar-range"></i> <?= (int)$job->project_duration_value . ' ' . htmlspecialchars((string)$job->project_duration_unit, ENT_QUOTES, 'UTF-8') ?>(s)</span>
                            <?php endif; ?>
                          </div>
                          <a href="<?= site_url('worker/feed') ?>" class="wd-btn wd-btn-primary wd-btn-sm">
                            <i class="mdi mdi-send-outline"></i> View &amp; Apply
                          </a>
                        </div>
                      </article>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </section>

              <!-- ===== My Applications ===== -->
              <section class="wd-card">
                <div class="wd-card-head">
                  <h2><i class="mdi mdi-clipboard-text-clock-outline"></i> My Applications</h2>
                  <div class="wd-app-pills">
                    <span class="wd-pill wd-pill-blue"><?= (int)($app_counts['submitted'] ?? 0) ?> pending</span>
                    <span class="wd-pill wd-pill-green"><?= (int)($app_counts['accepted'] ?? 0) ?> accepted</span>
                  </div>
                </div>

                <?php if (empty($my_applications)): ?>
                  <div class="wd-empty">
                    <i class="mdi mdi-send-outline"></i>
                    <div>You haven't applied to any jobs yet.</div>
                  </div>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table wd-table">
                      <thead>
                        <tr>
                          <th>Job</th>
                          <th>Location</th>
                          <th>Expected Rate</th>
                          <th>Status</th>
                          <th>Applied</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($my_applications as $a):
                          $st     = strtolower((string)($a->status ?? ''));
                          $aLoc   = trim(implode(', ', array_filter([trim((string)($a->city ?? '')), trim((string)($a->province ?? ''))])));
                          $stMap  = [
                            'submitted' => ['Pending', 'wd-badge-blue'],
                            'accepted'  => ['Accepted', 'wd-badge-green'],
                            'withdrawn' => ['Withdrawn', 'wd-badge-gray'],
                            'rejected'  => ['Not selected', 'wd-badge-red'],
                          ];
                          [$stLabel, $stClass] = $stMap[$st] ?? [ucfirst($st ?: '—'), 'wd-badge-gray'];
                          $rate = ($a->expected_rate ?? '') !== '' ? ('₱' . number_format((float)$a->expected_rate, 2) . ($a->rate_unit ? ' / ' . htmlspecialchars((string)$a->rate_unit, ENT_QUOTES, 'UTF-8') : '')) : '—';
                        ?>
                          <tr>
                            <td data-label="Job" class="fw-medium"><?= htmlspecialchars((string)($a->title ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td data-label="Location"><?= $aLoc !== '' ? htmlspecialchars($aLoc, ENT_QUOTES, 'UTF-8') : '—' ?></td>
                            <td data-label="Expected Rate"><?= $rate ?></td>
                            <td data-label="Status"><span class="wd-badge <?= $stClass ?>"><?= htmlspecialchars($stLabel, ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td data-label="Applied" class="text-muted"><?= htmlspecialchars(wd_time_ago($a->created_at ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td data-label="">
                              <?php if ($st === 'submitted'): ?>
                                <a class="wd-link-danger" href="<?= site_url('worker/feed/withdraw/' . (int)($a->id ?? 0)) ?>" onclick="return confirm('Withdraw this application?');">Withdraw</a>
                              <?php else: ?>—<?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </section>

              <!-- ===== Analytics: Service mix + Reviews ===== -->
              <div
                id="jmWorkerChartData"
                hidden
                data-labels="<?= htmlspecialchars(json_encode($labels ?? []), ENT_QUOTES, 'UTF-8') ?>"
                data-values="<?= htmlspecialchars(json_encode($counts ?? []), ENT_QUOTES, 'UTF-8') ?>"></div>

              <div class="wd-row wd-row-2">
                <section class="wd-card" id="svcMixPanel" data-mix-url="<?= site_url('services/mix') ?>">
                  <div class="wd-card-head">
                    <h2><i class="mdi mdi-chart-pie"></i> Types of Service</h2>
                    <div id="svcMixPills" class="wd-app-pills" style="display:none"></div>
                  </div>
                  <div class="wd-muted mb-2" id="svcMixCaption">Share of jobs by skill</div>
                  <div class="wd-svcmix">
                    <div class="wd-svcmix-chart">
                      <canvas id="svcMixChart" height="280"></canvas>
                    </div>
                    <div id="svcMixLegend" class="wd-svcmix-legend"></div>
                  </div>
                </section>

                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-comment-text-outline"></i> Latest Reviews</h2></div>
                  <?php if (empty($latest_reviews)): ?>
                    <div class="wd-empty"><i class="mdi mdi-star-outline"></i>
                      <div>No reviews yet. Complete jobs to earn client feedback.</div>
                    </div>
                  <?php else: ?>
                    <div class="table-responsive">
                      <table class="table wd-table">
                        <thead>
                          <tr>
                            <th>Client</th>
                            <th>Job</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>When</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($latest_reviews as $r): $stars = (int)($r->rating ?? 0);
                            $comment = trim((string)($r->comment ?? '')); ?>
                            <tr>
                              <td data-label="Client" class="fw-medium"><?= htmlspecialchars($r->client_name ?? '—', ENT_QUOTES) ?></td>
                              <td data-label="Job"><?= htmlspecialchars($r->job_title ?? '—', ENT_QUOTES) ?></td>
                              <td data-label="Rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                  <i class="mdi <?= $i <= $stars ? 'mdi-star text-warning' : 'mdi-star-outline text-muted' ?>"></i>
                                <?php endfor; ?>
                              </td>
                              <td data-label="Comment">
                                <div class="rv-clamp-2"><?= $comment !== '' ? nl2br(htmlspecialchars($comment, ENT_QUOTES)) : '—' ?></div>
                              </td>
                              <td data-label="When" class="text-muted"><?= htmlspecialchars($r->time_ago ?? '', ENT_QUOTES) ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>
                </section>
              </div>

            </div>

            <?php $this->load->view('includes_footer'); ?>
          </div>
        </div>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="<?= base_url('assets/js/dashboard-worker.js?v=1.0.0') ?>"></script>


      <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
      <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
      <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
      <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>
