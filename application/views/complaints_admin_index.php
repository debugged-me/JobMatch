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
      .panel {
        background: #fff;
        border: 1px solid #d9dee7;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(2, 6, 23, .08);
        padding: 14px
      }

      .filter-grid {
        display: grid;
        grid-template-columns: 220px 1fr 120px;
        gap: 10px;
        margin-bottom: 12px
      }

      .table thead th {
        background: #eef2f7;
        border-top: 0
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
          content: attr(data-th);
          text-transform: uppercase;
          font: 700 10px/1 Inter, system-ui, -apple-system, "Segoe UI", Roboto;
          color: #64748b;
          letter-spacing: .35px;
          align-self: start;
        }

        .table td:not([data-th]) {
          display: block;
          grid-template-columns: 1fr;
          border-bottom: 0;
        }

        .table td:not([data-th])::before {
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

        .panel .table tbody tr td[data-th="Title"]>* {
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
          content: attr(data-th);
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

        .complaints-table td[data-th="Title"]>* {
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
              <h4 class="mb-3"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h4>

              <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
              <?php endif; ?>

              <section class="panel">
                <form class="filter-grid" method="get" action="<?= site_url('admin/complaints') ?>">
                  <select name="status" class="form-control">
                    <option value="">All statuses</option>
                    <?php foreach (['open', 'under_review', 'resolved', 'dismissed'] as $s): ?>
                      <option value="<?= $s ?>" <?= (($filter['status'] ?? '') === $s ? 'selected' : '') ?>><?= ucwords(str_replace('_', ' ', $s)) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input type="text" name="q" class="form-control" placeholder="Search title/details" value="<?= htmlspecialchars($filter['q'] ?? '', ENT_QUOTES) ?>">
                  <button class="btn btn-primary">Filter</button>
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
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($items)): ?>
                        <tr>
                          <td colspan="6" class="text-center text-muted py-4">No complaints found.</td>
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
                          <tr onclick="location.href='<?= site_url('admin/complaints/' . $r->id) ?>'" style="cursor:pointer">
                            <td class="text-truncate" style="max-width:520px">
                              <?= htmlspecialchars($r->title ?? '', ENT_QUOTES) ?>
                            </td>
                            <td class="text-truncate" style="max-width:220px">
                              <?= $accused ? htmlspecialchars($accused, ENT_QUOTES, 'UTF-8') : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td class="text-truncate" style="max-width:220px">
                              <?= htmlspecialchars($reporter, ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                              <span class="badge-chip badge-type">
                                <?= strtoupper(htmlspecialchars($r->complaint_type ?? 'SCAM', ENT_QUOTES)) ?>
                              </span>
                            </td>
                            <td>
                              <span class="status-pill <?= $pill['cls'] ?>">
                                <span class="status-dot"></span><?= $pill['label'] ?>
                              </span>
                            </td>
                            <td class="text-muted"><?= date('Y-m-d H:i', strtotime($r->created_at ?? 'now')) ?></td>
                          </tr>
                      <?php endforeach;
                      endif; ?>
                    </tbody>

                  </table>
                </div>
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
      (function() {
        var tbl = document.querySelector('.complaints-table');
        if (!tbl) return;

        function labelize(table) {
          var heads = Array.from(table.querySelectorAll('thead th')).map(function(th) {
            return th.textContent.trim();
          });
          table.querySelectorAll('tbody tr').forEach(function(tr) {
            Array.from(tr.children).forEach(function(td, i) {
              if (!td.hasAttribute('data-th') && heads[i]) td.setAttribute('data-th', heads[i]);
            });
          });
        }

        labelize(tbl);
        var obs = new MutationObserver(function() {
          labelize(tbl);
        });
        if (tbl.tBodies && tbl.tBodies[0]) obs.observe(tbl.tBodies[0], {
          childList: true,
          subtree: true
        });
      })();
    </script>


  </body>

  </html>