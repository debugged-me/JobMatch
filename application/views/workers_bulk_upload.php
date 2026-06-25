<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php
  $page_title    = $page_title ?? 'Bulk Upload: Skilled Workers';
  $route_base    = $route_base ?? 'admin';
  $dashboardPath = 'dashboard/' . ($route_base === 'tesda' ? 'tesda' : 'admin');
  $workersBase   = $route_base . '/workers';
  ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --blue: #c1272d;
      --blue-2: #d63031;
      --blue-3: #1b5e9f;
      --gold: #2980b9;
      --silver: #c0c4cc;
      --ink: #0f172a;
      --muted: #6b7280;
      --bg: #f6f7fb;
      --card: #fff;
      --shadow: 0 10px 30px rgba(2, 6, 23, .10), 0 2px 8px rgba(2, 6, 23, .06);
    }

    body {
      background: var(--bg);
      color: var(--ink);
      font-family: "Karla", system-ui, -apple-system, "Segoe UI", Roboto, Arial
    }

    .app {
      max-width: 960px;
      margin: 0 auto;
      padding: 0 16px
    }

    .hero {
      position: relative;
      border-radius: 16px;
      color: #fff;
      padding: 16px;
      background: linear-gradient(135deg, var(--blue) 0%, var(--blue-2) 60%);
      box-shadow: var(--shadow);
      display: flex;
      align-items: center;
      gap: 12px
    }

    .hero .ico {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: grid;
      place-items: center;
      background: rgba(255, 255, 255, .14);
      border: 1px solid rgba(255, 255, 255, .2)
    }

    .hero h4 {
      margin: 0;
      font-weight: 700
    }

    .hero .sub {
      opacity: .95;
      font-size: .9rem
    }

    .card {
      background: var(--card);
      border-radius: 14px;
      box-shadow: var(--shadow);
      padding: 16px;
      border: 1px solid rgba(192, 196, 204, .55)
    }

    .accent:before {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      top: -1px;
      height: 4px;
      background: linear-gradient(90deg, #f59e0b, var(--blue-3));
      border-top-left-radius: 14px;
      border-top-right-radius: 14px
    }

    .drop {
      border: 2px dashed var(--silver);
      border-radius: 12px;
      padding: 14px;
      background: #fbfcfe
    }

    /* COMPACT MODAL */
    .modal-content {
      border-radius: 14px;
      border: 1px solid rgba(2, 6, 23, .08);
      box-shadow: var(--shadow)
    }

    .modal-sm-custom {
      max-width: 720px
    }

    .modal-header {
      border: 0;
      padding: 12px 14px
    }

    .modal-body {
      padding: 10px 14px 6px
    }

    .modal-footer {
      border: 0;
      padding: 10px 14px 14px
    }

    .mcard {
      background: #fff;
      border: 1px solid rgba(15, 23, 42, .08);
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(2, 6, 23, .05);
      padding: 12px
    }

    .mcard h6 {
      margin: 0 0 8px 0;
      font-weight: 700;
      font-size: .95rem
    }

    .divider {
      height: 1px;
      background: linear-gradient(90deg, rgba(2, 6, 23, .08), rgba(2, 6, 23, 0));
      margin: 8px 0 10px
    }

    .row-compact {
      row-gap: 10px
    }

    label.form-label {
      font-weight: 600;
      margin-bottom: 6px
    }

    .form-control {
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: .6rem .8rem;
      font-weight: 500
    }

    .form-control:disabled {
      background: #f1f5f9;
      color: #94a3b8
    }

    .form-text {
      margin-top: 4px
    }

    .muted {
      color: var(--muted);
      font-size: .85rem
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      padding: .55rem .9rem;
      border-radius: 10px;
      font-weight: 700
    }

    .btn-blue {
      background: var(--blue);
      border: 1px solid var(--blue);
      color: #fff
    }

    .btn-silver {
      background: #fff;
      border: 1px solid #c0c4cc;
      color: #111827
    }

    .btn-gold {
      background: #f59e0b;
      border: 1px solid #f59e0b;
      color: #111827
    }

    .btn-sm {
      padding: .45rem .75rem;
      border-radius: 9px
    }

    .breadcrumb-bar {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: .82rem;
      color: #64748b;
      margin-bottom: 8px
    }

    .breadcrumb-bar a {
      color: #64748b;
      text-decoration: none;
      font-weight: 600
    }

    .breadcrumb-bar a:hover {
      color: var(--blue)
    }

    .breadcrumb-bar .sep {
      color: #cbd5e1
    }

    .breadcrumb-bar .current {
      color: #334155;
      font-weight: 700
    }

    .drop-zone {
      border: 2px dashed var(--silver);
      border-radius: 12px;
      padding: 28px 20px;
      background: #fbfcfe;
      text-align: center;
      cursor: pointer;
      transition: border-color .2s, background .2s;
      position: relative;
    }

    .drop-zone:hover,
    .drop-zone.dragover {
      border-color: var(--blue);
      background: #fef2f2;
    }

    .drop-zone .drop-icon {
      font-size: 36px;
      color: var(--silver);
      margin-bottom: 8px;
    }

    .drop-zone .drop-text {
      font-weight: 600;
      color: #334155;
      font-size: .95rem;
    }

    .drop-zone .drop-sub {
      font-size: .82rem;
      color: var(--muted);
      margin-top: 4px;
    }

    .drop-zone input[type="file"] {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }

    .file-info {
      display: none;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      background: #f8fafc;
      margin-top: 10px;
      font-size: .88rem;
      font-weight: 600;
      color: #334155;
    }

    .file-info.show {
      display: flex;
    }

    .file-info .file-clear {
      margin-left: auto;
      background: transparent;
      border: 0;
      color: #dc2626;
      cursor: pointer;
      font-weight: 700;
    }

    .csv-help {
      background: #f8fafc;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 12px 14px;
      margin-top: 12px;
      font-size: .82rem;
      color: #475569;
    }

    .csv-help code {
      background: #e2e8f0;
      padding: 1px 5px;
      border-radius: 4px;
      font-size: .8rem;
    }

    .pw-toggle-btn {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: 0;
      color: #475569;
      cursor: pointer;
      padding: 4px;
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
              <a href="<?= site_url($dashboardPath) ?>"><i class="mdi mdi-home-outline"></i> Dashboard</a>
              <span class="sep">/</span>
              <span class="current">Bulk Upload Workers</span>
            </div>

            <!-- HERO -->
            <div class="hero mb-3">
              <div class="ico"><i class="mdi mdi-database-import-outline text-white"></i></div>
              <div>
                <h4><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h4>
                <div class="sub">Upload CSV to create/update skilled worker records</div>
              </div>
              <div class="ms-auto d-flex gap-2">
                <button class="btn btn-gold btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#addWorkerModal">
                  <i class="mdi mdi-account-plus-outline"></i> Add Worker
                </button>
                <a class="btn btn-silver btn-sm" href="<?= site_url($workersBase . '/template') ?>">
                  <i class="mdi mdi-file-download-outline"></i> Template (CSV)
                </a>
              </div>
            </div>

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline me-2"></i> <?= $error ?>
              </div>
            <?php endif; ?>

            <!-- BULK UPLOAD CARD -->
            <div class="card accent">
              <?= form_open_multipart($workersBase . '/preview', ['id' => 'uploadForm']); ?>
              <label for="file" class="form-label fw-semibold mb-2">Upload CSV / Excel</label>
              <div class="drop-zone" id="dropZone">
                <div class="drop-icon"><i class="mdi mdi-file-upload-outline"></i></div>
                <div class="drop-text">Drag & drop your file here, or click to browse</div>
                <div class="drop-sub">Supported formats: .csv, .xls, .xlsx</div>
                <input id="file" type="file" name="file" required accept=".csv,.xls,.xlsx">
              </div>
              <div class="file-info" id="fileInfo">
                <i class="mdi mdi-file-document-outline" style="font-size:20px;color:#2563eb"></i>
                <span id="fileName"></span>
                <span id="fileSize" style="color:#64748b;font-weight:400"></span>
                <button type="button" class="file-clear" id="fileClear"><i class="mdi mdi-close-circle"></i></button>
              </div>
              <div class="muted mt-2">Last name is sanitized (lowercase, A-Z/0–9). If blank, a random password is used.</div>

              <div class="csv-help">
                <strong>CSV format:</strong> Use headers: <code>first_name</code>, <code>last_name</code>, <code>email</code>, <code>phone</code>, <code>province</code>, <code>city</code>, <code>barangay</code>, <code>skill</code>, <code>password</code> (optional). Download the template for a ready-to-use example.
              </div>

              <div class="d-flex justify-content-end gap-2 mt-3">
                <a class="btn btn-silver" href="<?= site_url($dashboardPath) ?>">Cancel</a>
                <button class="btn btn-blue" id="previewBtn"><i class="mdi mdi-eye-outline"></i> Preview</button>
              </div>
              <?= form_close(); ?>
            </div>


          </div>
        </div>
        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <?php
  $csrf_name = $this->security->get_csrf_token_name();
  $csrf_hash = $this->security->get_csrf_hash();
  ?>

  <!-- COMPACT MODAL -->
  <div class="modal fade" id="addWorkerModal" tabindex="-1" aria-labelledby="addWorkerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm-custom">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="addWorkerModalLabel">Add Skilled Worker</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(0.2)"></button>
        </div>

        <form id="addWorkerForm" action="<?= site_url($workersBase . '/store') ?>" method="post" novalidate>
          <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>" data-csrf-name="<?= $csrf_name ?>" id="csrfToken">

          <div class="modal-body">
            <div class="mcard">
              <h6>Basic Details</h6>
              <div class="divider"></div>

              <div class="row row-compact">
                <div class="col-md-4 col-sm-12">
                  <label class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-4 col-sm-12">
                  <label class="form-label">Middle Name</label>
                  <input type="text" name="middle_name" class="form-control">
                </div>
                <div class="col-md-4 col-sm-12">
                  <label class="form-label">Last Name <span class="text-danger">*</span></label>
                  <input type="text" name="last_name" class="form-control" required>
                </div>

                <div class="col-md-6 col-sm-12">
                  <label class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 col-sm-12">
                  <label class="form-label">Phone</label>
                  <input type="text" name="phone" class="form-control">
                </div>

                <div class="col-md-4 col-sm-12">
                  <label class="form-label">Province</label>
                  <select name="province" id="addr_province" class="form-control">
                    <option value="">Select province</option>
                  </select>
                </div>
                <div class="col-md-4 col-sm-12">
                  <label class="form-label">City</label>
                  <select name="city" id="addr_city" class="form-control" disabled>
                    <option value="">Select city</option>
                  </select>
                </div>
                <div class="col-md-4 col-sm-12">
                  <label class="form-label">Barangay</label>
                  <select name="brgy" id="addr_brgy" class="form-control" disabled>
                    <option value="">Select barangay</option>
                  </select>
                </div>

                <div class="col-12">
                  <label class="form-label">Password</label>
                  <div style="position:relative">
                    <input type="password" name="password_override" id="pwOverride" class="form-control" placeholder="Leave blank to auto-generate from last name" style="padding-right:40px">
                    <button type="button" class="pw-toggle-btn" data-target="#pwOverride">
                      <i class="mdi mdi-eye-outline"></i>
                    </button>
                  </div>
                  <div class="form-text muted">If blank: uses sanitized last name; random if last name invalid.</div>
                </div>
                <div class="col-12 mt-2">
                  <!-- <div class="form-check">
    <input class="form-check-input" type="checkbox" value="1" id="send_activation" name="send_activation" checked>
    <label class="form-check-label" for="send_activation">
      Send activation email (with link)
    </label>
  </div>
</div> -->
                </div>

              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-silver" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-blue">
                <i class="mdi mdi-content-save-outline"></i> Save Worker
              </button>
            </div>
        </form>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Drag-and-drop file zone
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileClear = document.getElementById('fileClear');

    function formatSize(bytes) {
      if (bytes < 1024) return bytes + ' B';
      if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function showFileInfo(f) {
      if (!f) { fileInfo.classList.remove('show'); return; }
      fileName.textContent = f.name;
      fileSize.textContent = '(' + formatSize(f.size) + ')';
      fileInfo.classList.add('show');
    }

    if (dropZone) {
      ['dragenter', 'dragover'].forEach(function(ev) {
        dropZone.addEventListener(ev, function(e) {
          e.preventDefault();
          dropZone.classList.add('dragover');
        });
      });
      ['dragleave', 'drop'].forEach(function(ev) {
        dropZone.addEventListener(ev, function(e) {
          e.preventDefault();
          dropZone.classList.remove('dragover');
        });
      });
      dropZone.addEventListener('drop', function(e) {
        const files = e.dataTransfer.files;
        if (files.length) {
          fileInput.files = files;
          showFileInfo(files[0]);
        }
      });
      fileInput.addEventListener('change', function() {
        showFileInfo(this.files[0]);
      });
    }
    if (fileClear) {
      fileClear.addEventListener('click', function() {
        fileInput.value = '';
        showFileInfo(null);
      });
    }

    // Password show/hide toggle
    document.querySelectorAll('.pw-toggle-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const input = document.querySelector(this.dataset.target);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        this.querySelector('i').classList.toggle('mdi-eye-outline');
        this.querySelector('i').classList.toggle('mdi-eye-off-outline');
      });
    });

    // Preview button loading state
    const previewBtn = document.getElementById('previewBtn');
    const uploadForm = document.getElementById('uploadForm');
    if (previewBtn && uploadForm) {
      uploadForm.addEventListener('submit', function() {
        previewBtn.disabled = true;
        previewBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Uploading...';
      });
    }

    (function() {
      const $ = (s, ctx = document) => ctx.querySelector(s);
      const apiBase = '<?= site_url('address/api') ?>';
      const csrfInput = $('#csrfToken');
      const provSel = $('#addr_province');
      const citySel = $('#addr_city');
      const brgySel = $('#addr_brgy');
      const modal = document.getElementById('addWorkerModal');

      function applyCsrf(p) {
        if (p && p.csrf_name && p.csrf_hash && csrfInput) {
          csrfInput.name = p.csrf_name;
          csrfInput.value = p.csrf_hash;
        }
      }
      const opt = (v, t) => {
        const o = document.createElement('option');
        o.value = v;
        o.textContent = t ?? v;
        return o;
      };
      const reset = (sel, ph) => {
        sel.innerHTML = '';
        sel.appendChild(opt('', ph));
        sel.disabled = true;
      };

      // Load provinces when modal opens
      modal?.addEventListener('shown.bs.modal', async () => {
        if (provSel.options.length > 1) return;
        reset(provSel, 'Select province');
        reset(citySel, 'Select city');
        reset(brgySel, 'Select barangay');

        const r = await fetch(apiBase + '?scope=province', {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const d = await r.json();
        applyCsrf(d);
        if (d.ok && Array.isArray(d.items)) {
          provSel.disabled = false;
          d.items.forEach(p => provSel.appendChild(opt(p)));
        }
      });

      // Province  cities
      provSel?.addEventListener('change', async () => {
        reset(citySel, 'Select city…');
        reset(brgySel, 'Select barangay');
        const p = provSel.value;
        if (!p) return;

        const r = await fetch(apiBase + '?scope=city&province=' + encodeURIComponent(p), {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const d = await r.json();
        applyCsrf(d);
        if (d.ok && Array.isArray(d.items)) {
          citySel.disabled = false;
          d.items.forEach(c => citySel.appendChild(opt(c)));
        }
      });

      // City  barangays
      citySel?.addEventListener('change', async () => {
        reset(brgySel, 'Select barangay');
        const p = provSel.value,
          c = citySel.value;
        if (!p || !c) return;

        const r = await fetch(apiBase + '?scope=brgy&province=' + encodeURIComponent(p) + '&city=' + encodeURIComponent(c), {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const d = await r.json();
        applyCsrf(d);
        if (d.ok && Array.isArray(d.items)) {
          brgySel.disabled = false;
          d.items.forEach(b => brgySel.appendChild(opt(b)));
        }
      });

      // Submit minimal form via AJAX
      const form = $('#addWorkerForm');
      form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

        try {
          const fd = new FormData(form);
          if (csrfInput) fd.set(csrfInput.name, csrfInput.value);

          const res = await fetch(form.action, {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: fd
          });
          const data = await res.json();
          applyCsrf(data);

          if (data.ok) {
            const tempPw = data.temp_password ?? '(custom set / unchanged)';
            if (window.Swal) {
              Swal.fire({
                icon: 'success',
                title: 'Worker saved',
                html: 'Temporary password: <strong>' + tempPw + '</strong>'
              }).then(() => location.reload());
            } else {
              alert('Worker saved.\nTemporary password: ' + tempPw);
              location.reload();
            }
          } else {
            const errMsg = data.message || 'Unable to save';
            if (window.Swal) {
              Swal.fire({
                icon: 'error',
                title: 'Save failed',
                text: errMsg
              });
            } else {
              alert('Error: ' + errMsg);
            }
          }
        } catch (err) {
          if (window.Swal) {
            Swal.fire({
              icon: 'error',
              title: 'Request failed',
              text: 'Please try again.'
            });
          } else {
            alert('Request failed. Please try again.');
          }
        } finally {
          btn.disabled = false;
          btn.innerHTML = '<i class="mdi mdi-content-save-outline"></i> Save Worker';
        }
      });
    })();
  </script>
</body>

</html>