<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php $page_title = $title ?? 'Student Worker Reports'; ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Sora:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.1.1') ?>">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --brand-blue: #c1272d;
      --brand-blue-dark: #9b1f24;
      --brand-blue-mid: #d63031;
      --surface: #ffffff;
      --surface-muted: #f3f6fc;
      --surface-alt: #eef2fb;
      --bg: #f4f7fe;
      --ink: #0f172a;
      --muted: #64748b;
      --border: #d6def3;
      --border-strong: #b8c8e8;
      --green: #059669;
      --green-soft: rgba(5,150,105,.1);
      --red: #dc2626;
      --red-soft: rgba(220,38,38,.1);
      --amber: #d97706;
      --amber-soft: rgba(217,119,6,.1);
      --shadow-sm: 0 2px 8px rgba(15,23,42,.06);
      --radius: 20px;
      --radius-sm: 12px;
    }

    * { box-sizing: border-box; }

    body {
      font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
      background: var(--bg);
      color: var(--ink);
      -webkit-font-smoothing: antialiased;
    }

    body.dash-school-page .content-wrapper {
      padding: .45rem .55rem 0 !important;
    }

    .dash-page {
      padding: 16px 18px 40px;
      width: 100%;
      max-width: none;
      margin: 0;
    }

    .dash-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .dash-title {
      font-family: "Sora", sans-serif;
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--brand-blue-dark);
      letter-spacing: -.03em;
      line-height: 1.2;
    }

    .dash-sub {
      font-size: .78rem;
      color: var(--muted);
      margin-top: 4px;
      font-weight: 500;
    }

    .school-name-trim {
      display: inline-block;
      max-width: 30rem;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      vertical-align: bottom;
      color: var(--brand-blue-dark);
    }

    .dash-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      font-weight: 700;
      border-radius: 14px;
      padding: .68rem 1.2rem;
      border: 1.5px solid transparent;
      font-size: .875rem;
      font-family: inherit;
      cursor: pointer;
      transition: all .2s ease;
      white-space: nowrap;
      text-decoration: none;
      line-height: 1;
    }

    .btn-primary {
      background: linear-gradient(145deg, var(--brand-blue-mid) 0%, var(--brand-blue-dark) 100%);
      color: #fff;
      box-shadow: 0 4px 16px rgba(19,64,163,.28);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 28px rgba(19,64,163,.38);
      color: #fff;
      text-decoration: none;
    }

    .btn-outline {
      border-color: var(--brand-blue);
      color: var(--brand-blue);
      background: #fff;
      box-shadow: var(--shadow-sm);
    }

    .btn-outline:hover {
      background: var(--brand-blue);
      color: #fff;
      text-decoration: none;
    }

    .card {
      background: var(--surface);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
    }

    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
      margin-bottom: 20px;
    }

    .kpi-card {
      padding: 18px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .kpi-ico {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(19,64,163,.1);
      color: var(--brand-blue);
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .kpi-label {
      font-size: .75rem;
      color: var(--muted);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .kpi-val {
      font-family: "Sora", sans-serif;
      font-size: 1.55rem;
      font-weight: 800;
      color: var(--brand-blue-dark);
      line-height: 1.1;
      margin-top: 3px;
    }

    .filter-bar {
      padding: 18px 20px;
      display: grid;
      grid-template-columns: 1fr auto auto;
      gap: 12px;
      align-items: end;
      border-bottom: 1px solid var(--border);
    }

    .field-label {
      display: block;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 6px;
    }

    .search-wrap {
      position: relative;
    }

    .search-wrap .mdi {
      position: absolute;
      left: 11px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      font-size: 1.1rem;
      pointer-events: none;
    }

    .search-wrap input {
      width: 100%;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      padding: .62rem .9rem .62rem 2.2rem;
      font-size: .875rem;
      font-family: inherit;
      color: var(--ink);
      background: var(--surface-muted);
      outline: none;
      transition: border .2s, box-shadow .2s, background .2s;
    }

    .search-wrap input:focus {
      border-color: var(--brand-blue);
      box-shadow: 0 0 0 3px rgba(19,64,163,.1);
      background: #fff;
    }

    .table-wrap { overflow-x: auto; }

    table.users {
      width: 100%;
      border-collapse: collapse;
      font-size: .875rem;
    }

    table.users thead th {
      text-transform: uppercase;
      letter-spacing: .07em;
      font-size: .68rem;
      font-weight: 700;
      color: var(--muted);
      padding: 12px 16px;
      background: var(--surface-muted);
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
      text-align: left;
    }

    table.users tbody td {
      padding: 13px 16px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
      color: var(--ink);
    }

    table.users tbody tr:last-child td { border-bottom: none; }
    table.users tbody tr:hover { background: var(--surface-alt); }

    .u-name {
      font-weight: 600;
      color: var(--ink);
      line-height: 1.3;
    }

    .u-id {
      font-size: .72rem;
      color: var(--muted);
      margin-top: 2px;
    }

    .email-link {
      color: var(--brand-blue);
      font-weight: 500;
      text-decoration: none;
      font-size: .85rem;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: color .15s;
    }

    .email-link:hover {
      color: var(--brand-blue-dark);
      text-decoration: none;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      font-weight: 700;
      font-size: .72rem;
      border-radius: 999px;
      padding: .28rem .65rem;
      border: 1px solid transparent;
      white-space: nowrap;
      letter-spacing: .02em;
    }

    .pill-ok { background: var(--green-soft); color: var(--green); border-color: rgba(5,150,105,.2); }
    .pill-bad { background: var(--red-soft); color: var(--red); border-color: rgba(220,38,38,.2); }
    .pill-warn { background: var(--amber-soft); color: var(--amber); border-color: rgba(217,119,6,.2); }

    .muted-cell {
      font-size: .8rem;
      color: var(--muted);
      font-weight: 500;
    }

    .count-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      padding: .22rem .5rem;
      border-radius: 999px;
      font-size: .75rem;
      font-weight: 700;
      background: rgba(19,64,163,.08);
      color: var(--brand-blue);
      border: 1px solid rgba(19,64,163,.18);
    }

    .empty-state {
      padding: 52px 20px;
      text-align: center;
    }

    .empty-icon {
      width: 56px;
      height: 56px;
      border-radius: 16px;
      background: var(--surface-alt);
      color: var(--muted);
      font-size: 1.6rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
    }

    .empty-title {
      font-family: "Sora", sans-serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--brand-blue-dark);
      margin-bottom: 4px;
    }

    .empty-sub {
      font-size: .82rem;
      color: var(--muted);
    }

    .dash-divider {
      border: none;
      border-top: 1px solid var(--border);
      margin: 28px 0;
    }

    .dt-layout-table + .dt-layout-row { border-top: 1px solid var(--border); }
    .dt-container .dt-layout-row {
      margin: 0;
      padding: 12px 16px;
    }

    .dt-container .dt-length label,
    .dt-container .dt-info {
      font-size: .8rem;
      color: var(--muted);
      font-weight: 500;
    }

    .dt-container .dt-length select {
      border: 1.5px solid var(--border);
      border-radius: 9px;
      padding: .35rem 1.8rem .35rem .5rem;
      background: var(--surface-muted);
      color: var(--ink);
      font-size: .8rem;
    }

    .dt-container .dt-paging .dt-paging-button {
      min-width: 34px;
      height: 34px;
      border-radius: 9px;
      border: 1.5px solid var(--border) !important;
      background: var(--surface) !important;
      color: var(--muted) !important;
      padding: 0 10px !important;
      margin: 0 2px;
      font-size: .82rem;
      font-weight: 600;
    }

    .dt-container .dt-paging .dt-paging-button:hover {
      border-color: var(--brand-blue) !important;
      color: var(--brand-blue) !important;
      background: var(--surface-alt) !important;
    }

    .dt-container .dt-paging .dt-paging-button.current {
      background: linear-gradient(145deg, var(--brand-blue-mid), var(--brand-blue-dark)) !important;
      border-color: transparent !important;
      color: #fff !important;
      box-shadow: 0 3px 10px rgba(19,64,163,.3);
    }

    .dt-container .dt-paging .dt-paging-button.disabled {
      opacity: .45;
    }

    @media print {
      @page {
        size: A4 landscape;
        margin: 10mm;
      }

      .sidebar,
      .navbar,
      .dash-actions,
      .kpi-grid,
      .filter-bar,
      .dash-divider,
      .dt-container .dt-layout-row,
      #reportsTable_wrapper .dt-layout-row,
      #reportsTable_wrapper .dt-search,
      #reportsTable_wrapper .dt-length,
      #reportsTable_wrapper .dt-info,
      #reportsTable_wrapper .dt-paging {
        display: none !important;
      }

      body.dash-school-page .content-wrapper,
      .dash-page {
        padding: 0 !important;
        margin: 0 !important;
      }

      .card {
        border: none !important;
        box-shadow: none !important;
      }

      .u-id {
        display: none !important;
      }

      table.users thead th,
      table.users tbody td {
        font-size: 10px !important;
        padding: 6px 8px !important;
      }
    }

    @media (max-width: 1100px) {
      .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 900px) {
      .filter-bar { grid-template-columns: 1fr; }
      table.users thead { display: none; }
      table.users, table.users tbody, table.users tr, table.users td { display: block; width: 100%; }
      table.users tbody tr {
        border: 1px solid var(--border);
        border-radius: 14px;
        margin-bottom: 10px;
        padding: 12px 14px;
        background: var(--surface);
        box-shadow: var(--shadow-sm);
      }
      table.users tbody td {
        border: none;
        padding: 5px 0;
      }
      table.users tbody td[data-th]::before {
        content: attr(data-th);
        display: block;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 3px;
      }
    }

    @media (max-width: 640px) {
      .dash-page { padding: 12px 10px 30px; }
      .dash-header { flex-direction: column; align-items: stretch; }
      .dash-actions .btn { width: 100%; }
      .kpi-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body class="dash-school-page">
<div class="container-scroller">
  <?php $this->load->view('includes_nav'); ?>
  <div class="container-fluid page-body-wrapper">
    <?php $this->load->view('includes_nav_top'); ?>
    <div class="main-panel">
      <div class="content-wrapper pb-0">
        <div class="dash-page">

          <div class="dash-header">
            <div>
              <h1 class="dash-title"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
              <?php $scopeSchoolName = trim((string)($scope_school_name ?? '')); ?>
              <?php
                $scopeSchoolNameShort = trim((string)($scope_school_name_short ?? ''));
                if ($scopeSchoolNameShort === '') {
                  $scopeSchoolNameShort = $scopeSchoolName;
                }
                $reportQuery = [];
                $qNow = trim((string)($q ?? ''));
                if ($qNow !== '') {
                  $reportQuery['q'] = $qNow;
                }
                $reportCsvUrl = site_url('school-admin/reports/export-csv' . (!empty($reportQuery) ? '?' . http_build_query($reportQuery) : ''));
                $reportPdfUrl = site_url('school-admin/reports/export-pdf' . (!empty($reportQuery) ? '?' . http_build_query($reportQuery) : ''));
              ?>
           
            </div>
            <div class="dash-actions">
              <a class="btn btn-outline" href="<?= $reportCsvUrl ?>">
                <i class="mdi mdi-file-delimited-outline"></i> Download CSV
              </a>
              <a class="btn btn-outline" href="<?= $reportPdfUrl ?>">
                <i class="mdi mdi-file-pdf-box"></i> Download PDF
              </a>
             
            </div>
          </div>

          <?php
            $sum = is_array($summary ?? null) ? $summary : [];
            $totalCreated = (int)($sum['total_created'] ?? 0);
            $createdMonth = (int)($sum['created_this_month'] ?? 0);
            $hiredTotal   = (int)($sum['hired_total'] ?? 0);
            $hiredMonth   = (int)($sum['hired_this_month'] ?? 0);
          ?>

          <div class="kpi-grid">
            <div class="card kpi-card">
              <div class="kpi-ico"><i class="mdi mdi-account-multiple-outline"></i></div>
              <div>
                <div class="kpi-label">Total Created</div>
                <div class="kpi-val"><?= number_format($totalCreated) ?></div>
              </div>
            </div>
            <div class="card kpi-card">
              <div class="kpi-ico"><i class="mdi mdi-calendar-month-outline"></i></div>
              <div>
                <div class="kpi-label">Created This Month</div>
                <div class="kpi-val"><?= number_format($createdMonth) ?></div>
              </div>
            </div>
            <div class="card kpi-card">
              <div class="kpi-ico"><i class="mdi mdi-briefcase-check-outline"></i></div>
              <div>
                <div class="kpi-label">Total Hired</div>
                <div class="kpi-val"><?= number_format($hiredTotal) ?></div>
              </div>
            </div>
            <div class="card kpi-card">
              <div class="kpi-ico"><i class="mdi mdi-clock-check-outline"></i></div>
              <div>
                <div class="kpi-label">Hired This Month</div>
                <div class="kpi-val"><?= number_format($hiredMonth) ?></div>
              </div>
            </div>
          </div>

          <div class="card">
            <form class="filter-bar" method="get" action="<?= site_url('school-admin/reports') ?>">
              <div>
                <label class="field-label">Search</label>
                <div class="search-wrap">
                  <i class="mdi mdi-magnify"></i>
                  <input type="text" name="q" value="<?= htmlspecialchars((string)($q ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Search name, email, or phone...">
                </div>
              </div>
              <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i> Filter</button>
              <a class="btn btn-outline" href="<?= site_url('school-admin/reports') ?>">Reset</a>
            </form>

            <div class="table-wrap">
              <table id="reportsTable" class="users" style="width:100%">
                <thead>
                  <tr>
                    <th style="min-width:220px">Student</th>
                    <th style="min-width:220px">Email</th>
                    <th style="min-width:150px">Created</th>
                    <th style="min-width:110px">Hire Count</th>
                    <th style="min-width:160px">Latest Hired</th>
                    <th style="min-width:110px">Status</th>
                  </tr>
                </thead>
                <tbody>
                <?php if (!empty($rows)): foreach ($rows as $r):
                  $ln = trim((string)($r->last_name ?? ''));
                  $fn = trim((string)($r->first_name ?? ''));
                  $full = ($ln !== '' || $fn !== '') ? ($ln . ($ln && $fn ? ', ' : '') . $fn) : ('User #'.(int)($r->id ?? 0));
                  $email = (string)($r->email ?? '');
                  $createdAt = !empty($r->created_at) ? date('M d, Y h:i A', strtotime($r->created_at)) : '-';
                  $latestHired = !empty($r->latest_hired_at) ? date('M d, Y h:i A', strtotime($r->latest_hired_at)) : '—';
                  $hireCount = (int)($r->hire_count ?? 0);
                  $statusLower = strtolower((string)($r->status ?? 'active'));
                  $isActive = (int)($r->is_active ?? 0) === 1;

                  if ($statusLower === 'pending') {
                    $pillClass = 'pill-warn';
                    $pillText = 'Pending';
                    $pillIcon = 'mdi-timer-sand';
                  } elseif ($isActive) {
                    $pillClass = 'pill-ok';
                    $pillText = 'Active';
                    $pillIcon = 'mdi-check-circle-outline';
                  } else {
                    $pillClass = 'pill-bad';
                    $pillText = 'Inactive';
                    $pillIcon = 'mdi-close-circle-outline';
                  }
                ?>
                  <tr>
                    <td data-th="Worker">
                      <div class="u-name"><?= htmlspecialchars($full, ENT_QUOTES, 'UTF-8') ?></div>
                    </td>
                    <td data-th="Email">
                      <?php if (filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
                        <a class="email-link" href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
                          <i class="mdi mdi-email-outline"></i>
                          <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                      <?php else: ?>
                        <span class="muted-cell"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></span>
                      <?php endif; ?>
                    </td>
                    <td data-th="Created" class="muted-cell" data-order="<?= !empty($r->created_at) ? (int)strtotime($r->created_at) : 0 ?>"><?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8') ?></td>
                    <td data-th="Hire Count"><span class="count-badge"><?= number_format($hireCount) ?></span></td>
                    <td data-th="Latest Hired" class="muted-cell" data-order="<?= !empty($r->latest_hired_at) ? (int)strtotime($r->latest_hired_at) : 0 ?>"><?= htmlspecialchars($latestHired, ENT_QUOTES, 'UTF-8') ?></td>
                    <td data-th="Status">
                      <span class="pill <?= $pillClass ?>"><i class="mdi <?= $pillIcon ?>"></i><?= $pillText ?></span>
                    </td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr>
                    <td colspan="6">
                      <div class="empty-state">
                        <div class="empty-icon"><i class="mdi mdi-file-search-outline"></i></div>
                        <div class="empty-title">No reports found</div>
                        <div class="empty-sub">Try changing your filter keyword.</div>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <hr class="dash-divider">
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
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var table = document.getElementById('reportsTable');
    if (!table) return;
    if (table.querySelector('tbody td[colspan]')) return;

    new DataTable('#reportsTable', {
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      order: [[2, 'desc']],
      searching: false,
      language: {
        lengthMenu: 'Show _MENU_ rows',
        info: 'Showing _START_ to _END_ of _TOTAL_ records',
        infoEmpty: 'No records available',
        emptyTable: 'No student workers found'
      }
    });
  });
</script>
</body>
</html>
