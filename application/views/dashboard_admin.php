<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php $page_title = $page_title ?? 'Admin Dashboard'; ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.1.1') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-shell.css?v=1.1.0') ?>">
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
            $open_complaints = (int)($stats['open_complaints'] ?? 0);
            $activity        = $activity ?? [];
            $pending_users   = $pending_users ?? [];
            $recent_regs     = $recent_regs ?? [];
            $top_skills      = $top_skills ?? [];
            $admin_name      = $this->session->userdata('first_name') ?: 'Admin';
            ?>

            <!-- HERO -->
            <section class="wd-hero">
              <div class="wd-hero-id">
                <div class="wd-hero-text">
                  <div class="wd-hero-greet"><i class="mdi mdi-shield-crown-outline"></i> Admin Portal</div>
                  <h1 class="wd-hero-name"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                  <div class="wd-hero-meta"><span>Welcome back, <?= htmlspecialchars($admin_name, ENT_QUOTES, 'UTF-8') ?> — here's your platform overview.</span></div>
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
              <div class="wd-kpi">
                <div class="wd-kpi-ico tint-red"><i class="mdi mdi-alert-circle-outline"></i></div>
                <div>
                  <div class="wd-kpi-val"><?= number_format($open_complaints) ?></div>
                  <div class="wd-kpi-lbl">Open Complaints</div>
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

            <!-- Quick Actions -->
            <section class="wd-card wd-quick-actions">
              <div class="wd-card-head">
                <h2><i class="mdi mdi-lightning-bolt"></i> Quick Actions</h2>
              </div>
              <div class="wd-qa-grid">
                <a class="wd-qa-item" href="<?= site_url('users') ?>">
                  <div class="wd-qa-ico tint-red"><i class="mdi mdi-account-multiple-outline"></i></div>
                  <span>Manage Users</span>
                </a>
                <a class="wd-qa-item" href="<?= site_url('admin-reports') ?>">
                  <div class="wd-qa-ico tint-green"><i class="mdi mdi-chart-box-outline"></i></div>
                  <span>Reports</span>
                </a>
                <a class="wd-qa-item" href="<?= site_url('admin/complaints') ?>">
                  <div class="wd-qa-ico tint-amber"><i class="mdi mdi-flag-outline"></i></div>
                  <span>Complaints</span>
                </a>
                <a class="wd-qa-item" href="<?= site_url('admin/skills') ?>">
                  <div class="wd-qa-ico tint-indigo"><i class="mdi mdi-hammer-wrench"></i></div>
                  <span>Skills</span>
                </a>
                <a class="wd-qa-item" href="<?= site_url('hotlines') ?>">
                  <div class="wd-qa-ico tint-red"><i class="mdi mdi-phone-outline"></i></div>
                  <span>Hotlines</span>
                </a>
                <a class="wd-qa-item" href="<?= site_url('admin/workers/upload') ?>">
                  <div class="wd-qa-ico tint-green"><i class="mdi mdi-database-import-outline"></i></div>
                  <span>Bulk Upload</span>
                </a>
                <a class="wd-qa-item" href="<?= site_url('admin/pending_users') ?>">
                  <div class="wd-qa-ico tint-amber"><i class="mdi mdi-account-clock-outline"></i></div>
                  <span>Pending Users</span>
                </a>
                <a class="wd-qa-item" href="<?= site_url('admin/change_password') ?>">
                  <div class="wd-qa-ico tint-indigo"><i class="mdi mdi-lock-reset"></i></div>
                  <span>Change Password</span>
                </a>
              </div>
            </section>

            <!-- Main content row: Chart + Recent Activity -->
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
                      data-labels="<?= htmlspecialchars(json_encode($chart_labels ?? []), ENT_QUOTES, 'UTF-8') ?>"
                      data-values="<?= htmlspecialchars(json_encode($chart_values ?? []), ENT_QUOTES, 'UTF-8') ?>"></canvas>
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

              <!-- Second row: Pending Verifications + Top Skills -->
              <div class="wd-row wd-row-2">
                <!-- Pending Verifications -->
                <section class="wd-card">
                  <div class="wd-card-head">
                    <h2><i class="mdi mdi-account-clock-outline"></i> Pending Verifications</h2>
                    <a class="wd-link" href="<?= site_url('admin/pending_users') ?>">View all <i class="mdi mdi-arrow-right"></i></a>
                  </div>
                  <?php if (empty($pending_users)): ?>
                    <div class="wd-empty wd-empty-sm"><i class="mdi mdi-check-circle-outline"></i>
                      <div>All users verified.</div>
                    </div>
                  <?php else: ?>
                    <div class="wd-table-wrap">
                      <table class="wd-table">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($pending_users as $u): ?>
                            <tr>
                              <td data-label="Name" class="fw-medium">
                                <?= htmlspecialchars(trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $u->email ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?>
                              </td>
                              <td data-label="Role">
                                <span class="wd-badge wd-badge-gray"><?= htmlspecialchars(ucfirst($u->role ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                              </td>
                              <td data-label="Email" class="wd-cell-email"><?= htmlspecialchars($u->email ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                              <td data-label="Action">
                                <a class="wd-btn wd-btn-sm wd-btn-primary" href="<?= site_url('admin/activate/' . (int)$u->id) ?>">
                                  <i class="mdi mdi-check"></i> Approve
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>
                </section>

                <!-- Top Skills -->
                <section class="wd-card">
                  <div class="wd-card-head">
                    <h2><i class="mdi mdi-hammer-wrench"></i> Top In-Demand Skills</h2>
                  </div>
                  <?php if (empty($top_skills)): ?>
                    <div class="wd-empty wd-empty-sm"><i class="mdi mdi-chart-bell-curve"></i>
                      <div>No skill data available.</div>
                    </div>
                  <?php else: ?>
                    <div class="wd-skill-bars">
                      <?php
                      $max_cnt = 1;
                      foreach ($top_skills as $s) {
                        $max_cnt = max($max_cnt, (int)$s->cnt);
                      }
                      foreach ($top_skills as $s):
                        $pct = round(((int)$s->cnt / $max_cnt) * 100);
                      ?>
                        <div class="wd-skill-bar">
                          <div class="wd-skill-bar-head">
                            <span class="wd-skill-bar-name"><?= htmlspecialchars($s->title ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="wd-skill-bar-cnt"><?= (int)$s->cnt ?> workers</span>
                          </div>
                          <div class="wd-skill-bar-track">
                            <div class="wd-skill-bar-fill" style="width: <?= $pct ?>%"></div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </section>
              </div>

              <!-- Third row: Recent Registrations (full width) -->
              <section class="wd-card">
                <div class="wd-card-head">
                  <h2><i class="mdi mdi-account-plus-outline"></i> Recent Registrations</h2>
                  <a class="wd-link" href="<?= site_url('users') ?>">View all users <i class="mdi mdi-arrow-right"></i></a>
                </div>
                <?php if (empty($recent_regs)): ?>
                  <div class="wd-empty wd-empty-sm"><i class="mdi mdi-account-outline"></i>
                    <div>No recent registrations.</div>
                  </div>
                <?php else: ?>
                  <div class="wd-table-wrap">
                    <table class="wd-table">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Role</th>
                          <th>Status</th>
                          <th>Joined</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($recent_regs as $r):
                          $name = trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? ''));
                          $is_active = (int)($r->is_active ?? 0) === 1;
                        ?>
                          <tr>
                            <td data-label="Name" class="fw-medium">
                              <?= htmlspecialchars($name !== '' ? $name : '—', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td data-label="Email" class="wd-cell-email"><?= htmlspecialchars($r->email ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td data-label="Role">
                              <span class="wd-badge wd-badge-gray"><?= htmlspecialchars(ucfirst($r->role ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td data-label="Status">
                              <?php if ($is_active): ?>
                                <span class="wd-badge wd-badge-green"><i class="mdi mdi-check-circle-outline"></i> Active</span>
                              <?php else: ?>
                                <span class="wd-badge wd-badge-gray"><i class="mdi mdi-clock-outline"></i> Pending</span>
                              <?php endif; ?>
                            </td>
                            <td data-label="Joined">
                              <?php
                                $joined = $r->created_at ?? '';
                                if ($joined !== '') {
                                  $ts = strtotime($joined);
                                  echo $ts ? htmlspecialchars(date('M j, Y', $ts), ENT_QUOTES, 'UTF-8') : '—';
                                } else {
                                  echo '—';
                                }
                              ?>
                            </td>
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

  <!-- Vendor JS -->
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
  <script src="<?= base_url('assets/js/dashboard-admin.js?v=1.1.0') ?>"></script>

</body>

</html>
