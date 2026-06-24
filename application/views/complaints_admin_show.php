<?php defined('BASEPATH') or exit('No direct script access allowed');
$page_title = 'Complaint #' . (int)$item->id; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'JobMatch DavOr', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.7') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <style>
    :root {
      --ink: #0f172a;
      --muted: #64748b;
      --line: #e5e7eb;
      --chip: #f8fafc;
      --surface: #ffffff;
      --surface-2: #fbfcff;
      --radius: 14px;
      --shadow: 0 8px 22px rgba(2, 6, 23, .08);
      --brand: #2563eb;
      --brand-600: #1d4ed8;
    }

    body {
      font-family: "Karla", system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      color: var(--ink);
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
      background: #f59e0b;
    }

    .pill--under {
      background: #eff6ff;
      border-color: #93c5fd;
      color: #1e3a8a;
    }

    .pill--under .dot {
      background: #1e3a8a;
    }

    .pill--resolved {
      background: rgba(251, 191, 36, .18);
      border-color: rgba(251, 191, 36, .48);
      color: #92400e;
    }

    .pill--resolved .dot {
      background: #fbbf24;
    }

    .pill--dismissed {
      background: #f1f5f9;
      border-color: #cbd5e1;
      color: #334155;
    }

    .pill--dismissed .dot {
      background: #94a3b8;
    }

    .btn-lite {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #fff;
      border: 1px solid var(--line);
      color: #111827;
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

    .grid-wrap {
      display: grid;
      grid-template-columns: 2.1fr 1fr;
      gap: 16px;
    }

    @media (max-width:1100px) {
      .grid-wrap {
        grid-template-columns: 1.6fr 1fr;
      }
    }

    @media (max-width:900px) {
      .grid-wrap {
        grid-template-columns: 1fr;
      }
    }

    .summary {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    @media (max-width:700px) {
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
      color: #64748b;
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
      background: #f8fafc;
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
      color: #1e3a8a;
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
      color: #334155;
    }

    .formx .form-control {
      border-radius: 10px;
    }

    .stack-actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    @media (max-width:700px) {

      .stack-actions .btn-primary,
      .stack-actions .btn-lite {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>

<body>
  <?php $this->load->view('partials_translate_banner'); ?>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="app">

            <div class="complaint-head">
              <a href="<?= site_url('admin/complaints') ?>" class="btn-lite"><i class="mdi mdi-arrow-left"></i> Back to list</a>
              <h4 style="margin:0"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h4>
              <div style="margin-left:auto">
                <?php
                $status = (string)($item->status ?? 'open');
                $pill = [
                  'open'         => ['cls' => 'pill--open',     'label' => 'Open'],
                  'under_review' => ['cls' => 'pill--under',    'label' => 'Under review'],
                  'resolved'     => ['cls' => 'pill--resolved', 'label' => 'Resolved'],
                  'dismissed'    => ['cls' => 'pill--dismissed', 'label' => 'Dismissed'],
                ][$status] ?? ['cls' => 'pill--open', 'label' => 'Open'];
                ?>
                <span class="pill <?= $pill['cls'] ?>"><span class="dot"></span><?= $pill['label'] ?></span>
              </div>
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
                    <div class="k" style="font-size:12px; text-transform:none; color:#334155; font-weight:800; margin-bottom:8px;">
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