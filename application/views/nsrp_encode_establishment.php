<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view('partials/head', [
    'page_title' => 'Register Walk-in Establishment',
  ]); ?>
</head>
<body>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row justify-content-center">
            <div class="col-lg-6">
              <?php if ($msg = $this->session->flashdata('danger')): ?>
                <div class="alert alert-danger py-2"><?= $msg ?></div>
              <?php endif; ?>
              <div class="card">
                <div class="card-body">
                  <h4 class="fw-bold mb-1">Register Walk-in Establishment</h4>
                  <p class="text-muted" style="font-size:.85rem">Create an employer record, then continue to NSRP Form 2.</p>
                  <?= form_open('nsrp/encode_establishment') ?>
                    <div class="mb-3"><label class="form-label">Email *</label><input name="email" type="email" required class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Business Name *</label><input name="business_name" required class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Contact Person</label><input name="contact_person" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Mobile</label><input name="phone" class="form-control"></div>
                    <div class="d-flex justify-content-end gap-2">
                      <a href="<?= site_url('peso') ?>" class="btn btn-outline-secondary">Cancel</a>
                      <button class="btn btn-danger">Create &amp; Continue</button>
                    </div>
                  <?= form_close() ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
</body>
</html>
