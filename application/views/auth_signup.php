<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="LEFT4CODE">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@500;600;700;800;900&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('dist/css/app.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/signup.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/auth.css?v=20260617') ?>">
  <title>PESO Davao Oriental - Sign Up</title>
</head>

<body class="overflow-y-auto auth-page auth-page--signup">
  <div class="page-loader bg-background fixed inset-0 z-[100] flex items-center justify-center transition-opacity">
    <div class="loader-spinner !w-14"></div>
  </div>

  <div class="auth-shell">
    <div class="auth-wrap auth-wrap--signup">
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
          <span class="auth-kicker">Get started</span>
          <h1>Create your account and join the local employment network.</h1>
          <p>Build your worker profile or find skilled service providers with a verified JobMatch account.</p>
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
          <h2>Create your account</h2>
          <p>Register to manage jobs, hires, and messages.</p>
        </div>

        <?php foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info', 'msg' => 'success'] as $key => $class): ?>
          <?php if ($this->session->flashdata($key)): ?>
            <div class="alert alert-<?= $class ?> alert-dismissible fade show small mb-3 fade-up fd2" role="alert">
              <?= htmlspecialchars($this->session->flashdata($key), ENT_QUOTES, 'UTF-8') ?>
              <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>

        <?= form_open('auth/signup', ['id' => 'signupForm', 'class' => 'auth-form', 'data-email-check-url' => site_url('auth/email-available')]) ?>

        <div class="auth-field auth-field--full fade-up fd3">
          <label class="auth-label">I am registering as a</label>
          <div class="auth-role-grid">
            <label class="auth-role-card">
              <input
                type="radio"
                name="role"
                value="worker"
                <?= set_radio('role', 'worker'); ?>
                required>
              <span class="auth-role-icon">
                <i data-lucide="briefcase-business"></i>
              </span>
              <span>
                <strong>Skilled Worker</strong>
                <small>Offer services and manage your profile.</small>
              </span>
            </label>

            <label class="auth-role-card">
              <input
                type="radio"
                name="role"
                value="client"
                <?= set_radio('role', 'client'); ?>
                required>
              <span class="auth-role-icon auth-role-icon--client">
                <i data-lucide="users"></i>
              </span>
              <span>
                <strong>Individual / Employer</strong>
                <small>Find and hire skilled workers.</small>
              </span>
            </label>
          </div>
          <?php if (form_error('role')): ?>
            <div class="text-red-600 text-sm mt-2"><?= form_error('role'); ?></div>
          <?php endif; ?>
        </div>

        <div class="auth-grid2 fade-up fd3">
          <div class="auth-field">
            <label class="auth-label" for="first_name">First Name</label>
            <input
              id="first_name"
              class="auth-input"
              type="text"
              name="first_name"
              placeholder="First Name"
              value="<?= set_value('first_name') ?>"
              autocomplete="given-name"
              required
              aria-required="true" />
            <?php if (form_error('first_name')): ?>
              <div class="text-red-600 text-sm mt-2"><?= form_error('first_name'); ?></div>
            <?php endif; ?>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="last_name">Last Name</label>
            <input
              id="last_name"
              class="auth-input"
              type="text"
              name="last_name"
              placeholder="Last Name"
              value="<?= set_value('last_name') ?>"
              autocomplete="family-name"
              required
              aria-required="true" />
            <?php if (form_error('last_name')): ?>
              <div class="text-red-600 text-sm mt-2"><?= form_error('last_name'); ?></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="auth-grid2 fade-up fd3">
          <div class="auth-field">
            <label class="auth-label" for="signupEmail">Email</label>
            <div class="auth-control">
              <i data-lucide="mail" class="auth-control-icon"></i>
              <input
                id="signupEmail"
                class="auth-input auth-input--icon"
                type="email"
                name="email"
                placeholder="you@example.com"
                value="<?= set_value('email') ?>">
            </div>
            <div id="emailHint" class="alert alert-info alert-inline" role="alert" hidden></div>

            <?php if (form_error('email')): ?>
              <div class="alert alert-danger alert-inline" role="alert"><?= form_error('email'); ?></div>
            <?php endif; ?>
          </div>

          <div class="auth-field">
            <label class="auth-label" for="signupPassword">Password</label>
            <div class="auth-control">
              <i data-lucide="lock" class="auth-control-icon"></i>
              <input
                id="signupPassword"
                class="auth-input auth-input--icon auth-input-pass"
                type="password"
                name="password"
                placeholder="Create a password"
                autocomplete="new-password"
                required
                aria-required="true" />

              <button
                type="button"
                class="auth-eye"
                aria-label="Show password"
                data-toggle="password"
                data-target="#signupPassword">
                <i data-lucide="eye" class="auth-icon"></i>
              </button>
            </div>
            <?php if (form_error('password')): ?>
              <div class="text-red-600 text-sm mt-2"><?= form_error('password'); ?></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="auth-strength fade-up fd4" aria-hidden="true">
          <span class="active"></span>
          <span class="active"></span>
          <span class="active"></span>
          <span></span>
        </div>

        <div class="auth-tos fade-up fd4">
          <input
            type="checkbox"
            id="tos"
            name="accept_privacy"
            value="1"
            <?= set_checkbox('accept_privacy', '1'); ?>
            required>
          <label class="auth-tos-label" for="tos">
            In compliance with the Data Privacy Act of 2012, I consent to the collection and processing of my personal information and agree to the
            <a href="#" id="openPrivacy">Privacy Policy</a>.
          </label>
        </div>

        <?php if (form_error('accept_privacy')): ?>
          <div class="text-red-600 text-sm mt-2"><?= form_error('accept_privacy'); ?></div>
        <?php endif; ?>

        <div class="auth-field auth-field--full fade-up fd4">
          <label class="auth-label">Human verification</label>
          <div class="auth-captcha">
            <div
              class="g-recaptcha"
              data-sitekey="<?= html_escape($this->config->item('recaptcha_site_key')) ?>"
              data-callback="recaptchaOk"
              data-expired-callback="recaptchaExpired"
              data-error-callback="recaptchaError"></div>
          </div>
          <?php if (form_error('g-recaptcha-response')): ?>
            <div class="text-red-600 text-sm mt-2"><?= form_error('g-recaptcha-response'); ?></div>
          <?php endif; ?>
        </div>

        <div class="hp" aria-hidden="true">
          <label for="hp-website">Website</label>
          <input id="hp-website" type="text" name="website" autocomplete="off" tabindex="-1">
        </div>

        <button
          id="btnRegister"
          type="submit"
          disabled
          class="auth-btn auth-btn-primary fade-up fd4"
          aria-disabled="true"
          tabindex="-1">
          Register
        </button>

        <?= form_close() ?>

        <p class="auth-switch fade-up fd4">
          Already have an account?
          <a href="<?= site_url('auth/login') ?>" data-auth-transition="1">Login</a>
        </p>
      </main>
    </div>
  </div>

  <div id="pmModal" role="dialog" aria-modal="true" aria-labelledby="pmTitle">
    <div id="pmBackdrop" data-close="1"></div>
    <div id="pmPanel">
      <div id="pmHead">
        <h3 id="pmTitle">Privacy Policy</h3>
        <button type="button" class="pm-btn pm-close" data-close="1" aria-label="Close">&times;</button>
      </div>
      <div id="pmBody">
        <p><strong>Effective date:</strong> <?= date('F j, Y') ?></p>
        <p>Welcome to the <strong>Public Employment Service Office &ndash; Davao Oriental</strong>. We connect skilled workers with individuals or employers who need their services. This Privacy Policy explains what data we collect, how we use it, and the choices you have.</p>

        <h4>1) Information We Collect</h4>
        <ul>
          <li>Account data (name, email, password hash), role (worker or client), and account status/approval.</li>
          <li>Profile/portfolio content you add (photos, categories/skills, rates within admin-set ranges, descriptions, images/PDFs).</li>
          <li>Messaging content (threads/messages exchanged in the platform) and hire/transaction activity.</li>
          <li>Reviews/ratings after completed engagements.</li>
          <li>System notifications and related delivery data (email or in-app notification events).</li>
          <li>Basic usage, log, and device information for security and diagnostics.</li>
        </ul>

        <h4>2) How We Use Your Information</h4>
        <ul>
          <li>Create and maintain your account; show worker profiles/portfolios to clients.</li>
          <li>Enable search, hiring, confirmations, and in-app messaging between workers and clients.</li>
          <li>Send notifications about key transaction events; respond to password-reset requests.</li>
          <li>Display/moderate reviews; show admin dashboards/metrics (workers, clients, ongoing/completed engagements).</li>
          <li>Keep the service secure, prevent fraud/abuse, and maintain an audit trail of CRUD operations.</li>
        </ul>

        <h4>3) Legal Bases</h4>
        <p>We process your data to perform our contract with you (providing the PESO Davao Oriental service), for our legitimate interests (platform safety and service improvement), and to comply with legal obligations.</p>

        <h4>4) Sharing</h4>
        <p>We share data with service providers that help us operate (ex. hosting, email delivery) under appropriate safeguards. Worker profile content may be visible to signed-in clients as part of matching. We may disclose information if required by law.</p>

        <h4>5) Data Retention</h4>
        <p>We retain account, messaging, and transaction records while your account is active and as required for legitimate business needs and legal obligations. You may request deletion; some records may be retained for compliance, dispute resolution, and security.</p>

        <h4>6) Your Choices & Rights</h4>
        <ul>
          <li>Access and update your profile information in account settings.</li>
          <li>Request deletion of your account (subject to necessary retention).</li>
          <li>Control notification preferences in-app or via email settings.</li>
        </ul>

        <h4>7) Security</h4>
        <p>We use reasonable technical and organizational measures to protect personal data. No method of transmission or storage is 100% secure.</p>

        <h4>8) Children</h4>
        <p>PESO Davao Oriental is not directed to children under 16 and we do not knowingly collect personal data from children.</p>

        <h4>9) International Transfers</h4>
        <p>Your information may be processed in countries with different data-protection laws. We take steps to ensure appropriate safeguards are in place.</p>

        <h4>10) Changes</h4>
        <p>We may update this Policy. If changes are material, we&rsquo;ll notify you in the app or by email.</p>
      </div>
      <div id="pmFoot">
        <button type="button" class="pm-btn" data-close="1">Close</button>
        <button type="button" class="pm-btn pm-btn-primary" id="pmAgree" data-close="1">I Understand</button>
      </div>
    </div>
  </div>

  <script src="<?= base_url('dist/js/vendors/dom.js') ?>"></script>
  <script src="<?= base_url('dist/js/vendors/lucide.js') ?>"></script>
  <script src="<?= base_url('dist/js/vendors/modal.js') ?>"></script>
  <script src="<?= base_url('dist/js/components/base/page-loader.js') ?>"></script>
  <script src="<?= base_url('dist/js/components/base/lucide.js') ?>"></script>
  <script src="<?= base_url('dist/js/components/theme-switcher.js') ?>"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <script src="<?= base_url('assets/js/auth-common.js') ?>"></script>
  <script src="<?= base_url('assets/js/auth-signup.js') ?>"></script>
</body>

</html>
