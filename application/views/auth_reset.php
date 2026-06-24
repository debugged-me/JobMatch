<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Reset Password • JobMatch DavOr', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/icons-phosphor.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/auth-reset.css') ?>">

</head>

<body>
  <div class="wrap">
    <div class="card">
      <div class="card-head">
        <div class="icon"><i class="mdi mdi-lock-reset auth-icon"></i></div>
        <div>
          <h1>Reset Password</h1>
          <p>Create a strong new password</p>
        </div>
      </div>

      <div class="card-body">
        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert--err"><?= htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (validation_errors()): ?>
          <div class="alert alert--err"><?= validation_errors(); ?></div>
        <?php endif; ?>

        <?= form_open('auth/reset'); ?>
        <?php if (isset($this->security)): ?>
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <?php endif; ?>
        <input type="hidden" name="selector" value="<?= html_escape($selector) ?>">
        <input type="hidden" name="validator" value="<?= html_escape($validator) ?>">

        <div class="auth-block">
          <label for="password">New Password</label>
          <div class="field">
            <input id="password" class="input" type="password" name="password" minlength="8" required>
            <button type="button" class="toggle" data-toggle="#password" aria-label="Show password">
              <i class="mdi mdi-eye-outline"></i>
            </button>
          </div>
          <?= form_error('password', '<div class="alert alert--err auth-error">', '</div>'); ?>
        </div>

        <div class="auth-block-lg">
          <label for="password2">Confirm Password</label>
          <div class="field">
            <input id="password2" class="input" type="password" name="password2" required>
            <button type="button" class="toggle" data-toggle="#password2" aria-label="Show password">
              <i class="mdi mdi-eye-outline"></i>
            </button>
          </div>
          <?= form_error('password2', '<div class="alert alert--err auth-error">', '</div>'); ?>
        </div>

        <button class="btn" type="submit">
          <i class="mdi mdi-check-circle-outline"></i> Update Password
        </button>
        <?= form_close(); ?>

        <div class="text-center">
          <a class="back" href="<?= site_url('auth/login'); ?>">Back to login</a>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/auth-reset.js') ?>"></script>
</body>

</html>
