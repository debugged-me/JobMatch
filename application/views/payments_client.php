<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= html_escape($page_title ?? 'Payments (Spend History)') ?> • JobMatch</title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.9') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-shell.css?v=1.0.1') ?>">
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

            <div class="wd-pagehead">
              <div class="wd-pagehead-main">
                <h1><i class="mdi mdi-receipt-text-outline"></i> <?= html_escape($page_title ?? 'Payments') ?></h1>
                <div class="sub">Your spend history across hired workers.</div>
              </div>
            </div>

            <div class="wd-card" style="margin-bottom:20px">
              <div class="wd-summary">
                <div class="d-flex align-items-center gap-3">
                  <div class="wd-kpi-ico tint-indigo" style="width:46px;height:46px"><i class="mdi mdi-cash-multiple"></i></div>
                  <div>
                    <div class="wd-kpi-lbl">Total Spend</div>
                    <div class="wd-summary-amt">₱<?= number_format((float)($spend_total ?? 0), 2) ?></div>
                  </div>
                </div>
                <div class="wd-muted"><?= (int)($total_rows ?? 0) ?> payment<?= (int)($total_rows ?? 0) === 1 ? '' : 's' ?></div>
              </div>
            </div>

            <?php if (empty($items)): ?>
              <div class="wd-card">
                <div class="wd-empty"><i class="mdi mdi-receipt-text-outline"></i>
                  <div>No payments yet.</div>
                  <a class="wd-btn wd-btn-primary wd-btn-sm" style="margin-top:6px" href="<?= site_url('projects/active') ?>"><i class="mdi mdi-briefcase-outline"></i> Manage Projects</a>
                </div>
              </div>
            <?php else: ?>

              <div class="wd-grid">
                <?php foreach ($items as $r): ?>
                  <?php
                  $wname = trim(($r->w_first ?? '') . ' ' . ($r->w_last ?? ''));
                  $seed  = $wname !== '' ? $wname : ($r->w_email ?? 'Worker');
                  $avatar = !empty($r->w_avatar) ? base_url($r->w_avatar)
                    : 'https://api.dicebear.com/9.x/initials/svg?seed=' . rawurlencode($seed);
                  $proj  = $r->project_title ?: ('#' . $r->projectID);
                  $amt   = (float)($r->rate_agreed ?? 0);
                  $unit  = $r->rate_unit ? (' / ' . html_escape($r->rate_unit)) : '';
                  ?>
                  <div class="wd-itemcard">
                    <div class="wd-itemcard-body">
                      <div class="wd-person">
                        <img class="wd-avatar" src="<?= html_escape($avatar) ?>" alt="Avatar">
                        <div style="min-width:0">
                          <div class="wd-person-name"><?= html_escape($wname ?: ($r->w_email ?? 'Worker #' . $r->workerID)) ?></div>
                          <div class="wd-person-sub"><i class="mdi mdi-briefcase-outline"></i> <?= html_escape($proj) ?></div>
                        </div>
                        <span class="wd-badge wd-badge-green" style="margin-left:auto"><i class="mdi mdi-check-decagram-outline"></i> Paid</span>
                      </div>
                      <div class="wd-metaline">
                        <span><i class="mdi mdi-cash"></i> <strong>₱<?= number_format($amt, 2) ?></strong><?= $unit ?></span>
                        <?php if (!empty($r->paid_at)): ?>
                          <span><i class="mdi mdi-calendar-blank"></i> <?= date('M d, Y h:i A', strtotime($r->paid_at)) ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="wd-itemcard-foot">
                      <a class="wd-pill" href="<?= site_url('profile/worker/' . (int)$r->workerID) ?>"><i class="mdi mdi-account"></i> View Worker</a>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <?php if (!empty($total_pages) && $total_pages > 1): ?>
                <div class="wd-pager">
                  <a class="wd-btn wd-btn-ghost <?= empty($prev_url) ? 'disabled' : '' ?>" href="<?= $prev_url ?? '#' ?>"><i class="mdi mdi-chevron-left"></i> Prev</a>
                  <div class="wd-muted">Page <?= (int)$page ?> of <?= (int)$total_pages ?></div>
                  <a class="wd-btn wd-btn-ghost <?= empty($next_url) ? 'disabled' : '' ?>" href="<?= $next_url ?? '#' ?>">Next <i class="mdi mdi-chevron-right"></i></a>
                </div>
              <?php endif; ?>

            <?php endif; ?>

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
