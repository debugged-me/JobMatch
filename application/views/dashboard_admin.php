<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php $page_title = $page_title ?? 'Admin Dashboard'; ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.1.1') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-shell.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>

      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="wd">

            <?php
            $stats = $stats ?? [];
            $total_workers   = (int)($stats['total_workers'] ?? 0);
            $total_clients   = (int)($stats['total_clients'] ?? 0);
            $active_projects = (int)($stats['active_projects'] ?? 0);
            $pending_verify  = (int)($stats['pending_verifications'] ?? 0);
            $completed       = (int)($stats['completed'] ?? 0);
            $ongoing         = (int)($stats['ongoing'] ?? 0);
            $cancelled       = (int)($stats['cancelled'] ?? 0);
            $activity        = $activity ?? [];
            ?>

            <!-- HERO -->
            <section class="wd-hero">
              <div class="wd-hero-id">
                <div class="wd-hero-text">
                  <div class="wd-hero-greet"><i class="mdi mdi-shield-crown-outline"></i> Admin Portal</div>
                  <h1 class="wd-hero-name"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                  <div class="wd-hero-meta"><span>Platform overview and management at a glance.</span></div>
                  <div class="wd-hero-actions">
                    <a class="wd-btn wd-btn-primary" href="<?= site_url('admin/workers/upload') ?>"><i class="mdi mdi-database-import-outline"></i> Bulk Upload Workers</a>
                    <a class="wd-btn wd-btn-ghost" href="<?= site_url('admin/skills') ?>"><i class="mdi mdi-hammer-wrench"></i> Manage Skills</a>
                  </div>
                </div>
              </div>
            </section>

            <!-- Primary KPIs -->
            <div class="wd-kpis">
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-red"><i class="mdi mdi-account-hard-hat"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($total_workers) ?></div>
                  <div class="wd-kpi-lbl">Skilled Workers</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-amber"><i class="mdi mdi-account-group-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($total_clients) ?></div>
                  <div class="wd-kpi-lbl">Clients</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-green"><i class="mdi mdi-briefcase-check-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($active_projects) ?></div>
                  <div class="wd-kpi-lbl">Active Projects</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-indigo"><i class="mdi mdi-shield-account-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($pending_verify) ?></div>
                  <div class="wd-kpi-lbl">Pending Verifications</div>
                </div>
              </div>
            </div>

            <!-- Engagement KPIs -->
            <div class="wd-kpis cols-3">
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-green"><i class="mdi mdi-check-decagram-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($completed) ?></div>
                  <div class="wd-kpi-lbl">Completed Engagements</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-red"><i class="mdi mdi-progress-clock"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($ongoing) ?></div>
                  <div class="wd-kpi-lbl">Ongoing Engagements</div>
                </div>
              </div>
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-amber"><i class="mdi mdi-close-octagon-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($cancelled) ?></div>
                  <div class="wd-kpi-lbl">Cancelled</div>
                </div>
              </div>
            </div>

            <div class="wd-stack">
              <div class="wd-row wd-row-2">
                <!-- Chart -->
                <section class="wd-card">
                  <div class="wd-card-head">
                    <h2><i class="mdi mdi-chart-line"></i> Hires (last 30 days)</h2>
                    <span class="wd-muted">Auto-updated</span>
                  </div>
                  <div class="wd-chart-box">
                    <canvas
                      id="hiresChart"
                      data-labels="<?= htmlspecialchars(json_encode($chart_labels ?? ['Day 1', 'Day 5', 'Day 10', 'Day 15', 'Day 20', 'Day 25', 'Day 30']), ENT_QUOTES, 'UTF-8') ?>"
                      data-values="<?= htmlspecialchars(json_encode($chart_values ?? [4, 8, 6, 12, 9, 14, 17]), ENT_QUOTES, 'UTF-8') ?>"></canvas>
                  </div>
                </section>

                <!-- Recent Activity -->
                <section class="wd-card">
                  <div class="wd-card-head"><h2><i class="mdi mdi-history"></i> Recent Activity</h2></div>
                  <?php if (empty($activity)): ?>
                    <div class="wd-empty"><i class="mdi mdi-timeline-text-outline"></i>
                      <div>No recent activity.</div>
                    </div>
                  <?php else: ?>
                    <div class="wd-activity">
                      <?php foreach ($activity as $a): ?>
                        <div class="wd-activity-item">
                          <div class="wd-activity-ico"><i class="mdi <?= htmlspecialchars($a['icon'] ?? 'mdi-bell-outline') ?>"></i></div>
                          <div>
                            <div class="wd-activity-title"><?= htmlspecialchars($a['title'] ?? '') ?></div>
                            <div class="wd-activity-meta"><?= htmlspecialchars($a['meta'] ?? '') ?></div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </section>
              </div>
            </div>

          </div>
        </div>

        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <!-- Vendor JS -->
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
  <script src="<?= base_url('assets/js/dashboard-admin.js?v=1.0.0') ?>"></script>

</body>

</html>
