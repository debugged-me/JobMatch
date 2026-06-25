<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php $page_title = $page_title ?? 'School Admin Dashboard'; ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-shell.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
</head>

<body>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="wd">

            <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success" role="alert"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>

            <?php
            $total   = (int)($stats['total'] ?? 0);
            $activeC = (int)($stats['byActive']['1'] ?? 0);
            $inactC  = (int)($stats['byActive']['0'] ?? 0);
            ?>

            <!-- HERO -->
            <section class="wd-hero">
              <div class="wd-hero-id">
                <div class="wd-hero-text">
                  <div class="wd-hero-greet"><i class="mdi mdi-school-outline"></i> School Admin</div>
                  <h1 class="wd-hero-name"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                  <div class="wd-hero-meta"><span>Quick overview of the users you manage.</span></div>
                  <div class="wd-hero-actions">
                    <a class="wd-btn wd-btn-primary" href="<?= site_url('school-admin/create') ?>"><i class="mdi mdi-account-plus-outline"></i> Create Worker</a>
                    <a class="wd-btn wd-btn-ghost" href="<?= site_url('school-admin/workers') ?>"><i class="mdi mdi-account-group-outline"></i> Manage</a>
                    <a class="wd-btn wd-btn-ghost" href="<?= site_url('school-admin/bulk') ?>"><i class="mdi mdi-upload"></i> Bulk Upload</a>
                  </div>
                </div>
              </div>
            </section>

            <!-- KPIs -->
            <div class="wd-kpis cols-3">
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-red"><i class="mdi mdi-account-group"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($total) ?></div>
                  <div class="wd-kpi-lbl">Total Users</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-green"><i class="mdi mdi-check-circle-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($activeC) ?></div>
                  <div class="wd-kpi-lbl">Active</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-amber"><i class="mdi mdi-minus-circle-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($inactC) ?></div>
                  <div class="wd-kpi-lbl">Inactive</div>
                </div>
              </div>
            </div>

            <div class="wd-stack">
              <section class="wd-card">
                <div class="wd-card-head">
                  <div>
                    <h2><i class="mdi mdi-account-multiple-outline"></i> Recent Users</h2>
                    <div class="wd-section-sub">Latest added accounts</div>
                  </div>
                  <a class="wd-link" href="<?= site_url('school-admin/workers') ?>">View all <i class="mdi mdi-arrow-right"></i></a>
                </div>

                <?php
                $rows = [];
                if (!empty($recent)) {
                  foreach ($recent as $u) {
                    $r = (string)($u->role ?? '');
                    if (in_array($r, ['admin', 'tesda_admin', 'school admin', 'peso'], true)) continue;
                    $rows[] = $u;
                  }
                }
                ?>

                <?php if (empty($rows)): ?>
                  <div class="wd-empty"><i class="mdi mdi-account-off-outline"></i>
                    <div>No users yet. Create or bulk-upload workers to get started.</div>
                  </div>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table wd-table">
                      <thead>
                        <tr>
                          <th>Email</th>
                          <th>Name</th>
                          <th>Role</th>
                          <th>Status</th>
                          <th>Created</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($rows as $u):
                          $fn = (string)($u->first_name ?? '');
                          $ln = (string)($u->last_name ?? '');
                          $name = ($fn !== '' && $ln !== '') ? ($ln . ', ' . $fn) : ($ln !== '' ? $ln : ($fn !== '' ? $fn : '—'));
                          $isActive = ((int)($u->is_active ?? 0) === 1);
                        ?>
                          <tr>
                            <td data-label="Email" class="fw-medium"><?= htmlspecialchars((string)$u->email) ?></td>
                            <td data-label="Name"><?= htmlspecialchars($name) ?></td>
                            <td data-label="Role"><?= htmlspecialchars($u->role !== '' ? ucfirst((string)$u->role) : '—') ?></td>
                            <td data-label="Status">
                              <span class="wd-badge <?= $isActive ? 'wd-badge-green' : 'wd-badge-gray' ?>">
                                <i class="mdi <?= $isActive ? 'mdi-check-circle-outline' : 'mdi-minus-circle-outline' ?>"></i>
                                <?= $isActive ? 'Active' : 'Inactive' ?>
                              </span>
                            </td>
                            <td data-label="Created" class="text-muted"><?= !empty($u->created_at) ? date('M d, Y', strtotime($u->created_at)) : '—' ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </section>
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
</body>

</html>
