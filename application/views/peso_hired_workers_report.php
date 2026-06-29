<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'PESO Report', ENT_QUOTES, 'UTF-8') ?> • PESO Davao Oriental</title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --pr-accent: #c1272d;
      --pr-accent-dark: #9b1f24;
      --pr-ink: #0f172a;
      --pr-muted: #64748b;
      --pr-line: #e8eaee;
      --pr-bg: #f1f3f6;
      --pr-card: #fff;
    }

    body {
      font-family: "Karla", system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: var(--pr-bg);
      color: var(--pr-ink);
    }

    .pr-wrap {
      max-width: 1180px;
      margin: 0 auto;
      padding: 0 16px 24px;
    }

    .pr-topbar {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .pr-title {
      margin: 0;
      font-size: 1.4rem;
      font-weight: 800;
      letter-spacing: -.01em;
    }

    .pr-subtitle {
      margin: 3px 0 0;
      color: var(--pr-muted);
      font-size: .88rem;
    }

    .pr-btn {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      padding: .52rem .9rem;
      border-radius: 10px;
      border: 1px solid var(--pr-line);
      background: #fff;
      color: var(--pr-ink);
      text-decoration: none;
      font-weight: 600;
      font-size: .9rem;
      cursor: pointer;
      transition: background .15s ease, border-color .15s ease;
    }

    .pr-btn:hover {
      background: #f7f8fa;
      color: var(--pr-ink);
    }

    .pr-btn-accent {
      border-color: var(--pr-accent-dark);
      background: linear-gradient(135deg, var(--pr-accent), var(--pr-accent-dark));
      color: #fff;
    }

    .pr-btn-accent:hover {
      background: var(--pr-accent-dark);
      color: #fff;
    }

    .pr-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
      gap: 12px;
      margin-bottom: 16px;
    }

    .pr-stat {
      background: var(--pr-card);
      border: 1px solid var(--pr-line);
      border-radius: 14px;
      padding: 16px 18px;
      box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
    }

    .pr-stat .label {
      display: flex;
      align-items: center;
      gap: 7px;
      color: var(--pr-muted);
      font-size: .78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .pr-stat .label .mdi {
      color: var(--pr-accent);
      font-size: 1.05rem;
    }

    .pr-stat .value {
      margin-top: 6px;
      font-size: 1.7rem;
      font-weight: 800;
      line-height: 1;
    }

    .pr-stat.is-highlight {
      background: linear-gradient(135deg, var(--pr-accent), var(--pr-accent-dark));
      border-color: var(--pr-accent-dark);
    }

    .pr-stat.is-highlight .label,
    .pr-stat.is-highlight .label .mdi,
    .pr-stat.is-highlight .value {
      color: #fff;
    }

    .pr-panel {
      background: var(--pr-card);
      border: 1px solid var(--pr-line);
      border-radius: 16px;
      box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
      overflow: hidden;
    }

    .pr-panel-head {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 14px 18px;
      border-bottom: 1px solid var(--pr-line);
      font-weight: 700;
    }

    .pr-pills {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .pr-pill {
      display: inline-flex;
      align-items: center;
      padding: .22rem .6rem;
      border-radius: 999px;
      font-size: .74rem;
      font-weight: 600;
      background: rgba(193, 39, 45, .08);
      color: var(--pr-accent);
    }

    .pr-table {
      width: 100%;
      border-collapse: collapse;
      font-size: .9rem;
    }

    .pr-table thead th {
      text-align: left;
      padding: .65rem .9rem;
      background: #f7f8fa;
      color: var(--pr-muted);
      font-size: .72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      border-bottom: 1px solid var(--pr-line);
      white-space: nowrap;
    }

    .pr-table tbody td {
      padding: .7rem .9rem;
      border-bottom: 1px solid #f1f3f5;
      vertical-align: middle;
    }

    .pr-table tbody tr:last-child td {
      border-bottom: none;
    }

    .pr-table tbody tr:hover {
      background: #fafbfc;
    }

    .pr-muted {
      color: var(--pr-muted);
    }

    .pr-strong {
      font-weight: 700;
    }

    .pr-status {
      display: inline-flex;
      align-items: center;
      padding: .2rem .55rem;
      border-radius: 999px;
      font-size: .74rem;
      font-weight: 700;
      background: rgba(22, 163, 74, .12);
      color: #15803d;
      text-transform: capitalize;
    }

    .pr-empty {
      text-align: center;
      color: var(--pr-muted);
      padding: 40px 16px;
    }

    .pr-empty .mdi {
      font-size: 42px;
      color: #c3c7cf;
    }

    .small {
      font-size: .78rem;
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
          <div class="pr-wrap">

            <?php
            $stats = isset($stats) && is_array($stats) ? $stats : [];
            $totalHires = (int)($stats['total_hires'] ?? 0);
            $totalWorkers = (int)($stats['total_workers'] ?? 0);
            $totalClients = (int)($stats['total_clients'] ?? 0);
            $hiredThisMonth = (int)($stats['hired_this_month'] ?? 0);
            $hiredThisYear = (int)($stats['hired_this_year'] ?? 0);
            $rows = isset($rows) && is_array($rows) ? $rows : [];
            $activeFilterLabels = isset($active_filter_labels) && is_array($active_filter_labels) ? $active_filter_labels : [];
            ?>

            <div class="pr-topbar">
              <div>
                <h1 class="pr-title"><?= htmlspecialchars($page_title ?? 'PESO Report', ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="pr-subtitle">Workers employed across employer projects</p>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <button type="button" class="pr-btn pr-btn-accent" data-bs-toggle="modal" data-bs-target="#filtersModal">
                  <i class="mdi mdi-filter-variant"></i> Filter
                </button>
                <a class="pr-btn" href="<?= htmlspecialchars($print_url ?? site_url('peso/reports/hired-workers?print=1'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">
                  <i class="mdi mdi-printer"></i> Print
                </a>
                <a class="pr-btn" href="<?= htmlspecialchars($reset_url ?? site_url('peso/reports/hired-workers'), ENT_QUOTES, 'UTF-8') ?>">
                  <i class="mdi mdi-refresh"></i> Reset
                </a>
              </div>
            </div>

            <section class="pr-stats">
              <article class="pr-stat is-highlight">
                <div class="label"><i class="mdi mdi-account-check"></i> Employed This Month</div>
                <div class="value"><?= number_format($hiredThisMonth) ?></div>
              </article>
              <article class="pr-stat">
                <div class="label"><i class="mdi mdi-calendar-check"></i> Employed This Year</div>
                <div class="value"><?= number_format($hiredThisYear) ?></div>
              </article>
              <article class="pr-stat">
                <div class="label"><i class="mdi mdi-briefcase-check"></i> Hired Records<?= !empty($activeFilterLabels) ? ' (Filtered)' : '' ?></div>
                <div class="value"><?= number_format($totalHires) ?></div>
              </article>
              <article class="pr-stat">
                <div class="label"><i class="mdi mdi-account-hard-hat"></i> Workers</div>
                <div class="value"><?= number_format($totalWorkers) ?></div>
              </article>
              <article class="pr-stat">
                <div class="label"><i class="mdi mdi-account-tie"></i> Clients</div>
                <div class="value"><?= number_format($totalClients) ?></div>
              </article>
            </section>

            <section class="pr-panel">
              <div class="pr-panel-head">
                <span><i class="mdi mdi-format-list-bulleted text-danger"></i> Hired Workers List</span>
                <?php if (!empty($activeFilterLabels)): ?>
                  <div class="pr-pills">
                    <?php foreach ($activeFilterLabels as $label): ?>
                      <span class="pr-pill"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

              <?php if (empty($rows)): ?>
                <div class="pr-empty">
                  <i class="mdi mdi-account-search-outline"></i>
                  <p class="mb-0 mt-2">No hired worker records found for the selected filters.</p>
                </div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="pr-table">
                    <thead>
                      <tr>
                        <th>Worker</th>
                        <th>Client</th>
                        <th>Project</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Hired Date</th>
                        <th>Updated Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($rows as $r): ?>
                        <?php
                        $rate = isset($r['rate']) && $r['rate'] !== null && $r['rate'] !== ''
                          ? number_format((float)$r['rate'], 2)
                          : '—';
                        $unit = trim((string)($r['rate_unit'] ?? ''));
                        if ($rate !== '—' && $unit !== '') {
                          $rate .= ' / ' . $unit;
                        }

                        $createdAtRaw = trim((string)($r['created_at'] ?? ''));
                        $updatedAtRaw = trim((string)($r['updated_at'] ?? ''));
                        $createdAt = $createdAtRaw !== '' && strtotime($createdAtRaw)
                          ? date('M d, Y h:i A', strtotime($createdAtRaw))
                          : '—';
                        $updatedAt = $updatedAtRaw !== '' && strtotime($updatedAtRaw)
                          ? date('M d, Y h:i A', strtotime($updatedAtRaw))
                          : '—';
                        $projectTitle = trim((string)($r['project_title'] ?? ''));
                        if ($projectTitle === '') {
                          $projectTitle = '—';
                        }
                        ?>
                        <tr>
                          <td>
                            <div class="pr-strong"><?= htmlspecialchars((string)($r['worker_name'] ?? ('Worker #' . (int)($r['worker_id'] ?? 0))), ENT_QUOTES, 'UTF-8') ?></div>
                            <?php if (!empty($r['worker_email'])): ?>
                              <div class="pr-muted small"><?= htmlspecialchars((string)$r['worker_email'], ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                          </td>
                          <td><?= htmlspecialchars((string)($r['client_name'] ?? ('Client #' . (int)($r['client_id'] ?? 0))), ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($projectTitle, ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($rate, ENT_QUOTES, 'UTF-8') ?></td>
                          <td><span class="pr-status"><?= htmlspecialchars((string)($r['status'] ?? 'hired'), ENT_QUOTES, 'UTF-8') ?></span></td>
                          <td><?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8') ?></td>
                          <td><?= htmlspecialchars($updatedAt, ENT_QUOTES, 'UTF-8') ?></td>
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

  <?php
  $filters = isset($filters) && is_array($filters) ? $filters : [];
  $opts = isset($filter_options) && is_array($filter_options) ? $filter_options : [];
  $clients = isset($opts['clients']) && is_array($opts['clients']) ? $opts['clients'] : [];
  $workers = isset($opts['workers']) && is_array($opts['workers']) ? $opts['workers'] : [];
  $projects = isset($opts['projects']) && is_array($opts['projects']) ? $opts['projects'] : [];
  ?>

  <div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content" method="get" action="<?= site_url('peso/reports/hired-workers') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="filtersModalLabel"><i class="mdi mdi-filter-variant me-1"></i>Filter Hired Workers Report</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="date_from">From Date</label>
              <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars((string)($filters['date_from'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="date_to">To Date</label>
              <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars((string)($filters['date_to'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="client_id">Client</label>
              <select id="client_id" name="client_id" class="form-control">
                <option value="">All Clients</option>
                <?php foreach ($clients as $opt): ?>
                  <?php $id = (int)($opt['id'] ?? 0); ?>
                  <option value="<?= $id ?>" <?= ((int)($filters['client_id'] ?? 0) === $id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)($opt['label'] ?? ('Client #' . $id)), ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="worker_id">Worker</label>
              <select id="worker_id" name="worker_id" class="form-control">
                <option value="">All Workers</option>
                <?php foreach ($workers as $opt): ?>
                  <?php $id = (int)($opt['id'] ?? 0); ?>
                  <option value="<?= $id ?>" <?= ((int)($filters['worker_id'] ?? 0) === $id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)($opt['label'] ?? ('Worker #' . $id)), ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label" for="project_id">Project</label>
              <select id="project_id" name="project_id" class="form-control">
                <option value="">All Projects</option>
                <?php foreach ($projects as $opt): ?>
                  <?php $id = (int)($opt['id'] ?? 0); ?>
                  <option value="<?= $id ?>" <?= ((int)($filters['project_id'] ?? 0) === $id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)($opt['label'] ?? ('Project #' . $id)), ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a class="pr-btn" href="<?= site_url('peso/reports/hired-workers') ?>">Clear</a>
          <button type="submit" class="pr-btn pr-btn-accent">Apply Filters</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>
