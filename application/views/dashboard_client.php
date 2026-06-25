<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'JobMatch — Client Dashboard', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/universal.css') ?>">

  <link rel="stylesheet" href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-client.css?v=2.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
</head>

<body>
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

            $first_name   = $p->first_name ?? ($p->fName ?? ($this->session->userdata('first_name') ?: ''));
            $last_name    = $p->last_name  ?? ($p->lName ?? '');
            $display_name = trim(($last_name ?: '') . ($first_name ? ', ' . $first_name : ''));
            $seed_name    = trim(($first_name ?: '') . ' ' . ($last_name ?: ''));
            $seed         = $seed_name !== '' ? $seed_name : ($this->session->userdata('first_name') ?: 'Client');

            $rawAvatar = trim((string)($this->session->userdata('avatar') ?: ($p->avatar ?? '')));

            // Normalize local path once
            $rawAvatarClean = ltrim(str_replace(['\\', './'], ['/', ''], $rawAvatar), '/');

            // Build absolute avatar URL
            if ($rawAvatar !== '' && preg_match('#^https?://#i', $rawAvatar)) {
              $avatarAbs = $rawAvatar;
            } elseif ($rawAvatarClean !== '') {
              $avatarAbs = base_url($rawAvatarClean);
            } else {
              $avatarAbs = base_url('uploads/avatars/avatar.png');
            }

            // If local path, confirm it exists; else fallback
            if ($rawAvatarClean !== '' && !preg_match('#^https?://#i', $rawAvatar)) {
              $absFile = FCPATH . $rawAvatarClean;
              if (!is_file($absFile)) {
                $avatarAbs = base_url('uploads/avatars/avatar.png');
              }
            }

            // Cache-bust: prefer updated_at; else filemtime if local; else 1
            $ver = 1;

            if ($rawAvatarClean !== '' && !preg_match('#^https?://#i', $rawAvatar) && is_file(FCPATH . $rawAvatarClean)) {
              $ver = filemtime(FCPATH . $rawAvatarClean) ?: 1;
            } elseif (!empty($p->updated_at)) {
              $ver = strtotime($p->updated_at) ?: 1;
            }


            $avatarUrl = $avatarAbs . (strpos($avatarAbs, '?') === false ? '?' : '&') . 'v=' . $ver;

            $phoneNo = $p->phoneNo ?? '';
            $loc     = trim(
              ($p->brgy ?? '') .
                (($p->brgy ?? '') && ($p->city ?? '') ? ', ' : '') . ($p->city ?? '') .
                ((($p->brgy ?? '') || ($p->city ?? '')) && ($p->province ?? '') ? ', ' : '') . ($p->province ?? '')
            );
            $address = $p->address ?? '';

            $company    = trim((string)($p->companyName ?? ''));
            $has_company_position_field = function_exists('client_has_company_position_field') ? client_has_company_position_field() : false;
            $company_position = ($has_company_position_field && isset($p->company_position)) ? trim((string)$p->company_position) : '';
            $employer   = trim((string)($p->employer ?? ''));
            $biz_name   = trim((string)($p->business_name ?? ''));
            $biz_loc    = trim((string)($p->business_location ?? ''));

            $org_label  = function_exists('client_org_label') ? client_org_label($p) : '';
            $is_individual_employer = function_exists('client_is_individual_employer') ? client_is_individual_employer($p) : false;

            $has_business_details = ($company !== '' || ($has_company_position_field && $company_position !== '') || $employer !== '' || $biz_name !== '' || $biz_loc !== '');

            $id_image = $p->id_image ?? '';
            $permit   = $p->business_permit ?? '';

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
            if (!function_exists('is_image_path')) {
              function is_image_path($path)
              {
                $ext = strtolower(pathinfo(is_string($path) ? $path : '', PATHINFO_EXTENSION));
                return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
              }
            }
            if (!function_exists('is_pdf_path')) {
              function is_pdf_path($path)
              {
                $ext = strtolower(pathinfo(is_string($path) ? $path : '', PATHINFO_EXTENSION));
                return $ext === 'pdf';
              }
            }

            $idAbs           = !empty($id_image) ? base_url(ltrim(str_replace('\\', '/', $id_image), '/')) : null;
            $permitAbs       = !empty($permit)   ? base_url(ltrim(str_replace('\\', '/', $permit), '/'))   : null;
            $idViewerUrl     = $idAbs     ? (viewer_url_from_abs($idAbs)     ?: $idAbs)     : null;
            $permitViewerUrl = $permitAbs ? (viewer_url_from_abs($permitAbs) ?: $permitAbs) : null;

            $jobs_posted = (int)($stats['jobs_posted'] ?? 0);
            $jobs_active = (int)($stats['jobs_active'] ?? 0);
            $hires_total = (int)($stats['hires_total'] ?? 0);
            $spend_total = (float)($stats['spend_total'] ?? 0);

            $certs = [];
            if (!empty($p->certificates)) {
              $tmp = is_string($p->certificates) ? json_decode($p->certificates, true) : (array)$p->certificates;
              if (is_array($tmp)) $certs = array_values(array_filter($tmp));
            } elseif (!empty($p->cert_files)) {
              $tmp = is_string($p->cert_files) ? json_decode($p->cert_files, true) : (array)$p->cert_files;
              if (is_array($tmp)) $certs = array_values(array_filter($tmp));
            }

            $isVerified = ($first_name && $last_name && !empty($id_image));
            $greetName  = $first_name !== '' ? $first_name : 'there';
            ?>

            <!-- ============ HERO — identity first ============ -->
            <section class="wd-hero">
              <div class="wd-hero-id">
                <?php $defaultEsc = htmlspecialchars(base_url('uploads/avatars/avatar.png'), ENT_QUOTES, 'UTF-8'); ?>
                <img class="wd-hero-avatar"
                  src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                  alt="Avatar"
                  onerror="this.onerror=null;this.src='<?= $defaultEsc ?>';">
                <div class="wd-hero-text">
                  <div class="wd-hero-greet">Welcome back 👋</div>
                  <h1 class="wd-hero-name">
                    <?= htmlspecialchars($display_name !== '' ? $display_name : ($p->email ?? $this->session->userdata('email') ?? 'Client'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="wd-verified <?= $isVerified ? '' : 'is-unverified' ?>" title="Account status">
                      <i class="mdi <?= $isVerified ? 'mdi-check-decagram-outline' : 'mdi-alert-decagram-outline' ?>"></i>
                      <?= $isVerified ? 'Verified' : 'Verification needed' ?>
                    </span>
                  </h1>
                  <?php if ($org_label !== ''): ?>
                    <div class="wd-hero-org">
                      <?= htmlspecialchars($org_label, ENT_QUOTES, 'UTF-8') ?>
                      <?php if ($has_company_position_field && $company_position !== ''): ?>
                        <small><?= htmlspecialchars($company_position, ENT_QUOTES, 'UTF-8') ?></small>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <div class="wd-hero-meta">
                    <?php if ($loc): ?><span><i class="mdi mdi-map-marker"></i> <?= htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    <?php if ($phoneNo): ?><span><i class="mdi mdi-phone"></i> <?= htmlspecialchars($phoneNo, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    <?php if ($address): ?><span><i class="mdi mdi-home-map-marker"></i> <?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                  </div>
                  <div class="wd-hero-actions">
                    <a href="<?= site_url('projects/create') ?>" class="wd-btn wd-btn-primary"><i class="mdi mdi-briefcase-plus-outline"></i> Post a Job</a>
                    <a href="<?= site_url('client/edit') ?>" class="wd-btn wd-btn-ghost"><i class="mdi mdi-account-edit-outline"></i> Edit Profile</a>
                  </div>
                </div>
              </div>
            </section>

            <!-- ============ KPI CARDS ============ -->
            <div class="wd-kpis">
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-red"><i class="mdi mdi-briefcase-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= $jobs_posted ?></div>
                  <div class="wd-kpi-lbl">Jobs Posted</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-green"><i class="mdi mdi-briefcase-check"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= $jobs_active ?></div>
                  <div class="wd-kpi-lbl">Active Jobs</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-amber"><i class="mdi mdi-account-multiple-check"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= $hires_total ?></div>
                  <div class="wd-kpi-lbl">Total Hires</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-indigo"><i class="mdi mdi-cash-multiple"></i></div>
                <div>
                  <div class="wd-kpi-val">₱<?= number_format($spend_total, 2) ?></div>
                  <div class="wd-kpi-lbl">Total Spend</div>
                </div>
              </div>
            </div>

            <div class="wd-stack">

              <!-- ============ Business + Verification ============ -->
              <div class="wd-row wd-row-2">
                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-briefcase-outline"></i> Business / Project</h2></div>
                  <?php if (!$has_business_details && !$is_individual_employer): ?>
                    <div class="wd-empty wd-empty-sm">No business or project details yet.</div>
                  <?php else: ?>
                    <?php if ($is_individual_employer): ?>
                      <div class="wd-kv"><span class="k">Employer Type</span><span class="v">Individual Employer</span></div>
                    <?php endif; ?>
                    <?php if ($company !== ''): ?><div class="wd-kv"><span class="k">Company</span><span class="v"><?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></span></div><?php endif; ?>
                    <?php if ($has_company_position_field && $company_position !== ''): ?><div class="wd-kv"><span class="k">Position</span><span class="v"><?= htmlspecialchars($company_position, ENT_QUOTES, 'UTF-8') ?></span></div><?php endif; ?>
                    <?php if ($employer !== ''): ?><div class="wd-kv"><span class="k">Employer</span><span class="v"><?= htmlspecialchars($employer, ENT_QUOTES, 'UTF-8') ?></span></div><?php endif; ?>
                    <?php if ($biz_name !== ''): ?><div class="wd-kv"><span class="k">Project / Business</span><span class="v"><?= htmlspecialchars($biz_name, ENT_QUOTES, 'UTF-8') ?></span></div><?php endif; ?>
                    <?php if ($biz_loc !== ''): ?><div class="wd-kv"><span class="k">Business Location</span><span class="v"><?= htmlspecialchars($biz_loc, ENT_QUOTES, 'UTF-8') ?></span></div><?php endif; ?>
                  <?php endif; ?>
                </section>

                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-shield-account-outline"></i> Verification &amp; Documents</h2></div>

                  <div class="wd-docs">
                    <div class="wd-doc">
                      <i class="mdi mdi-id-card"></i>
                      <div class="wd-doc-info">
                        <div class="wd-doc-name">Government ID</div>
                        <div class="wd-muted"><?= $idViewerUrl ? 'Uploaded' : 'Not uploaded' ?></div>
                      </div>
                      <?php if ($idViewerUrl): ?>
                        <a class="wd-doc-link" href="<?= htmlspecialchars($idViewerUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View</a>
                      <?php endif; ?>
                    </div>
                    <div class="wd-doc">
                      <i class="mdi mdi-file-certificate-outline"></i>
                      <div class="wd-doc-info">
                        <div class="wd-doc-name">Business Permit</div>
                        <div class="wd-muted"><?= $permitViewerUrl ? 'Uploaded' : 'Not uploaded' ?></div>
                      </div>
                      <?php if ($permitViewerUrl): ?>
                        <a class="wd-doc-link" href="<?= htmlspecialchars($permitViewerUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View</a>
                      <?php endif; ?>
                    </div>
                  </div>

                  <?php if (!empty($certs)): ?>
                    <div style="margin-top:14px">
                      <div class="wd-muted" style="font-weight:600;margin-bottom:2px">Certificates</div>
                      <?php
                      $items = [];
                      foreach ($certs as $c) {
                        if (is_string($c)) {
                          $path  = $c;
                          $title = pathinfo($c, PATHINFO_FILENAME);
                        } elseif (is_array($c) && !empty($c['path'])) {
                          $path  = (string)$c['path'];
                          $title = trim($c['title'] ?? pathinfo($path, PATHINFO_FILENAME));
                        } else {
                          continue;
                        }
                        $abs  = preg_match('#^https?://#i', $path) ? $path : base_url($path);
                        $view = viewer_url_from_abs($abs) ?: $abs;
                        $items[] = ['path' => $path, 'title' => $title, 'abs' => $abs, 'view' => $view];
                      }
                      ?>
                      <div class="wd-certs">
                        <?php foreach ($items as $it):
                          $img = is_image_path($it['path']);
                          $pdf = is_pdf_path($it['path']);
                        ?>
                          <div class="c-card"
                            <?= $img ? 'style="background-image:url(\'' . htmlspecialchars($it['abs'], ENT_QUOTES) . '\')"' : '' ?>>
                            <?php if (!$img): ?>
                              <div class="text-center">
                                <i class="mdi <?= $pdf ? 'mdi-file-pdf-box' : 'mdi-file' ?>" style="font-size:38px;<?= $pdf ? 'color:#b91c1c' : 'color:#64748b' ?>"></i>
                                <div style="font-size:11px;margin-top:4px;color:#475569;max-width:92%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                  <?= htmlspecialchars($it['title'], ENT_QUOTES) ?>
                                </div>
                              </div>
                            <?php else: ?>
                              <span class="file-pill"><?= htmlspecialchars(strtoupper(pathinfo($it['path'], PATHINFO_EXTENSION)), ENT_QUOTES) ?></span>
                            <?php endif; ?>
                            <div class="c-overlay">
                              <a href="<?= htmlspecialchars($it['view'], ENT_QUOTES) ?>" target="_blank" rel="noopener" class="c-tag">
                                <i class="mdi mdi-eye"></i> View
                              </a>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if (!$isVerified || empty($permit)): ?>
                    <div class="wd-empty wd-empty-sm" style="margin-top:12px">
                      <i class="mdi mdi-shield-alert-outline"></i>
                      <div>Complete your verification to build trust with workers.</div>
                      <a href="<?= site_url('client/edit') ?>" class="wd-btn wd-btn-primary wd-btn-sm" style="margin-top:6px"><i class="mdi mdi-upload"></i> Upload Documents</a>
                    </div>
                  <?php endif; ?>
                </section>
              </div>

              <!-- ============ Recent Jobs ============ -->
              <section class="wd-card">
                <div class="wd-card-head">
                  <h2><i class="mdi mdi-clipboard-text-outline"></i> Recent Jobs</h2>
                  <a href="<?= site_url('projects/active') ?>" class="wd-link">View all <i class="mdi mdi-arrow-right"></i></a>
                </div>
                <?php if (empty($recent_jobs)): ?>
                  <div class="wd-empty">
                    <i class="mdi mdi-briefcase-outline"></i>
                    <div>No jobs yet. Post your first job to start hiring.</div>
                    <a href="<?= site_url('projects/create') ?>" class="wd-btn wd-btn-primary wd-btn-sm" style="margin-top:6px"><i class="mdi mdi-briefcase-plus-outline"></i> Post a Job</a>
                  </div>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table wd-table">
                      <thead>
                        <tr>
                          <th>Title</th>
                          <th>Status</th>
                          <th>Applicants</th>
                          <th>Posted</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($recent_jobs as $j):
                          $status = strtolower($j->status ?? 'open');
                          $icon   = $status === 'open' ? 'mdi-lock-open-outline' : ($status === 'hired' ? 'mdi-account-check' : 'mdi-archive-outline');
                          $badge  = $status === 'open' ? 'wd-badge-green' : ($status === 'hired' ? 'wd-badge-blue' : 'wd-badge-gray');
                          $label  = ucfirst($status);
                        ?>
                          <tr>
                            <td data-label="Title" class="fw-medium"><?= htmlspecialchars($j->title ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td data-label="Status"><span class="wd-badge <?= $badge ?>"><i class="mdi <?= $icon ?>"></i> <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td data-label="Applicants"><?= (int)($j->applicants ?? 0) ?></td>
                            <td data-label="Posted" class="text-muted"><?= htmlspecialchars($j->posted_ago ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td data-label="">
                              <a class="wd-link" href="<?= site_url('projects/active') ?>">View</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </section>

            </div>

          </div>
        </div>

        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
  <script src="<?= base_url('assets/js/dashboard-client.js?v=1.0.0') ?>"></script>

</body>

</html>
