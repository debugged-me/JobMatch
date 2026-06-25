  <?php defined('BASEPATH') or exit('No direct script access allowed');
  $page_title = 'Complaints (All)'; ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($page_title ?? 'JobMatch DavOr', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
    <style>
      :root {
        --brand: #c1272d;
        --brand-700: #9e1b21
      }

      body {
        background: linear-gradient(180deg, #f6f8fc, #eef2f7 60%, #e9edf3 100%)
      }

      .app {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 12px
      }

      .panel {
        background: #fff;
        border: 1px solid #d9dee7;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(2, 6, 23, .08);
        padding: 14px
      }

      /* Hero header */
      .page-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--brand) 0%, var(--brand-700) 100%);
        border-radius: 18px;
        padding: 22px 24px;
        color: #fff;
        box-shadow: 0 14px 30px rgba(193, 39, 45, .26);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px
      }

      .page-hero::after {
        content: "";
        position: absolute;
        right: -40px;
        top: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 255, 255, .16), transparent 70%);
        pointer-events: none
      }

      .page-hero .hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        z-index: 1
      }

      .page-hero .hero-ic {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        background: rgba(255, 255, 255, .16);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px
      }

      .page-hero h1 {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -.2px
      }

      .page-hero p {
        margin: 2px 0 0;
        font-size: 13px;
        opacity: .9
      }

      .hero-stat {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, .15);
        border: 1px solid rgba(255, 255, 255, .25);
        color: #fff;
        border-radius: 9999px;
        padding: .5rem .85rem;
        font-weight: 700;
        font-size: 13px;
        position: relative;
        z-index: 1
      }

      /* Status summary cards */
      .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 16px
      }

      @media (max-width: 760px) {
        .stat-grid {
          grid-template-columns: repeat(2, 1fr)
        }
      }

      .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-left: 4px solid var(--accent, #cbd5e1);
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 4px 12px rgba(2, 6, 23, .05);
        cursor: pointer;
        transition: transform .15s, box-shadow .15s;
        text-decoration: none;
        display: block
      }

      .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(2, 6, 23, .1)
      }

      .stat-card.active {
        box-shadow: 0 0 0 2px var(--accent, #cbd5e1) inset, 0 8px 18px rgba(2, 6, 23, .08)
      }

      .stat-card .sc-top {
        display: flex;
        align-items: center;
        justify-content: space-between
      }

      .stat-card .sc-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .03em
      }

      .stat-card .sc-num {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        margin-top: 6px
      }

      .stat-card .sc-ic {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px
      }

      .filter-grid {
        display: grid;
        grid-template-columns: 220px 1fr 120px;
        gap: 10px;
        margin-bottom: 12px
      }

      /* Filter toolbar */
      .filter-toolbar {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eef2f7
      }

      .ft-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 170px
      }

      .ft-grow {
        flex: 1;
        min-width: 220px
      }

      .ft-field label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        margin: 0
      }

      .ctrl,
      .ctrl-search {
        height: 42px;
        border: 1px solid #d9dee7;
        border-radius: 10px;
        background: #fff;
        font-size: 14px;
        color: #0f172a;
        transition: border-color .15s, box-shadow .15s
      }

      .ctrl {
        padding: 0 12px;
        width: 100%
      }

      .ctrl:focus,
      .ctrl-search:focus-within {
        outline: 0;
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(193, 39, 45, .12)
      }

      .ctrl-search {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 12px
      }

      .ctrl-search i {
        color: #94a3b8;
        font-size: 18px
      }

      .ctrl-search input {
        border: 0;
        outline: 0;
        background: transparent;
        width: 100%;
        height: 100%;
        font-size: 14px;
        color: #0f172a
      }

      .ft-actions {
        display: flex;
        gap: 8px
      }

      .ft-actions .btn {
        height: 42px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 10px;
        font-weight: 700;
        padding: 0 16px
      }

      .filter-toolbar .btn-primary {
        background: var(--brand);
        border-color: var(--brand)
      }

      .filter-toolbar .btn-primary:hover {
        background: var(--brand-700);
        border-color: var(--brand-700)
      }

      /* Table */
      .complaints-table {
        margin: 0
      }

      .complaints-table thead th {
        background: #f8fafc;
        border-top: 0;
        border-bottom: 1px solid #e5e7eb;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-size: .72rem;
        font-weight: 700;
        color: #64748b;
        padding: 12px 14px
      }

      .complaints-table tbody td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle
      }

      .complaints-table tbody tr {
        transition: background .12s
      }

      .complaints-table tbody tr:hover td {
        background: #fcfdff
      }

      .complaints-table tbody tr td:first-child {
        font-weight: 600;
        color: #0f172a
      }

      .text-muted {
        color: #64748b !important
      }

      .badge-chip {
        border-radius: 999px;
        padding: .35rem .6rem;
        font-weight: 700;
        font-size: 11.5px;
        letter-spacing: .25px
      }

      .badge-type {
        background: #ffe5e8;
        color: #9a0820;
        border: 1px solid #ffc4cb
      }

      .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: .3rem .6rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
        letter-spacing: .2px;
        border: 1px solid transparent
      }

      .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%
      }

      .status-open {
        background: #fff7ed;
        border-color: #fdba74;
        color: #9a3412
      }

      .status-open .status-dot {
        background: #f59e0b
      }

      .status-under {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1e3a8a
      }

      .status-under .status-dot {
        background: #1e3a8a
      }

      .status-resolved {
        background: rgba(251, 191, 36, .18);
        border-color: rgba(251, 191, 36, .5);
        color: #92400e
      }

      .status-resolved .status-dot {
        background: #fbbf24
      }

      .status-dismissed {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #334155
      }

      .status-dismissed .status-dot {
        background: #94a3b8
      }

      @media (max-width: 768px) {
        .filter-grid {
          grid-template-columns: 1fr;
          gap: 8px;
        }

        .filter-grid .btn {
          width: 100%;
        }

        .table-responsive {
          overflow: visible;
        }

        .table thead {
          display: none;
        }

        .table {
          border-collapse: separate;
          border-spacing: 0 12px;
        }

        .table tbody,
        .table tr,
        .table td {
          display: block;
          width: 100%;
        }

        .table tbody tr {
          background: #fff;
          border: 1px solid #d9dee7;
          border-radius: 12px;
          box-shadow: 0 6px 16px rgba(2, 6, 23, .06);
          padding: 10px 12px;
          cursor: pointer;
        }

        .table tbody tr:hover {
          background: #fff;
        }

        .table tbody tr td {
          display: grid;
          grid-template-columns: 110px 1fr;
          gap: 8px;
          align-items: baseline;
          padding: 8px 0;
          border-bottom: 1px dashed #e5e7eb;
        }

        .table tbody tr td:last-child {
          border-bottom: 0;
          padding-bottom: 2px;
        }

        .table td::before {
          content: attr(data-label);
          text-transform: uppercase;
          font: 700 10px/1 Inter, system-ui, -apple-system, "Segoe UI", Roboto;
          color: #64748b;
          letter-spacing: .35px;
          align-self: start;
        }

        .table td:not([data-label]) {
          display: block;
          grid-template-columns: 1fr;
          border-bottom: 0;
        }

        .table td:not([data-label])::before {
          content: none;
        }

        .text-truncate {
          max-width: 100% !important;
        }
      }

      @media (max-width: 768px) {
        .panel .filter-grid {
          position: sticky;
          top: 0;
          z-index: 15;
          background: #fff;
          padding-top: 4px;
          padding-bottom: 8px;
        }
      }

      @media (max-width:768px) {
        .panel .table tbody tr td {
          grid-template-columns: minmax(84px, 36%) 1fr;
        }

        .panel .table tbody tr td>* {
          overflow-wrap: anywhere;
          word-break: break-word;
        }

        .panel .table tbody tr td[data-label="Title"]>* {
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .panel .table tbody tr {
          position: relative;
          overflow: hidden;
          isolation: isolate;
          box-shadow: 0 6px 16px rgba(2, 6, 23, .06);
          margin: 0;
        }

        .panel .table tbody tr td:last-child {
          border-bottom: 0;
        }

        .panel .table tbody tr td:not([data-th]) {
          border-bottom: 0;
        }

        .badge-chip {
          font-size: 11px;
          padding: .28rem .55rem;
        }

        .status-pill {
          padding: .28rem .55rem;
        }
      }

      @media (max-width: 768px) {
        .filter-grid {
          grid-template-columns: 1fr !important;
          gap: 8px !important;
        }

        .filter-grid .btn {
          width: 100% !important;
        }

        .complaints-table thead {
          display: none !important;
        }

        .complaints-table {
          border-collapse: separate !important;
          border-spacing: 0 12px !important;
          width: 100%;
        }

        .complaints-table tbody,
        .complaints-table tr,
        .complaints-table td {
          display: block !important;
          width: 100% !important;
          box-sizing: border-box;
        }

        .complaints-table tbody tr {
          background: #fff !important;
          border: 1px solid #d9dee7 !important;
          border-radius: 12px !important;
          box-shadow: 0 6px 16px rgba(2, 6, 23, .06) !important;
          padding: 10px 12px !important;
          margin: 0 !important;
          position: relative;
          overflow: hidden;
          isolation: isolate;
        }

        .complaints-table.table-hover>tbody>tr:hover>* {
          background-color: #fff !important;
        }

        .complaints-table tbody tr td {
          display: grid !important;
          grid-template-columns: minmax(96px, 44%) 1fr !important;
          gap: 8px !important;
          align-items: baseline !important;
          padding: 8px 0 !important;
          border-bottom: 1px dashed #e5e7eb !important;
        }

        .complaints-table tbody tr td:last-child {
          border-bottom: 0 !important;
          padding-bottom: 2px !important;
        }

        .complaints-table td::before {
          content: attr(data-label);
          text-transform: uppercase;
          font: 700 10px/1 Inter, system-ui, -apple-system, "Segoe UI", Roboto;
          color: #64748b;
          letter-spacing: .35px;
          align-self: start;
        }

        .complaints-table td>* {
          overflow-wrap: anywhere;
          word-break: break-word;
          -webkit-hyphens: auto;
          hyphens: auto;
        }

        .complaints-table td[data-label="Title"]>* {
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .badge-chip {
          font-size: 11px !important;
          padding: .28rem .55rem !important;
        }

        .status-pill {
          padding: .28rem .55rem !important;
        }

        .panel .filter-grid {
          position: sticky;
          top: 0;
          z-index: 15;
          background: #fff;
          padding-top: 4px;
          padding-bottom: 8px;
        }
      }
    </style>
  </head>

  <body>
    <?php $this->load->view('partials_translate_banner'); ?>
    <div class="container-scroller">
      <?php $this->load->view('includes_nav'); ?>
      <div class="container-fluid page-body-wrapper">
        <?php $this->load->view('includes_nav_top'); ?>
        <div class="main-panel">
          <div class="content-wrapper pb-0">
            <div class="app">

              <div class="breadcrumb-bar" style="display:flex;align-items:center;gap:6px;font-size:.82rem;color:#64748b;margin-bottom:8px">
                <a href="<?= site_url('dashboard/admin') ?>" style="color:#64748b;text-decoration:none;font-weight:600"><i class="mdi mdi-home-outline"></i> Dashboard</a>
                <span style="color:#cbd5e1">/</span>
                <span style="color:#334155;font-weight:700">Complaints</span>
              </div>

              <div class="page-hero">
                <div class="hero-left">
                  <div class="hero-ic"><i class="mdi mdi-shield-alert-outline"></i></div>
                  <div>
                    <h1>Complaints</h1>
                    <p>Review, triage, and resolve user-reported complaints.</p>
                  </div>
                </div>
                <span class="hero-stat"><i class="mdi mdi-format-list-bulleted"></i> <?= (int)($total ?? count($items)) ?> total</span>
              </div>

              <?php if (isset($summary) && is_array($summary)): ?>
                <div class="stat-grid">
                  <?php
                  $curStatus = $filter['status'] ?? '';
                  $summaryCards = [
                    'open'         => ['label' => 'Open',         'accent' => '#f59e0b', 'ic' => 'mdi-alert-circle-outline',  'bg' => 'rgba(245,158,11,.12)',  'fg' => '#b45309'],
                    'under_review' => ['label' => 'Under Review', 'accent' => '#1e3a8a', 'ic' => 'mdi-progress-clock',        'bg' => 'rgba(30,58,138,.10)',   'fg' => '#1e3a8a'],
                    'resolved'     => ['label' => 'Resolved',     'accent' => '#16a34a', 'ic' => 'mdi-check-circle-outline',  'bg' => 'rgba(22,163,74,.12)',   'fg' => '#166534'],
                    'dismissed'    => ['label' => 'Dismissed',    'accent' => '#94a3b8', 'ic' => 'mdi-close-circle-outline',  'bg' => '#f1f5f9',               'fg' => '#475569'],
                  ];
                  foreach ($summaryCards as $k => $info):
                  ?>
                    <a class="stat-card <?= $curStatus === $k ? 'active' : '' ?>" style="--accent:<?= $info['accent'] ?>" href="<?= site_url('admin/complaints?status=' . $k) ?>">
                      <div class="sc-top">
                        <span class="sc-label"><?= $info['label'] ?></span>
                        <span class="sc-ic" style="background:<?= $info['bg'] ?>;color:<?= $info['fg'] ?>"><i class="mdi <?= $info['ic'] ?>"></i></span>
                      </div>
                      <div class="sc-num"><?= (int)($summary[$k] ?? 0) ?></div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
              <?php endif; ?>

              <section class="panel">
                <form class="filter-toolbar" method="get" action="<?= site_url('admin/complaints') ?>">
                  <div class="ft-field">
                    <label>Status</label>
                    <select name="status" class="ctrl">
                      <option value="">All statuses</option>
                      <?php foreach (['open', 'under_review', 'resolved', 'dismissed'] as $s): ?>
                        <option value="<?= $s ?>" <?= (($filter['status'] ?? '') === $s ? 'selected' : '') ?>><?= ucwords(str_replace('_', ' ', $s)) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="ft-field">
                    <label>Type</label>
                    <select name="type" class="ctrl">
                      <option value="">All types</option>
                      <?php foreach (['scam', 'abuse', 'spam', 'other'] as $t): ?>
                        <option value="<?= $t ?>" <?= (($filter['type'] ?? '') === $t ? 'selected' : '') ?>><?= ucwords($t) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="ft-field ft-grow">
                    <label>Search</label>
                    <div class="ctrl-search">
                      <i class="mdi mdi-magnify"></i>
                      <input type="text" name="q" placeholder="Search title or details…" value="<?= htmlspecialchars($filter['q'] ?? '', ENT_QUOTES) ?>">
                    </div>
                  </div>
                  <div class="ft-actions">
                    <button class="btn btn-primary"><i class="mdi mdi-filter-variant"></i> Filter</button>
                    <?php if (!empty($filter['status']) || !empty($filter['type']) || !empty($filter['q'])): ?>
                      <a class="btn btn-light" href="<?= site_url('admin/complaints') ?>"><i class="mdi mdi-close"></i> Clear</a>
                    <?php endif; ?>
                  </div>
                </form>

                <div class="table-responsive">
                  <table class="table table-sm table-hover complaints-table">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th style="width:170px">Reported user</th>
                        <th style="width:160px">Reporter</th>
                        <th style="width:120px">Type</th>
                        <th style="width:160px">Status</th>
                        <th style="width:170px">Created</th>
                        <th style="width:90px">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($items)): ?>
                        <tr>
                          <td colspan="7" class="text-center py-5">
                            <div style="font-size:48px;color:#cbd5e1;margin-bottom:8px"><i class="mdi mdi-shield-alert-outline"></i></div>
                            <h5 style="font-weight:700;color:#475569;margin-bottom:4px">No complaints found</h5>
                            <p style="color:#64748b;font-size:.9rem;margin-bottom:12px">Try adjusting your filters.</p>
                            <a href="<?= site_url('admin/complaints') ?>" class="btn btn-sm btn-light"><i class="mdi mdi-filter-remove-outline"></i> Clear filters</a>
                          </td>
                        </tr>
                        <?php else: foreach ($items as $r): ?>
                          <?php
                          $status = (string)($r->status ?? 'open');
                          $pill = [
                            'open'         => ['cls' => 'status-open',     'label' => 'Open'],
                            'under_review' => ['cls' => 'status-under',    'label' => 'Under review'],
                            'resolved'     => ['cls' => 'status-resolved', 'label' => 'Resolved'],
                            'dismissed'    => ['cls' => 'status-dismissed', 'label' => 'Dismissed'],
                          ][$status] ?? ['cls' => 'status-open', 'label' => 'Open'];

                          // Reporter name
                          $reporter = trim(($r->r_first ?? '') . ' ' . ($r->r_last ?? ''));
                          if ($reporter === '') $reporter = 'User #' . (int)($r->reporter_id ?? 0);

                          // Accused (reported user) name
                          $accused = trim(($r->a_first ?? '') . ' ' . ($r->a_last ?? ''));
                          if ($accused === '' && !empty($r->against_user_id)) {
                            $accused = 'User #' . (int)$r->against_user_id;
                          }
                          ?>
                          <tr>
                            <td data-label="Title" class="text-truncate" style="max-width:520px">
                              <?= htmlspecialchars($r->title ?? '', ENT_QUOTES) ?>
                            </td>
                            <td data-label="Reported user" class="text-truncate" style="max-width:220px">
                              <?= $accused ? htmlspecialchars($accused, ENT_QUOTES, 'UTF-8') : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td data-label="Reporter" class="text-truncate" style="max-width:220px">
                              <?= htmlspecialchars($reporter, ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td data-label="Type">
                              <span class="badge-chip badge-type">
                                <?= strtoupper(htmlspecialchars($r->complaint_type ?? 'SCAM', ENT_QUOTES)) ?>
                              </span>
                            </td>
                            <td data-label="Status">
                              <span class="status-pill <?= $pill['cls'] ?>">
                                <span class="status-dot"></span><?= $pill['label'] ?>
                              </span>
                            </td>
                            <td data-label="Created" class="text-muted"><?= date('Y-m-d H:i', strtotime($r->created_at ?? 'now')) ?></td>
                            <td data-label="Actions">
                              <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/complaints/' . $r->id) ?>" title="View">
                                <i class="mdi mdi-eye"></i> View
                              </a>
                            </td>
                          </tr>
                      <?php endforeach;
                      endif; ?>
                    </tbody>

                  </table>
                </div>

                <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-top:12px;padding-top:10px;border-top:1px solid #d9dee7">
                    <div style="font-size:.85rem;color:#64748b;font-weight:600">
                      Showing <?= $pagination['from'] ?>–<?= $pagination['to'] ?> of <?= number_format($pagination['total']) ?> complaints
                    </div>
                    <div style="display:flex;gap:4px">
                      <?php
                        $cur  = (int)$pagination['page'];
                        $last = (int)$pagination['total_pages'];
                        $mk = function ($p) use ($filter) {
                          $u = site_url('admin/complaints') . '?page=' . $p;
                          if (!empty($filter['status'])) $u .= '&status=' . urlencode($filter['status']);
                          if (!empty($filter['type']))   $u .= '&type=' . urlencode($filter['type']);
                          if (!empty($filter['q']))      $u .= '&q=' . urlencode($filter['q']);
                          return $u;
                        };
                        $pages = [];
                        for ($p = 1; $p <= $last; $p++) {
                          if ($p === 1 || $p === $last || ($p >= $cur - 1 && $p <= $cur + 1)) $pages[] = $p;
                        }
                        $lnk = 'display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;border-radius:8px;border:1px solid #d9dee7;background:#fff;color:#334155;font-weight:700;font-size:.85rem;text-decoration:none;padding:0 8px';
                        $act = 'display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;border-radius:8px;background:#c1272d;color:#fff;font-weight:700;font-size:.85rem;padding:0 8px';
                      ?>
                      <?php if ($cur > 1): ?><a href="<?= $mk($cur - 1) ?>" style="<?= $lnk ?>"><i class="mdi mdi-chevron-left"></i></a><?php endif; ?>
                      <?php $prev = 0; foreach ($pages as $p): ?>
                        <?php if ($prev && $p - $prev > 1): ?><span style="display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:34px;color:#94a3b8;font-weight:700">…</span><?php endif; ?>
                        <?php if ($p === $cur): ?>
                          <span style="<?= $act ?>"><?= $p ?></span>
                        <?php else: ?>
                          <a href="<?= $mk($p) ?>" style="<?= $lnk ?>"><?= $p ?></a>
                        <?php endif; ?>
                        <?php $prev = $p; endforeach; ?>
                      <?php if ($cur < $last): ?><a href="<?= $mk($cur + 1) ?>" style="<?= $lnk ?>"><i class="mdi mdi-chevron-right"></i></a><?php endif; ?>
                    </div>
                  </div>
                <?php endif; ?>
              </section>


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
      setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(el) {
          el.style.transition = 'opacity .4s';
          el.style.opacity = '0';
          setTimeout(function() { el.remove(); }, 400);
        });
      }, 4000);
    </script>


  </body>

  </html>