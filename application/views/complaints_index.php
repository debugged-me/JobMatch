<?php defined('BASEPATH') or exit('No direct script access allowed');
$isAdmin    = !empty($isAdmin);
$page_title = $title ?? ($isAdmin ? 'All Scam Reports' : 'My Scam Reports'); ?>
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
      --ink: #202826;
      --brand: #c1272d;
      --brand-dark: #9b1f24;
      --muted: #5c6663;
      --line: #e2e8e3;
      --chip: rgba(193, 39, 45, .08);
      --bg: #f7f9f6;
      --bg2: #eef2ee;
      --radius: 14px;
      --shadow: 0 2px 14px rgba(32, 40, 38, .06)
    }

    body {
      background: linear-gradient(180deg, var(--bg), var(--bg2) 70%, #e9edf3)
    }

    .app {
      max-width: 1060px;
      margin: 0 auto;
      padding: 0 14px
    }

    .page-head {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 6px 0 16px
    }

    .page-head .icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: var(--chip)
    }

    .page-head .icon i {
      font-size: 20px;
      color: var(--brand)
    }

    .page-head .title {
      margin: 0;
      font: 700 24px/1.2 Inter;
      color: var(--ink)
    }

    .page-sub {
      font-size: 13px;
      color: var(--muted);
      margin-top: 2px
    }

    .card {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow)
    }

    .card-head {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 16px;
      border-bottom: 1px solid var(--line)
    }

    .card-head h6 {
      margin: 0;
      font: 800 13px/1 Inter;
      color: var(--ink)
    }

    .toolbar {
      margin-left: auto;
      display: flex;
      gap: 8px
    }

    .btn-brand {
      background: var(--brand);
      border: 1px solid var(--brand);
      color: #fff;
      border-radius: 12px;
      font-weight: 700;
      padding: .45rem .85rem
    }

    .btn-brand:hover {
      background: var(--brand-dark);
      border-color: var(--brand-dark);
      color: #fff
    }

    .table-wrap {
      padding: 12px 16px
    }

    .table-r {
      margin-bottom: 0
    }

    .table-r thead th {
      background: var(--bg);
      border-top: 0;
      position: sticky;
      top: 0;
      z-index: 1
    }

    .table-r th,
    .table-r td {
      vertical-align: middle
    }

    .table-r tbody tr {
      cursor: pointer
    }

    .table-r tbody tr:hover {
      background: var(--chip)
    }

    .badge-chip {
      border-radius: 999px;
      padding: .35rem .6rem;
      font-weight: 600;
      font-size: 11.5px;
      letter-spacing: .25px
    }

    .badge-type {
      background: #ffe5e8;
      color: #9a0820;
      border: 1px solid #ffc4cb
    }

    .muted {
      color: var(--muted)
    }

    .empty-row {
      height: 120px
    }

    .page-spacer {
      height: 28px
    }

    /* Status pill */
    .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .3rem .6rem;
      border-radius: 999px;
      font-weight: 700;
      font-size: 12px;
      letter-spacing: .2px;
      border: 1px solid transparent;
    }

    .status-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%
    }

    .status-open {
      background: #fff7ed;
      border-color: #fdba74;
      color: #9a3412;
    }

    .status-open .status-dot {
      background: #f59e0b;
    }

    .status-under {
      background: #eff6ff;
      border-color: #93c5fd;
      color: #1e3a8a;
    }

    .status-under .status-dot {
      background: #1e3a8a;
    }

    .status-resolved {
      background: rgba(251, 191, 36, .18);
      border-color: rgba(251, 191, 36, .5);
      color: #92400e;
    }

    .status-resolved .status-dot {
      background: #fbbf24;
    }

    .status-dismissed {
      background: #f1f5f9;
      border-color: #cbd5e1;
      color: #334155;
    }

    .status-dismissed .status-dot {
      background: #94a3b8;
    }

    /* Details truncation (2 lines) */
    .truncate-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      max-width: 540px;
    }

    /* Mobile */
    @media (max-width: 768px) {
      .app {
        padding: 0 10px;
      }

      .card {
        border-radius: 16px;
        overflow: hidden;
      }

      .table-responsive {
        overflow: visible;
      }

      .table-r thead {
        display: none;
      }

      .table-r {
        border-collapse: separate;
        border-spacing: 0 12px;
      }

      .table-r tbody,
      .table-r tr,
      .table-r td {
        display: block;
        width: 100%;
      }

      .table-r tbody tr {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        box-shadow: var(--shadow);
        padding: 10px 12px;
        cursor: pointer;
      }

      .table-r tbody tr:hover {
        background: #fff;
      }

      .table-r td {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 8px;
        align-items: baseline;
        padding: 10px 0;
        border-bottom: 1px dashed #e5e7eb;
      }

      .table-r td:last-child {
        border-bottom: 0;
        padding-bottom: 2px;
      }

      .table-r td::before {
        content: attr(data-th);
        text-transform: uppercase;
        font: 700 10px/1 Inter, system-ui, -apple-system, "Segoe UI", Roboto;
        color: var(--muted);
        letter-spacing: .4px;
        align-self: start;
      }

      .table-r .text-truncate {
        max-width: 100% !important;
      }

      .status-pill {
        justify-self: start;
      }

      .text-muted {
        color: #6b7280 !important;
      }

      .toolbar {
        width: 100%;
      }

      .toolbar .btn-brand {
        width: 100%;
        text-align: center;
      }

      .truncate-2 {
        max-width: 100%;
      }
    }

    /* Action icons */
    .actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .icon-btn {
      display: inline-grid;
      place-items: center;
      width: 34px;
      height: 34px;
      border-radius: 10px;
      border: 1px solid var(--line);
      background: #fff;
    }

    .icon-btn:hover {
      background: #f8fafc
    }

    .icon-btn i {
      font-size: 18px;
      color: #0f172a;
      opacity: .82
    }

    .icon-btn--del i {
      color: #9a0820
    }

    /* Title + inline details */
    .title-line {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
    }

    .title-txt {
      font-weight: 600;
      color: var(--ink);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 320px;
    }

    .title-sep {
      opacity: .35
    }

    .details-inline {
      color: #64748b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 400px;
    }

    @media (max-width:768px) {

      .title-txt,
      .details-inline {
        max-width: 100%
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

            <div class="page-head">
              <div class="icon"><i class="mdi mdi-shield-alert-outline"></i></div>
              <div>
                <h1 class="title"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="page-sub"><?= $isAdmin ? 'Review all scam reports submitted by workers and clients.' : 'Track the status of your submitted reports.' ?></div>
              </div>
            </div>

            <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger" role="alert"><i class="mdi mdi-alert-outline"></i> <?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success" role="alert"><i class="mdi mdi-check-circle-outline"></i> <?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>

            <section class="card">
              <div class="card-head">
                <h6><?= $isAdmin ? 'All Reports' : 'My Reports' ?></h6>
                <div class="toolbar">
                  <?php if (!$isAdmin): ?>
                    <a href="<?= site_url('complaints/create') ?>" class="btn btn-sm btn-brand">
                      <i class="mdi mdi-plus"></i> New Report
                    </a>
                  <?php endif; ?>
                </div>
              </div>

              <div class="table-wrap">
                <div class="table-responsive">
                  <table class="table table-sm table-hover table-r">
                    <thead>
                      <tr>
                        <th>Title & Details</th>
                        <?php if ($isAdmin): ?>
                          <th style="width:170px">Reported by</th>
                        <?php endif; ?>
                        <th style="width:180px">Reported user</th>
                        <th style="width:110px">Type</th>
                        <th style="width:140px">Status</th>
                        <th style="width:160px">Created</th>
                        <th style="width:110px">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($items)): ?>
                        <tr class="empty-row">
                          <td colspan="<?= $isAdmin ? 7 : 6 ?>" class="text-center muted">
                            <?php if ($isAdmin): ?>
                              <i class="mdi mdi-information-outline"></i> No scam reports have been submitted yet.
                            <?php else: ?>
                              <i class="mdi mdi-information-outline"></i> No reports yet. Click <strong>New Report</strong> to file your first complaint.
                            <?php endif; ?>
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

                          $accused = trim((string)($r->against_user_name ?? ''));
                          if ($accused === '' && !empty($r->against_user_id)) $accused = 'User #' . (int)$r->against_user_id;

                          $reporter = trim((string)($r->reporter_name ?? ''));
                          if ($reporter === '' && !empty($r->reporter_id)) $reporter = 'User #' . (int)$r->reporter_id;
                          $reporterRole = trim((string)($r->r_role ?? ''));

                          $titleSafe   = htmlspecialchars($r->title ?? 'Untitled', ENT_QUOTES, 'UTF-8');
                          $detailsRaw  = (string)($r->details ?? '');
                          $detailsText = trim(strip_tags($detailsRaw));
                          $detailsSafe = htmlspecialchars($detailsText, ENT_QUOTES, 'UTF-8');
                          ?>
                          <tr onclick="location.href='<?= site_url('complaints/' . $r->id) ?>'">
                            <!-- Title + inline details -->
                            <td data-th="Title & Details" title="<?= $titleSafe . ($detailsSafe ? ' ' . $detailsSafe : '') ?>">
                              <div class="title-line">
                                <span class="title-txt"><?= $titleSafe ?></span>
                                <?php if ($detailsSafe !== ''): ?>
                                  <span class="title-sep">•</span>
                                  <span class="details-inline"><?= $detailsSafe ?></span>
                                <?php endif; ?>
                              </div>
                            </td>

                            <?php if ($isAdmin): ?>
                              <!-- Reported by -->
                              <td data-th="Reported by" class="text-truncate" style="max-width:190px" title="<?= htmlspecialchars($reporter, ENT_QUOTES, 'UTF-8') ?>">
                                <span class="title-txt" style="max-width:160px;display:inline-block;vertical-align:bottom"><?= htmlspecialchars($reporter ?: '—', ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($reporterRole !== ''): ?>
                                  <div class="muted" style="font-size:11px;text-transform:capitalize"><?= htmlspecialchars($reporterRole, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                              </td>
                            <?php endif; ?>

                            <!-- Reported user -->
                            <td class="text-truncate" style="max-width:200px" title="<?= $accused ? htmlspecialchars($accused, ENT_QUOTES, 'UTF-8') : '' ?>">
                              <?= $accused ? htmlspecialchars($accused, ENT_QUOTES, 'UTF-8') : '<span class="muted">—</span>' ?>
                            </td>

                            <!-- Type -->
                            <td>
                              <span class="badge-chip badge-type">
                                <?= strtoupper(htmlspecialchars($r->complaint_type ?? 'SCAM', ENT_QUOTES, 'UTF-8')) ?>
                              </span>
                            </td>

                            <!-- Status -->
                            <td>
                              <span class="status-pill <?= $pill['cls'] ?>">
                                <span class="status-dot"></span><?= $pill['label'] ?>
                              </span>
                            </td>

                            <!-- Created -->
                            <td class="text-muted">
                              <?= date('Y-m-d H:i', strtotime($r->created_at ?? 'now')) ?>
                            </td>

                            <!-- Actions -->
                            <td data-th="Actions">
                              <div class="actions">
                                <a class="icon-btn"
                                  href="<?= site_url('complaints/edit/' . $r->id) ?>"
                                  title="Edit"
                                  onclick="event.stopPropagation();">
                                  <i class="mdi mdi-pencil-outline"></i>
                                </a>
                                <form action="<?= site_url('complaints/delete/' . $r->id) ?>"
                                  method="post"
                                  style="display:inline"
                                  onsubmit="event.stopPropagation(); return confirm('Delete this report? This cannot be undone.');">
                                  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                  <button type="submit" class="icon-btn icon-btn--del" title="Delete" onclick="event.stopPropagation();">
                                    <i class="mdi mdi-delete-outline"></i>
                                  </button>
                                </form>
                              </div>
                            </td>
                          </tr>
                      <?php endforeach;
                      endif; ?>
                    </tbody>

                  </table>
                </div>
              </div>
            </section>

            <div class="page-spacer"></div>
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
      const table = document.querySelector('.table-r');
      if (!table) return;

      // Add data-th labels for mobile layout
      const heads = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
      table.querySelectorAll('tbody tr').forEach(tr => {
        Array.from(tr.children).forEach((td, i) => {
          if (!td.hasAttribute('data-th') && heads[i]) td.setAttribute('data-th', heads[i]);
        });
      });

      const obs = new MutationObserver(() => {
        table.querySelectorAll('tbody tr').forEach(tr => {
          Array.from(tr.children).forEach((td, i) => {
            if (!td.hasAttribute('data-th') && heads[i]) td.setAttribute('data-th', heads[i]);
          });
        });
      });
      obs.observe(table.tBodies[0], {
        childList: true,
        subtree: true
      });
    })();
  </script>

</body>

</html>