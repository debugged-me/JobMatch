<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php
    $page_title  = $page_title ?? 'Import Result';
    $route_base  = $route_base ?? 'admin';
    $workersBase = $route_base . '/workers';
  ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <style>
    :root{
      --blue:#2563eb; --blue-2:#2563eb;
      --gold:#f59e0b; --gold-2:#d97706;
      --silver:#c0c4cc; --line:#e5e7eb;
      --ink:#0f172a; --muted:#6b7280; --bg:#f6f7fb; --card:#fff;
      --shadow:0 10px 30px rgba(2,6,23,.10), 0 2px 8px rgba(2,6,23,.06);
    }
    body{background:var(--bg);color:var(--ink);font-family:"Karla",system-ui,-apple-system,"Segoe UI",Roboto,Arial}
    .app{max-width:960px;margin:0 auto;padding:0 16px}

    /* HERO */
    .hero{
      position:relative;border-radius:16px;color:#fff;padding:18px;
      background:linear-gradient(135deg,var(--blue) 0%,var(--blue-2) 60%);
      box-shadow:var(--shadow);display:flex;align-items:center;gap:12px;overflow:hidden;
    }
    .hero:after{
      content:""; position:absolute; right:-60px; bottom:-60px; width:220px; height:220px; border-radius:50%;
      background:radial-gradient(circle at center, rgba(245,158,11,.45), rgba(245,158,11,0) 60%); filter:blur(6px);
    }
    .hero .ico{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;
      background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.2)}
    .hero h4{margin:0;font-weight:700}

    /* CARDS */
    .card{background:var(--card);border-radius:16px;box-shadow:var(--shadow);padding:18px;border:1px solid rgba(192,196,204,.55)}
    .accent:before{content:"";display:block;height:4px;margin:-18px -18px 14px -18px;border-top-left-radius:16px;border-top-right-radius:16px;
      background:linear-gradient(90deg,var(--gold),var(--blue))}

    /* KPIs */
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}
    .kpi{display:flex;align-items:center;gap:.7rem;border:1px solid var(--line);border-radius:14px;padding:.8rem .95rem;background:#fff}
    .kpi .label{color:var(--muted);font-size:.82rem}
    .kpi .val{font-weight:800;font-size:1.1rem}
    .kpi i{font-size:1.15rem}

    /* BUTTONS */
    .btn{display:inline-flex;align-items:center;gap:.55rem;padding:.66rem 1.05rem;border-radius:12px;font-weight:700}
    .btn-blue{background:var(--blue);border:1px solid var(--blue);color:#fff}
    .btn-blue:hover{background:#162f73;border-color:#162f73}
    .btn-silver{background:#fff;border:1px solid var(--silver);color:#111827}
    .btn-silver:hover{background:#f8fafc}

    /* ALERT */
    .alert{border-radius:12px}
    .notes h6{font-weight:700;margin:0 0 6px}
    .notes ul{margin:0;padding-left:1.1rem}
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

            <!-- HERO -->
            <div class="hero mb-2">
              <div class="ico"><i class="mdi mdi-check-decagram-outline text-white"></i></div>
              <div><h4><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h4></div>
              <div class="ms-auto"></div>
            </div>

            <?php if (!empty($result['ok'])): ?>
              <!-- KPIs -->
              <div class="card accent mb-3">
                <div class="grid">
                  <div class="kpi"><i class="mdi mdi-account-plus-outline text-success"></i>
                    <div><div class="label">New Profiles</div><div class="val"><?= (int)($result['inserted'] ?? 0) ?></div></div>
                  </div>
                  <div class="kpi"><i class="mdi mdi-account-edit-outline text-primary"></i>
                    <div><div class="label">Updated Profiles</div><div class="val"><?= (int)($result['updated'] ?? 0) ?></div></div>
                  </div>
                  <div class="kpi"><i class="mdi mdi-account-multiple-plus-outline text-info"></i>
                    <div><div class="label">New Accounts</div><div class="val"><?= (int)($result['created_users'] ?? 0) ?></div></div>
                  </div>
                  <div class="kpi"><i class="mdi mdi-tag-multiple-outline text-warning"></i>
                    <div><div class="label">Skills Linked</div><div class="val"><?= (int)($result['skill_links'] ?? 0) ?></div></div>
                  </div>
                  <?php if (isset($result['skipped_no_email'])): ?>
                  <div class="kpi"><i class="mdi mdi-alert-circle-outline text-danger"></i>
                    <div><div class="label">Skipped (No Email)</div><div class="val"><?= (int)$result['skipped_no_email'] ?></div></div>
                  </div>
                  <?php endif; ?>
                </div>
              </div>

              <!-- NOTES -->
              <?php if (!empty($result['notes'])): ?>
                <div class="card notes mb-3">
                  <h6>Notes</h6>
                  <ul>
                    <?php foreach ($result['notes'] as $n): ?>
                      <li><?= htmlspecialchars($n, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <div class="d-flex justify-content-end">
                <a class="btn btn-blue" href="<?= site_url($workersBase . '/upload') ?>">
                  <i class="mdi mdi-arrow-left"></i> Back to Upload
                </a>
              </div>

            <?php else: ?>
              <div class="card mb-3">
                <div class="alert alert-danger mb-0">
                  Import failed: <?= htmlspecialchars($result['message'] ?? 'Unknown error', ENT_QUOTES, 'UTF-8') ?>
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <a class="btn btn-silver" href="<?= site_url($workersBase . '/upload') ?>">Back</a>
              </div>
            <?php endif; ?>

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


