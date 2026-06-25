<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php $page_title = $page_title ?? 'Change Password'; ?>
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
      --gold-2: #1b5e9f;
      --silver: #c0c4cc;
      --silver-2: #e5e7eb;
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

    /* Hero */
    .hero {
      position: relative;
      border-radius: 16px;
      color: #fff;
      padding: 18px;
      background: linear-gradient(135deg, var(--blue) 0%, var(--blue-2) 60%);
      box-shadow: var(--shadow);
      overflow: hidden;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .hero:after {
      content: "";
      position: absolute;
      right: -60px;
      bottom: -60px;
      width: 220px;
      height: 220px;
      border-radius: 50%;
      background: radial-gradient(circle at center, rgba(245, 158, 11, .45), rgba(245, 158, 11, 0) 60%);
      filter: blur(6px);
    }

    .hero .ico {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: rgba(255, 255, 255, .14);
      border: 1px solid rgba(255, 255, 255, .2)
    }

    .hero h4 {
      margin: 0;
      font-weight: 700;
      letter-spacing: .2px
    }

    .hero .sub {
      opacity: .95;
      font-size: .92rem
    }

    /* Card */
    .card {
      background: var(--card);
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 20px;
      border: 1px solid rgba(192, 196, 204, .55);
    }

    .accent {
      position: relative
    }

    .accent:before {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      top: -1px;
      height: 4px;
      background: linear-gradient(90deg, var(--gold), var(--blue-3));
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
    }

    /* Inputs */
    .form-control {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: .85rem .95rem;
      font-weight: 500;
      transition: border-color .18s ease, box-shadow .18s ease;
    }

    .form-control:focus {
      outline: 0;
      border-color: var(--blue-3);
      box-shadow: 0 0 0 3px rgba(193, 39, 45, .18)
    }

    .muted {
      color: var(--muted);
      font-size: .9rem
    }

    .chips {
      display: flex;
      flex-wrap: wrap;
      gap: .45rem;
      margin-top: 10px
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      padding: .3rem .6rem;
      border-radius: 9999px;
      border: 1px solid var(--silver-2);
      background: #fff;
      font-size: .85rem
    }

    .chip i {
      font-size: 1rem
    }

    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: .55rem;
      padding: .66rem 1.05rem;
      border-radius: 12px;
      font-weight: 700
    }

    .btn i {
      font-size: 1.05rem
    }

    .btn-gold {
      background: var(--gold);
      border: 1px solid var(--gold);
      color: #111827
    }

    .btn-gold:hover {
      background: var(--gold-2);
      border-color: var(--gold-2);
      color: #fff
    }

    .btn-blue {
      background: var(--blue);
      border: 1px solid var(--blue);
      color: #fff
    }

    .btn-blue:hover {
      background: var(--brand-blue-dark, #a61f2b);
      border-color: var(--brand-blue-dark, #a61f2b)
    }

    .btn-silver {
      background: #fff;
      border: 1px solid var(--silver);
      color: #111827
    }

    .btn-silver:hover {
      background: #f8fafc
    }

    .input-wrap {
      position: relative
    }

    .toggle-eye {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: 0;
      color: #475569;
      cursor: pointer;
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
      color: #c1272d
    }

    .breadcrumb-bar .sep {
      color: #cbd5e1
    }

    .breadcrumb-bar .current {
      color: #334155;
      font-weight: 700
    }

    .pw-strength {
      margin-top: 8px;
    }

    .pw-strength-bar {
      height: 6px;
      border-radius: 9999px;
      background: #e5e7eb;
      overflow: hidden;
      transition: background .3s ease;
    }

    .pw-strength-fill {
      height: 100%;
      width: 0%;
      border-radius: 9999px;
      transition: width .3s ease, background .3s ease;
    }

    .pw-strength-label {
      font-size: .78rem;
      font-weight: 700;
      margin-top: 4px;
      color: #64748b;
    }

    .pw-match {
      font-size: .82rem;
      font-weight: 600;
      margin-top: 6px;
      display: none;
    }

    .pw-match.show {
      display: block;
    }

    .pw-match.ok {
      color: #16a34a;
    }

    .pw-match.bad {
      color: #ef4444;
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
              <span class="current">Change Password</span>
            </div>

            <!-- HERO -->
            <div class="hero mb-3">
              <div class="ico"><i class="mdi mdi-lock-reset text-white"></i></div>
              <div>
                <h4><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h4>
                <div class="sub">Update your admin password securely</div>
              </div>
              <div class="ms-auto">
                <a class="btn btn-gold btn-sm" href="<?= site_url('dashboard/admin') ?>">
                  <i class="mdi mdi-view-dashboard-outline"></i> Back to Dashboard
                </a>
              </div>
            </div>

            <!-- FLASH / VALIDATION -->
            <?php foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info', 'msg' => 'success'] as $key => $class): ?>
              <?php if ($this->session->flashdata($key)): ?>
                <div class="alert alert-<?= $class ?> alert-dismissible fade show" role="alert">
                  <?= htmlspecialchars($this->session->flashdata($key), ENT_QUOTES, 'UTF-8') ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>

            <?php if (validation_errors()): ?>
              <div class="alert alert-danger d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline me-2"></i>
                <div><?= validation_errors(); ?></div>
              </div>
            <?php endif; ?>

            <!-- FORM CARD -->
            <div class="card accent">
              <?= form_open('admin/change_password'); ?>
              <?php if (isset($this->security)): ?>
                <input type="hidden"
                  name="<?= $this->security->get_csrf_token_name(); ?>"
                  value="<?= $this->security->get_csrf_hash(); ?>">
              <?php endif; ?>

              <div class="mb-3">
                <label class="form-label fw-semibold">Current Password</label>
                <div class="input-wrap">
                  <input type="password" name="current_password" id="current_password" class="form-control" minlength="6" required>
                  <button type="button" class="toggle-eye" data-target="#current_password">
                    <i class="mdi mdi-eye-outline"></i>
                  </button>
                </div>
                <div class="muted mt-1">Enter your existing password to confirm this change.</div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <div class="input-wrap">
                  <input type="password" name="new_password" id="new_password" class="form-control" minlength="8" required placeholder="At least 8 characters">
                  <button type="button" class="toggle-eye" data-target="#new_password">
                    <i class="mdi mdi-eye-outline"></i>
                  </button>
                </div>
                <div class="pw-strength">
                  <div class="pw-strength-bar">
                    <div class="pw-strength-fill" id="pw-strength-fill"></div>
                  </div>
                  <div class="pw-strength-label" id="pw-strength-label">Enter a password</div>
                </div>
                <div class="chips">
                  <span class="chip"><i class="mdi mdi-shield-key-outline"></i> Min. 8 chars</span>
                  <span class="chip"><i class="mdi mdi-alphabetical-variant"></i> Mix of letters & numbers</span>
                  <span class="chip"><i class="mdi mdi-lock-outline"></i> Avoid common words</span>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <div class="input-wrap">
                  <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="8" required>
                  <button type="button" class="toggle-eye" data-target="#confirm_password">
                    <i class="mdi mdi-eye-outline"></i>
                  </button>
                </div>
                <div class="pw-match" id="pw-match"></div>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-silver" href="<?= site_url('dashboard/admin') ?>">
                  <i class="mdi mdi-close-circle-outline"></i> Cancel
                </a>
                <button class="btn btn-blue">
                  <i class="mdi mdi-content-save-outline"></i> Update Password
                </button>
              </div>
              <?= form_close(); ?>
            </div>

            <div class="my-4"></div>
          </div>
        </div>
        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
  <script>
    setTimeout(function() {
      document.querySelectorAll('.alert').forEach(function(el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
          new bootstrap.Alert(el).close();
        } else {
          el.remove();
        }
      });
    }, 4000);

    document.querySelectorAll('.toggle-eye').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const input = document.querySelector(this.dataset.target);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        this.querySelector('i').classList.toggle('mdi-eye-outline');
        this.querySelector('i').classList.toggle('mdi-eye-off-outline');
      });
    });

    // Password strength meter
    var newPw = document.getElementById('new_password');
    var strengthFill = document.getElementById('pw-strength-fill');
    var strengthLabel = document.getElementById('pw-strength-label');
    var confirmPw = document.getElementById('confirm_password');
    var matchEl = document.getElementById('pw-match');

    function scorePassword(pw) {
      var score = 0;
      if (!pw) return 0;
      if (pw.length >= 8) score += 1;
      if (pw.length >= 12) score += 1;
      if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score += 1;
      if (/\d/.test(pw)) score += 1;
      if (/[^A-Za-z0-9]/.test(pw)) score += 1;
      return Math.min(score, 5);
    }

    var strengthLevels = [
      { width: '0%',   color: '#e5e7eb', label: 'Enter a password' },
      { width: '20%',  color: '#ef4444', label: 'Very weak' },
      { width: '40%',  color: '#f59e0b', label: 'Weak' },
      { width: '60%',  color: '#eab308', label: 'Fair' },
      { width: '80%',  color: '#22c55e', label: 'Good' },
      { width: '100%', color: '#16a34a', label: 'Strong' }
    ];

    function updateStrength() {
      var score = scorePassword(newPw.value);
      var info = strengthLevels[score];
      strengthFill.style.width = info.width;
      strengthFill.style.background = info.color;
      strengthLabel.textContent = info.label;
      strengthLabel.style.color = score === 0 ? '#64748b' : info.color;
      updateMatch();
    }

    function updateMatch() {
      var v1 = newPw.value;
      var v2 = confirmPw.value;
      if (!v2) {
        matchEl.classList.remove('show', 'ok', 'bad');
        matchEl.textContent = '';
        return;
      }
      matchEl.classList.add('show');
      if (v1 === v2) {
        matchEl.classList.add('ok');
        matchEl.classList.remove('bad');
        matchEl.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Passwords match';
      } else {
        matchEl.classList.add('bad');
        matchEl.classList.remove('ok');
        matchEl.innerHTML = '<i class="mdi mdi-alert-circle-outline"></i> Passwords do not match';
      }
    }

    if (newPw) newPw.addEventListener('input', updateStrength);
    if (confirmPw) confirmPw.addEventListener('input', updateMatch);

    // Pre-logout confirmation after successful password change
    var successAlert = document.querySelector('.alert-success');
    if (successAlert && successAlert.textContent.indexOf('password') !== -1) {
      setTimeout(function() {
        Swal.fire({
          icon: 'success',
          title: 'Password Changed',
          text: 'You will be logged out shortly. Please log in with your new password.',
          timer: 3000,
          showConfirmButton: false
        });
      }, 500);
    }
  </script>
</body>

</html>