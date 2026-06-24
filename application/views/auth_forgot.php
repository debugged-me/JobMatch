<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Forgot Password • JobMatch DavOr', ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Brand assets / fonts -->
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/icons-phosphor.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/auth-forgot.css') ?>">

</head>

<body>
  <div class="wrap">
    <div class="card">
      <div class="card-head">
        <div class="icon"><i class="mdi mdi-account-key-outline auth-icon"></i></div>
        <div>
          <h1>Forgot Password</h1>
          <p>Well email you a secure reset link</p>
        </div>
      </div>

      <div class="card-body">
        <?php if ($this->session->flashdata('success')): ?>
          <div class="alert alert--ok"><?= htmlspecialchars($this->session->flashdata('success'), ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert--err"><?= htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('info')): ?>
          <div class="alert alert--info"><?= htmlspecialchars($this->session->flashdata('info'), ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if (validation_errors()): ?>
          <div class="alert alert--err"><?= validation_errors(); ?></div>
        <?php endif; ?>

        <?= form_open('auth/forgot'); ?>
        <?php if (isset($this->security)): ?>
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <?php endif; ?>

        <div class="auth-block">
          <label>Email</label>
          <input class="input" type="email" name="email" value="<?= set_value('email'); ?>" required>
          <?= form_error('email', '<div class="alert alert--err auth-error">', '</div>'); ?>
        </div>

        <button class="btn" type="submit">
          <i class="mdi mdi-send"></i> Send Reset Link
        </button>
        <?= form_close(); ?>

        <div class="text-center">
          <a class="back" href="<?= site_url('auth/login'); ?>">Back to login</a>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
