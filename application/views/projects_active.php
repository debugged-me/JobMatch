<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'JobMatch DavOr', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.6') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">

  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />

  <style>
    html {
      scrollbar-gutter: stable;
    }

    :root {
      --ink: #0f172a;
      --muted: #6b7280;
      --line: #e5e7eb;
      --card: #fff;
      --indigo-200: #ffcccc;
      --indigo-300: #ffb3b3;
      --indigo-400: #ff9999;
      --indigo-500: #c1272d;
      --blue-focus: #c1272d;
      --shadow-1: 0 6px 18px rgba(2, 6, 23, .06), 0 1px 0 rgba(2, 6, 23, .04);
      --shadow-2: 0 16px 36px rgba(2, 6, 23, .12), 0 3px 10px rgba(2, 6, 23, .08)
    }

    body {
      background: #f6f7fb;
      color: var(--ink);
      font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial
    }

    .content-wrapper {
      padding-top: 1rem
    }

    .app {
      max-width: 1120px;
      margin: 0 auto;
      padding: 0 16px
    }

    .eyebrow {
      font-size: .85rem;
      color: var(--muted);
      font-weight: 600;
      letter-spacing: .3px
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      padding: .5rem .9rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: .9rem;
      transition: all .18s
    }

    .btn i {
      font-size: 1.05rem
    }

    .btn-brand {
      background: var(--indigo-500);
      border: 1px solid var(--indigo-500);
      color: #fff
    }

    .btn-brand:hover {
      background: var(--indigo-400);
      border-color: var(--indigo-400)
    }

    .btn-light {
      background: #fff;
      border: 1px solid var(--line);
      color: var(--ink)
    }

    .btn-light:hover {
      background: #f1f5f9
    }

    .btn-danger-soft {
      background: #fff;
      border: 1px solid #fecaca;
      color: #b91c1c
    }

    .btn-danger-soft:hover {
      background: #fee2e2
    }

    .form-control,
    select.form-control {
      width: 100%;
      background: #fff;
      border: 1px solid #111827;
      border-radius: 10px;
      padding: .70rem .9rem;
      font-size: 1rem;
      transition: border-color .18s ease, box-shadow .18s ease
    }

    .form-control:focus,
    select.form-control:focus {
      outline: 0;
      border-color: var(--blue-focus);
      box-shadow: 0 0 0 3px rgba(43, 77, 165, .15)
    }

    .form-text {
      color: var(--muted)
    }

    .card-flat {
      background: var(--card);
      border: 1px solid var(--indigo-200);
      border-radius: 14px;
      box-shadow: var(--shadow-1);
      transition: transform .16s, box-shadow .18s, border-color .18s
    }

    .card-flat:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-2);
      border-color: var(--indigo-400)
    }

    .pgrid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 18px
    }

    .p-card {
      overflow: hidden;
      display: flex;
      flex-direction: column
    }

    .p-cover {
      position: relative;
      height: 160px;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: inset 0 -12px 22px rgba(2, 6, 23, .05);
      overflow: hidden
    }

    .p-cover.hasimg {
      background-size: cover;
      background-position: center;
      box-shadow: inset 0 -38px 54px rgba(2, 6, 23, .18)
    }

    .p-overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding: 14px;
      background: linear-gradient(to top, rgba(15, 23, 42, .55), rgba(15, 23, 42, .15) 45%, rgba(15, 23, 42, 0));
      color: #fff;
      text-decoration: none;
      font-weight: 700;
      gap: .5rem;
      opacity: 0;
      transform: translateY(6px);
      transition: .18s
    }

    .p-card:hover .p-overlay {
      opacity: 1;
      transform: translateY(0)
    }

    .section {
      padding: 16px
    }

    .badge-soft {
      display: inline-flex;
      align-items: center;
      border-radius: 9999px;
      padding: .2rem .55rem;
      font-weight: 600;
      font-size: .8rem;
      border: 1px solid var(--indigo-200);
      background: #eef2ff;
      color: #3730a3
    }

    .badge-muted {
      border-color: #cbd5e1;
      background: #f1f5f9;
      color: #334155
    }

    .chips {
      display: flex;
      flex-wrap: wrap;
      gap: .4rem
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .28rem .6rem;
      border-radius: 9999px;
      border: 1px solid var(--line);
      background: #fff;
      font-size: .8rem;
      color: #334155;
      text-decoration: none;
      transition: all .18s;
      box-shadow: 0 2px 6px rgba(2, 6, 23, .06)
    }

    .chip:hover {
      border-color: var(--indigo-300);
      color: #1f2937
    }

    .meta {
      color: #94a3b8;
      font-size: .85rem
    }

    .form-label .eyebrow-hint {
      font-weight: 500;
      font-size: .78rem;
      color: var(--muted);
      margin-left: .35rem
    }

    .input-wrap {
      position: relative
    }

    .input-wrap .mdi {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      opacity: .55
    }

    .input-wrap .form-control,
    .input-wrap select.form-control {
      padding-left: 2.1rem
    }

    .helper-inline {
      display: flex;
      gap: .5rem;
      align-items: center;
      margin-top: .35rem
    }

    .helper-inline .link {
      font-weight: 600;
      cursor: pointer
    }

    .helper-inline .link:hover {
      text-decoration: underline
    }

    #customCatWrap {
      display: none
    }

    .select2-container--bootstrap4 .select2-selection {
      border-radius: 10px;
      border: 1px solid #111827;
      min-height: 44px;
      display: flex;
      align-items: center;
      padding: .2rem .5rem;
    }

    .select2-container--bootstrap4 .select2-selection__rendered {
      line-height: 1.2
    }

    .select2-container--bootstrap4 .select2-selection__clear {
      margin-right: .25rem
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
      height: 44px
    }

    .select2-container--bootstrap4 .select2-selection--multiple {
      min-height: 44px;
      height: 44px;
      overflow: hidden;
      align-items: center;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
      display: none;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-search__field {
      margin-top: 0;
    }

    .select2-container {
      width: 100% !important;
    }

    .select2-container--open .select2-dropdown {
      z-index: 3000;
    }

    .select2-container--bootstrap4 .select2-selection__rendered {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
    }

    .select2-results__option {
      white-space: normal;
    }

    .modal-backdrop-lite {
      position: fixed;
      inset: 0;
      background: rgba(2, 6, 23, .45);
      z-index: 1050;
      display: none;
      padding: 20px
    }

    .modal-card {
      max-width: 520px;
      margin: 5vh auto;
      background: #fff;
      border: 1px solid var(--indigo-200);
      border-radius: 14px;
      padding: 16px;
      box-shadow: var(--shadow-2)
    }

    #catChips .chip {
      position: relative;
      padding-right: 26px;
    }

    #catChips .chip .chip-x {
      position: absolute;
      right: 6px;
      top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: transparent;
      line-height: 1;
      font-size: 16px;
      cursor: pointer;
    }


    .addr-col {
      position: relative;
    }

    .addr-col .select2-container--bootstrap4 .select2-dropdown {
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
      min-width: 0 !important;
      max-width: none !important;
    }

    .addr-col {
      position: relative;
      overflow: visible;
    }

    .addr-dd.select2-dropdown {
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
      min-width: 0 !important;
      max-width: none !important;
      z-index: 3000;
    }

    .addr-dd .select2-results__options {
      max-height: 320px !important;
      overflow-y: auto !important;
    }

    .addr-dd .select2-results__option {
      white-space: normal;
    }

    .select2-container--bootstrap4 .select2-selection__rendered {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
    }

    .cat-col {
      position: relative;
      overflow: visible;
    }

    .cat-dd.select2-dropdown {
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
      min-width: 0 !important;
      max-width: none !important;
      z-index: 3000;
    }

    .cat-dd .select2-results__options {
      max-height: 320px !important;
      overflow-y: auto !important;
    }

    .cat-dd .select2-results__option {
      white-space: normal;
    }

    .select2-container--bootstrap4 .select2-selection--multiple {
      min-height: 44px;
      height: 44px;
      overflow: hidden;
      align-items: center;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
      display: none;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-search__field {
      margin-top: 0;
    }

    .select2-container--bootstrap4 .select2-selection__rendered {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
    }

    .select2-container--bootstrap4 .select2-selection {
      min-height: 48px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      padding: .2rem .5rem;
    }

    .select2-container--bootstrap4 .select2-selection__rendered {
      line-height: 1.2;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
      text-align: left;
    }


    .cat-col {
      position: relative;
      overflow: visible;
    }

    .cat-dd.select2-dropdown {
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
      min-width: 0 !important;
      max-width: none !important;
      z-index: 3000;
    }

    .cat-dd .select2-results__options {
      max-height: 60vh !important;
      overflow-y: auto !important;
    }

    .cat-dd .select2-results__option {
      white-space: normal;
    }

    .select2-container--bootstrap4 .select2-selection {
      display: block !important;
      min-height: 48px;
      border-radius: 10px;
      padding: .6rem .9rem;
    }

    .select2-container--bootstrap4 .select2-selection__rendered {
      text-align: left !important;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
      min-width: 0;
    }

    select.form-control {
      color: var(--muted);
    }

    .form-control.has-value {
      color: var(--ink) !important;
    }

    .select2-container--bootstrap4 .select2-selection__rendered {
      color: var(--muted);
    }

    .select2-container--bootstrap4.select2-has-value .select2-selection__rendered {
      color: var(--ink) !important;
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
              <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Projects', ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success d-flex align-items-center"><i class="mdi mdi-check-circle-outline me-2"></i><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger d-flex align-items-center"><i class="mdi mdi-alert-circle-outline me-2"></i><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>

            <?php
            if (!function_exists('is_img')) {
              function is_img($f)
              {
                return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
              }
            }
            if (!function_exists('is_pdf')) {
              function is_pdf($f)
              {
                return strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf';
              }
            }
            if (!function_exists('first_img')) {
              function first_img($arr)
              {
                foreach (($arr ?? []) as $f) {
                  if (is_img($f)) return $f;
                }
                return null;
              }
            }
            if (!function_exists('first_file')) {
              function first_file($arr)
              {
                foreach (($arr ?? []) as $f) {
                  return $f;
                }
                return null;
              }
            }
            if (!function_exists('file_label')) {
              function file_label($f)
              {
                if (!$f) return '';
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) return 'View image';
                if ($ext === 'pdf') return 'Open PDF';
                return 'Open file';
              }
            }
            if (!function_exists('file_icon')) {
              function file_icon($f)
              {
                if (!$f) return 'mdi-file-outline';
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) return 'mdi-eye-outline';
                if ($ext === 'pdf') return 'mdi-file-pdf-box';
                return 'mdi-file-outline';
              }
            }
            if (!function_exists('chip')) {
              function chip($f)
              {
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                $ico = ($ext === 'pdf') ? 'mdi-file-pdf-box' : (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) ? 'mdi-image-outline' : 'mdi-file-outline');
                return '<a class="chip" href="' . base_url($f) . '" target="_blank" title="' . html_escape(basename($f)) . '"><i class="mdi ' . $ico . '"></i>' . html_escape(basename($f)) . '</a>';
              }
            }
            ?>

            <div class="d-flex align-items-center gap-2 mb-3">
              <?php
              $isHistory    = isset($tab) && $tab === 'history';
              $isClientView = !empty($isClient) ? $isClient : ($this->session->userdata('role') === 'client');

              $activeUrl  = $isClientView ? site_url('projects/active')        : site_url('projects/worker_active');
              $historyUrl = $isClientView ? site_url('projects/history')       : site_url('projects/worker_history');
              ?>
              <a href="<?= $activeUrl ?>" class="btn <?= $isHistory ? 'btn-light' : 'btn-brand' ?>">
                <i class="mdi mdi-clipboard-text-clock-outline"></i> Active
              </a>
              <a href="<?= $historyUrl ?>" class="btn <?= $isHistory ? 'btn-brand' : 'btn-light' ?>">
                <i class="mdi mdi-history"></i> History
              </a>

              <?php if ($mode === 'list'): ?>
                <?php if ($isClientView): ?>
                  <a class="btn btn-brand ms-auto" href="<?= site_url('projects/create') ?>">
                    <i class="mdi mdi-plus"></i> Post Project
                  </a>
                  <?php if (isset($tab) && $tab === 'history'): ?>
                    <form method="post" action="<?= site_url('projects/clear_history') ?>" class="d-inline">
                      <?php if ($this->config->item('csrf_protection')): ?>
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                          value="<?= $this->security->get_csrf_hash(); ?>">
                      <?php endif; ?>
                      <button class="btn btn-danger-soft">
                        <i class="mdi mdi-broom"></i> Clear History
                      </button>
                    </form>

                    <form method="post" action="<?= site_url('projects/restore_history') ?>" class="d-inline">
                      <?php if ($this->config->item('csrf_protection')): ?>
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                          value="<?= $this->security->get_csrf_hash(); ?>">
                      <?php endif; ?>
                      <button class="btn btn-light">
                        <i class="mdi mdi-backup-restore"></i> Restore
                      </button>
                    </form>
                  <?php endif; ?>
                <?php endif; ?>
              <?php else: ?>
                <a class="btn btn-light ms-auto" href="<?= $activeUrl ?>">
                  <i class="mdi mdi-arrow-left"></i> Back to List
                </a>
              <?php endif; ?>
            </div>



            <?php if ($mode === 'list'): ?>

              <?php if (empty($items)): ?>
                <div class="card-flat section text-center">
                  <i class="mdi mdi-clipboard-list-outline" style="font-size:42px;color:#94a3b8"></i>
                  <p class="mb-2"><?= $isHistory ? 'No closed projects yet' : 'No active projects yet' ?></p>
                  <?php if (!$isHistory && !empty($isClientView) && $isClientView): ?>
                    <a class="btn btn-light" href="<?= site_url('projects/create') ?>">
                      <i class="mdi mdi-plus"></i> Post your first project
                    </a>
                  <?php endif; ?>
                </div>
              <?php else: ?>

                <div class="pgrid">
                  <?php foreach ($items as $it):
                    $cover = first_img($it['files'] ?? []);
                    $first = first_file($it['files'] ?? []);
                    $label = file_label($first);
                    $icon  = file_icon($first);
                    $more  = max(0, count($it['files'] ?? []) - 1);
                    $loc   = trim(($it['brgy'] ? $it['brgy'] . ', ' : '') . ($it['city'] ? $it['city'] . ($it['province'] ? ', ' : '') : '') . ($it['province'] ?? ''));

                    $agreedRow = $this->db->select('rate_agreed, rateUnit')
                      ->from('transactions')
                      ->where('projectID', (int)$it['id'])
                      ->where_in('status', ['accepted', 'active'])
                      ->where('rate_agreed IS NOT NULL', null, false)
                      ->order_by('transactionID', 'DESC')
                      ->limit(1)
                      ->get()
                      ->row();
                  ?>
                    <div class="card-flat p-card">
                      <div class="p-cover <?= $cover ? 'hasimg' : '' ?>" style="<?= $cover ? 'background-image:url(' . htmlspecialchars(base_url($cover), ENT_QUOTES, 'UTF-8') . ')' : '' ?>">
                        <?php if (!$cover): ?><i class="mdi mdi-image-multiple-outline" style="font-size:40px;color:#cbd5e1"></i><?php endif; ?>
                        <?php if (!empty($first)): ?>
                          <a class="p-overlay" href="<?= base_url($first) ?>" target="_blank" rel="noopener" aria-label="<?= html_escape($label) ?>">
                            <span class="badge-soft"><i class="mdi <?= $icon ?>"></i> <?= html_escape($label) ?><?= $more > 0 ? '<span class="meta ms-1">+' . (int)$more . ' more</span>' : '' ?></span>
                          </a>
                        <?php endif; ?>
                      </div>

                      <div class="section pt-3">
                        <div class="d-flex align-items-start mb-2">
                          <h6 class="mb-0 me-2 flex-grow-1"><?= html_escape($it['title']) ?></h6>
                          <span class="badge-soft <?= ($it['visibility'] === 'private') ? 'badge-muted' : '' ?>">
                            <?= $it['visibility'] === 'private' ? 'Private' : 'Public' ?>
                          </span>
                        </div>

                        <?php if (!empty($it['category'])): ?>
                          <div class="meta mb-1"><i class="mdi mdi-tag-multiple-outline me-1"></i><?= html_escape($it['category']) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($it['description'])): ?>
                          <p class="text-muted mb-2"><?= nl2br(html_escape($it['description'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($it['files'])): ?>
                          <div class="meta mb-1">Files</div>
                          <div class="chips mb-2"><?php foreach ($it['files'] as $f) echo chip($f); ?></div>
                        <?php endif; ?>

                        <div class="meta d-flex flex-wrap gap-3 align-items-center">
                          <span><i class="mdi mdi-calendar-blank me-1"></i><?= date('M d, Y', strtotime($it['created_at'])) ?></span>
                          <?php if ($loc): ?><span><i class="mdi mdi-map-marker-outline me-1"></i><?= html_escape($loc) ?></span><?php endif; ?>

                          <?php if ($agreedRow && $agreedRow->rate_agreed !== null): ?>
                            <span>
                              <i class="mdi mdi-cash me-1"></i>
                              Agreed: â‚±<?= number_format((float)$agreedRow->rate_agreed, 2) ?>
                              <?= $agreedRow->rateUnit ? ' / ' . html_escape($agreedRow->rateUnit) : '' ?>
                            </span>
                          <?php elseif ($it['budget_min'] !== null || $it['budget_max'] !== null || $it['employment_term'] || $it['payment_cycle']): ?>
                            <span>
                              <i class="mdi mdi-cash me-1"></i>
                              <?php
                              $min = $it['budget_min'] !== null ? number_format((float)$it['budget_min'], 2) : '—';
                              $max = $it['budget_max'] !== null ? number_format((float)$it['budget_max'], 2) : '—';
                              echo $min . ' – ' . $max;

                              // Employment term label
                              $termMap = [
                                '1_month'       => '1 mo',
                                '6_months'      => '6 mos',
                                '1_year'        => '1 yr',
                                'project_based' => 'project-based'
                              ];
                              $termLabel = isset($termMap[$it['employment_term'] ?? '']) ? $termMap[$it['employment_term']] : '';

                              // Payment label
                              $payLabel = '';
                              if (!empty($it['payment_cycle'])) {
                                $payLabel = ($it['payment_cycle'] === 'monthly') ? 'monthly' : 'yearly';
                              }

                              $extras = [];
                              if ($termLabel) $extras[] = $termLabel;
                              if ($payLabel)  $extras[] = $payLabel;

                              if (!empty($extras)) {
                                echo ' <span class="meta">(' . html_escape(implode(' Â· ', $extras)) . ')</span>';
                              }

                              // Duration
                              if (!empty($it['project_duration_value']) && !empty($it['project_duration_unit'])) {
                                $dv = (int)$it['project_duration_value'];
                                $du = $it['project_duration_unit'];
                                $duText = $du . ($dv > 1 ? 's' : '');
                                echo ' <span class="meta">• ' . (int)$dv . ' ' . html_escape($duText) . '</span>';
                              }
                              ?>
                            </span>
                          <?php endif; ?>

                        </div>
                        <?php if (!empty($isClient) && $isClient && isset($tab) && $tab === 'history'): ?>
                          <?php
                          $txs = $this->db->select('transactionID, workerID')
                            ->from('transactions')
                            ->where('projectID', (int)$it['id'])
                            ->where_in('status', ['accepted', 'active', 'completed'])
                            ->order_by('transactionID', 'DESC')
                            ->get()->result();

                          foreach ($txs as $txRow) {
                            $has = $this->db->select('reviewID')
                              ->from('reviews')
                              ->where('transactionID', (int)$txRow->transactionID)
                              ->where('clientID', (int)$this->session->userdata('user_id'))
                              ->get()->row();

                            if (!$has) {
                              $wu = $this->db->select('first_name,last_name')
                                ->from('users')
                                ->where('id', (int)$txRow->workerID)
                                ->get()->row();
                              $wname = $wu ? trim(($wu->first_name ?? '') . ' ' . ($wu->last_name ?? '')) : ('Worker #' . $txRow->workerID);
                          ?>
                              <div class="d-flex justify-content-end gap-2 mt-2">
                                <button type="button"
                                  class="btn btn-brand"
                                  data-review-open
                                  data-tx="<?= (int)$txRow->transactionID ?>"
                                  data-pid="<?= (int)$it['id'] ?>"
                                  data-wid="<?= (int)$txRow->workerID ?>"
                                  data-wname="<?= html_escape($wname) ?>">
                                  <i class="mdi mdi-star-outline"></i> Review <?= html_escape($wname) ?>
                                </button>
                              </div>
                          <?php
                            }
                          }
                          ?>
                        <?php endif; ?>

                        <?php if (!empty($isClient) && $isClient && $it['status'] === 'active'): ?>
                          <?php
                          $tx = $this->db->select('status')
                            ->from('transactions')
                            ->where('projectID', (int)$it['id'])
                            ->order_by('transactionID', 'DESC')
                            ->limit(1)
                            ->get()
                            ->row();

                          $actionStatus = '';
                          if ($tx) {
                            $st = strtolower((string)$tx->status);

                            if (in_array($st, ['accepted', 'active'], true)) {
                              $actionStatus =
                                '<span class="badge-soft" style="border-color:#bbf7d0;background:#ecfdf5;color:#065f46">
             <i class="mdi mdi-progress-clock"></i> Ongoing
           </span>';
                            } elseif (in_array($st, ['declined', 'denied'], true)) {
                              $actionStatus =
                                '<span class="badge-soft" style="border-color:#fecaca;background:#fff1f2;color:#b91c1c">
             <i class="mdi mdi-close-octagon-outline"></i> Denied
           </span>';
                            } elseif ($st === 'completed') {
                              $actionStatus =
                                '<span class="badge-soft" style="border-color:#fde68a;background:#fffbeb;color:#92400e">
             <i class="mdi mdi-check-decagram"></i> Completed
           </span>';
                            } elseif ($st === 'invited') {
                              $actionStatus =
                                '<span class="badge-soft" style="border-color:#cbd5e1;background:#f1f5f9;color:#334155">
             <i class="mdi mdi-email-outline"></i> Invited
           </span>';
                            }
                          }
                          ?>

                          <div class="d-flex justify-content-end gap-2 mt-2">
                            <?php if ($actionStatus !== ''): ?>
                              <div class="d-flex align-items-center"><?= $actionStatus ?></div>
                            <?php else: ?>
                              <a href="<?= site_url('projects/find_workers/' . $it['id']) ?>" class="btn btn-brand">
                                <i class="mdi mdi-account-search-outline"></i> Find Workers
                              </a>
                            <?php endif; ?>

                            <a href="<?= site_url('projects/close/' . $it['id']) ?>" class="btn btn-danger-soft"
                              onclick="return confirm('Close this project?');">
                              <i class="mdi mdi-close-octagon-outline"></i> Close
                            </a>
                          </div>
                        <?php endif; ?>


                      </div>

                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="mt-3"><?= $pagination ?? '' ?></div>
              <?php endif; ?>

            <?php else: ?>

              <div class="card-flat section">
                <form method="post" action="<?= site_url('projects/store') ?>" enctype="multipart/form-data">
                  <?php if ($this->config->item('csrf_protection')): ?>
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                  <?php endif; ?>

                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                      <input type="text" name="title" class="form-control" required maxlength="150"
                        placeholder="ex. Electrical rewiring for 2-storey house">
                    </div>

                    <div class="col-12">
                      <label class="form-label fw-semibold mb-1">Categories</label>
                      <small class="eyebrow-hint d-block mb-2">select one or more, or type to add</small>

                      <div class="cat-col">
                        <select name="categories[]" class="form-control js-skill-select" multiple
                          data-placeholder="Select categories">
                          <?php foreach ($skills as $s): ?>
                            <option value="<?= (int)$s->skillID ?>"><?= html_escape($s->Title) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>

                      <div id="catChips" class="chips mt-2"></div>
                    </div>






                    <div class="col-12">
                      <label class="form-label fw-semibold">Description</label>
                      <textarea name="description" class="form-control" rows="5" maxlength="5000" placeholder="Describe the job or business need..."></textarea>
                    </div>
                    <!-- ===== Row A: Employment & Pay (3 cols) ===== -->
                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Employment Term</label>
                      <select name="employment_term" class="form-control js-val">
                        <option value="">Select</option>
                        <option value="1_month">1 month</option>
                        <option value="6_months">6 months</option>
                        <option value="1_year">1 year</option>
                        <option value="project_based">Project-based</option>
                      </select>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Payment</label>
                      <select name="payment_cycle" class="form-control js-val" id="payment_cycle">
                        <option value="">Select</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                      </select>
                      <div class="form-text">Choose how often the worker is paid.</div>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Pay Unit</label>
                      <select name="payment_unit" class="form-control js-val" id="payment_unit" disabled>
                        <option value="">Select</option>
                      </select>
                      <div class="form-text">Changes based on "Payment".</div>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Project Duration</label>
                      <div class="d-flex gap-2">
                        <input type="number" min="1" name="project_duration_value" class="form-control" placeholder="ex. 3">
                        <select name="project_duration_unit" class="form-control js-val" style="max-width:160px">
                          <option value="">Unit</option>
                          <option value="day">Day(s)</option>
                          <option value="week">Week(s)</option>
                          <option value="month">Month(s)</option>
                          <option value="year">Year(s)</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Budget Min</label>
                      <input type="number" step="0.01" name="budget_min" class="form-control" placeholder="0.00">
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Budget Max</label>
                      <input type="number" step="0.01" name="budget_max" class="form-control" placeholder="0.00">
                    </div>


                    <div class="col-md-4 addr-col">
                      <label class="form-label fw-semibold">Province</label>
                      <select id="proj_province" name="province"
                        class="form-control js-addr-select"
                        data-pre="<?= html_escape(set_value('province', '')) ?>">
                        <option value=""></option>
                      </select>
                    </div>

                    <div class="col-md-4 addr-col">
                      <label class="form-label fw-semibold">City/Municipality</label>
                      <select id="proj_city" name="city"
                        class="form-control js-addr-select"
                        data-pre="<?= html_escape(set_value('city', '')) ?>" disabled>
                        <option value=""></option>
                      </select>
                    </div>

                    <div class="col-md-4 addr-col">
                      <label class="form-label fw-semibold">Barangay</label>
                      <select id="proj_brgy" name="brgy"
                        class="form-control js-addr-select"
                        data-pre="<?= html_escape(set_value('brgy', '')) ?>" disabled>
                        <option value=""></option>
                      </select>
                    </div>


                    <div class="col-12">
                      <label class="form-label fw-semibold">Files (PDF / Images)</label>
                      <input type="file" name="files[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
                      <div class="form-text">You can upload multiple files (max 10MB each).</div>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">Visibility</label>
                      <select name="visibility" class="form-control">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                      </select>
                    </div>
                  </div>

                  <div class="d-flex gap-2 justify-content-end mt-3">
                    <a class="btn btn-light" href="<?= site_url('projects/active') ?>">Cancel</a>
                    <button class="btn btn-brand">Post Project</button>
                  </div>
                </form>
              </div>

            <?php endif; ?>
            <div id="reviewModal" class="modal-backdrop-lite" role="dialog" aria-modal="true" aria-hidden="true">
              <div class="modal-card card-flat">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="mb-0"><i class="mdi mdi-star-outline me-1"></i> Leave a Review</h6>
                  <button type="button" class="btn btn-light btn-sm" id="reviewCloseBtn">
                    <i class="mdi mdi-close"></i>
                  </button>
                </div>

                <form method="post" action="<?= site_url('projects/save_review') ?>">
                  <?php if ($this->config->item('csrf_protection')): ?>
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                  <?php endif; ?>

                  <input type="hidden" name="transactionID" id="rv_tx">
                  <input type="hidden" id="rv_pid" value="">
                  <div class="mb-2">
                    <label class="form-label fw-semibold">Worker</label>
                    <input type="text" class="form-control" id="rv_wname" disabled>
                  </div>
                  <div class="mb-2">
                    <label class="form-label fw-semibold">Rating</label>
                    <select name="rating" class="form-control" required>
                      <option value="5">5 - Excellent</option>
                      <option value="4">4 - Very Good</option>
                      <option value="3">3 - Good</option>
                      <option value="2">2 - Fair</option>
                      <option value="1">1 - Poor</option>
                    </select>
                  </div>
                  <div class="mb-2">
                    <label class="form-label fw-semibold">Comment <span class="text-muted">(optional)</span></label>
                    <textarea name="comment" class="form-control" rows="4" maxlength="2000" placeholder="Share feedback about the work..."></textarea>
                  </div>
                  <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" id="reviewCancelBtn">Cancel</button>
                    <button class="btn btn-brand">Submit Review</button>
                  </div>
                </form>
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

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    (function() {
      if (!window.jQuery || !jQuery.fn.select2) {
        console.warn('Select2 not initialized (jQuery missing). Using native <select>.');
        return;
      }

      var $sel = jQuery('.js-skill-select');

      var $parent = $sel.closest('.cat-col');
      if (!$parent.length) $parent = jQuery(document.body);

      if (!$sel.data('select2')) {
        $sel.select2({
          theme: 'bootstrap4',
          width: '100%',
          multiple: true,
          tags: true,
          allowClear: true,
          closeOnSelect: false,
          dropdownParent: $parent,
          dropdownCssClass: 'cat-dd',
          placeholder: function() {
            var el = jQuery(this);
            return el.data('placeholder') || 'Select categories';
          }
        });
      }

      function renderChips() {
        var data = $sel.select2('data') || [];
        var $out = jQuery('#catChips').empty();
        data.forEach(function(item) {
          if (!item.id) return;
          var safeText = jQuery('<div>').text(item.text).html();
          var $chip = jQuery(
            '<span class="chip" data-val="' + item.id + '">' +
            '<i class="mdi mdi-tag-outline"></i> ' + safeText +
            '<button type="button" class="chip-x" aria-label="Remove">&times;</button>' +
            '</span>'
          );
          $out.append($chip);
        });
      }
      $sel.on('change select2:select select2:unselect', renderChips);
      renderChips();

      jQuery(document).on('click', '#catChips .chip .chip-x', function() {
        var val = jQuery(this).closest('.chip').data('val');
        var $opt = $sel.find('option').filter(function() {
          return jQuery(this).val() == val;
        });
        if ($opt.length === 0) {
          $opt = jQuery('<option>').val(val).text(val).appendTo($sel);
        }
        $opt.prop('selected', false);
        $sel.trigger('change');
      });
    })();
  </script>


  <script>
    (function() {
      var modal = document.getElementById('reviewModal');
      var txInput = document.getElementById('rv_tx');
      var pidInput = document.getElementById('rv_pid');
      var wnameEl = document.getElementById('rv_wname');

      function openReview(opts) {
        txInput.value = opts.tx || '';
        pidInput.value = opts.pid || '';
        wnameEl.value = opts.wname || '';
        modal.style.display = 'block';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
      }

      function closeReview() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }
      document.querySelectorAll('[data-review-open]').forEach(function(btn) {
        btn.addEventListener('click', function() {
          openReview({
            tx: this.getAttribute('data-tx'),
            pid: this.getAttribute('data-pid'),
            wname: this.getAttribute('data-wname')
          });
        });
      });
      var closeBtn = document.getElementById('reviewCloseBtn');
      var cancelBtn = document.getElementById('reviewCancelBtn');
      if (closeBtn) closeBtn.addEventListener('click', closeReview);
      if (cancelBtn) cancelBtn.addEventListener('click', closeReview);
      modal.addEventListener('click', function(e) {
        if (e.target === modal) closeReview();
      });
      var params = new URLSearchParams(location.search);
      var autoPid = params.get('review');
      if (autoPid) {
        var autoBtn = document.querySelector('[data-review-open][data-pid="' + autoPid + '"]');
        if (autoBtn) autoBtn.click();
      }
    })();
  </script>

  <script>
    (function() {
      if (!window.jQuery || !jQuery.fn.select2) return;

      const apiBase = '<?= site_url('address/api') ?>';

      const $prov = jQuery('#proj_province');
      const $city = jQuery('#proj_city');
      const $brgy = jQuery('#proj_brgy');

      const $provParent = $prov.closest('.addr-col');
      const $cityParent = $city.closest('.addr-col');
      const $brgyParent = $brgy.closest('.addr-col');

      function tplSelection(data) {
        if (!data || !data.text) return '';
        const text = data.text.toString();
        const short = text.length > 48 ? (text.slice(0, 48) + '…') : text;
        return jQuery('<span>').text(short).attr('title', text);
      }

      function tplResult(data) {
        if (!data || !data.text) return '';
        return jQuery('<div>').text(data.text);
      }

      const s2 = {
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select',
        templateSelection: tplSelection,
        templateResult: tplResult,
        dropdownCssClass: 'addr-dd'
      };

      $prov.select2({
        ...s2,
        placeholder: 'Select province',
        dropdownParent: $provParent
      });
      $city.select2({
        ...s2,
        placeholder: 'Select city/municipality',
        dropdownParent: $cityParent
      });
      $brgy.select2({
        ...s2,
        placeholder: 'Select barangay',
        dropdownParent: $brgyParent
      });

      const pre = {
        province: ($prov.data('pre') || '').trim(),
        city: ($city.data('pre') || '').trim(),
        brgy: ($brgy.data('pre') || '').trim()
      };

      function safe(v) {
        return jQuery('<div>').text(v).html();
      }

      function fill($el, items) {
        const opts = ['<option value=""></option>'].concat(items.map(v => `<option value="${safe(v)}">${safe(v)}</option>`));
        $el.html(opts.join(''));
        $el.prop('disabled', items.length === 0);
        $el.trigger('change.select2');
      }

      function api(params) {
        const url = apiBase + '?' + new URLSearchParams(params).toString();
        return fetch(url, {
            credentials: 'same-origin'
          })
          .then(r => r.json())
          .then(j => {
            if (!j.ok) throw new Error(j.msg || 'Address API error');
            return j.items || [];
          });
      }

      async function loadProvinces() {
        $prov.prop('disabled', true);
        try {
          const items = await api({
            scope: 'province'
          });
          fill($prov, items);
          $prov.prop('disabled', false);

          if (pre.province && items.includes(pre.province)) {
            $prov.val(pre.province).trigger('change');
            await onProvince(true);
          }
        } catch (e) {
          console.error(e);
        }
      }

      async function onProvince(init) {
        const pv = $prov.val();
        fill($city, []);
        fill($brgy, []);
        $city.prop('disabled', !pv);
        $brgy.prop('disabled', true);
        if (!pv) return;

        try {
          const items = await api({
            scope: 'city',
            province: pv
          });
          fill($city, items);
          $city.prop('disabled', false);

          if (init && pre.city && items.includes(pre.city)) {
            $city.val(pre.city).trigger('change');
            await onCity(true);
          }
        } catch (e) {
          console.error(e);
        }
      }

      async function onCity(init) {
        const pv = $prov.val(),
          ct = $city.val();
        fill($brgy, []);
        $brgy.prop('disabled', !(pv && ct));
        if (!(pv && ct)) return;

        try {
          const items = await api({
            scope: 'brgy',
            province: pv,
            city: ct
          });
          fill($brgy, items);
          $brgy.prop('disabled', false);

          if (init && pre.brgy && items.includes(pre.brgy)) {
            $brgy.val(pre.brgy).trigger('change');
          }
        } catch (e) {
          console.error(e);
        }
      }

      $prov.on('change', () => onProvince(false));
      $city.on('change', () => onCity(false));

      jQuery('form[action*="projects/store"]').on('submit', function() {
        if ($prov.val()) $prov.prop('disabled', false);
        if ($city.val()) $city.prop('disabled', false);
        if ($brgy.val()) $brgy.prop('disabled', false);
      });

      loadProvinces();
    })();
  </script>

  <script>
    (function() {
      var payCycleSel = document.getElementById('payment_cycle');
      var payUnitSel = document.getElementById('payment_unit');

      if (!payCycleSel || !payUnitSel) return;

      var optionsMap = {
        'monthly': [{
            v: 'hour',
            t: 'Per hour'
          },
          {
            v: 'day',
            t: 'Per day'
          },
          {
            v: 'month',
            t: 'Per month'
          }
        ],
        'yearly': [{
            v: 'day',
            t: 'Per day'
          },
          {
            v: 'month',
            t: 'Per month'
          },
          {
            v: 'year',
            t: 'Per year'
          }
        ]
      };

      function fillPayUnits() {
        var cycle = payCycleSel.value;
        var opts = optionsMap[cycle] || [];
        payUnitSel.innerHTML = '<option value="">Select</option>';
        if (!opts.length) {
          payUnitSel.disabled = true;
          payUnitSel.classList.remove('has-value');
          return;
        }
        opts.forEach(function(o) {
          var opt = document.createElement('option');
          opt.value = o.v;
          opt.textContent = o.t;
          payUnitSel.appendChild(opt);
        });
        payUnitSel.disabled = false;
        payUnitSel.classList.toggle('has-value', !!payUnitSel.value);
      }


      fillPayUnits();
      payCycleSel.addEventListener('change', fillPayUnits);
    })();
  </script>
  <script>
    (function() {
      document.querySelectorAll('select.form-control.js-val').forEach(function(sel) {
        function paint() {
          sel.classList.toggle('has-value', !!sel.value);
        }
        paint();
        sel.addEventListener('change', paint);
      });

      if (window.jQuery && jQuery.fn.select2) {
        function markS2($el) {
          var has = !!($el.val() && ($el.val() + '' !== ''));
          $el.next('.select2').toggleClass('select2-has-value', has);
        }
        jQuery('select.form-control.js-val').each(function() {
          markS2(jQuery(this));
        });
        jQuery(document).on('change', 'select.form-control.js-val', function() {
          markS2(jQuery(this));
        });

        jQuery('.js-addr-select, .js-skill-select').each(function() {
          var $t = jQuery(this);

          function sync() {
            var val = $t.val();
            var has = Array.isArray(val) ? val.length > 0 : !!val;
            $t.next('.select2').toggleClass('select2-has-value', has);
          }
          sync();
          $t.on('change', sync);
        });
      }
    })();
  </script>

</body>

</html>