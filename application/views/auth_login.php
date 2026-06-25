<!DOCTYPE html>
<html lang="en">
<link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="LEFT4CODE">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@500;600;700;800;900&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('dist/css/app.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/signup.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/auth.css?v=20260617') ?>">
  <title>PESO Davao Oriental - Login</title>
</head>

<body class="overflow-y-auto auth-page auth-page--login">
  <div class="page-loader bg-background fixed inset-0 z-[100] flex items-center justify-center transition-opacity">
    <div class="loader-spinner !w-14"></div>
  </div>

  <div class="auth-shell">
    <div class="auth-wrap auth-wrap--login">
      <aside class="auth-visual">
        <div class="auth-visual-glow"></div>
        <div class="auth-visual-art">
          <img src="<?= base_url('assets/images/job-matching.png') ?>" alt="Job matching illustration">
        </div>

        <div class="auth-visual-brand">
          <div class="auth-visual-mark">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="">
          </div>
          <div>
            <p class="auth-visual-name">PESO Davao Oriental</p>
            <p class="auth-visual-tag">Public Employment Service Office</p>
          </div>
        </div>

        <div class="auth-visual-copy">
          <span class="auth-kicker">Job matching portal</span>
          <h1>Connecting skilled workers and employers across the province</h1>
          <p>Manage opportunities, messages, and hiring activity in one secure workspace.</p>
        </div>
      </aside>

      <main class="auth-panel">
        <div class="auth-mobile-brand">
          <a href="<?= site_url('/') ?>" class="auth-brand-link">
            <span class="auth-brand-mark">
              <img src="<?= base_url('assets/images/logo.png') ?>" alt="">
            </span>
            <span>
              <strong>PESO Davao Oriental</strong>
              <small>Public Employment Service Office</small>
            </span>
          </a>
        </div>

        <div class="auth-head fade-up fd2">
          <h2>Welcome back</h2>
          <p>Sign in to continue to your JobMatch account.</p>
        </div>

        <?php foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info', 'msg' => 'success'] as $key => $class): ?>
          <?php if ($this->session->flashdata($key)): ?>
            <div class="alert alert-<?= $class ?> small mb-3 fade-up fd2" role="alert">
              <?= htmlspecialchars($this->session->flashdata($key), ENT_QUOTES, 'UTF-8') ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>

        <?php if (validation_errors()): ?>
          <div class="alert alert-danger small mb-3 fade-up fd2">
            <?= validation_errors(); ?>
          </div>
        <?php endif; ?>

        <?= form_open('auth/login', ['class' => 'auth-form']) ?>
        <?php if (isset($this->security)): ?>
          <input type="hidden"
            name="<?= $this->security->get_csrf_token_name(); ?>"
            value="<?= $this->security->get_csrf_hash(); ?>">
        <?php endif; ?>

        <div class="auth-field fade-up fd3">
          <label class="auth-label" for="loginEmail">Email or Username</label>
          <div class="auth-control">
            <i data-lucide="user" class="auth-control-icon"></i>
            <input
              id="loginEmail"
              class="auth-input auth-input--icon"
              type="text"
              name="email"
              placeholder="you@example.com or username"
              value="<?= set_value('email'); ?>"
              autocomplete="username"
              autocapitalize="none"
              spellcheck="false"
              required />
          </div>
          <?= form_error('email', '<div class="text-red-600 text-sm mt-2">', '</div>'); ?>
        </div>

        <div class="auth-field fade-up fd4">
          <div class="auth-label-row">
            <label class="auth-label" for="loginPassword">Password</label>
            <a href="<?= site_url('auth/forgot'); ?>" class="auth-link">Forgot password?</a>
          </div>

          <div class="auth-control">
            <i data-lucide="lock" class="auth-control-icon"></i>
            <input
              id="loginPassword"
              class="auth-input auth-input--icon auth-input-pass"
              type="password"
              name="password"
              placeholder="Enter your password"
              autocomplete="current-password"
              required />

            <button
              type="button"
              class="auth-eye"
              aria-label="Show password"
              data-toggle="password"
              data-target="#loginPassword">
              <i data-lucide="eye" class="auth-icon"></i>
            </button>
          </div>

          <?= form_error('password', '<div class="text-red-600 text-sm mt-2">', '</div>'); ?>
        </div>

        <button type="submit" class="auth-btn auth-btn-primary fade-up fd5">
          Login
        </button>

        <?= form_close() ?>

        <p class="auth-switch fade-up fd5">
          Don't have an account?
          <a href="<?= site_url('auth/signup') ?>" data-auth-transition="1">Create account</a>
        </p>
      </main>
    </div>

    <p class="auth-privacy-note">
      In compliance with the Data Privacy Act of 2012 (RA 10173), your personal information is collected and processed solely for PESO Davao Oriental services.
    </p>
  </div>

  <script src="<?= base_url('dist/js/vendors/dom.js') ?>"></script>
  <script src="<?= base_url('dist/js/vendors/lucide.js') ?>"></script>
  <script src="<?= base_url('dist/js/components/base/page-loader.js') ?>"></script>
  <script src="<?= base_url('dist/js/components/base/lucide.js') ?>"></script>

  <script src="<?= base_url('assets/js/auth-common.js') ?>"></script>
</body>

</html>
