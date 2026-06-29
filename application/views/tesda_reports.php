<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php $page_title = $page_title ?? 'TESDA Worker Reports'; ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --tr-accent: #c1272d;
      --tr-accent-dark: #9b1f24;
      --surface: #ffffff;
      --bg: #f1f3f6;
      --ink: #0f172a;
      --muted: #64748b;
      --line: #e8eaee;
      --radius: 16px;
      --shadow: 0 1px 2px rgba(16, 24, 40, .04), 0 8px 24px -16px rgba(16, 24, 40, .25);
    }

    body {
      font-family: "Karla", ui-sans-serif, system-ui, sans-serif;
      background: var(--bg);
      color: var(--ink);
    }

    .tr-wrap {
      max-width: 1180px;
      margin: 0 auto;
      padding: 6px 14px 28px;
    }

    .tr-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 16px;
    }

    .tr-title {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 800;
      letter-spacing: -.02em;
      line-height: 1.2;
    }

    .tr-sub {
      margin: 4px 0 0;
      color: var(--muted);
      font-size: .84rem;
    }

    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-bottom: 16px;
    }

    .kpi {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 15px 16px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .kpi .ico {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: #fff;
      flex-shrink: 0;
    }

    .kpi-total .ico { background: linear-gradient(145deg, var(--tr-accent), var(--tr-accent-dark)); }
    .kpi-month .ico { background: linear-gradient(145deg, #16a34a, #047857); }
    .kpi-hired .ico { background: linear-gradient(145deg, #d97706, #b45309); }
    .kpi-hired-month .ico { background: linear-gradient(145deg, #7c3aed, #5b21b6); }

    .kpi-label {
      margin: 0;
      color: var(--muted);
      font-size: .72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .kpi-val {
      margin: 3px 0 0;
      font-size: 1.5rem;
      font-weight: 800;
      line-height: 1;
      color: var(--ink);
    }

    .card {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .filter {
      padding: 14px 16px;
      border-bottom: 1px solid var(--line);
      display: flex;
      gap: 8px;
      align-items: center;
      flex-wrap: wrap;
    }

    .filter input {
      flex: 1 1 280px;
      border: 1px solid var(--line);
      border-radius: 11px;
      padding: .58rem .8rem;
      font: inherit;
      background: #f7f8fa;
      outline: none;
    }

    .filter input:focus {
      border-color: var(--tr-accent);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(193, 39, 45, .1);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .42rem;
      border-radius: 11px;
      padding: .6rem .95rem;
      border: 1px solid transparent;
      font-weight: 700;
      font-size: .85rem;
      text-decoration: none;
      line-height: 1;
      transition: .18s ease;
      cursor: pointer;
      white-space: nowrap;
    }

    .btn-primary {
      background: linear-gradient(145deg, var(--tr-accent), var(--tr-accent-dark));
      border-color: var(--tr-accent-dark);
      color: #fff;
    }

    .btn-primary:hover { color: #fff; transform: translateY(-1px); }

    .btn-outline {
      background: #fff;
      border-color: var(--line);
      color: var(--ink);
    }

    .btn-outline:hover { border-color: var(--tr-accent); color: var(--tr-accent); }

    .table-wrap { overflow-x: auto; }

    table.rep {
      width: 100%;
      border-collapse: collapse;
      font-size: .88rem;
    }

    table.rep thead th {
      padding: 11px 13px;
      background: #f7f8fa;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: .05em;
      font-size: .68rem;
      font-weight: 700;
      border-bottom: 1px solid var(--line);
      text-align: left;
      white-space: nowrap;
    }

    table.rep tbody td {
      padding: 12px 13px;
      border-bottom: 1px solid #f1f3f5;
      vertical-align: middle;
    }

    table.rep tbody tr:last-child td { border-bottom: none; }
    table.rep tbody tr:hover { background: #fafbfc; }

    .name { font-weight: 700; color: var(--ink); line-height: 1.25; }
    .subtext { font-size: .76rem; color: var(--muted); margin-top: 2px; }

    .count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 36px;
      padding: .2rem .55rem;
      border-radius: 999px;
      font-size: .76rem;
      font-weight: 700;
      color: var(--tr-accent-dark);
      background: rgba(193, 39, 45, .1);
      border: 1px solid rgba(193, 39, 45, .18);
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: .3rem;
      border-radius: 999px;
      padding: .22rem .58rem;
      font-size: .72rem;
      font-weight: 700;
      border: 1px solid transparent;
    }

    .pill-active { color: #166534; background: rgba(22, 163, 74, .12); border-color: rgba(22, 163, 74, .2); }
    .pill-inactive { color: #92400e; background: rgba(217, 119, 6, .12); border-color: rgba(217, 119, 6, .2); }

    .empty {
      padding: 30px 14px;
      text-align: center;
      color: var(--muted);
      border: 1px dashed var(--line);
      border-radius: 12px;
      background: #f7f8fa;
      margin: 14px;
      font-size: .9rem;
    }

    @media (max-width: 1199.98px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575.98px) { .kpi-grid { grid-template-columns: 1fr; } }
  </style>
</head>

<body class="tesda-reports-page">
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="tr-wrap">
            <div class="tr-head">
              <div>
                <h1 class="tr-title"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="tr-sub">Track workers added under your TESDA account and their hire activity.</p>
              </div>
            </div>

            <?php
            $sum = is_array($summary ?? null) ? $summary : [];
            $totalCreated = (int)($sum['total_created'] ?? 0);
            $createdMonth = (int)($sum['created_this_month'] ?? 0);
            $hiredTotal = (int)($sum['hired_total'] ?? 0);
            $hiredMonth = (int)($sum['hired_this_month'] ?? 0);
            ?>

            <section class="kpi-grid">
              <article class="kpi kpi-total">
                <span class="ico"><i class="mdi mdi-account-multiple-outline"></i></span>
                <div>
                  <p class="kpi-label">Total Added Workers</p>
                  <p class="kpi-val"><?= number_format($totalCreated) ?></p>
                </div>
              </article>
              <article class="kpi kpi-month">
                <span class="ico"><i class="mdi mdi-calendar-month-outline"></i></span>
                <div>
                  <p class="kpi-label">Added This Month</p>
                  <p class="kpi-val"><?= number_format($createdMonth) ?></p>
                </div>
              </article>
              <article class="kpi kpi-hired">
                <span class="ico"><i class="mdi mdi-briefcase-check-outline"></i></span>
                <div>
                  <p class="kpi-label">Total Hires</p>
                  <p class="kpi-val"><?= number_format($hiredTotal) ?></p>
                </div>
              </article>
              <article class="kpi kpi-hired-month">
                <span class="ico"><i class="mdi mdi-clock-check-outline"></i></span>
                <div>
                  <p class="kpi-label">Hires This Month</p>
                  <p class="kpi-val"><?= number_format($hiredMonth) ?></p>
                </div>
              </article>
            </section>

            <section class="card">
              <form class="filter" method="get" action="<?= site_url('tesda/reports') ?>">
                <input type="text" name="q" value="<?= htmlspecialchars((string)($q ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Search name, email, or phone" />
                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i> Filter</button>
                <a class="btn btn-outline" href="<?= site_url('tesda/reports') ?>">Reset</a>
              </form>

              <?php $rows = isset($rows) && is_array($rows) ? $rows : []; ?>
              <?php if (empty($rows)): ?>
                <div class="empty">No workers found for this TESDA account.</div>
              <?php else: ?>
                <div class="table-wrap">
                  <table class="rep">
                    <thead>
                      <tr>
                        <th style="min-width:210px">Worker</th>
                        <th style="min-width:220px">Email</th>
                        <th style="min-width:130px">Created</th>
                        <th style="min-width:120px">Hire Count</th>
                        <th style="min-width:150px">Latest Hired</th>
                        <th style="min-width:120px">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($rows as $r): ?>
                        <?php
                        $ln = trim((string)($r->last_name ?? ''));
                        $fn = trim((string)($r->first_name ?? ''));
                        $full = ($ln !== '' || $fn !== '') ? ($ln . ($ln !== '' && $fn !== '' ? ', ' : '') . $fn) : ('User #' . (int)($r->id ?? 0));
                        $email = (string)($r->email ?? '-');
                        $created = !empty($r->created_at) ? date('M d, Y h:i A', strtotime((string)$r->created_at)) : '-';
                        $latestHired = !empty($r->latest_hired_at) ? date('M d, Y h:i A', strtotime((string)$r->latest_hired_at)) : '-';
                        $hireCount = (int)($r->hire_count ?? 0);
                        $isActive = (int)($r->is_active ?? 0) === 1;
                        $statusRaw = strtolower(trim((string)($r->status ?? '')));
                        if ($isActive && $statusRaw !== 'pending') {
                          $statusClass = 'pill pill-active';
                          $statusText = 'Active';
                        } else {
                          $statusClass = 'pill pill-inactive';
                          $statusText = 'Inactive';
                        }
                        ?>
                        <tr>
                          <td>
                            <div class="name"><?= htmlspecialchars($full, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="subtext">ID #<?= (int)($r->id ?? 0) ?></div>
                          </td>
                          <td><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($created, ENT_QUOTES, 'UTF-8') ?></td>
                          <td><span class="count"><?= number_format($hireCount) ?></span></td>
                          <td><?= htmlspecialchars($latestHired, ENT_QUOTES, 'UTF-8') ?></td>
                          <td><span class="<?= $statusClass ?>"><?= htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8') ?></span></td>
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

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>
