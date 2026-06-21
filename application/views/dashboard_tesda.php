<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php $page_title = $page_title ?? 'TESDA - Dashboard'; ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.9') ?>">
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
            $k_total     = (int)($stats['workers_total']      ?? $stats['total_workers']     ?? 0);
            $k_new_7d    = (int)($stats['workers_new_7d']     ?? $stats['new_7d']            ?? 0);
            $k_certified = (int)($stats['workers_certified']  ?? $stats['tesda_certified']   ?? $stats['certified'] ?? 0);
            $k_near_exp  = (int)($stats['certs_expiring_30d'] ?? $stats['expiring_30d']      ?? $stats['expiring_30'] ?? 0);
            ?>

            <!-- HERO -->
            <section class="wd-hero">
              <div class="wd-hero-id">
                <div class="wd-hero-text">
                  <div class="wd-hero-greet"><i class="mdi mdi-shield-account-outline"></i> TESDA Portal</div>
                  <h1 class="wd-hero-name"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                  <div class="wd-hero-meta">
                    <span>Onboard and maintain TESDA-certified skilled workers.</span>
                  </div>
                  <div class="wd-hero-actions">
                    <a class="wd-btn wd-btn-primary" href="<?= site_url('tesda/workers/upload') ?>"><i class="mdi mdi-database-import-outline"></i> Upload Workers</a>
                    <a class="wd-btn wd-btn-ghost" href="<?= site_url('tesda/workers/template') ?>"><i class="mdi mdi-file-download-outline"></i> CSV Template</a>
                  </div>
                </div>
              </div>
            </section>

            <!-- KPIs -->
            <div class="wd-kpis">
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-red"><i class="mdi mdi-account-hard-hat"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($k_total) ?></div>
                  <div class="wd-kpi-lbl">Skilled Workers</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-green"><i class="mdi mdi-account-plus-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($k_new_7d) ?></div>
                  <div class="wd-kpi-lbl">New (last 7 days)</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-amber"><i class="mdi mdi-certificate"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($k_certified) ?></div>
                  <div class="wd-kpi-lbl">TESDA Certified</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-indigo"><i class="mdi mdi-clock-alert-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($k_near_exp) ?></div>
                  <div class="wd-kpi-lbl">Certs expiring (30d)</div>
                </div>
              </div>
            </div>

            <div class="wd-stack">
              <div class="wd-row wd-row-2">
                <!-- Quick actions -->
                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-lightning-bolt-outline"></i> Quick Actions</h2></div>
                  <p class="wd-muted" style="margin-top:-8px">Use these tools to onboard and maintain records. The full upload flow lives on its own page.</p>
                  <div class="wd-hero-actions" style="margin-top:4px">
                    <a class="wd-btn wd-btn-primary" href="<?= site_url('tesda/workers/upload') ?>"><i class="mdi mdi-database-import-outline"></i> Upload Workers</a>
                    <a class="wd-btn wd-btn-ghost" href="<?= site_url('tesda/workers/template') ?>"><i class="mdi mdi-file-download-outline"></i> Download CSV Template</a>
                  </div>
                </section>

                <!-- Guidelines -->
                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-information-outline"></i> Guidelines</h2></div>
                  <ul class="wd-list">
                    <li><i class="mdi mdi-check-circle-outline"></i> For bulk onboarding, prepare a CSV using the provided template.</li>
                    <li><i class="mdi mdi-check-circle-outline"></i> Only include columns you have data for; others can be left blank.</li>
                    <li><i class="mdi mdi-check-circle-outline"></i> You can also add or edit individual workers from the Upload page after preview.</li>
                  </ul>
                </section>
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
</body>

</html>
