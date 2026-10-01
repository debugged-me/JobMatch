<?php defined('BASEPATH') or exit('No direct script access allowed');
$page_title = 'Complaint #' . (int)($item->id ?? 0); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('partials/head', [
    'page_title' => 'JobMatch DavOr',
    'css' => [
      'assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css',
    ],
  ]); ?>
  <style>
  :root {
  --ink: var(--jm-blue-900);
  --brand: var(--jm-primary);
  --muted: var(--jm-slate-500);
  --line: var(--jm-line-2);
  --chip: var(--jm-indigo-50);
  --radius: 14px;
  --shadow: 0 8px 22px rgba(2, 6, 23, .10)
  }
  body {
  background: linear-gradient(180deg, var(--jm-wash-1), var(--jm-wash-2) 70%, var(--jm-wash-3))
  }
  .app {
  max-width: 920px;
  margin: 0 auto;
  padding: 0 14px
  }
  .page-head {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 6px 0 16px
  }
  .page-head .icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: var(--chip)
  }
  .page-head .icon i {
  font-size: 20px;
  color: var(--brand)
  }
  .page-head .title {
  margin: 0;
  font: 700 24px/1.2 Inter;
  color: var(--ink)
  }
  .eyebrow {
  font-weight: 700;
  color: var(--jm-gray-800);
  margin-bottom: 10px
  }
  .panel {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 16px
  }
  .meta {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 13px;
  color: var(--jm-slate-700)
  }
  .meta .item {
  display: flex;
  gap: 6px;
  align-items: center;
  background: var(--jm-wash-4);
  border: 1px solid var(--jm-gray-200);
  border-radius: 999px;
  padding: 6px 10px
  }
  .badge-chip {
  border-radius: 999px;
  padding: .35rem .6rem;
  font-weight: 600;
  font-size: 11.5px;
  letter-spacing: .25px
  }
  .badge-type {
  background: #ffe5e8;
  color: #9a0820;
  border: 1px solid #ffc4cb
  }
  .status-dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 6px
  }
  .st-open {
  background: var(--jm-amber-500)
  }
  .st-under {
  background: var(--jm-blue-900)
  }
  .st-resolved {
  background: #fbbf24
  }
  .st-dismissed {
  background: var(--jm-slate-400)
  }
  .divider {
  border-top: 1px solid var(--line);
  margin: 14px 0
  }
  .evidence-list li {
  margin-bottom: 4px
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
          <div class="app">

            <div class="page-head">
              <div class="icon"><i class="mdi mdi-file-document-alert-outline"></i></div>
              <h1 class="title"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
            </div>

            <section class="panel">
              <div class="d-flex align-items-center mb-3">
                <a href="<?= site_url('complaints') ?>" class="btn btn-light border"><i class="mdi mdi-arrow-left"></i> Back</a>
                <div class="ml-auto">
                  <?php
                  $status = (string)($item->status ?? 'open');
                  $map = [
                    'open'         => ['dot' => 'st-open', 'label' => 'Open', 'class' => 'warning'],
                    'under_review' => ['dot' => 'st-under', 'label' => 'Under review', 'class' => 'info'],
                    'resolved'     => ['dot' => 'st-resolved', 'label' => 'Resolved', 'class' => 'success'],
                    'dismissed'    => ['dot' => 'st-dismissed', 'label' => 'Dismissed', 'class' => 'secondary'],
                  ];
                  $m = $map[$status] ?? $map['open'];
                  ?>
                  <span class="status-dot <?= $m['dot'] ?>"></span>
                  <span class="badge badge-<?= $m['class'] ?>"><?= $m['label'] ?></span>
                </div>
              </div>

              <div class="meta mb-3">
                <div class="item"><i class="mdi mdi-tag-outline"></i> <span class="badge-chip badge-type"><?= strtoupper(htmlspecialchars($item->complaint_type ?? 'SCAM', ENT_QUOTES)) ?></span></div>
                <div class="item"><i class="mdi mdi-calendar-clock"></i> Created: <?= date('Y-m-d H:i', strtotime($item->created_at ?? 'now')) ?></div>
                <?php if (!empty($item->updated_at)): ?>
                  <div class="item"><i class="mdi mdi-update"></i> Updated: <?= date('Y-m-d H:i', strtotime($item->updated_at)) ?></div>
                <?php endif; ?>
                <?php if (!empty($item->against_user_name)): ?>
                  <div class="item"><i class="mdi mdi-account-alert-outline"></i> Reported user: <?= htmlspecialchars($item->against_user_name, ENT_QUOTES) ?></div>
                <?php endif; ?>
              </div>

              <h5 class="mb-2"><?= htmlspecialchars($item->title ?? 'Untitled', ENT_QUOTES) ?></h5>
              <p class="mb-2"><?= nl2br(htmlspecialchars($item->details ?? '', ENT_QUOTES)) ?></p>

              <?php if (!empty($item->evidence_files)): ?>
                <div class="divider"></div>
                <h6 class="mb-2"><i class="mdi mdi-paperclip"></i> Evidence</h6>
                <ul class="evidence-list mb-2">
                  <?php foreach ((array)json_decode($item->evidence_files, true) as $f): ?>
                    <li>
                      <a href="<?= site_url($f['path']) ?>" target="_blank">
                        <?= htmlspecialchars($f['name'] ?? basename($f['path']), ENT_QUOTES) ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>

              <?php if (!empty($item->admin_notes)): ?>
                <div class="alert alert-info mt-3 mb-0">
                  <strong><i class="mdi mdi-information-outline"></i> Admin notes:</strong><br>
                  <?= nl2br(htmlspecialchars($item->admin_notes, ENT_QUOTES)) ?>
                </div>
              <?php endif; ?>
            </section>

            <?php $this->load->view('includes_footer'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>