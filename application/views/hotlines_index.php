<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Hotlines', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-shell.css?v=1.0.1') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
</head>

<body>
  <?php $this->load->view('partials_translate_banner'); ?>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="wd">

            <div class="wd-pagehead">
              <div class="wd-pagehead-main">
                <h1><i class="mdi mdi-lifebuoy"></i> <?= htmlspecialchars($page_title ?? 'Hotlines', ENT_QUOTES) ?></h1>
                <div class="sub">Emergency &amp; assistance contacts for <strong><?= htmlspecialchars($audience, ENT_QUOTES) ?></strong> and everyone.</div>
              </div>
            </div>

            <?php if (empty($rows)): ?>
              <div class="wd-card">
                <div class="wd-empty"><i class="mdi mdi-phone-off-outline"></i>
                  <div>No hotlines available right now.</div>
                </div>
              </div>
            <?php else: ?>
              <div class="wd-grid">
                <?php foreach ($rows as $r): ?>
                  <div class="wd-itemcard">
                    <div class="wd-itemcard-body">
                      <div class="d-flex justify-content-between align-items-start gap-2">
                        <div style="min-width:0">
                          <div class="wd-person-name" style="font-size:15px"><?= htmlspecialchars($r->title, ENT_QUOTES) ?></div>
                          <?php if (!empty($r->agency)): ?>
                            <div class="wd-person-sub"><i class="mdi mdi-office-building-outline"></i> <?= htmlspecialchars($r->agency, ENT_QUOTES) ?></div>
                          <?php endif; ?>
                        </div>
                        <span class="wd-code"><?= htmlspecialchars($r->phone, ENT_QUOTES) ?></span>
                      </div>
                      <?php if (!empty($r->notes)): ?>
                        <div class="wd-muted" style="margin-top:10px;line-height:1.5"><?= nl2br(htmlspecialchars($r->notes, ENT_QUOTES)) ?></div>
                      <?php endif; ?>
                    </div>
                    <div class="wd-itemcard-foot">
                      <a class="wd-pill" href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $r->phone), ENT_QUOTES) ?>"><i class="mdi mdi-phone"></i> Call now</a>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>
