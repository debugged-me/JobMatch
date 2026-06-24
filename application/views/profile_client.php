<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Client Profile', ENT_QUOTES, 'UTF-8') ?> • JobMatch DavOr</title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/universal.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.9') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <!-- CSRF for AJAX -->
  <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
  <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">

  <style>
    :root {
      --blue-900: #1e3a8a;
      --blue-700: #1d4ed8;
      --blue-600: #2563eb;
      --blue-500: #2563eb;
      --gold-700: #c89113;
      --gold-600: #f0b429;
      --silver-600: #a7afba;
      --silver-500: #c0c6d0;
      --silver-300: #d9dee7;
      --silver-200: #e7ebf2;
      --silver-100: #f6f8fc;
      --radius: 12px;
      --pad-panel: 12px;
      --fs-title: 20px;
      --fs-sub: 12.5px;
      --fs-body: 13px;
      --fs-kpi: 18px;
      --fs-kpi-label: 12px;
      --shadow-1: 0 6px 16px rgba(2, 6, 23, .08);
    }

    body {
      font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      font-size: var(--fs-body);
      background: linear-gradient(180deg, var(--silver-100), #eef2f7 60%, #e9edf3 100%);
      color: #0f172a;
    }

    .container-fluid.page-body-wrapper .main-panel .content-wrapper {
      padding: 0 12px;
      padding-top: .6rem;
    }

    .app {
      width: 100%;
      padding: 0 12px;
    }

    .eyebrow {
      font-size: 12px;
      color: #64748b;
      font-weight: 600;
      letter-spacing: .2px;
      margin: 4px 0 8px
    }

    .profile-card {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      background: #fff;
      box-shadow: var(--shadow-1);
      border: 1px solid var(--silver-300)
    }

    .profile-cover {
      height: 120px;
      background: #fff url('<?= base_url("assets/images/banner.png") ?>') center top/contain no-repeat
    }

    .profile-brandbar {
      position: absolute;
      left: 0;
      top: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--blue-900), var(--blue-700), var(--blue-500))
    }

    .profile-gold {
      height: 3px;
      background: linear-gradient(90deg, var(--gold-700), var(--gold-600))
    }

    .profile-main {
      display: grid;
      grid-template-columns: 84px 1fr auto;
      gap: 14px;
      align-items: center;
      padding: 12px
    }

    .avatar {
      width: 84px;
      height: 84px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
      margin-top: -60px;
      box-shadow: 0 8px 18px rgba(2, 6, 23, .14)
    }

    .profile-name {
      font-size: var(--fs-title);
      font-weight: 800;
      margin: 0;
      color: var(--blue-900)
    }

    .profile-sub {
      color: #6b7280;
      font-size: var(--fs-sub)
    }

    .meta {
      color: #64748b;
      font-size: 12.5px
    }

    .badge-soft {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .25rem .5rem;
      border-radius: 9999px;
      border: 1px solid var(--silver-300);
      background: #fff;
      font-weight: 700;
      font-size: 12px
    }

    .panel {
      background: #fff;
      border: 1px solid var(--silver-300);
      border-radius: 12px;
      box-shadow: var(--shadow-1);
      padding: var(--pad-panel)
    }

    .panel--wide {
      grid-column: 1/-1
    }

    .panel-head {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px
    }

    .empty {
      color: #6b7280;
      border: 1px dashed var(--silver-300);
      border-radius: 10px;
      padding: 10px;
      text-align: center;
      background: linear-gradient(180deg, #fff, #fbfcff)
    }

    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px
    }

    @media (max-width:992px) {
      .kpi-grid {
        grid-template-columns: 1fr 1fr
      }
    }

    @media (max-width:600px) {
      .kpi-grid {
        grid-template-columns: 1fr
      }
    }

    .kpi .label {
      font-size: var(--fs-kpi-label);
      color: #6b7280
    }

    .kpi .value {
      font-size: var(--fs-kpi);
      font-weight: 800
    }

    .layout {
      margin-top: 12px;
      display: grid;
      grid-template-columns: 6fr 5fr;
      gap: 12px
    }

    @media (max-width:992px) {
      .layout {
        grid-template-columns: 1fr
      }
    }

    .btn-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
      background: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #475569;
      cursor: pointer;
      transition: .16s border-color, .16s transform, .16s color;
      margin-left: 6px;
    }

    .btn-icon:hover {
      border-color: #cfd8e3;
      color: #0f172a;
      transform: translateY(-1px)
    }

    .btn-icon.ok:hover {
      border-color: rgba(22, 163, 74, .35);
      color: #166534
    }

    .btn-icon.info:hover {
      border-color: #bfdbfe;
      color: #2563eb
    }

    .btn-icon.bad:hover {
      border-color: rgba(239, 68, 68, .35);
      color: #991b1b
    }

    .c-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 10px
    }

    .c-card {
      position: relative;
      height: 130px;
      border: 1px solid var(--silver-300);
      border-radius: 10px;
      overflow: hidden;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 6px
    }

    .c-card.hasimg {
      background-size: cover;
      background-position: center
    }

    .c-overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding: 8px;
      background: linear-gradient(to top, rgba(15, 23, 42, .55), rgba(15, 23, 42, .05) 55%, rgba(15, 23, 42, 0))
    }

    .c-tag {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .35rem .6rem;
      border-radius: 9999px;
      background: linear-gradient(180deg, var(--blue-700), var(--blue-500));
      color: #fff;
      font-weight: 800;
      font-size: 12px;
      text-decoration: none
    }

    .file-pill {
      position: absolute;
      top: 8px;
      left: 8px;
      background: #fff;
      border: 1px solid var(--silver-300);
      border-radius: 9999px;
      padding: .15rem .45rem;
      font-size: 11px;
      font-weight: 700;
      color: #334155
    }

    /* responsive table labels for Recent Jobs */
    @media (max-width: 768px) {
      .panel .table thead {
        display: none;
      }

      .panel .table {
        border-collapse: separate;
        border-spacing: 0 12px;
      }

      .panel .table tbody,
      .panel .table tr,
      .panel .table td {
        display: block;
        width: 100%;
      }

      .panel .table tbody tr {
        background: #fff;
        border: 1px solid var(--silver-300);
        border-radius: 14px;
        box-shadow: var(--shadow-1);
        padding: 12px;
      }

      .panel .table tbody tr td {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 8px;
        align-items: baseline;
        padding: 8px 0;
        border-bottom: 1px dashed #e5e7eb;
      }

      .panel .table tbody tr td:last-child {
        border-bottom: 0;
        padding-bottom: 2px;
      }

      .panel .table td::before {
        content: attr(data-th);
        text-transform: uppercase;
        font: 700 10px/1 Inter, system-ui;
        color: #64748b;
        letter-spacing: .35px;
        align-self: start;
      }
    }
  </style>
</head>

<body>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="app">
            <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Client Profile', ENT_QUOTES, 'UTF-8') ?></div>

            <?php
            // ——— SAME DATA HYDRATION (no session fallbacks)
            $c = isset($c) ? $c : (isset($profile) ? $profile : (object)[]);
            $first_name   = $c->first_name ?? $c->fName ?? '';
            $last_name    = $c->last_name  ?? $c->lName  ?? '';
            $display_name = trim(($last_name ? $last_name . ', ' : '') . $first_name);
            if ($display_name === '') $display_name = $c->email ?? 'Client';

            $avatarUrl = function_exists('avatar_url')
              ? avatar_url($c->avatar ?? '')
              : (function ($raw) {
                $raw = trim((string)$raw);
                if ($raw !== '' && preg_match('#^https?://#i', $raw)) return $raw;
                return $raw !== '' ? base_url(str_replace('\\', '/', $raw)) : base_url('uploads/avatars/avatar.png');
              })($c->avatar ?? '');

            $phoneNo = $c->phoneNo ?? '';
            $loc = trim((($c->brgy ?? '') ? ($c->brgy . ', ') : '') . (($c->city ?? '') ? ($c->city . (($c->province ?? '') ? ', ' : '')) : '') . ($c->province ?? ''));
            $address = $c->address ?? '';

            $company  = trim((string)($c->companyName ?? ''));
            $has_company_position_field = client_has_company_position_field();
            $company_position = ($has_company_position_field && isset($c->company_position))
              ? trim((string)$c->company_position)
              : '';
            $employer = trim((string)($c->employer ?? ''));
            $biz_name = trim((string)($c->business_name ?? ''));
            $biz_loc  = trim((string)($c->business_location ?? ''));
            $org_label = client_org_label($c);
            $is_individual_employer = client_is_individual_employer($c);
            $has_business_details = ($company !== '' || ($has_company_position_field && $company_position !== '') || $employer !== '' || $biz_name !== '' || $biz_loc !== '');

            $id_image = $c->id_image ?? '';
            $permit   = $c->business_permit ?? '';

            $status_raw = strtolower(trim((string)($c->status ?? '')));
            $is_active  = isset($c->is_active) ? ((int)$c->is_active === 1) : ($status_raw !== 'inactive');
            $is_pending = ($status_raw === 'pending');
            $status_lbl  = $is_pending ? 'Pending' : ($is_active ? 'Active' : 'Inactive');
            $status_icon = $is_pending ? 'mdi-timer-sand' : ($is_active ? 'mdi-check-decagram-outline' : 'mdi-close-octagon-outline');

            $jobs_posted = (int)($stats['jobs_posted'] ?? 0);
            $jobs_active = (int)($stats['jobs_active'] ?? 0);
            $hires_total = (int)($stats['hires_total'] ?? 0);
            $spend_total = (float)($stats['spend_total'] ?? 0);

            // ---------- media/preview URL builder (same behavior as dashboard)
            if (!function_exists('viewer_url_from_abs')) {
              function viewer_url_from_abs($absUrl)
              {
                if (!$absUrl) return null;
                $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                if (!$pathOnly) return null;
                $fParam   = ltrim($pathOnly, '/');
                // remove base path if app is in a subfolder
                $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                  $fParam = substr($fParam, strlen($basePath) + 1);
                }
                return site_url('media/preview') . '?f=' . rawurlencode($fParam);
              }
            }

            // file absolute URLs
            $idAbs     = $id_image ? base_url($id_image) : null;
            $permitAbs = $permit   ? base_url($permit)   : null;

            // PREVIEW LINKS (now use /media/preview just like dashboard)
            $idView     = $idAbs     ? (viewer_url_from_abs($idAbs)     ?: $idAbs)     : null;
            $permitView = $permitAbs ? (viewer_url_from_abs($permitAbs) ?: $permitAbs) : null;

            // certificates
            function is_image_path($p)
            {
              $e = strtolower(pathinfo((string)$p, PATHINFO_EXTENSION));
              return in_array($e, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            }
            function is_pdf_path($p)
            {
              $e = strtolower(pathinfo((string)$p, PATHINFO_EXTENSION));
              return $e === 'pdf';
            }

            $certs = [];
            if (!empty($c->certificates)) {
              $tmp = is_string($c->certificates) ? json_decode($c->certificates, true) : (array)$c->certificates;
              if (is_array($tmp)) $certs = array_values(array_filter($tmp));
            } elseif (!empty($c->cert_files)) {
              $tmp = is_string($c->cert_files) ? json_decode($c->cert_files, true) : (array)$c->cert_files;
              if (is_array($tmp)) $certs = array_values(array_filter($tmp));
            }
            $certItems = [];
            foreach ((array)$certs as $ct) {
              if (is_string($ct)) {
                $path = $ct;
                $title = pathinfo($ct, PATHINFO_FILENAME);
              } elseif (is_array($ct) && !empty($ct['path'])) {
                $path = (string)$ct['path'];
                $title = trim($ct['title'] ?? pathinfo($path, PATHINFO_FILENAME));
              } else continue;
              $abs  = preg_match('#^https?://#i', $path) ? $path : base_url($path);
              $view = viewer_url_from_abs($abs) ?: $abs;
              $certItems[] = ['path' => $path, 'title' => $title, 'abs' => $abs, 'view' => $view];
            }

            $uid = (int)($c->id ?? $c->user_id ?? 0);
            ?>

            <!-- PROFILE -->
            <div class="profile-card" data-user-id="<?= $uid ?>">
              <div class="profile-brandbar"></div>
              <div class="profile-cover"></div>
              <div class="profile-gold"></div>
              <div class="profile-main">
                <?php $def = htmlspecialchars(base_url('uploads/avatars/avatar.png'), ENT_QUOTES, 'UTF-8'); ?>
                <img class="avatar" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar" onerror="this.onerror=null;this.src='<?= $def ?>';">

                <div>
                  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <h3 class="profile-name"><?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') ?></h3>
                    <span class="badge-soft" title="Account status"><i class="mdi <?= $status_icon ?>"></i> <?= htmlspecialchars($status_lbl, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if (!empty($c->email)): ?>
                      <span class="badge-soft"><i class="mdi mdi-email-outline"></i> <?= htmlspecialchars($c->email, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                  </div>
                  <?php if ($org_label !== ''): ?>
                    <div class="profile-sub"><?= htmlspecialchars($org_label, ENT_QUOTES, 'UTF-8') ?></div>
                  <?php endif; ?>
                  <?php if ($has_company_position_field && $company_position !== ''): ?>
                    <div class="profile-sub" style="font-size:11px;color:#94a3b8">
                      <?= htmlspecialchars($company_position, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                  <?php endif; ?>
                  <div class="meta" style="margin-top:2px">
                    <?php if ($loc): ?><span class="me-3"><i class="mdi mdi-map-marker"></i> <?= htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    <?php if ($phoneNo): ?><span class="me-3"><i class="mdi mdi-phone"></i> <?= htmlspecialchars($phoneNo, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    <?php if ($address): ?><span class="wrap"><i class="mdi mdi-home-map-marker"></i> <?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                  </div>
                </div>

                <!-- Admin action bar -->
                <div>
                  <?php if ($is_pending): ?>
                    <button type="button" class="btn-icon ok js-approve" title="Approve"><i class="mdi mdi-check-decagram-outline"></i></button>
                    <button type="button" class="btn-icon info js-resend" title="Resend activation"><i class="mdi mdi-send"></i></button>
                  <?php endif; ?>
                  <?php if ($uid): ?>
                    <?php if ($is_active): ?>
                      <button type="button" class="btn-icon bad js-toggle" data-active="1" title="Deactivate"><i class="mdi mdi-account-cancel-outline"></i></button>
                    <?php else: ?>
                      <button type="button" class="btn-icon ok js-toggle" data-active="0" title="Activate"><i class="mdi mdi-account-check-outline"></i></button>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- KPIs -->
            <div class="kpi-grid" style="margin:10px 0 6px">
              <div class="panel kpi">
                <div style="display:flex;align-items:center;gap:10px">
                  <div class="icon" style="background:rgba(37,99,235,.10)"><i class="mdi mdi-briefcase-outline" style="font-size:18px;color:#2563eb"></i></div>
                  <div>
                    <div class="label">Jobs Posted</div>
                    <div class="value"><?= $jobs_posted ?></div>
                  </div>
                </div>
                <div class="text-muted" style="font-size:12px;margin-top:4px">All-time</div>
              </div>
              <div class="panel kpi">
                <div style="display:flex;align-items:center;gap:10px">
                  <div class="icon" style="background:rgba(251,191,36,.18)"><i class="mdi mdi-briefcase-check" style="font-size:18px;color:#b45309"></i></div>
                  <div>
                    <div class="label">Active Jobs</div>
                    <div class="value"><?= $jobs_active ?></div>
                  </div>
                </div>
                <div class="text-muted" style="font-size:12px;margin-top:4px">Open right now</div>
              </div>
              <div class="panel kpi">
                <div style="display:flex;align-items:center;gap:10px">
                  <div class="icon" style="background:rgba(245,158,11,.12)"><i class="mdi mdi-account-multiple-check" style="font-size:18px;color:#f59e0b"></i></div>
                  <div>
                    <div class="label">Total Hires</div>
                    <div class="value"><?= $hires_total ?></div>
                  </div>
                </div>
                <div class="text-muted" style="font-size:12px;margin-top:4px">All-time</div>
              </div>
              <div class="panel kpi">
                <div style="display:flex;align-items:center;gap:10px">
                  <div class="icon" style="background:rgba(99,102,241,.12)"><i class="mdi mdi-cash-multiple" style="font-size:18px;color:#6366f1"></i></div>
                  <div>
                    <div class="label">Total Spend</div>
                    <div class="value">â‚±<?= number_format($spend_total, 2) ?></div>
                  </div>
                </div>
                <div class="text-muted" style="font-size:12px;margin-top:4px">All-time</div>
              </div>
            </div>

            <div class="layout">
              <section class="panel">
                <div class="panel-head"><i class="mdi mdi-briefcase-outline"></i>
                  <h6>Business / Project</h6>
                </div>
                <div class="panel-body">
                  <?php if ($is_individual_employer): ?>
                    <div class="mb-1"><strong>Employer Type:</strong> Individual Employer</div>
                  <?php endif; ?>

                  <?php if (!$has_business_details): ?>
                    <div class="empty">No business or project details yet.</div>
                  <?php else: ?>
                    <?php if ($company !== ''): ?><div class="mb-1"><strong>Company:</strong> <?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    <?php if ($has_company_position_field && $company_position !== ''): ?><div class="mb-1"><strong>Position:</strong> <?= htmlspecialchars($company_position, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    <?php if ($employer !== ''): ?><div class="mb-1"><strong>Employer:</strong> <?= htmlspecialchars($employer, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    <?php if ($biz_name !== ''): ?><div class="mb-1"><strong>Project / Business Name:</strong> <?= htmlspecialchars($biz_name, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    <?php if ($biz_loc !== ''): ?><div class="mb-1"><strong>Business Location:</strong> <?= htmlspecialchars($biz_loc, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                  <?php endif; ?>
                </div>
              </section>

              <section class="panel">
                <div class="panel-head"><i class="mdi mdi-shield-account-outline"></i>
                  <h6>Verification & Documents</h6>
                </div>
                <div class="panel-body">
                  <div class="mb-2">
                    <strong>Government ID:</strong>
                    <?php if ($idView): ?>
                      <a class="btn-primary-brand" href="<?= htmlspecialchars($idView, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><i class="mdi mdi-id-card"></i> View</a>
                    <?php else: ?><span class="meta">Not uploaded</span><?php endif; ?>
                  </div>
                  <div class="mb-2" style="margin-top:6px">
                    <strong>Business Permit:</strong>
                    <?php if ($permitView): ?>
                      <a class="btn-primary-brand" href="<?= htmlspecialchars($permitView, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><i class="mdi mdi-file-certificate-outline"></i> View</a>
                    <?php else: ?><span class="meta">Not uploaded</span><?php endif; ?>
                  </div>

                  <?php if (!empty($certItems)): ?>
                    <div style="margin-top:10px">
                      <strong>Certificates:</strong>
                      <div class="c-grid certs-row" style="margin-top:6px">
                        <?php foreach ($certItems as $it): $img = is_image_path($it['path']);
                          $pdf = is_pdf_path($it['path']); ?>
                          <div class="c-card <?= $img ? 'hasimg' : '' ?>" <?= $img ? 'style="background-image:url(\'' . htmlspecialchars($it['abs'], ENT_QUOTES) . '\')"' : '' ?>>
                            <?php if (!$img): ?>
                              <div class="text-center">
                                <i class="mdi <?= $pdf ? 'mdi-file-pdf-box' : 'mdi-file' ?>" style="font-size:40px;<?= $pdf ? 'color:#b91c1c' : 'color:#334155' ?>"></i>
                                <div style="font-size:11px;margin-top:4px;color:#334155;max-width:92%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                  <?= htmlspecialchars($it['title'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                              </div>
                            <?php else: ?>
                              <span class="file-pill"><?= htmlspecialchars(strtoupper(pathinfo($it['path'], PATHINFO_EXTENSION)), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <div class="c-overlay">
                              <a href="<?= htmlspecialchars($it['view'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="c-tag"><i class="mdi mdi-eye"></i> View</a>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="empty" style="margin-top:8px">No client certificates uploaded.</div>
                  <?php endif; ?>
                </div>
              </section>

              <section class="panel panel--wide">
                <div class="panel-head"><i class="mdi mdi-clipboard-text-outline"></i>
                  <h6>Recent Jobs</h6>
                </div>
                <div class="panel-body">
                  <?php if (empty($recent_jobs)): ?>
                    <div class="empty">No jobs yet.</div>
                  <?php else: ?>
                    <div class="table-responsive">
                      <table class="table table-sm" style="width:100%">
                        <thead class="bg-light">
                          <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Applicants</th>
                            <th>Posted</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($recent_jobs as $j): ?>
                            <tr>
                              <td class="fw-medium"><?= htmlspecialchars($j->title ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                              <td>
                                <?php $st = strtolower($j->status ?? 'open');
                                $icon = $st === 'open' ? 'mdi-lock-open-outline' : ($st === 'hired' ? 'mdi-account-check' : 'mdi-archive-outline');
                                $label = ucfirst($st); ?>
                                <span class="badge-soft"><i class="mdi <?= $icon ?>"></i> <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                              </td>
                              <td><?= (int)($j->applicants ?? 0) ?></td>
                              <td class="text-muted"><?= htmlspecialchars($j->posted_ago ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                              <td style="white-space:nowrap">
                                <a class="btn-primary-brand" href="<?= site_url('projects/active') ?>" target="_blank" rel="noopener"><i class="mdi mdi-eye-outline"></i> View</a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>
                </div>
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
  <script>
    (function() {
      const URL_TOGGLE = '<?= site_url('users/toggle')  ?>';
      const URL_APPROVE = '<?= site_url('users/approve') ?>';
      const URL_RESEND = '<?= site_url('users/resend')  ?>';

      const metaName = document.querySelector('meta[name="csrf-token-name"]');
      const metaHash = document.querySelector('meta[name="csrf-token-hash"]');
      const getCSRF = () => ({
        name: metaName.getAttribute('content'),
        hash: metaHash.getAttribute('content')
      });
      const setCSRF = (n, h) => {
        if (n && h) {
          metaName.setAttribute('content', n);
          metaHash.setAttribute('content', h);
        }
      };

      async function post(url, payload) {
        const csrf = getCSRF();
        const fd = new FormData();
        Object.entries(payload || {}).forEach(([k, v]) => fd.append(k, v));
        fd.append(csrf.name, csrf.hash);
        const res = await fetch(url, {
          method: 'POST',
          body: fd,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const json = await res.json().catch(() => ({}));
        if (json && json.csrf_name && json.csrf_hash) setCSRF(json.csrf_name, json.csrf_hash);
        if (!res.ok || json.ok === false) throw new Error(json.msg || 'Request failed');
        return json;
      }

      function setStatus(pill, state) { // 'active' | 'inactive' | 'pending'
        if (!pill) return;
        pill.className = 'badge-soft';
        if (state === 'active') {
          pill.innerHTML = '<i class="mdi mdi-check-decagram-outline"></i> Active';
        } else if (state === 'inactive') {
          pill.innerHTML = '<i class="mdi mdi-close-octagon-outline"></i> Inactive';
        } else {
          pill.innerHTML = '<i class="mdi mdi-timer-sand"></i> Pending';
        }
      }

      document.addEventListener('click', async (e) => {
        const root = document.querySelector('.profile-card');
        if (!root) return;
        const uid = parseInt(root.getAttribute('data-user-id') || '0', 10);
        const statusPill = root.querySelector('.badge-soft');

        // Approve
        const approve = e.target.closest('.js-approve');
        if (approve) {
          if (!uid) return alert('Missing user id');
          if (!confirm('Approve this user manually?')) return;
          approve.disabled = true;
          try {
            await post(URL_APPROVE, {
              id: uid
            });
            setStatus(statusPill, 'active');
            // swap action buttons
            const bar = approve.parentElement;
            bar.innerHTML = '<button type="button" class="btn-icon bad js-toggle" data-active="1" title="Deactivate"><i class="mdi mdi-account-cancel-outline"></i></button>';
          } catch (err) {
            alert(err.message || 'Approve failed');
          } finally {
            approve.disabled = false;
          }
          return;
        }

        // Resend activation
        const resend = e.target.closest('.js-resend');
        if (resend) {
          if (!uid) return alert('Missing user id');
          resend.disabled = true;
          try {
            const res = await post(URL_RESEND, {
              id: uid
            });
            if (res.items && res.items.link) {
              if (!confirm((res.msg || 'Link ready') + '. Open now?')) {
                prompt('Copy activation link:', res.items.link);
              } else {
                window.open(res.items.link, '_blank');
              }
            } else {
              alert(res.msg || 'Activation email sent');
            }
          } catch (err) {
            alert(err.message || 'Send failed');
          } finally {
            resend.disabled = false;
          }
          return;
        }

        // Toggle active
        const toggle = e.target.closest('.js-toggle');
        if (toggle) {
          if (!uid) return alert('Missing user id');
          const nowActive = toggle.getAttribute('data-active') === '1';
          const nextActive = nowActive ? 0 : 1;
          toggle.disabled = true;
          try {
            await post(URL_TOGGLE, {
              id: uid,
              active: String(nextActive)
            });
            const icon = nextActive ? 'mdi-account-cancel-outline' : 'mdi-account-check-outline';
            toggle.innerHTML = '<i class="mdi ' + icon + '"></i>';
            toggle.classList.toggle('bad', !!nextActive);
            toggle.classList.toggle('ok', !nextActive);
            toggle.setAttribute('data-active', String(nextActive));
            toggle.title = nextActive ? 'Deactivate' : 'Activate';
            setStatus(statusPill, nextActive ? 'active' : 'inactive');
          } catch (err) {
            alert(err.message || 'Toggle failed');
          } finally {
            toggle.disabled = false;
          }
          return;
        }
      });

      // Mobile labels for Recent Jobs table
      (function() {
        var tbl = document.querySelector('.panel.panel--wide table');
        if (!tbl) return;

        function relabel(t) {
          var heads = [...t.querySelectorAll('thead th')].map(th => th.textContent.trim());
          t.querySelectorAll('tbody tr').forEach(tr => {
            [...tr.children].forEach((td, i) => {
              if (!td.hasAttribute('data-th') && heads[i]) td.setAttribute('data-th', heads[i]);
            });
          });
        }
        relabel(tbl);
        var obs = new MutationObserver(() => relabel(tbl));
        if (tbl.tBodies && tbl.tBodies[0]) obs.observe(tbl.tBodies[0], {
          childList: true,
          subtree: true
        });
      })();
    })();
  </script>
</body>

</html>
