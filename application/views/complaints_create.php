<?php defined('BASEPATH') or exit('No direct script access allowed');
$page_title = 'Report a Scam'; ?>
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
      --ink: #c1272d;
      --brand: #2980b9;
      --muted: #64748b;
      --line: #d9dee7;
      --bg: #f6f8fc;
      --bg2: #eef2f7;
      --chip: #eef2ff;
      --radius: 14px;
      --r-sm: 10px;
      --shadow: 0 8px 22px rgba(2, 6, 23, .10);
    }

    body {
      background: linear-gradient(180deg, var(--bg), var(--bg2) 70%, #e9edf3)
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
      font: 700 24px/1.2 Inter, system-ui;
      color: var(--ink)
    }

    .page-sub {
      font-size: 13px;
      color: var(--muted);
      margin-top: 2px
    }

    .card {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
    }

    .card-head {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 16px;
      border-bottom: 1px solid var(--line)
    }

    .card-head h6 {
      margin: 0;
      font: 800 13px/1 Inter;
      color: var(--ink);
      letter-spacing: .3px
    }

    .card-body {
      padding: 18px 16px
    }

    .help {
      font-size: 12px;
      color: var(--muted)
    }

    label {
      font-weight: 600
    }

    .btn-brand {
      background: #f5f8ff;
      border: 1px solid var(--brand);
      color: var(--ink);
      border-radius: 12px;
      font-weight: 700;
      padding: .5rem .95rem
    }

    .btn-brand:hover {
      background: #e9f0ff
    }

    .btn-ghost {
      border: 1px solid var(--line);
      border-radius: 12px
    }

    .chip-tag {
      display: inline-flex;
      gap: 6px;
      align-items: center;
      padding: 6px 10px;
      border-radius: 999px;
      background: var(--chip);
      border: 1px solid #dbe3ff;
      color: #19328a;
      font-size: 12px
    }

    .uploader {
      border: 1px dashed #c9d3ea;
      border-radius: 12px;
      padding: 14px;
      background: #fbfcff
    }

    .uploader input[type=file] {
      display: block
    }

    .file-list {
      margin: 8px 0 0;
      padding-left: 18px
    }

    .file-list li {
      font-size: 12.5px;
      color: #374151
    }

    .page-spacer {
      height: 28px
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

            <!-- Header -->
            <div class="page-head">
              <div class="icon"><i class="mdi mdi-shield-alert-outline"></i></div>
              <div>
                <h1 class="title"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="page-sub">Help us keep JobMatch safe. Provide clear details and attach evidence.</div>
              </div>
            </div>

            <!-- Flash -->
            <?php if ($this->session->flashdata('danger')): ?>
              <div class="alert alert-danger mt-2 mb-3" role="alert">
                <i class="mdi mdi-alert-outline"></i> <?= $this->session->flashdata('danger'); ?>
              </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success mt-2 mb-3" role="alert">
                <i class="mdi mdi-check-circle-outline"></i> <?= $this->session->flashdata('success'); ?>
              </div>
            <?php endif; ?>

            <!-- Form -->
            <section class="card">
              <div class="card-head">
                <i class="mdi mdi-clipboard-text-outline"></i>
                <h6>Submit Report</h6>
                <span class="ml-auto chip-tag"><i class="mdi mdi-lock"></i> Private & reviewed by moderators</span>
              </div>

              <div class="card-body">
                <form action="<?= site_url('complaints/store') ?>" method="post" enctype="multipart/form-data" novalidate>
                  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                  <!-- Row 1: Title -->
                  <div class="form-row">
                    <div class="form-group col-12">
                      <label for="title">Title</label>
                      <input id="title"
                        type="text"
                        name="title"
                        class="form-control"
                        placeholder="Short summary (e.g., Paid but never received item)"
                        required minlength="8"
                        value="<?= html_escape(set_value('title')) ?>">
                    </div>
                  </div>

                  <!-- Row 2: Type + Reported user -->
                  <div class="form-row">
                    <div class="form-group col-md-4">
                      <label for="complaint_type">Complaint type</label>
                      <select id="complaint_type" name="complaint_type" class="form-control" required>
                        <option value="scam" <?= set_select('complaint_type', 'scam',  true) ?>>Scam</option>
                        <option value="abuse" <?= set_select('complaint_type', 'abuse') ?>>Abuse</option>
                        <option value="spam" <?= set_select('complaint_type', 'spam')  ?>>Spam</option>
                        <option value="other" <?= set_select('complaint_type', 'other') ?>>Other</option>
                      </select>
                      <small class="help d-block mt-1">Pick the closest category.</small>
                    </div>

                    <div class="form-group col-md-8">
                      <label for="against_user_id">Reported user (optional)</label>
                      <select id="against_user_id" name="against_user_id" class="form-control">
                        <option value="">— Not sure / None</option>
                        <?php foreach (($users ?? []) as $u):
                          $uid   = (int)$u->id;
                          $name  = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
                          $role  = $u->role ?? 'User';
                          $isSel = set_value('against_user_id') !== '' ? ((int)set_value('against_user_id') === $uid) : false;
                        ?>
                          <option value="<?= $uid ?>" <?= $isSel ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name !== '' ? $name : ('User #' . $uid), ENT_QUOTES) ?> (<?= htmlspecialchars(ucfirst($role), ENT_QUOTES) ?>)
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <?php if (empty($users)): ?>
                        <small class="help d-block mt-1 text-danger">
                          No users available to select. Ask an admin to seed users or relax the filter.
                        </small>
                      <?php else: ?>
                        <div class="help mt-1">If you know the account that scammed you, select it here.</div>
                      <?php endif; ?>
                    </div>
                  </div><!-- close Row 2 -->

                  <!-- Row 3: Details (REQUIRED) -->
                  <div class="form-row">
                    <div class="form-group col-12">
                      <label for="details">Details</label>
                      <textarea id="details"
                        name="details"
                        class="form-control"
                        rows="6"
                        placeholder="Describe what happened. Include dates, amounts, usernames, links, etc."
                        required minlength="20"><?= html_escape(set_value('details')) ?></textarea>
                      <small class="help d-block mt-1">Minimum 20 characters. Be as specific as possible.</small>
                    </div>
                  </div>

                  <!-- Row 4: Evidence -->
                  <div class="form-row">
                    <div class="form-group col-12">
                      <label>Evidence files <span class="help">(images/PDF, multiple)</span></label>
                      <div class="uploader">
                        <input id="evidence" type="file" name="evidence[]" class="form-control-file" multiple accept=".jpg,.jpeg,.png,.pdf" aria-describedby="evidenceHelp">
                        <small id="evidenceHelp" class="help d-block mt-1">You can upload screenshots, receipts, chats, or PDFs.</small>
                        <ul id="fileList" class="file-list"></ul>
                      </div>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="d-flex align-items-center justify-content-end">
                    <a href="<?= site_url('complaints') ?>" class="btn btn-light btn-ghost mr-2">Cancel</a>
                    <button class="btn btn-brand"><i class="mdi mdi-send"></i> Submit report</button>
                  </div>
                </form>
              </div>
            </section>

            <div class="page-spacer"></div>
            <?php $this->load->view('includes_footer'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script>
    // simple filename preview for the evidence uploader
    (function() {
      var input = document.getElementById('evidence');
      if (!input) return;
      var out = document.getElementById('fileList');
      input.addEventListener('change', function() {
        out.innerHTML = '';
        if (!this.files || !this.files.length) {
          return;
        }
        Array.from(this.files).forEach(function(f) {
          var li = document.createElement('li');
          li.textContent = f.name;
          out.appendChild(li);
        });
      });
    })();
  </script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>