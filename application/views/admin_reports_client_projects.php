<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'JobMatch', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.0.7') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/universal.css') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --blue-900: #1e3a8a;
      --silver-300: #d9dee7;
      --silver-100: #f6f8fc;
      --radius: 12px;
      --pad-panel: 12px;
      --fs-body: 13px;
      --shadow-1: 0 6px 16px rgba(2, 6, 23, .08);
    }

    html,
    body {
      height: 100%
    }

    body {
      font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      font-size: var(--fs-body);
      background: linear-gradient(180deg, var(--silver-100), #eef2f7 60%, #e9edf3 100%);
      color: #0f172a;
    }

    .content-wrapper {
      padding-top: .6rem
    }

    .app {
      max-width: 1000px;
      margin: 0 auto;
      padding: 0 12px
    }

    .eyebrow {
      font-size: 12px;
      color: #64748b;
      font-weight: 600;
      letter-spacing: .2px;
      margin: 4px 0 8px
    }

    .panel {
      background: #fff;
      border: 1px solid var(--silver-300);
      border-radius: var(--radius);
      box-shadow: var(--shadow-1);
      padding: var(--pad-panel);
      margin-bottom: 14px
    }

    .panel-head {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px
    }

    .panel-head i {
      font-size: 18px;
      color: #a7afba
    }

    .panel-head h6 {
      margin: 0;
      font-size: 13px;
      font-weight: 800;
      color: var(--blue-900)
    }

    .empty {
      color: #6b7280;
      border: 1px dashed var(--silver-300);
      border-radius: 10px;
      padding: 10px;
      text-align: center;
      background: linear-gradient(180deg, #fff, #fbfcff)
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

    .table-r th,
    .table-r td {
      padding-top: 12px;
      padding-bottom: 12px;
      vertical-align: middle
    }

    @media (max-width: 768px) {
      .table-responsive {
        overflow-x: visible
      }

      .table-r {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px
      }

      .table-r thead {
        display: none
      }

      .table-r tbody tr {
        display: block;
        padding: 10px;
        border: 1px solid var(--silver-300);
        border-radius: 12px;
        background: #fff;
        box-shadow: var(--shadow-1)
      }

      .table-r tbody tr+tr {
        margin-top: 8px
      }

      .table-r td {
        display: grid;
        grid-template-columns: 110px 1fr;
        gap: 8px;
        padding: 6px 0 !important;
        border: 0 !important;
        font-size: 12.5px
      }

      .table-r td::before {
        content: attr(data-label);
        font-weight: 700;
        color: #334155
      }
    }
  </style>
  <style>
    @media print {

      .container-scroller>.container-fluid.page-body-wrapper>.main-panel>.content-wrapper .app>*,
      .container-scroller>.container-fluid.page-body-wrapper>*:not(.main-panel) {
        display: none !important;
      }

      .container-scroller,
      .page-body-wrapper,
      .main-panel,
      .content-wrapper,
      .app {
        display: block !important;
        padding: 0 !important;
        margin: 0 !important;
      }

      body {
        background: #fff !important;
      }

      .print-area {
        display: block !important;
      }
    }

    @media screen {
      .print-area {
        display: none;
      }
    }

    .print-title {
      font-weight: 800;
      font-size: 16px;
      color: #1e3a8a;
      margin-bottom: 8px
    }

    .print-sub {
      color: #6b7280;
      font-size: 12px;
      margin-bottom: 10px
    }

    .print-box {
      border: 1px solid #d9dee7;
      border-radius: 10px;
      padding: 12px;
      margin-bottom: 12px
    }

    .print-box h6 {
      margin: 0 0 8px;
      font-size: 13px;
      font-weight: 800;
      color: #1e3a8a
    }

    .print-table {
      width: 100%;
      border-collapse: collapse
    }

    .print-table th,
    .print-table td {
      border: 1px solid #e5e7eb;
      padding: 8px;
      font-size: 12.5px;
      vertical-align: top
    }

    .print-small {
      font-size: 12px;
      color: #6b7280
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

            <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Client Projects - Report', ENT_QUOTES, 'UTF-8') ?></div>

            <!-- Header summary -->
            <section class="panel">
              <div class="panel-head"><i class="mdi mdi-account-tie-outline"></i>
                <h6>Client</h6>
              </div>
              <div class="panel-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div>
                    <div class="fw-bold" style="font-size:15px"><?= htmlspecialchars($client_label ?? ('Client #' . (int)$clientID), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-muted" style="font-size:12px">ID <?= (int)$clientID ?></div>
                  </div>
                  <div>
                    <a class="badge-soft" href="<?= site_url('admin/reports') ?>"><i class="mdi mdi-arrow-left"></i> Back to Reports</a>
                  </div>
                </div>
              </div>
            </section>
            <div class="d-flex justify-content-end mb-2">
              <div class="btn-group">
                <a class="btn btn-sm btn-light" href="<?= current_url() . '?print=1' ?>" target="_blank" rel="noopener">
                  <i class="mdi mdi-printer"></i> Print
                </a>
              </div>
            </div>

            <!-- Projects table -->
            <section class="panel">
              <div class="panel-head"><i class="mdi mdi-briefcase-outline"></i>
                <h6>Projects</h6>
              </div>
              <div class="panel-body">
                <?php if (empty($projects)): ?>
                  <div class="empty">No projects found for this client.</div>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table table-sm table-r" style="width:100%">
                      <thead class="bg-light">
                        <tr>
                          <th>ID</th>
                          <th>Title</th>
                          <th>Status</th>
                          <th>Created</th>
                          <th>Applicants</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($projects as $p): ?>
                          <tr>
                            <td data-label="ID"><?= (int)$p['id'] ?></td>
                            <td data-label="Title" class="fw-medium"><?= htmlspecialchars($p['title'] ?? '', ENT_QUOTES) ?></td>
                            <td data-label="Status"><?= htmlspecialchars($p['status'] ?? '', ENT_QUOTES) ?></td>
                            <td data-label="Created" class="text-muted"><?= htmlspecialchars($p['created_at'] ?? '', ENT_QUOTES) ?></td>
                            <td data-label="Applicants"><?= (int)$p['applicant_count'] ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>
            </section>
            <!-- PRINT-ONLY AREA -->
            <div class="print-area">
              <div class="print-title">Client Projects — Detailed Applicants</div>
              <div class="print-sub">
                Client: <?= htmlspecialchars($client_label ?? ('Client #' . (int)$clientID), ENT_QUOTES) ?> (ID <?= (int)$clientID ?>) •
                Generated: <?= date('M d, Y h:i A') ?> • Timezone: Asia/Manila
              </div>

              <div class="print-box">
                <h6>Projects</h6>
                <?php if (empty($projects)): ?>
                  <div class="print-small">No projects found for this client.</div>
                <?php else: ?>
                  <table class="print-table">
                    <thead>
                      <tr>
                        <th style="width:50px">ID</th>
                        <th>Project Title</th>
                        <th style="width:90px">Applicants</th>
                        <th>Applicant Names</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($projects as $p):
                        $pid   = (int)$p['id'];
                        $names = $printMode ? ($projectApplicants[$pid]['names'] ?? []) : [];
                      ?>
                        <tr>
                          <td><?= $pid ?></td>
                          <td><?= htmlspecialchars($p['title'] ?? '', ENT_QUOTES) ?></td>
                          <td><?= (int)$p['applicant_count'] ?></td>
                          <td>
                            <?php if (!empty($names)): ?>
                              <?= htmlspecialchars(implode('; ', $names), ENT_QUOTES) ?>
                            <?php else: ?>
                              <span class="print-small">—</span>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                  <div class="print-small" style="margin-top:6px">Names appear when available in the Users table.</div>
                <?php endif; ?>
              </div>
            </div>

            <?php $this->load->view('includes_footer'); ?>

          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
  $i18nJs = base_url('assets/js/i18n.js?v=' . (is_file(FCPATH . 'assets/js/i18n.js') ? filemtime(FCPATH . 'assets/js/i18n.js') : time()));
  $scanJs = base_url('assets/js/i18n.autoscan.js?v=' . (is_file(FCPATH . 'assets/js/i18n.autoscan.js') ? filemtime(FCPATH . 'assets/js/i18n.autoscan.js') : time()));
  ?>
  <script src="<?= $i18nJs ?>"></script>
  <script src="<?= $scanJs ?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      const saved = localStorage.getItem('lang_pref') || 'en';
      await I18N.init({
        defaultLang: saved
      });
      I18NAutoScan.init();
    });
  </script>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>