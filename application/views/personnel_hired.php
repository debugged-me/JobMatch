<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Hired Personnel', ENT_QUOTES, 'UTF-8') ?> • JobMatch</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.8') ?>">
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
                <h1><i class="mdi mdi-account-group-outline"></i> Hired Personnel</h1>
                <div class="sub">Workers you've hired across your projects.</div>
              </div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline me-2"></i><?= $this->session->flashdata('success'); ?>
              </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline me-2"></i><?= $this->session->flashdata('error'); ?>
              </div>
            <?php endif; ?>

            <?php
            $list = isset($items) ? $items : (isset($rows) ? $rows : []);
            ?>

            <?php if (empty($list)): ?>
              <div class="wd-card">
                <div class="wd-empty"><i class="mdi mdi-account-multiple-outline"></i>
                  <div>No hired personnel yet. Once you hire workers, they'll appear here.</div>
                  <a class="wd-btn wd-btn-primary wd-btn-sm" style="margin-top:6px" href="<?= site_url('projects/active') ?>"><i class="mdi mdi-briefcase-outline"></i> Manage Projects</a>
                </div>
              </div>
            <?php else: ?>
              <div class="wd-grid">
                <?php foreach ($list as $r): ?>
                  <?php
                  $full  = trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? ''));
                  $seed  = $full !== '' ? $full : ($r->email ?? 'Worker');
                  $avatar = !empty($r->w_avatar) ? base_url($r->w_avatar)
                    : 'https://api.dicebear.com/9.x/initials/svg?seed=' . rawurlencode($seed);
                  $rateStr = ($r->rate !== null) ? number_format((float)$r->rate, 2) : '—';
                  $unitStr = !empty($r->rate_unit) ? (' / ' . html_escape($r->rate_unit)) : '';
                  $when    = !empty($r->updated_at) ? $r->updated_at : ($r->created_at ?? '');
                  ?>
                  <div class="wd-itemcard">
                    <div class="wd-itemcard-body">
                      <div class="wd-person">
                        <img class="wd-avatar" src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
                        <div style="min-width:0">
                          <div class="wd-person-name"><?= html_escape($full ?: ($r->email ?? 'Worker')) ?></div>
                          <?php if (!empty($r->project_title)): ?>
                            <div class="wd-person-sub"><i class="mdi mdi-briefcase-outline"></i> <?= html_escape($r->project_title) ?></div>
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="wd-metaline">
                        <span><i class="mdi mdi-cash"></i> <strong><?= $rateStr ?></strong><?= $unitStr ?></span>
                        <?php if ($when): ?>
                          <span><i class="mdi mdi-calendar-blank"></i> <?= date('M d, Y', strtotime($when)) ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="wd-itemcard-foot">
                      <a class="wd-pill" href="<?= site_url('profile/worker/' . (int)$r->worker_id) ?>"><i class="mdi mdi-account"></i> View Profile</a>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
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
