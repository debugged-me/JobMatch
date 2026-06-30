<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * NSRP Records — PESO report listing all Form 1 (jobseeker) and
 * Form 2 (establishment) registrations.
 * Vars: $type, $q, $status, $jobseekers[], $establishments[], $worker_counts[], $est_counts[]
 */
$isJob = ($type === 'jobseeker');
$counts = $isJob ? $worker_counts : $est_counts;
$pagination = array_merge([
    'page' => 1,
    'per_page' => 25,
    'per_page_options' => [10, 25, 50, 100],
    'total' => 0,
    'total_pages' => 1,
    'from' => 0,
    'to' => 0,
], is_array($pagination ?? null) ? $pagination : []);
$page = max(1, (int)$pagination['page']);
$perPage = max(1, (int)$pagination['per_page']);
$total = max(0, (int)$pagination['total']);
$totalPages = max(1, (int)$pagination['total_pages']);
$from = max(0, (int)$pagination['from']);
$to = max(0, (int)$pagination['to']);
$badge = function ($s) {
    $s = strtolower((string)$s);
    $map = ['assessed' => 'success', 'submitted' => 'info', 'draft' => 'secondary'];
    $cls = $map[$s] ?? 'secondary';
    return '<span class="badge bg-' . $cls . '">' . html_escape(ucfirst($s ?: 'draft')) . '</span>';
};
$fmtDate = function ($value) {
    $ts = strtotime((string)$value);
    return $ts ? date('M d, Y g:i A', $ts) : '—';
};
$qs = function ($overrides = []) use ($type, $q, $status, $page, $perPage) {
    $params = array_merge([
        'type' => $type,
        'q' => $q,
        'status' => $status,
        'page' => $page,
        'per_page' => $perPage,
    ], $overrides);
    foreach ($params as $key => $value) {
        if ($value === '' || $value === null) unset($params[$key]);
    }
    return http_build_query($params);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= html_escape($page_title ?? 'NSRP Records') ?> - JobMatch</title>
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <style>
    .nsrp-rec .kpi{background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:.8rem 1rem}
    .nsrp-rec .kpi .n{font-size:1.4rem;font-weight:700}
    .nsrp-rec .kpi .l{font-size:.72rem;color:#64748b;text-transform:uppercase;letter-spacing:0}
    .nsrp-rec .tabbtn{border:1px solid #e2e8f0;background:#fff;border-radius:8px;padding:.45rem .9rem;font-weight:600;color:#475569;text-decoration:none;font-size:.85rem}
    .nsrp-rec .tabbtn.active{background:#c1272d;color:#fff;border-color:#c1272d}
    .nsrp-rec .filter-panel{background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:1rem;display:grid;grid-template-columns:minmax(260px,1fr) minmax(160px,190px) minmax(150px,170px) auto;gap:.85rem;align-items:end}
    .nsrp-rec .filter-field label{display:block;font-size:.76rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0;margin-bottom:.35rem}
    .nsrp-rec .filter-field .form-control,.nsrp-rec .filter-field .form-select{height:40px;border-radius:8px}
    .nsrp-rec .filter-search .input-group-text{height:40px;background:#f8fafc;border-color:#ced4da;border-radius:8px 0 0 8px;color:#64748b}
    .nsrp-rec .filter-search .form-control{border-left:0;border-radius:0 8px 8px 0}
    .nsrp-rec .filter-actions{display:flex;gap:.5rem;justify-content:flex-end}
    .nsrp-rec .filter-actions .btn{height:40px;border-radius:8px;white-space:nowrap}
    .nsrp-rec .record-card{border:1px solid #e2e8f0;border-radius:8px;overflow:hidden}
    .nsrp-rec .nsrp-table{min-width:980px;table-layout:fixed}
    .nsrp-rec .nsrp-table th{background:#f8fafc;color:#475569;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0;border-top:0;white-space:nowrap}
    .nsrp-rec .nsrp-table td,.nsrp-rec .nsrp-table th{vertical-align:middle;padding:.85rem .95rem}
    .nsrp-rec .cell-title{font-weight:700;color:#1f2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .nsrp-rec .cell-sub{font-size:.8rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .nsrp-rec .cell-muted{color:#64748b;font-size:.84rem}
    .nsrp-rec .action-group{display:flex;justify-content:flex-end;gap:.4rem;flex-wrap:wrap}
    .nsrp-rec .action-group .btn{font-size:.78rem;padding:.3rem .55rem;line-height:1.2}
    .nsrp-rec .record-footer{border-top:1px solid #e2e8f0;padding:.85rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap}
    .nsrp-rec .record-range{font-size:.85rem;color:#64748b}
    .nsrp-rec .pagination .page-link{color:#475569}
    .nsrp-rec .pagination .active .page-link{background:#c1272d;border-color:#c1272d;color:#fff}
    .nsrp-rec .table-col-person{width:30%}
    .nsrp-rec .table-col-ref{width:14%}
    .nsrp-rec .table-col-status{width:11%}
    .nsrp-rec .table-col-assessed{width:14%}
    .nsrp-rec .table-col-updated{width:14%}
    .nsrp-rec .table-col-actions{width:17%}
    .nsrp-rec .table-col-establishment{width:40%}
    .nsrp-rec .table-col-vacancies{width:12%}
    @media (max-width: 991.98px){.nsrp-rec .filter-panel{grid-template-columns:1fr 1fr}.nsrp-rec .filter-actions{justify-content:flex-start}}
    @media (max-width: 575.98px){.nsrp-rec .filter-panel{grid-template-columns:1fr}.nsrp-rec .filter-actions{display:grid;grid-template-columns:1fr 1fr}.nsrp-rec .record-footer{align-items:flex-start}.nsrp-rec .pagination{width:100%;overflow-x:auto;padding-bottom:.1rem}}
  </style>
</head>
<body>
  <?php $this->load->view('partials_translate_banner'); ?>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-4 nsrp-rec">

          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
              <div class="text-muted" style="font-size:.75rem">PESO Davao Oriental</div>
              <h4 class="mb-0 fw-bold">NSRP Records</h4>
            </div>
            <div class="d-flex gap-2">
              <a href="<?= site_url('nsrp/encode') ?>" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-account-plus-outline"></i> New Jobseeker</a>
              <a href="<?= site_url('nsrp/encode_establishment') ?>" class="btn btn-sm btn-outline-danger"><i class="mdi mdi-domain"></i> New Establishment</a>
            </div>
          </div>

          <?php foreach (['success', 'danger'] as $cls): ?>
            <?php if ($msg = $this->session->flashdata($cls)): ?>
              <div class="alert alert-<?= $cls ?> py-2"><?= $msg ?></div>
            <?php endif; ?>
          <?php endforeach; ?>

          <!-- KPIs -->
          <div class="row g-3 mb-3">
            <div class="col-6 col-md-3"><div class="kpi"><div class="l">Total</div><div class="n"><?= (int)$counts['total'] ?></div></div></div>
            <div class="col-6 col-md-3"><div class="kpi"><div class="l">Draft</div><div class="n"><?= (int)$counts['draft'] ?></div></div></div>
            <div class="col-6 col-md-3"><div class="kpi"><div class="l">Submitted</div><div class="n"><?= (int)$counts['submitted'] ?></div></div></div>
            <div class="col-6 col-md-3"><div class="kpi"><div class="l">Assessed</div><div class="n"><?= (int)$counts['assessed'] ?></div></div></div>
          </div>

          <!-- Tabs -->
          <div class="d-flex gap-2 mb-3">
            <a class="tabbtn <?= $isJob ? 'active' : '' ?>" href="<?= site_url('nsrp/records?' . $qs(['type' => 'jobseeker', 'page' => 1])) ?>"><i class="mdi mdi-account-multiple"></i> Jobseekers (Form 1)</a>
            <a class="tabbtn <?= !$isJob ? 'active' : '' ?>" href="<?= site_url('nsrp/records?' . $qs(['type' => 'establishment', 'page' => 1])) ?>"><i class="mdi mdi-domain"></i> Establishments (Form 2)</a>
          </div>

          <form method="get" action="<?= site_url('nsrp/records') ?>" class="filter-panel mb-3">
            <input type="hidden" name="type" value="<?= html_escape($type) ?>">
            <input type="hidden" name="page" value="1">
            <div class="filter-field filter-search">
              <label for="nsrp-q">Search</label>
              <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                <input id="nsrp-q" name="q" class="form-control" placeholder="Name, email<?= $isJob ? ', ref no.' : '' ?>" value="<?= html_escape($q) ?>">
              </div>
            </div>
            <div class="filter-field">
              <label for="nsrp-status">Status</label>
              <select id="nsrp-status" name="status" class="form-select">
                <option value="">All statuses</option>
                <?php foreach (['draft','submitted','assessed'] as $st): ?>
                  <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="filter-field">
              <label for="nsrp-per-page">Rows</label>
              <select id="nsrp-per-page" name="per_page" class="form-select">
                <?php foreach ((array)$pagination['per_page_options'] as $option): ?>
                  <option value="<?= (int)$option ?>" <?= $perPage === (int)$option ? 'selected' : '' ?>><?= (int)$option ?> per page</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="filter-actions">
              <button type="submit" class="btn btn-danger"><i class="mdi mdi-filter-outline"></i> Apply</button>
              <a href="<?= site_url('nsrp/records?' . $qs(['type' => $type, 'q' => '', 'status' => '', 'page' => 1])) ?>" class="btn btn-outline-secondary"><i class="mdi mdi-refresh"></i> Reset</a>
            </div>
          </form>

          <div class="card record-card">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle nsrp-table">
                  <?php if ($isJob): ?>
                    <colgroup>
                      <col class="table-col-person"><col class="table-col-ref"><col class="table-col-status">
                      <col class="table-col-assessed"><col class="table-col-updated"><col class="table-col-actions">
                    </colgroup>
                    <thead>
                      <tr>
                        <th>Jobseeker</th>
                        <th>Ref. No.</th>
                        <th>Status</th>
                        <th>Assessed by</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($jobseekers)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No jobseeker records found.</td></tr>
                      <?php else: foreach ($jobseekers as $r): $id = (int)$r['id']; ?>
                        <?php $name = trim(($r['last_name'] ?? '') . ', ' . ($r['first_name'] ?? ''), ', '); ?>
                        <tr>
                          <td>
                            <div class="cell-title"><?= html_escape($name) ?: '—' ?></div>
                            <div class="cell-sub"><?= html_escape($r['email'] ?? '') ?></div>
                          </td>
                          <td><span class="cell-muted"><?= html_escape($r['nsrp_reference'] ?? '') ?: '—' ?></span></td>
                          <td><?= $badge($r['nsrp_status'] ?? 'draft') ?></td>
                          <td><span class="cell-muted"><?= html_escape($r['assessed_by'] ?? '') ?: '—' ?></span></td>
                          <td><span class="cell-muted"><?= html_escape($fmtDate($r['updated_at'] ?? '')) ?></span></td>
                          <td>
                            <div class="action-group">
                              <a class="btn btn-sm btn-outline-primary" href="<?= site_url('nsrp/jobseeker/' . $id) ?>"><i class="mdi mdi-pencil"></i> Open</a>
                              <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('nsrp/print_jobseeker/' . $id) ?>" target="_blank"><i class="mdi mdi-printer"></i> Print</a>
                              <a class="btn btn-sm btn-outline-danger" href="<?= site_url('nsrp/send_jobseeker/' . $id) ?>" onclick="return confirm('Send a confidential copy to this jobseeker via Messages?')"><i class="mdi mdi-send"></i> Send</a>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; endif; ?>
                    </tbody>
                  <?php else: ?>
                    <colgroup>
                      <col class="table-col-establishment"><col class="table-col-vacancies"><col class="table-col-status">
                      <col class="table-col-updated"><col class="table-col-actions">
                    </colgroup>
                    <thead>
                      <tr>
                        <th>Establishment</th>
                        <th>Vacancies</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($establishments)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No establishment records found.</td></tr>
                      <?php else: foreach ($establishments as $r): $id = (int)$r['id']; ?>
                        <tr>
                          <td>
                            <div class="cell-title"><?= html_escape($r['business'] ?? '') ?: '—' ?></div>
                            <div class="cell-sub"><?= html_escape($r['email'] ?? '') ?></div>
                          </td>
                          <td><span class="badge bg-light text-dark"><?= (int)($r['vacancy_count'] ?? 0) ?></span></td>
                          <td><?= $badge($r['nsrp_status'] ?? 'draft') ?></td>
                          <td><span class="cell-muted"><?= html_escape($fmtDate($r['updated_at'] ?? '')) ?></span></td>
                          <td>
                            <div class="action-group">
                              <a class="btn btn-sm btn-outline-primary" href="<?= site_url('nsrp/establishment/' . $id) ?>"><i class="mdi mdi-pencil"></i> Open</a>
                              <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('nsrp/print_establishment/' . $id) ?>" target="_blank"><i class="mdi mdi-printer"></i> Print</a>
                              <a class="btn btn-sm btn-outline-danger" href="<?= site_url('nsrp/send_establishment/' . $id) ?>" onclick="return confirm('Send a confidential copy to this establishment via Messages?')"><i class="mdi mdi-send"></i> Send</a>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; endif; ?>
                    </tbody>
                  <?php endif; ?>
                </table>
              </div>
            </div>
            <div class="record-footer">
              <div class="record-range">
                <?php if ($total > 0): ?>
                  Showing <?= (int)$from ?> to <?= (int)$to ?> of <?= (int)$total ?> records
                <?php else: ?>
                  Showing 0 records
                <?php endif; ?>
              </div>
              <?php if ($totalPages > 1): ?>
                <?php $windowStart = max(1, $page - 2); $windowEnd = min($totalPages, $page + 2); ?>
                <nav aria-label="NSRP records pagination">
                  <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                      <a class="page-link" href="<?= $page <= 1 ? '#' : site_url('nsrp/records?' . $qs(['page' => $page - 1])) ?>">Previous</a>
                    </li>
                    <?php if ($windowStart > 1): ?>
                      <li class="page-item"><a class="page-link" href="<?= site_url('nsrp/records?' . $qs(['page' => 1])) ?>">1</a></li>
                      <?php if ($windowStart > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $windowStart; $i <= $windowEnd; $i++): ?>
                      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= site_url('nsrp/records?' . $qs(['page' => $i])) ?>"><?= (int)$i ?></a>
                      </li>
                    <?php endfor; ?>
                    <?php if ($windowEnd < $totalPages): ?>
                      <?php if ($windowEnd < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                      <li class="page-item"><a class="page-link" href="<?= site_url('nsrp/records?' . $qs(['page' => $totalPages])) ?>"><?= (int)$totalPages ?></a></li>
                    <?php endif; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                      <a class="page-link" href="<?= $page >= $totalPages ? '#' : site_url('nsrp/records?' . $qs(['page' => $page + 1])) ?>">Next</a>
                    </li>
                  </ul>
                </nav>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/nav.js') ?>"></script>
</body>
</html>
