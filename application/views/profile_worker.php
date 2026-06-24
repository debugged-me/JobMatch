<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Worker Profile', ENT_QUOTES, 'UTF-8') ?> • JobMatch</title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.8') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/mobile-global.css?v=20251006') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/mobile-patch.css?v=20251006') ?>">

  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --bg: #f7f9fb;
      --card: #ffffff;
      --ink: #0f172a;
      --muted: #64748b;
      --line: #e6eaf0;
      --brand: #6366f1;
      --brand-200: #c7d2fe;
      --brand-50: #eef2ff;
      --accent: #22c55e;
      --warn: #f59e0b;
      --shadow-1: 0 6px 18px rgba(2, 6, 23, .08);
      --shadow-2: 0 16px 38px rgba(2, 6, 23, .14);
    }

    body {
      background: var(--bg);
      color: var(--ink);
      font-family: "Karla", ui-sans-serif, system-ui, -apple-system
    }

    .content-wrapper {
      padding-top: .8rem
    }

    .app {
      max-width: 1180px;
      margin: 0 auto;
      padding: 0 16px
    }

    .eyebrow {
      font-size: .85rem;
      color: var(--muted);
      font-weight: 600;
      letter-spacing: .3px;
      margin-bottom: .15rem
    }

    .profile-card {
      position: relative;
      border-radius: 16px;
      overflow: hidden;
      background: var(--card);
      box-shadow: var(--shadow-1);
      border: 1px solid var(--line)
    }

    .profile-cover {
      height: 180px;
      background: url('<?= base_url("assets/images/banner.png") ?>') center/cover no-repeat
    }

    .profile-main {
      display: grid;
      grid-template-columns: 96px 1fr auto;
      gap: 18px;
      align-items: center;
      padding: 18px
    }

    .avatar {
      width: 96px;
      height: 96px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid var(--card);
      margin-top: -64px;
      box-shadow: 0 10px 24px rgba(2, 6, 23, .18)
    }

    .profile-title {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap
    }

    .profile-name {
      font-size: clamp(18px, 1.9vw, 22px);
      font-weight: 700;
      margin: 0
    }

    .profile-sub {
      color: var(--muted);
      font-weight: 600;
      margin-top: 2px
    }

    .meta {
      color: var(--muted);
      font-size: .95rem
    }

    .quick-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap
    }

    .btn-soft {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: .55rem .85rem;
      border-radius: 12px;
      border: 1px solid var(--brand-200);
      background: var(--brand-50);
      font-weight: 700;
      color: #3730a3;
      text-decoration: none;
      transition: all .18s ease
    }

    .btn-soft:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-1)
    }

    .stat-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      padding: 0 18px 18px
    }

    @media (max-width:768px) {
      .profile-main {
        grid-template-columns: 72px 1fr
      }

      .avatar {
        width: 72px;
        height: 72px;
        margin-top: -48px
      }

      .stat-row {
        grid-template-columns: repeat(2, 1fr)
      }
    }

    .stat {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: .65rem .8rem;
      display: flex;
      align-items: center;
      gap: 10px
    }

    .stat i {
      font-size: 20px;
      color: #94a3b8
    }

    .stat .v {
      font-weight: 800
    }

    .box-grid {
      margin-top: 18px;
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 16px
    }

    .box {
      grid-column: span 6;
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 14px;
      box-shadow: var(--shadow-1);
      padding: 16px
    }

    .box.wide {
      grid-column: span 12
    }

    .box-head {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px
    }

    .box-head i {
      font-size: 22px;
      color: #94a3b8
    }

    .box-head h6 {
      margin: 0;
      font-size: 15px;
      font-weight: 800;
      letter-spacing: .2px
    }

    .box-body {
      color: var(--ink)
    }

    .empty {
      color: var(--muted);
      border: 1px dashed var(--line);
      border-radius: 12px;
      padding: 14px;
      text-align: center
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .28rem .6rem;
      border-radius: 9999px;
      border: 1px solid var(--brand);
      background: var(--brand);
      font-size: .85rem;
      font-weight: 700;
      color: #fff;
      text-decoration: none;
      transition: .18s
    }

    .pill i {
      color: #fff
    }

    a.pill:hover {
      background: #1d4ed8;
      border-color: #1d4ed8
    }

    .chips {
      display: flex;
      flex-wrap: wrap;
      gap: .45rem
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .24rem .55rem;
      border-radius: 9999px;
      border: 1px solid var(--line);
      background: #fff;
      font-size: .8rem
    }

    .c-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      gap: 12px
    }

    .c-card {
      position: relative;
      height: 120px;
      border: 1px solid var(--line);
      border-radius: 12px;
      overflow: hidden;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: .16s ease
    }

    .c-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-2)
    }

    .c-card.hasimg {
      background-size: cover;
      background-position: center;
      box-shadow: inset 0 -34px 60px rgba(2, 6, 23, .22)
    }

    .c-overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding: 10px;
      background: linear-gradient(to top, rgba(15, 23, 42, .60), rgba(15, 23, 42, .15) 50%, rgba(15, 23, 42, 0));
      opacity: 0;
      transition: .18s
    }

    .c-card:hover .c-overlay {
      opacity: 1
    }

    .c-tag {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      padding: .35rem .6rem;
      border-radius: 9999px;
      background: rgba(255, 255, 255, .16);
      color: #fff;
      font-weight: 800
    }

    .c-icon {
      font-size: 38px;
      color: #cbd5e1
    }

    /* Hire modal preview thumbnails */
    #hireModal .proj-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    #hireModal .proj-thumb {
      width: 84px;
      height: 84px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
      flex: 0 0 auto;
      box-shadow: 0 2px 6px rgba(2, 6, 23, .06);
      max-width: none;
      /* override Bootstrap img {max-width:100%} */
    }

    #hireModal .proj-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .28rem .6rem;
      border-radius: 9999px;
      border: 1px solid #e5e7eb;
      background: #fff;
      font-size: .82rem;
      text-decoration: none;
      box-shadow: 0 2px 6px rgba(2, 6, 23, .06);
    }

    #hireModal .proj-chip i {
      font-size: 1rem;
    }

    .c-cap {
      margin-top: 6px;
      font-size: .82rem;
      line-height: 1.2;
      color: #334155;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis
    }

    @media (max-width: 768px) {
      .content-wrapper {
        padding-top: .6rem;
      }

      body {
        padding-bottom: max(14px, env(safe-area-inset-bottom));
      }

      .profile-cover {
        height: 110px;
        background-size: cover !important;
      }

      .profile-main {
        grid-template-columns: 64px 1fr;
        gap: 10px 12px;
        padding: 12px;
      }

      .avatar {
        width: 64px;
        height: 64px;
        margin-top: -44px;
        border-width: 3px;
      }

      .profile-title {
        gap: 8px;
      }

      .profile-name {
        font-size: 18px;
      }

      .profile-sub,
      .meta {
        font-size: 12px;
      }

      .quick-actions {
        width: 100%;
      }

      .quick-actions .btn-soft {
        flex: 1 1 160px;
        justify-content: center;
      }

      .stat-row {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 0 12px 12px;
      }

      .stat {
        padding: .6rem .7rem;
      }

      .box-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        margin-top: 12px;
      }

      .box,
      .box.wide {
        grid-column: 1 / -1;
        padding: 14px;
        border-radius: 16px;
      }

      .box-head h6 {
        font-size: 14px;
      }

      .c-card {
        height: 120px;
      }

      .c-overlay {
        padding: 10px;
      }

      .c-tag {
        font-size: 11.5px;
        padding: .35rem .65rem;
      }

      .box .table-responsive {
        overflow: visible;
      }

      .box .table thead {
        display: none;
      }

      .box .table {
        border-collapse: separate;
        border-spacing: 0 12px;
      }

      .box .table tbody,
      .box .table tr,
      .box .table td {
        display: block;
        width: 100%;
      }

      .box .table tbody tr {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        box-shadow: var(--shadow-1);
        padding: 10px 12px;
      }

      .box .table tbody tr td {
        display: grid;
        grid-template-columns: minmax(90px, 34%) 1fr;
        gap: 8px;
        align-items: baseline;
        padding: 8px 0;
        border-bottom: 1px dashed #e5e7eb;
      }

      .box .table tbody tr td:last-child {
        border-bottom: 0;
        padding-bottom: 2px;
      }

      .box .table td::before {
        content: attr(data-th);
        text-transform: uppercase;
        letter-spacing: .32px;
        font: 700 10px/1 Inter, system-ui, -apple-system, "Segoe UI", Roboto;
        color: #64748b;
      }

      .box .table td>* {
        overflow-wrap: anywhere;
        word-break: break-word;
      }

      .chip {
        font-size: .78rem;
        padding: .22rem .5rem;
      }

      #svcMixPanel .box-body>div {
        grid-template-columns: 1fr !important;
      }

      #svcMixLegend {
        margin-top: 10px;
      }
    }

    img[loading="lazy"] {
      background: #f3f4f6;
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
            <div class="mb-3">
              <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Worker Profile', ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <?php if ($this->session->flashdata('error')) { ?>
              <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('error'); ?></div>
            <?php } ?>
            <?php if ($this->session->flashdata('success')) { ?>
              <div class="alert alert-success" role="alert"><?= $this->session->flashdata('success'); ?></div>
            <?php } ?>

            <?php
            $full = trim(($w->first_name ?? '') . ' ' . ($w->last_name ?? ''));
            $seed = $full !== '' ? $full : ($w->email ?? 'Worker');
            $avatar = function_exists('avatar_url')
              ? avatar_url($w->avatar ?? '')
              : (function ($raw) {
                $raw = trim((string)$raw);
                if ($raw !== '' && preg_match('#^https?://#i', $raw)) return $raw;
                if ($raw !== '') return base_url(str_replace('\\', '/', $raw));
                return base_url('uploads/avatars/avatar.png');
              })($w->avatar ?? '');

            $loc = trim(($w->brgy ? $w->brgy . ', ' : '') . ($w->city ? $w->city . ($w->province ? ', ' : '') : '') . ($w->province ?? ''));
            $headline = (string)($w->headline ?? '');
            $bio = (string)($w->bio ?? '');
            $phone = trim((string)($w->phoneNo ?? ''));

            $avgRating = isset($reviews['avg']) ? (float)$reviews['avg'] : null;
            $revCount  = isset($reviews['count']) ? (int)$reviews['count'] : null;

            $aggMin = isset($aggMin) ? $aggMin : null;
            $aggMax = isset($aggMax) ? $aggMax : null;

            $certs = isset($certs) ? $certs : [];
            $items = isset($items) ? $items : [];
            $skill_rates = isset($skill_rates) ? $skill_rates : [];
            $latest_reviews = isset($latest_reviews) ? $latest_reviews : [];
            if (empty($certs) && isset($w)) {
              $fromJson = function ($raw) {
                $out = [];
                if (empty($raw)) return $out;
                $tmp = is_string($raw) ? json_decode($raw, true) : (array)$raw;
                if (!is_array($tmp)) return $out;
                foreach ($tmp as $c) {
                  if (is_string($c) && trim($c) !== '') {
                    $out[] = $c;
                  } elseif (is_array($c) && !empty($c['path'])) {
                    $out[] = (string)$c['path'];
                  }
                }
                return $out;
              };
              if (!empty($w->certificates)) {
                $certs = $fromJson($w->certificates);
              }
              if (empty($certs) && !empty($w->cert_files)) {
                $certs = $fromJson($w->cert_files);
              }
            }

            if (!function_exists('wp_path_of')) {
              function wp_path_of($f)
              {
                return is_array($f) ? (string)($f['path'] ?? '') : (string)$f;
              }
            }
            if (!function_exists('wp_name_of')) {
              function wp_name_of($f)
              {
                return trim(is_array($f) ? (string)($f['name'] ?? '') : '');
              }
            }
            if (!function_exists('wp_is_img')) {
              function wp_is_img($f)
              {
                $p = wp_path_of($f);
                return in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
              }
            }
            if (!function_exists('wp_file_icon')) {
              function wp_file_icon($f)
              {
                $p = wp_path_of($f);
                $e = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                if (in_array($e, ['jpg', 'jpeg', 'png', 'webp'])) return 'mdi-image-outline';
                if ($e === 'pdf') return 'mdi-file-pdf-box';
                return 'mdi-file-outline';
              }
            }

            ?>
            <?php
            if (!function_exists('tw_parse_experiences')) {

              function tw_parse_experiences($experiences, $fallbackObj = null)
              {
                if (is_array($experiences) && !empty($experiences)) return $experiences;
                $candidates = [];
                foreach (['experiences', 'experience', 'work_experience', 'work_exp'] as $k) {
                  if ($fallbackObj && isset($fallbackObj->$k) && $fallbackObj->$k) {
                    $candidates[] = $fallbackObj->$k;
                  }
                }

                foreach ($candidates as $raw) {
                  $arr = is_string($raw) ? json_decode($raw, true) : (array)$raw;
                  if (is_array($arr) && count($arr)) return $arr;
                }
                return [];
              }
            }

            if (!function_exists('tw_fmt_period')) {
              function tw_fmt_period($from, $to, $to_present = false)
              {
                $fmt = function ($s) {
                  $s = trim((string)$s);
                  if ($s === '') return '';
                  if (preg_match('/^\d{4}\-\d{2}$/', $s)) {
                    $ts = strtotime($s . '-01');
                    return $ts ? date('M Y', $ts) : $s;
                  }
                  if (preg_match('/^\d{4}$/', $s)) return $s;
                  return $s;
                };
                $a = $fmt($from);
                $b = $to_present ? '' : $fmt($to);
                if ($a === '' && $b === '') return '—';
                if ($a !== '' && $b !== '') return $a . ' — ' . $b;
                return $a . ' — Present';
              }
            }
            ?>

            <div class="profile-card">
              <div class="profile-cover"></div>
              <div class="profile-main">
                <?php $defaultEsc = htmlspecialchars(base_url('uploads/avatars/avatar.png'), ENT_QUOTES, 'UTF-8'); ?>
                <img
                  class="avatar"
                  src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>"
                  alt="Avatar"
                  style="object-fit:cover"
                  onerror="this.onerror=null;this.src='<?= $defaultEsc ?>';">

                <div>
                  <div class="profile-title">
                    <h3 class="profile-name">
                      <?= html_escape($full !== '' ? $full : ($w->email ?? 'Worker')) ?>
                    </h3>
                    <?php if ($avgRating !== null && $revCount !== null) { ?>
                      <span class="meta"><strong><?= number_format($avgRating, 1) ?>/5</strong> (<?= (int)$revCount ?> review<?= $revCount === 1 ? '' : 's' ?>)</span>
                    <?php } ?>
                  </div>

                  <?php if (!empty($headline)) { ?>
                    <div class="profile-sub"><?= html_escape($headline) ?></div>
                  <?php } ?>

                  <?php if ($loc || $phone) { ?>
                    <div class="meta mt-1">
                      <?php if ($loc) { ?><span class="me-3"><i class="mdi mdi-map-marker-outline me-1"></i> <?= html_escape($loc) ?></span><?php } ?>
                      <?php if ($phone) {
                        $tel = preg_replace('/[^\d+]/', '', $phone); ?>
                        <span><i class="mdi mdi-phone-outline me-1"></i> <a href="tel:<?= html_escape($tel) ?>"><?= html_escape($phone) ?></a></span>
                      <?php } ?>
                    </div>
                  <?php } ?>
                </div>

                <?php
                $recipient_id = 0;
                foreach (['user_id', 'id', 'workerID', 'worker_id', 'uid'] as $k) {
                  if (isset($w->$k) && is_numeric($w->$k) && (int)$w->$k > 0) {
                    $recipient_id = (int)$w->$k;
                    break;
                  }
                }

                $myId = 0;
                foreach (['id', 'user_id', 'uid', 'account_id'] as $k) {
                  $v = $this->session->userdata($k);
                  if (is_numeric($v)) {
                    $myId = (int)$v;
                    break;
                  }
                }

                $canHire = $this->session->userdata('logged_in')
                  && $this->session->userdata('role') === 'client'
                  && $recipient_id > 0
                  && $recipient_id !== $myId;
                ?>

                <?php if ($canHire): ?>
                  <div class="quick-actions">
                    <a href="#" class="btn-soft" data-start-chat="<?= (int)$recipient_id ?>">
                      <i class="mdi mdi-message-text-outline"></i> Message
                    </a>
                    <a href="#"
                      class="btn-soft hire-btn"
                      data-user-id="<?= (int)$recipient_id ?>"
                      data-worker-name="<?= html_escape($full !== '' ? $full : ($w->email ?? 'Worker')) ?>">
                      <i class="mdi mdi-briefcase-plus-outline"></i> Hire
                    </a>
                  </div>
                <?php endif; ?>


              </div>

              <div class="stat-row">
                <div class="stat"><i class="mdi mdi-star"></i> <span class="v"><?= $avgRating !== null ? number_format($avgRating, 1) : '—' ?></span>&nbsp;Rating</div>
                <div class="stat"><i class="mdi mdi-comment-text-multiple-outline"></i> <span class="v"><?= $revCount !== null ? (int)$revCount : 0 ?></span>&nbsp;Reviews</div>
                <div class="stat"><i class="mdi mdi-briefcase-outline"></i> <span class="v"><?= count($items) ?></span>&nbsp;Portfolio</div>
                <div class="stat"><i class="mdi mdi-file-certificate-outline"></i> <span class="v"><?= count($certs) ?></span>&nbsp;Certificates</div>
              </div>
            </div>

            <div class="box-grid">

              <section class="box wide">
                <div class="box-head"><i class="mdi mdi-file-certificate-outline"></i>
                  <h6>Clearances &amp; Certificates</h6>
                </div>
                <div class="box-body">
                  <?php if (empty($certs)) { ?>
                    <div class="empty">No certificates uploaded.</div>
                  <?php } else { ?>
                    <?php
                    $imgCerts = [];
                    $docCerts = [];
                    foreach ($certs as $f) {
                      if (wp_is_img($f)) $imgCerts[] = $f;
                      else $docCerts[] = $f;
                    }
                    ?>
                    <?php if (!empty($imgCerts)) { ?>
                      <div class="c-grid mb-2">
                        <?php foreach ($imgCerts as $f) {
                          $path = wp_path_of($f);
                          $name = wp_name_of($f);
                          $label = $name !== '' ? $name : basename($path);

                          $absUrl = preg_match('#^https?://#i', $path) ? $path : base_url($path);
                          $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                          $fParam   = ltrim($pathOnly, '/');
                          $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                          if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                            $fParam = substr($fParam, strlen($basePath) + 1);
                          }
                          $viewerUrl = site_url('media/preview') . '?f=' . rawurlencode($fParam);
                        ?>
                          <div class="c-card hasimg" style="background-image:url('<?= htmlspecialchars($absUrl, ENT_QUOTES, 'UTF-8') ?>')">
                            <a class="c-overlay" href="<?= htmlspecialchars($viewerUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="View image">
                              <span class="c-tag"><i class="mdi mdi-eye-outline"></i>View image</span>
                            </a>
                          </div>
                        <?php } ?>
                      </div>

                      <!-- clickable list of names (for images too) -->
                      <div class="chips">
                        <?php foreach ($imgCerts as $f) {
                          $path = wp_path_of($f);
                          $name = wp_name_of($f);
                          $label = $name !== '' ? $name : basename($path);

                          $absUrl = preg_match('#^https?://#i', $path) ? $path : base_url($path);
                          $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                          $fParam   = ltrim($pathOnly, '/');
                          $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                          if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                            $fParam = substr($fParam, strlen($basePath) + 1);
                          }
                          $viewerUrl = site_url('media/preview') . '?f=' . rawurlencode($fParam);
                        ?>
                          <a class="chip" href="<?= htmlspecialchars($viewerUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" title="<?= html_escape($label) ?>">
                            <i class="mdi mdi-image-outline"></i><?= html_escape($label) ?>
                          </a>
                        <?php } ?>
                      </div>
                    <?php } ?>

                    <?php if (!empty($docCerts)) { ?>
                      <div class="chips">
                        <?php foreach ($docCerts as $f) {
                          $path = wp_path_of($f);
                          $name = wp_name_of($f);
                          $label = $name !== '' ? $name : basename($path);

                          $absUrl = preg_match('#^https?://#i', $path) ? $path : base_url($path);
                          $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                          $fParam   = ltrim($pathOnly, '/');
                          $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                          if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                            $fParam = substr($fParam, strlen($basePath) + 1);
                          }
                          $viewerUrl = site_url('media/preview') . '?f=' . rawurlencode($fParam);
                        ?>
                          <a class="chip" href="<?= htmlspecialchars($viewerUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" title="<?= html_escape($label) ?>">
                            <i class="mdi <?= wp_file_icon($f) ?>"></i><?= html_escape($label) ?>
                          </a>
                        <?php } ?>
                      </div>
                    <?php } ?>



                  <?php } ?>
                </div>
              </section>

              <section class="box">
                <div class="box-head"><i class="mdi mdi-lightbulb-on-outline"></i>
                  <h6>Skills & Rates</h6>
                </div>
                <div class="box-body">
                  <?php if (empty($skill_rates)) { ?>
                    <div class="empty">No skills provided.</div>
                  <?php } else { ?>
                    <div class="table-responsive">
                      <table class="table table-sm mb-0">
                        <thead>
                          <tr>
                            <th>Skill</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($skill_rates as $r) { ?>
                            <tr>
                              <td><?= html_escape($r['title'] ?? '') ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } ?>
                </div>
              </section>

              <section class="box">
                <div class="box-head"><i class="mdi mdi-information-outline"></i>
                  <h6>About</h6>
                </div>
                <div class="box-body">
                  <?php if (!empty($bio)) { ?>
                    <div class="text-muted" style="white-space:pre-line"><?= html_escape($bio) ?></div>
                  <?php } else { ?>
                    <div class="empty">No bio yet.</div>
                  <?php } ?>
                </div>
              </section>
              <?php
              // get experiences from $experiences or from $w->experiences JSON
              $xp = tw_parse_experiences(isset($experiences) ? $experiences : [], $w ?? null);
              ?>
              <section class="box wide">
                <div class="box-head"><i class="mdi mdi-briefcase-outline"></i>
                  <h6>Work Experience</h6>
                </div>
                <div class="box-body">
                  <?php if (empty($xp)) { ?>
                    <div class="empty">No work experience added.</div>
                  <?php } else { ?>
                    <div class="table-responsive">
                      <table class="table table-sm mb-0" style="border:1px solid #e6eaf0">
                        <thead>
                          <tr style="background:#e9f0fb">
                            <th style="white-space:nowrap">Role / Title</th>
                            <th>Employer / Project</th>
                            <th style="white-space:nowrap;width:180px">Period</th>
                            <th>Description</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($xp as $row):
                            $role     = htmlspecialchars(trim($row['role']     ?? ''), ENT_QUOTES, 'UTF-8');
                            $employer = htmlspecialchars(trim($row['employer'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $from     = trim($row['from'] ?? '');
                            $to       = trim($row['to']   ?? '');
                            $present  = !empty($row['to_present']) || ($to === '' && !empty($from));
                            $desc     = htmlspecialchars(trim($row['desc'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $period   = tw_fmt_period($from, $to, $present);
                          ?>
                            <tr>
                              <td class="fw-semibold"><?= $role !== '' ? $role : '—' ?></td>
                              <td>
                                <?php if ($employer !== '') { ?>
                                  <span class="chip"><i class="mdi mdi-domain"></i><?= $employer ?></span>
                                <?php } else { ?>
                                  <span class="text-muted">—</span>
                                <?php } ?>
                              </td>
                              <td class="text-muted" style="white-space:nowrap"><?= htmlspecialchars($period, ENT_QUOTES, 'UTF-8') ?></td>
                              <td><?= $desc !== '' ? nl2br($desc) : '<span class="text-muted">—</span>' ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } ?>
                </div>
              </section>

              <section class="box wide">
                <div class="box-head"><i class="mdi mdi-briefcase-outline"></i>
                  <h6>Portfolio</h6>
                </div>
                <div class="box-body">
                  <?php if (empty($items)) { ?>
                    <div class="empty">No public items yet.</div>
                  <?php } else { ?>
                    <div class="table-responsive">
                      <table class="table table-sm mb-0" style="border:1px solid #e6eaf0">
                        <thead>
                          <tr style="background:#e9f0fb">
                            <th>Title & Description</th>
                            <th>Files</th>
                            <th style="white-space:nowrap;width:120px">When</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($items as $it) {
                            $dateStr = '—';
                            if (!empty($it['created_at'])) {
                              $ts = strtotime($it['created_at']);
                              if ($ts !== false) {
                                $dateStr = date('M d, Y', $ts);
                              }
                            }
                          ?>
                            <tr>
                              <td>
                                <div class="fw-semibold"><?= html_escape($it['title'] ?? 'Untitled') ?></div>
                                <?php if (!empty($it['description'])) { ?>
                                  <div class="text-muted small" style="white-space:pre-line"><?= html_escape($it['description']) ?></div>
                                <?php } ?>
                              </td>
                              <td>
                                <?php if (!empty($it['files'])) { ?>
                                  <?php foreach ($it['files'] as $f) {
                                    $absUrl = preg_match('#^https?://#i', $f) ? $f : base_url($f);
                                    $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                                    $fParam   = ltrim($pathOnly, '/');
                                    $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                                    if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                                      $fParam = substr($fParam, strlen($basePath) + 1);
                                    }
                                    $viewerUrl = site_url('media/preview') . '?f=' . rawurlencode($fParam);
                                  ?>
                                    <div>
                                      <a href="<?= htmlspecialchars($viewerUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" title="<?= html_escape(basename($f)) ?>">
                                        <i class="mdi <?= is_img($f) ? 'mdi-image-outline' : 'mdi-file-outline' ?>"></i>
                                        <?= html_escape(basename($f)) ?>
                                      </a>
                                    </div>
                                  <?php } ?>
                                <?php } else { ?>
                                  <span class="text-muted">—</span>
                                <?php } ?>
                              </td>
                              <td class="text-muted"><?= html_escape($dateStr) ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } ?>
                </div>
              </section>

              <!-- Types of Service (pie chart) -->
              <section class="box wide" id="svcMixPanel">
                <div class="box-head">
                  <i class="mdi mdi-chart-pie"></i>
                  <h6>Types of Service</h6>
                  <div id="svcMixPills" class="d-flex gap-2" style="margin-left:auto; display:none"></div>
                </div>
                <div class="box-body">
                  <div class="muted mb-2" id="svcMixCaption">Share of all hires by skill</div>
                  <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;align-items:center">
                    <div style="min-height:320px"><canvas id="svcMixChart" height="300"></canvas></div>
                    <div id="svcMixLegend" style="font-size:12.5px"></div>
                  </div>
                </div>
              </section>

              <!-- Latest Reviews -->
              <section class="box wide">
                <div class="box-head">
                  <i class="mdi mdi-comment-text-outline"></i>
                  <h6>Latest Reviews</h6>
                </div>
                <div class="box-body">
                  <?php if (empty($latest_reviews)) { ?>
                    <div class="empty">No reviews yet.</div>
                  <?php } else { ?>
                    <div class="table-responsive">
                      <table class="table table-sm mb-0" style="border:1px solid #e6eaf0">
                        <thead>
                          <tr style="background:#e9f0fb">
                            <th style="white-space:nowrap">Client</th>
                            <th>Job</th>
                            <th style="width:160px">Rating</th>
                            <th style="white-space:nowrap;width:120px">When</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if (!function_exists('tw_time_ago')) {
                            function tw_time_ago($dt)
                            {
                              if (!$dt) return '—';
                              $ts = is_numeric($dt) ? (int)$dt : strtotime($dt);
                              if (!$ts) return '—';
                              $diff = time() - $ts;
                              if ($diff < 60)  return $diff . ' sec' . ($diff == 1 ? '' : 's') . ' ago';
                              $mins = floor($diff / 60);
                              if ($mins < 60)  return $mins . ' min' . ($mins == 1 ? '' : 's') . ' ago';
                              $hrs  = floor($mins / 60);
                              if ($hrs  < 24)  return $hrs . ' hr' . ($hrs == 1 ? '' : 's') . ' ago';
                              $days = floor($hrs / 24);
                              if ($days < 30)  return $days . ' day' . ($days == 1 ? '' : 's') . ' ago';
                              $months = floor($days / 30);
                              if ($months < 12) return $months . ' month' . ($months == 1 ? '' : 's') . ' ago';
                              $years = floor($months / 12);
                              return $years . ' year' . ($years == 1 ? '' : 's') . ' ago';
                            }
                          }
                          foreach ($latest_reviews as $r) {
                            $client = (string)($r->client_name ?? $r->client ?? '');
                            $job    = (string)($r->job_title   ?? $r->job    ?? '');
                            $rating = (int)   ($r->rating      ?? 0);
                            $when   = isset($r->time_ago) && $r->time_ago !== '' ? (string)$r->time_ago : tw_time_ago($r->created_at ?? null);
                          ?>
                            <tr>
                              <td class="fw-medium"><?= htmlspecialchars($client !== '' ? $client : '—', ENT_QUOTES, 'UTF-8') ?></td>
                              <td><?= htmlspecialchars($job !== '' ? $job : '—', ENT_QUOTES, 'UTF-8') ?></td>
                              <td>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                  <i class="mdi <?= $i <= $rating ? 'mdi-star text-warning' : 'mdi-star-outline text-muted' ?>"></i>
                                <?php endfor; ?>
                              </td>
                              <td class="text-muted"><?= htmlspecialchars($when, ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } ?>
                </div>
              </section>


            </div><!-- /box-grid -->

          </div>
        </div>
        <!-- Hire Modal -->
        <div class="modal fade" id="hireModal" tabindex="-1" aria-labelledby="hireModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="hireModalLabel">Hire request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">
                <div id="hireModalBody">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Project (optional)</label>
                    <select id="hireProject" class="form-select">
                      <option value="">— No specific project —</option>
                    </select>
                    <div class="form-text" id="hireHelp"></div>
                  </div>

                  <div id="hirePreview" class="mb-3"></div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">Billing</label>
                    <div class="btn-group w-100" role="group" aria-label="Billing type">
                      <input class="btn-check" type="radio" name="hireRateUnit" id="ruHour" value="hour" autocomplete="off">
                      <label class="btn btn-outline-secondary" for="ruHour">Per hour</label>

                      <input class="btn-check" type="radio" name="hireRateUnit" id="ruDay" value="day" autocomplete="off">
                      <label class="btn btn-outline-secondary" for="ruDay">Per day</label>

                      <input class="btn-check" type="radio" name="hireRateUnit" id="ruProject" value="project" autocomplete="off" checked>
                      <label class="btn btn-outline-secondary" for="ruProject">Per project</label>
                    </div>
                    <div class="form-text">If you pick a project above, we'll prefill from that project's billing when possible.</div>
                  </div>

                  <div class="mb-2">
                    <label class="form-label fw-semibold">Proposed rate</label>
                    <div class="input-group">
                      <span class="input-group-text">â‚±</span>
                      <input type="number" step="0.01" min="0" class="form-control" id="hireRate" placeholder="e.g. 500.00">
                      <span class="input-group-text">per <span id="hireRateUnitSuffix">project</span></span>
                    </div>
                    <div class="form-text">Leave empty if you'd rather discuss inside chat.</div>
                  </div>

                  <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="hireInvite" checked>
                    <label class="form-check-label" for="hireInvite">
                      Include hire invitation (shows Accept/Decline in chat)
                    </label>
                  </div>
                </div>

                <div id="hireEmpty" class="d-none">
                  <div class="alert alert-info mb-0">
                    You don't have any active projects yet.
                  </div>
                </div>

              </div>

              <div class="modal-footer">
                <button id="hireSendBtn" type="button" class="btn btn-primary">
                  <i class="mdi mdi-send"></i> Send Hire Request
                </button>
              </div>
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
      if (window.__TW_HIRE_CHAT_BOUND__) return;
      window.__TW_HIRE_CHAT_BOUND__ = true;

      function csrfPair() {
        <?php if ($this->security->get_csrf_token_name()): ?>
          return {
            name: '<?= $this->security->get_csrf_token_name(); ?>',
            hash: '<?= $this->security->get_csrf_hash(); ?>'
          };
        <?php else: ?>
          return null;
        <?php endif; ?>
      }

      function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
      }

      function toast(msg, kind) {
        if (window.showToast) window.showToast(msg, kind);
      }

      function setRateSuffix(val) {
        if (!H.rateSuffix) return;
        H.rateSuffix.textContent = (val === 'hour' ? 'hour' : (val === 'day' ? 'day' : 'project'));
      }

      function ensureHireModal() {
        var el = document.getElementById('hireModal');
        return {
          el: el,
          modal: new bootstrap.Modal(el),
          projectSel: el.querySelector('#hireProject'),
          sendBtn: el.querySelector('#hireSendBtn'),
          help: el.querySelector('#hireHelp'),
          bodyWrap: el.querySelector('#hireModalBody'),
          emptyWrap: el.querySelector('#hireEmpty'),
          preview: el.querySelector('#hirePreview'),
          inviteChk: el.querySelector('#hireInvite'),
          ruHour: el.querySelector('#ruHour'),
          ruDay: el.querySelector('#ruDay'),
          ruProject: el.querySelector('#ruProject'),
          rateInput: el.querySelector('#hireRate'),
          rateSuffix: el.querySelector('#hireRateUnitSuffix')
        };
      }

      var H = ensureHireModal();
      var currentWorkerId = null;
      var currentWorkerName = 'worker';

      ['ruHour', 'ruDay', 'ruProject'].forEach(function(id) {
        if (!H[id]) return;
        H[id].addEventListener('change', function() {
          if (this.checked) setRateSuffix(this.value);
        });
      });

      function applyProjectBillingToModal(meta) {
        var ru = (meta && meta.rate_unit) ? String(meta.rate_unit).toLowerCase() : '';
        if (ru === 'hour' && H.ruHour) H.ruHour.checked = true;
        else if (ru === 'day' && H.ruDay) H.ruDay.checked = true;
        else if (H.ruProject) H.ruProject.checked = true;
        setRateSuffix((ru === 'hour' || ru === 'day') ? ru : 'project');
      }

      function renderPreview(meta) {
        if (!H.preview) return;
        if (!meta || !Array.isArray(meta.files) || !meta.files.length) {
          H.preview.innerHTML = '<div class="text-muted small">No files attached.</div>';
        } else {
          var imgs = meta.files.filter(f => f.type === 'image').slice(0, 6);
          var pdfs = meta.files.filter(f => f.type === 'pdf').slice(0, 6);
          var html = '<div class="proj-preview">';
          imgs.forEach(f => {
            html += '<img class="proj-thumb" src="' + esc(f.url) + '" alt="' + esc(f.name) + '">';
          });
          pdfs.forEach(f => {
            html += '<a class="proj-chip" href="' + esc(f.url) + '" target="_blank" rel="noopener"><i class="mdi mdi-file-pdf-box"></i> ' + esc(f.name) + '</a>';
          });
          html += '</div>';
          H.preview.innerHTML = html;
        }
        applyProjectBillingToModal(meta);
      }

      function fetchAndPreview(pid) {
        if (!pid) {
          H.preview && (H.preview.innerHTML = '');
          applyProjectBillingToModal(null);
          return;
        }
        fetch('<?= site_url("projects/api/one/") ?>' + pid, {
            credentials: 'same-origin'
          })
          .then(r => r.json()).then(res => {
            if (!res || !res.ok) throw 0;
            renderPreview(res);
          })
          .catch(() => {
            H.preview && (H.preview.innerHTML = '<div class="text-muted small">Could not load files.</div>');
            applyProjectBillingToModal(null);
          });
      }

      function loadProjectsIntoModal() {
        if (!H.projectSel) return;
        H.projectSel.innerHTML = '<option value="">Loading…</option>';
        H.help.textContent = '';
        H.emptyWrap.classList.add('d-none');
        H.bodyWrap.classList.remove('d-none');
        H.preview && (H.preview.innerHTML = '');
        if (H.inviteChk) H.inviteChk.checked = true;
        if (H.ruProject) H.ruProject.checked = true;
        setRateSuffix('project');
        if (H.rateInput) H.rateInput.value = '';

        fetch('<?= site_url("projects/api/active-min") ?>', {
            credentials: 'same-origin'
          })
          .then(r => r.json())
          .then(res => {
            if (!res || !res.ok) throw 0;
            var items = res.items || [];
            if (!items.length) {
              H.bodyWrap.classList.add('d-none');
              H.emptyWrap.classList.remove('d-none');

              H.sendBtn.textContent = 'Go to Projects';
              H.sendBtn.setAttribute('data-redirect', '<?= site_url("projects/active") ?>');
              H.sendBtn.classList.remove('btn-primary');
              H.sendBtn.classList.add('btn-outline-primary');
              H.sendBtn.disabled = false;
              return;
            }
            var html = '<option value="">— No specific project —</option>';
            items.forEach(p => {
              html += '<option value="' + p.id + '">' + esc(p.title) + '</option>';
            });
            H.projectSel.innerHTML = html;
            H.help.textContent = 'You have ' + items.length + ' active project' + (items.length > 1 ? 's' : '') + '.';
            H.sendBtn.removeAttribute('data-redirect');
            H.sendBtn.classList.remove('btn-outline-primary');
            H.sendBtn.classList.add('btn-primary');
            H.sendBtn.disabled = false;
          })
          .catch(() => {
            H.projectSel.innerHTML = '<option value="">— No specific project —</option>';
            H.help.textContent = 'Could not load projects right now.';

            H.bodyWrap.classList.add('d-none');
            H.emptyWrap.classList.remove('d-none');

            H.sendBtn.textContent = 'Go to Projects';
            H.sendBtn.setAttribute('data-redirect', '<?= site_url("projects/active") ?>');
            H.sendBtn.classList.remove('btn-primary');
            H.sendBtn.classList.add('btn-outline-primary');
            H.sendBtn.disabled = false;
          });
      }

      document.querySelectorAll('.hire-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          currentWorkerId = this.getAttribute('data-user-id');
          currentWorkerName = this.getAttribute('data-worker-name') || 'worker';
          if (!currentWorkerId) return;
          loadProjectsIntoModal();
          H.sendBtn.disabled = false;
          H.sendBtn.classList.remove('disabled');
          H.modal.show();
        });
      });

      H.projectSel && H.projectSel.addEventListener('change', function() {
        fetchAndPreview(this.value);
      });

      H.sendBtn.addEventListener('click', function() {
        var redirectTo = this.getAttribute('data-redirect');
        if (redirectTo) {
          window.location.href = redirectTo;
          return;
        }

        if (!currentWorkerId) return;

        var fd = new FormData();
        fd.append('user_id', currentWorkerId);

        var pid = H.projectSel ? (H.projectSel.value || '') : '';
        if (pid) fd.append('project_id', pid);

        var ru = (H.ruHour && H.ruHour.checked) ? 'hour' :
          (H.ruDay && H.ruDay.checked) ? 'day' :
          'project';
        fd.append('rate_unit', ru);

        var rateVal = H.rateInput && H.rateInput.value ? H.rateInput.value : '';
        if (rateVal !== '') fd.append('rate', rateVal);

        if (H.inviteChk && H.inviteChk.checked) fd.append('invite', '1');

        var csrf = csrfPair();
        if (csrf) {
          fd.append(csrf.name, csrf.hash);
        }

        fetch('<?= site_url("notifications/notify_hire") ?>', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          })
          .then(r => r.ok ? r.json() : {
            ok: false,
            message: 'HTTP ' + r.status
          })
          .then(res => {
            var ok = !!(res && res.ok);
            if (ok) {
              H.modal.hide();
              toast('Hire request sent to ' + currentWorkerName + '. Please wait for confirmation.', 'success');
            } else {
              toast(res && res.message ? res.message : 'Could not send hire request', 'error');
            }
          })
          .catch(() => {
            toast('Could not send hire request', 'error');
          });
      });

      document.addEventListener('click', function(e) {
        const el = e.target.closest('[data-start-chat]');
        if (!el) return;
        e.preventDefault();
        const to = el.getAttribute('data-start-chat');
        if (!to) return;

        el.classList.add('disabled');
        el.setAttribute('aria-disabled', 'true');

        const fd = new FormData();
        const c = csrfPair();
        if (c) {
          fd.append(c.name, c.hash);
        }
        fd.append('to', to);

        fetch('<?= site_url('messages/api/start') ?>', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          })
          .then(r => r.ok ? r.json() : {
            ok: false,
            message: 'HTTP ' + r.status
          })
          .then(res => {
            if (res && res.ok && res.link) {
              window.location.href = res.link;
            } else {
              toast(res && res.message ? res.message : 'Could not start chat', 'error');
              el.classList.remove('disabled');
              el.removeAttribute('aria-disabled');
            }
          })
          .catch(() => {
            toast('Could not start chat', 'error');
            el.classList.remove('disabled');
            el.removeAttribute('aria-disabled');
          });
      });
    })();
  </script>


  <script>
    (function() {
      const wrap = document.querySelector('.chat-wrap');
      const role = (wrap?.dataset.role || '').toLowerCase();
      const tid = parseInt(wrap?.dataset.thread || '0', 10);
      const INV = window.__TW_INVITE || null;
      const STAT = window.__TW_INVITE_STATUS || null;

      if (!INV && !STAT) return;

      const banner = document.getElementById('projBanner');
      const bannerIn = document.getElementById('projBannerInner');

      function esc(s) {
        return String(s || '')
          .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
      }

      function renderFiles(files) {
        if (!Array.isArray(files) || !files.length) return '<div class="small text-muted">No files attached.</div>';
        let imgs = files.filter(f => f.type === 'image').slice(0, 6);
        let pdfs = files.filter(f => f.type === 'pdf').slice(0, 6);
        let html = '<div style="display:flex;flex-wrap:wrap;gap:10px">';
        imgs.forEach(f => {
          html += '<img src="' + esc(f.url) + '" class="proj-thumb" style="width:84px;height:84px;object-fit:cover;border:1px solid #e5e7eb;border-radius:10px">';
        });
        pdfs.forEach(f => {
          html += '<a href="' + esc(f.url) + '" target="_blank" rel="noopener" class="proj-chip"><i class="mdi mdi-file-pdf-box"></i> ' + esc(f.name) + '</a>';
        });
        html += '</div>';
        return html;
      }

      function render() {
        let html = '';
        if (INV) {
          html += '<div><div class="fw-semibold mb-1">Project: ' + esc(INV.title || '—') + '</div>' + renderFiles(INV.files) + '</div>';
        }

        if (STAT && STAT.state) {
          const isAcc = ('' + STAT.state).toLowerCase() === 'accepted';
          const label = isAcc ? 'Accepted' : 'Declined';
          html += '<div class="mt-2"><span class="badge ' + (isAcc ? 'bg-success' : 'bg-danger') + '">' + label + '</span></div>';
        } else if (INV && INV.can_act && role === 'worker') {
          html += '<div class="mt-2 d-flex gap-2">' +
            '<button id="btnAccept" class="btn btn-sm btn-primary"><i class="mdi mdi-check"></i> Accept</button>' +
            '<button id="btnDecline" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-close"></i> Decline</button>' +
            '</div>';
        } else if (INV && role === 'client') {
          html += '<div class="mt-2 alert alert-info py-2 px-3 mb-0">' +
            '<strong>Invitation sent.</strong> Waiting for the worker to accept or decline. You\'ll see updates here.' +
            '</div>';
        }

        bannerIn.innerHTML = html;
        banner.classList.remove('d-none');

        if (!STAT && INV && INV.can_act && role === 'worker') {
          const csrf = {
            name: '<?= $this->security->get_csrf_token_name(); ?>',
            hash: '<?= $this->security->get_csrf_hash(); ?>'
          };

          function send(action) {
            if (!tid || !INV?.pid) {
              window.showToast && window.showToast('Missing thread or project id', 'error');
              return;
            }
            const fd = new FormData();
            fd.append('thread_id', tid);
            fd.append('project_id', INV.pid);
            fd.append(csrf.name, csrf.hash);
            let url;
            if (action === 'accept') {
              if (INV.rate != null && INV.rate !== '') fd.append('rate', INV.rate);
              if (INV.rate_unit) fd.append('rate_unit', INV.rate_unit);
              url = '<?= site_url('transactions/api_accept') ?>';
            } else {
              fd.append('action', 'decline');
              url = '<?= site_url('messages/api/invite-action') ?>';
            }
            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                body: fd
              })
              .then(r => r.json())
              .then(res => {
                if (!res || !res.ok) {
                  window.showToast && window.showToast(res?.message || 'Failed', 'error');
                  return;
                }
                window.showToast && window.showToast(res.message || 'Updated', 'success');
                const isAcc = (action === 'accept');
                bannerIn.innerHTML =
                  (INV ? ('<div><div class="fw-semibold mb-1">Project: ' + esc(INV.title || '—') + '</div>' + renderFiles(INV.files) + '</div>') : '') +
                  '<div class="mt-2"><span class="badge ' + (isAcc ? 'bg-success' : 'bg-danger') + '">' + (isAcc ? 'Accepted' : 'Declined') + '</span></div>';
              })
              .catch(() => window.showToast && window.showToast('Failed', 'error'));
          }
          document.getElementById('btnAccept')?.addEventListener('click', () => send('accept'));
          document.getElementById('btnDecline')?.addEventListener('click', () => send('decline'));
        }
      }

      render();
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    (async function() {
      const canvas = document.getElementById('svcMixChart');
      if (!canvas) return;

      const pillsBox = document.getElementById('svcMixPills');
      const caption = document.getElementById('svcMixCaption');

      async function fetchMix(status) {
        const qs = status ? ('?status=' + encodeURIComponent(status)) : '';
        const res = await fetch('<?= site_url('services/mix') ?>' + qs, {
          credentials: 'same-origin'
        });
        const j = await res.json().catch(() => null);
        return (j && j.ok && j.items) ? j.items : {
          rows: [],
          total: 0
        };
      }

      function setCaption(kind) {
        const map = {
          completed: 'Share of completed jobs by skill',
          in_progress: 'Share of in-progress jobs by skill',
          any: 'Share of all hires by skill'
        };
        caption.textContent = map[kind] || 'Share of jobs by skill';
      }

      function legend(rows, total) {
        const box = document.getElementById('svcMixLegend');
        const html = (rows || []).map(r => {
          const pct = total ? Math.round((r.count / total) * 100) : 0;
          return `<div style="margin:.25rem 0"><strong>${r.title}</strong>: ${pct}% <span class="text-muted">(${r.count})</span></div>`;
        }).join('') || '<div class="text-muted">No data.</div>';
        box.innerHTML = html;
      }

      let chart;

      function drawPie(rows) {
        const labels = rows.map(r => r.title);
        const values = rows.map(r => r.count);
        if (chart) chart.destroy();
        chart = new Chart(canvas.getContext('2d'), {
          type: 'pie',
          data: {
            labels,
            datasets: [{
              data: values
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                callbacks: {
                  label: (ctx) => {
                    const c = ctx.parsed,
                      tot = values.reduce((a, b) => a + b, 0) || 1;
                    const pct = Math.round((c / tot) * 100);
                    return `${ctx.label}: ${c} (${pct}%)`;
                  }
                }
              }
            }
          }
        });
      }

      const [mixCompleted, mixInProg, mixAny] = await Promise.all([
        fetchMix('completed'),
        fetchMix('in_progress'),
        fetchMix('any')
      ]);

      const dataMap = {
        completed: mixCompleted,
        in_progress: mixInProg,
        any: mixAny
      };

      const hasCompleted = (mixCompleted.total || 0) > 0;
      const hasInProg = (mixInProg.total || 0) > 0;

      const pills = [];

      function makePill(key, label, total) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-sm btn-outline-primary';
        btn.dataset.key = key;
        btn.textContent = `${label} (${total||0})`;
        btn.addEventListener('click', () => {
          current = key;
          setActivePill();
          setCaption(current);
          const d0 = dataMap[current] || {
            rows: [],
            total: 0
          };
          drawPie(d0.rows);
          legend(d0.rows, d0.total);
        });
        return btn;
      }

      if (hasCompleted && hasInProg) {
        pills.push(makePill('completed', 'Completed', mixCompleted.total));
        pills.push(makePill('in_progress', 'In progress', mixInProg.total));
        pills.push(makePill('any', 'All hires', mixAny.total));
      } else if ((mixAny.total || 0) > 0) {
        // Only "any" has data → no pills needed
      }

      function setActivePill() {
        const kids = pillsBox.querySelectorAll('button[data-key]');
        kids.forEach(b => b.classList.toggle('btn-primary', b.dataset.key === current));
        kids.forEach(b => b.classList.toggle('btn-outline-primary', b.dataset.key !== current));
      }

      let current = hasCompleted ? 'completed' : (hasInProg ? 'in_progress' : 'any');

      if (pills.length) {
        pills.forEach(p => pillsBox.appendChild(p));
        pillsBox.style.display = 'flex';
        setActivePill();
      } else {
        pillsBox.style.display = 'none';
      }

      setCaption(current);
      const d0 = dataMap[current] || {
        rows: [],
        total: 0
      };
      drawPie(d0.rows);
      legend(d0.rows, d0.total);
    })();
  </script>

  <script>
    (function() {
      function labelize(table) {
        const heads = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        table.querySelectorAll('tbody tr').forEach(tr => {
          Array.from(tr.children).forEach((td, i) => {
            if (!td.hasAttribute('data-th') && heads[i]) td.setAttribute('data-th', heads[i]);
          });
        });
      }
      const tables = document.querySelectorAll('.box .table');
      tables.forEach(t => {
        labelize(t);
        const tb = t.tBodies && t.tBodies[0];
        if (tb) new MutationObserver(() => labelize(t)).observe(tb, {
          childList: true,
          subtree: true
        });
      });
    })();
  </script>

</body>

</html>
