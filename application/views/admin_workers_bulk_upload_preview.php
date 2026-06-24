<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php
    $page_title  = $page_title ?? 'Preview Skilled Workers';
    $route_base  = $route_base ?? 'admin';
    $workersBase = $route_base . '/workers';
  ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.6') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <style>
    :root{
      --blue:#2563eb; --blue-2:#2563eb; --blue-3:#1e3a8a;
      --gold:#f59e0b; --gold-2:#d97706;
      --silver:#c0c4cc; --line:#e5e7eb;
      --ink:#0f172a; --muted:#6b7280; --bg:#f6f7fb; --card:#fff;
      --shadow:0 10px 30px rgba(2,6,23,.10), 0 2px 8px rgba(2,6,23,.06);
    }
    body{background:var(--bg);font-family:"Karla",system-ui,-apple-system,"Segoe UI",Roboto,Arial;color:var(--ink)}
    .app{max-width:1120px;margin:0 auto;padding:0 16px}

    /* HERO */
    .hero{
      position:relative;border-radius:16px;color:#fff;padding:18px;
      background:linear-gradient(135deg,var(--blue) 0%,var(--blue-2) 60%);
      box-shadow:var(--shadow);display:flex;align-items:center;gap:12px;overflow:hidden;
    }
    .hero:after{
      content:"";position:absolute;right:-60px;bottom:-60px;width:220px;height:220px;border-radius:50%;
      background:radial-gradient(circle at center, rgba(245,158,11,.45), rgba(245,158,11,0) 60%);
      filter:blur(6px);
    }
    .hero .ico{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;
      background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.2)}
    .hero h4{margin:0;font-weight:700}

    /* STATUS PILLS */
    .rowstats{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0 14px}
    .pill{display:inline-flex;align-items:center;gap:.5rem;border:1px solid var(--line);
      border-radius:9999px;padding:.38rem .72rem;background:#fff;font-weight:700;font-size:.85rem}
    .pill i{font-size:1.05rem}
    .pill--accent{border-color:var(--silver); background:linear-gradient(180deg,#fff, #fbfbff)}

    /* CARD / TABLE */
    .card{background:var(--card);border-radius:16px;box-shadow:var(--shadow);padding:16px;border:1px solid rgba(192,196,204,.55)}
    .table thead th{position:sticky;top:0;background:#fff;border-bottom:2px solid var(--line);z-index:1;white-space:nowrap}
    .table tbody tr{transition:background .12s ease}
    .table tbody tr:hover{background:#f8fbff}
    .table tbody tr:nth-child(odd){background:#fafbff}

    /* BUTTONS */
    .btn{display:inline-flex;align-items:center;gap:.55rem;padding:.66rem 1.05rem;border-radius:12px;font-weight:700}
    .btn i{font-size:1.05rem}
    .btn-blue{background:var(--blue);border:1px solid var(--blue);color:#fff}
    .btn-blue:hover{background:#162f73;border-color:#162f73}
    .btn-blue:disabled{opacity:.6;cursor:not-allowed}
    .btn-silver{background:#fff;border:1px solid var(--silver);color:#111827}
    .btn-silver:hover{background:#f8fafc}

    /* ALERT TRIMS */
    .alert{border-radius:12px}
    .alert-danger{border-left:4px solid #ef4444}
    .alert-warning{border-left:4px solid var(--gold)}
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
            <?php
              $valid = (int)($summary['valid_count'] ?? 0);
              $skip  = (int)($summary['skip_count'] ?? 0);
              $dupeC = is_array($dupes ?? null) ? count($dupes) : 0;
            ?>

            <!-- HERO -->
            <div class="hero mb-2">
              <div class="ico"><i class="mdi mdi-clipboard-text-outline text-white"></i></div>
              <div><h4><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h4></div>
              <div class="ms-auto">
                <a class="btn btn-silver btn-sm" href="<?= site_url($workersBase . '/upload') ?>">
                  <i class="mdi mdi-refresh"></i> Start Over
                </a>
              </div>
            </div>

            <!-- VALIDATION MESSAGES -->
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <strong>Issues:</strong>
                <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
              </div>
            <?php endif; ?>

            <?php if (!empty($dupes)): ?>
              <div class="alert alert-warning">
                <strong>Duplicates:</strong>
                <ul class="mb-0"><?php foreach ($dupes as $d): ?><li><?= htmlspecialchars($d) ?></li><?php endforeach; ?></ul>
              </div>
            <?php endif; ?>

            <!-- STATUS -->
            <div class="rowstats">
              <span class="pill pill--accent"><i class="mdi mdi-check-circle-outline text-success"></i> Valid: <?= $valid ?></span>
              <span class="pill pill--accent"><i class="mdi mdi-alert-circle-outline text-danger"></i> Skipped: <?= $skip ?></span>
              <span class="pill pill--accent"><i class="mdi mdi-content-duplicate text-warning"></i> Duplicates: <?= $dupeC ?></span>
            </div>

            <!-- TABLE -->
            <div class="card">
              <?php if (empty($rows)): ?>
                <div class="text-muted">No rows to preview.</div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-sm table-bordered align-middle mb-0">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Headline</th>
                        <th>Years</th>
                        <th>TESDA Cert</th>
                        <th>Expiry</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i=1; foreach ($rows as $r): ?>
                        <tr>
                          <td><?= $i++ ?></td>
                          <td><?= htmlspecialchars($r['email'] ?? '') ?></td>
                          <td><?= htmlspecialchars(trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''))) ?></td>
                          <td><?= htmlspecialchars($r['phone'] ?? '') ?></td>
                          <td><?= htmlspecialchars(trim(($r['province'] ?? '').', '.($r['city'] ?? '').', '.($r['brgy'] ?? ''))) ?></td>
                          <td><?= htmlspecialchars($r['headline'] ?? '') ?></td>
                          <td><?= htmlspecialchars($r['years_experience'] ?? '') ?></td>
                          <td><?= htmlspecialchars($r['tesda_cert_no'] ?? '') ?></td>
                          <td><?= htmlspecialchars($r['tesda_expiry'] ?? '') ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                  <?= form_open($workersBase . '/import'); ?>
                    <input type="hidden" name="filename" value="<?= htmlspecialchars($file, ENT_QUOTES, 'UTF-8') ?>">
                    <button class="btn btn-blue" <?= !empty($can_import) ? '' : 'disabled' ?>>
                      <i class="mdi mdi-database-import-outline"></i> Import & Create Accounts
                    </button>
                  <?= form_close(); ?>
                  <a class="btn btn-silver" href="<?= site_url($workersBase . '/upload') ?>">Cancel</a>
                </div>
              <?php endif; ?>
            </div>

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


