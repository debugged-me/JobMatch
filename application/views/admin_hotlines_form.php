<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Hotline', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root{ --silver-300:#d9dee7; --blue-900:#1e3a8a; --shadow-1:0 6px 16px rgba(2,6,23,.08) }
    .app{max-width:900px;margin:0 auto;padding:0 12px}
    .eyebrow{font-size:12px;color:#64748b;font-weight:600;letter-spacing:.2px;margin:4px 0 8px}
    .panel{background:#fff;border:1px solid var(--silver-300);border-radius:12px;box-shadow:var(--shadow-1);padding:12px}
    .panel-head{display:flex;align-items:center;gap:8px;margin-bottom:8px}
    .panel-head h6{margin:0;font-size:13px;font-weight:800;color:var(--blue-900)}
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

          <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Hotline', ENT_QUOTES) ?></div>

          <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
          <?php endif; ?>

          <section class="panel">
            <div class="panel-head"><i class="mdi mdi-phone-in-talk-outline" style="color:#a7afba"></i><h6>Details</h6></div>

            <form method="post">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Title <span class="text-danger">*</span></label>
                  <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($form->title ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Agency</label>
                  <input type="text" name="agency" class="form-control" value="<?= htmlspecialchars($form->agency ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone <span class="text-danger">*</span></label>
                  <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($form->phone ?? '', ENT_QUOTES) ?>">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Audience</label>
                  <select name="audience" class="form-select">
                    <?php foreach (['all'=>'All','worker'=>'Worker','client'=>'Client','admin'=>'Admin'] as $k=>$v): ?>
                      <option value="<?= $k ?>" <?= (isset($form->audience) && $form->audience===$k)?'selected':'' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Sort Order</label>
                  <input type="number" name="sort_order" class="form-control" value="<?= (int)($form->sort_order ?? 0) ?>">
                </div>
              <div class="col-md-12">
  <div class="form-check m-0">
    <input
      class="form-check-input ms-0 me-2"  
      type="checkbox"
      name="is_active"
      id="is_active"
      value="1"
      <?= !empty($form->is_active)?'checked':'' ?>>
    <label class="form-check-label mb-0" for="is_active">Active</label>
  </div>
</div>


              <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="mdi mdi-content-save"></i> Save</button>
                <a class="btn btn-light" href="<?= site_url('admin/hotlines') ?>">Cancel</a>
              </div>
            </form>
          </section>

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


