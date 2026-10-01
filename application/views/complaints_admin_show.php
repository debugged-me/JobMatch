<?php defined('BASEPATH') or exit('No direct script access allowed');
$page_title = 'Complaint #' . (int)$item->id; ?>
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
  --ink: var(--jm-slate-900);
  --muted: var(--jm-slate-500);
  --line: var(--jm-gray-200);
  --chip: var(--jm-slate-50);
  --surface: var(--jm-white);
  --surface-2: var(--jm-wash-4);
  --radius: 14px;
  --shadow: 0 8px 22px rgba(2, 6, 23, .08);
  --brand: var(--jm-primary);
  --brand-600: #9e1b21;
  }
  body {
  font-family: "Karla", system-ui, -apple-system, "Segoe UI", Roboto, Arial;
  color: var(--ink);
  background: linear-gradient(180deg, var(--jm-wash-1), var(--jm-wash-2) 60%, var(--jm-wash-3) 100%);
  }
  .cardx {
  background: var(--surface);
  border: 1px solid var(--line);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  }
  .cardx--pad {
  padding: 16px;
  }
  .subtle {
  color: var(--muted);
  }
  .complaint-head {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 12px;
  }
  .pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: .4rem .7rem;
  border-radius: 9999px;
  border: 1px solid;
  font-weight: 700;
  font-size: 12px;
  }
  .dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  }
  .pill--open {
  background: #fff7ed;
  border-color: #fdba74;
  color: #9a3412;
  }
  .pill--open .dot {
  background: var(--jm-amber-500);
  }
  .pill--under {
  background: var(--jm-blue-50);
  border-color: #93c5fd;
  color: var(--jm-blue-900);
  }
  .pill--under .dot {
  background: var(--jm-blue-900);
  }
  .pill--resolved {
  background: rgba(251, 191, 36, .18);
  border-color: rgba(251, 191, 36, .48);
  color: var(--jm-amber-800);
  }
  .pill--resolved .dot {
  background: #fbbf24;
  }
  .pill--dismissed {
  background: var(--jm-slate-100);
  border-color: var(--jm-slate-300);
  color: var(--jm-slate-700);
  }
  .pill--dismissed .dot {
  background: var(--jm-slate-400);
  }
  .btn-lite {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1px solid var(--line);
  color: var(--jm-gray-900);
  border-radius: 10px;
  padding: .5rem .8rem;
  font-weight: 700;
  text-decoration: none;
  }
  .btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--brand);
  border: 1px solid var(--brand-600);
  color: #fff;
  border-radius: 10px;
  padding: .55rem 1rem;
  font-weight: 700;
  text-decoration: none;
  }
  .breadcrumb-bar {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: .82rem;
  color: var(--jm-slate-500);
  margin-bottom: 8px;
  }
  .breadcrumb-bar a {
  color: var(--jm-slate-500);
  text-decoration: none;
  font-weight: 600;
  }
  .breadcrumb-bar a:hover {
  color: var(--brand);
  }
  .breadcrumb-bar .sep {
  color: var(--jm-slate-300);
  }
  .breadcrumb-bar .current {
  color: var(--jm-slate-700);
  font-weight: 700;
  }
  .page-hero {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-600) 100%);
  border-radius: 18px;
  padding: 22px 24px;
  color: #fff;
  box-shadow: 0 14px 30px rgba(193, 39, 45, .26);
  margin-bottom: 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
  }
  .page-hero::after {
  content: "";
  position: absolute;
  right: -40px;
  top: -60px;
  width: 220px;
  height: 220px;
  background: radial-gradient(circle, rgba(255, 255, 255, .16), transparent 70%);
  pointer-events: none;
  }
  .page-hero .hero-left {
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  z-index: 1;
  }
  .page-hero .hero-ic {
  width: 54px;
  height: 54px;
  border-radius: 14px;
  background: rgba(255, 255, 255, .16);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  }
  .page-hero h1 {
  margin: 0;
  font-size: 22px;
  font-weight: 800;
  letter-spacing: -.2px;
  }
  .page-hero p {
  margin: 2px 0 0;
  font-size: 13px;
  opacity: .9;
  }
  .panel {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 12px;
  box-shadow: 0 6px 16px rgba(2, 6, 23, .08);
  padding: 14px;
  }
  .grid-wrap {
  display: grid;
  grid-template-columns: 2.1fr 1fr;
  gap: 16px;
  }
  @media (max-width:992px) {
  .grid-wrap {
  grid-template-columns: 1.6fr 1fr;
  }
  }
  @media (max-width:992px) {
  .grid-wrap {
  grid-template-columns: 1fr;
  }
  }
  .summary {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  }
  @media (max-width:768px) {
  .summary {
  grid-template-columns: 1fr;
  }
  }
  .krow {
  background: var(--surface-2);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 10px 12px;
  }
  .krow .k {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: .35px;
  color: var(--jm-slate-500);
  font-weight: 800;
  }
  .krow .v {
  margin-top: 4px;
  font-weight: 600;
  }
  .body-text {
  line-height: 1.65;
  }
  .badge-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  border-radius: 999px;
  padding: .35rem .6rem;
  font-weight: 700;
  font-size: 11.5px;
  letter-spacing: .25px;
  background: #ffe5e8;
  color: #9a0820;
  border: 1px solid #ffc4cb;
  }
  .evidence-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 10px;
  }
  .evi {
  position: relative;
  background: var(--jm-slate-50);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 10px;
  min-height: 110px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  }
  .evi a {
  text-decoration: none;
  color: var(--jm-blue-900);
  font-weight: 700;
  word-break: break-word;
  }
  .file-pill {
  position: absolute;
  top: 8px;
  left: 8px;
  font-size: 10.5px;
  padding: .15rem .45rem;
  border-radius: 9999px;
  background: #fff;
  border: 1px solid var(--line);
  }
  .formx .form-group {
  margin-bottom: 12px;
  }
  .formx label {
  font-size: 12px;
  font-weight: 700;
  color: var(--jm-slate-700);
  }
  .formx .form-control {
  border-radius: 10px;
  }
  .stack-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  }
  @media (max-width:768px) {
  .stack-actions .btn-primary,
  .stack-actions .btn-lite {
  width: 100%;
  justify-content: center;
  }
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

            <div class="breadcrumb-bar">
              <a href="<?= site_url('dashboard/admin') ?>"><i class="mdi mdi-home-outline"></i> Dashboard</a>
              <span class="sep">/</span>
              <a href="<?= site_url('admin/complaints') ?>">Complaints</a>
              <span class="sep">/</span>
              <span class="current">#<?= (int)$item->id ?></span>
            </div>

            <?php
            $status = (string)($item->status ?? 'open');
            $pill = [
              'open'         => ['cls' => 'pill--open',     'label' => 'Open'],
              'under_review' => ['cls' => 'pill--under',    'label' => 'Under review'],
              'resolved'     => ['cls' => 'pill--resolved', 'label' => 'Resolved'],
              'dismissed'    => ['cls' => 'pill--dismissed', 'label' => 'Dismissed'],
            ][$status] ?? ['cls' => 'pill--open', 'label' => 'Open'];
            ?>
            <div class="page-hero">
              <div class="hero-left">
                <div class="hero-ic"><i class="mdi mdi-shield-alert-outline"></i></div>
                <div>
                  <h1><?= htmlspecialchars($item->title ?? 'Untitled', ENT_QUOTES, 'UTF-8') ?></h1>
                  <p>Complaint #<?= (int)$item->id ?> · <span class="pill <?= $pill['cls'] ?>"><span class="dot"></span><?= $pill['label'] ?></span></p>
                </div>
              </div>
              <a href="<?= site_url('admin/complaints') ?>" class="btn-lite" style="position:relative;z-index:1"><i class="mdi mdi-arrow-left"></i> Back to list</a>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('danger')): ?>
              <div class="alert alert-danger"><?= $this->session->flashdata('danger'); ?></div>
            <?php endif; ?>

            <div class="grid-wrap">
              <div class="cardx cardx--pad">
                <div class="summary">
                  <div class="krow">
                    <div class="k">Reporter</div>
                    <div class="v">
                      <?php
                      $rname = trim(($item->r_first ?? '') . ' ' . ($item->r_last ?? ''));
                      echo $rname !== '' ? htmlspecialchars($rname, ENT_QUOTES, 'UTF-8') : 'User #' . (int)($item->reporter_id ?? 0);
                      ?>
                    </div>
                  </div>

                  <?php if (!empty($item->against_user_id)): ?>
                    <div class="krow">
                      <div class="k">Reported user</div>
                      <div class="v">
                        <?php
                        $aname = trim(($item->a_first ?? '') . ' ' . ($item->a_last ?? ''));
                        echo $aname !== '' ? htmlspecialchars($aname, ENT_QUOTES, 'UTF-8') : 'User #' . (int)$item->against_user_id;
                        ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <div class="krow">
                    <div class="k">Type</div>
                    <div class="v"><span class="badge-chip"><?= strtoupper(htmlspecialchars($item->complaint_type ?? 'SCAM', ENT_QUOTES)) ?></span></div>
                  </div>

                  <div class="krow">
                    <div class="k">Created</div>
                    <div class="v"><?= date('Y-m-d H:i', strtotime($item->created_at ?? 'now')) ?></div>
                  </div>

                  <?php if (!empty($item->updated_at)): ?>
                    <div class="krow">
                      <div class="k">Updated</div>
                      <div class="v"><?= date('Y-m-d H:i', strtotime($item->updated_at)) ?></div>
                    </div>
                  <?php endif; ?>
                </div>

                <div style="margin-top:14px">
                  <h5 style="margin:0 0 6px"><?= htmlspecialchars($item->title ?? 'Untitled', ENT_QUOTES) ?></h5>
                  <div class="body-text"><?= nl2br(htmlspecialchars($item->details ?? '', ENT_QUOTES)) ?></div>
                </div>

                <?php if (!empty($item->evidence_files)): ?>
                  <div style="margin-top:16px">
                    <div class="k" style="font-size:12px; text-transform:none; color:var(--jm-slate-700); font-weight:800; margin-bottom:8px;">
                      <i class="mdi mdi-paperclip"></i> Evidence
                    </div>
                    <div class="evidence-grid">
                      <?php foreach ((array)json_decode($item->evidence_files, true) as $f): ?>
                        <?php
                        $name = htmlspecialchars($f['name'] ?? basename($f['path']), ENT_QUOTES);
                        $type = htmlspecialchars($f['type'] ?? '', ENT_QUOTES);
                        $size = !empty($f['size']) ? (float)$f['size'] . ' KB' : '';
                        ?>
                        <div class="evi">
                          <span class="file-pill"><?= $type ?: 'FILE' ?></span>
                          <div>
                            <a href="<?= site_url($f['path']) ?>" target="_blank" rel="noopener"><?= $name ?></a>
                            <?php if ($size): ?><div class="subtle" style="font-size:12px"><?= $size ?></div><?php endif; ?>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endif; ?>
              </div>

              <div class="cardx cardx--pad">
                <h6 style="margin:0 0 8px; font-weight:800">Update Status</h6>
                <p class="subtle" style="margin-top:-2px">Change complaint state and leave internal notes.</p>

                <form class="formx" method="post" action="<?= site_url('admin/complaints/' . $item->id . '/status') ?>">
                  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                  <!-- NEW: tell controller to go back to list -->
                  <input type="hidden" name="redirect_to" value="list">

                  <div class="form-group">
                    <label for="statusSel">Status</label>
                    <select id="statusSel" name="status" class="form-control" required>
                      <?php foreach (['open', 'under_review', 'resolved', 'dismissed'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($item->status === $s ? 'selected' : '') ?>><?= ucwords(str_replace('_', ' ', $s)) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="adminNotes">Admin notes</label>
                    <textarea id="adminNotes" name="admin_notes" class="form-control" rows="5" placeholder="Notes visible to admins only"><?= htmlspecialchars($item->admin_notes ?? '', ENT_QUOTES) ?></textarea>
                  </div>

                  <div class="stack-actions">
                    <button class="btn-primary" type="submit"><i class="mdi mdi-content-save"></i> Save changes</button>
                    <a class="btn-lite" href="<?= site_url('admin/complaints') ?>">Cancel</a>
                  </div>
                </form>
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