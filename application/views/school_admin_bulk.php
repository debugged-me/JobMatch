<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php $page_title = $page_title ?? 'Bulk Upload Workers'; ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.1.1') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root{--ink:#0f172a;--muted:#6b7280;--line:#e5e7eb;--primary:#2563eb;--hover:rgba(2,6,23,.03)}
    body{font-family:"Karla",system-ui,-apple-system,"Segoe UI",Roboto,Arial;background:#f8fafc;color:var(--ink)}
    .admin-header{position:sticky;top:0;z-index:40;background:#fff;border-bottom:1px solid var(--line)}
    .card{background:#fff;border:1px solid var(--line);border-radius:18px;box-shadow:0 1px 2px rgba(0,0,0,.04)}
    .btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:12px;padding:.6rem 1rem;font-weight:700;border:1px solid transparent;transition:all .15s ease}
    .btn-primary{background:var(--primary);border-color:var(--primary);color:#fff}
    .btn-ghost{background:#fff;border:1px solid var(--line);color:#111827}
    .table thead th{font-weight:700;color:#374151;border-bottom:1px solid var(--line)!important}
    .table-responsive{overflow-x:auto}
    .muted{color:#64748b}
  </style>
</head>
<body>
<div class="container-scroller">
  <?php $this->load->view('includes_nav'); ?>
  <div class="container-fluid page-body-wrapper">
    <?php $this->load->view('includes_nav_top'); ?>
    <div class="main-panel">
      <div class="content-wrapper pb-0">
        <div class="px-4 md:px-8 max-w-6xl mx-auto">
          <div class="admin-header">
            <div class="py-4 flex items-center justify-between">
              <h1 class="text-2xl md:text-3xl font-bold"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
              <a class="btn btn-ghost" href="<?= site_url('school-admin/workers') ?>"><i class="mdi mdi-arrow-left"></i> Back</a>
            </div>
          </div>

          <div class="mt-4">
            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert"><?= $this->session->flashdata('success'); ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('danger')): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= $this->session->flashdata('danger'); ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
            <?php endif; ?>
          </div>

          <?php $hasPreview = !empty($preview); ?>

          <?php if (!$hasPreview): ?>
            <div class="card p-6 mt-4">
              <p class="mb-2 muted">CSV headers:</p>
              <pre class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm">first_name,last_name,email,phone,role,is_active,status,visibility</pre>
              <ul class="mt-2 text-sm muted">
                <li>email required and unique</li>
                <li>role must be worker or client</li>
                <li>is_active: 1 or 0 (default 1)</li>
                <li>status: active/inactive (default active)</li>
                <li>visibility: private/public (default private)</li>
              </ul>

              <form method="post" enctype="multipart/form-data" action="<?= base_url('school-admin/bulk_preview'); ?>" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <div class="md:col-span-2">
                  <label class="text-sm font-semibold mb-1 block">CSV File</label>
                  <input type="file" name="csv" class="form-control" accept=".csv" required>
                </div>
                <div>
                  <button class="btn btn-primary w-full"><i class="mdi mdi-eye-outline"></i> Preview</button>
                </div>
              </form>
            </div>
          <?php else: ?>
            <div class="card p-6 mt-4">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-lg font-semibold">Preview</h3>
                  <p class="text-xs muted"><?= count($preview) ?> rows ready. Duplicate or invalid roles will be skipped.</p>
                </div>
                <form method="post" action="<?= base_url('school-admin/bulk_commit'); ?>">
                  <input type="hidden" name="rows" value='<?= json_encode($preview) ?>'>
                  <button class="btn btn-primary"><i class="mdi mdi-upload"></i> Import &amp; Send Emails</button>
                  <a class="btn btn-ghost" href="<?= site_url('school-admin/bulk') ?>">Cancel</a>
                </form>
              </div>

              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr><th>#</th><th>Email</th><th>Name</th><th>Phone</th><th>Role</th><th>Active</th><th>Status</th><th>Visibility</th></tr>
                  </thead>
                  <tbody>
                    <?php $i=1; foreach ($preview as $r):
                      $roleOk = in_array((string)($r['role'] ?? ''), ['worker','client'], true);
                      if (!$roleOk) { continue; }
                      $fn = (string)($r['first_name'] ?? '');
                      $ln = (string)($r['last_name'] ?? '');
                      $name = ($fn !== '' && $ln !== '') ? ($ln.', '.$fn) : ($ln !== '' ? $ln : ($fn !== '' ? $fn : ''));
                    ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= htmlspecialchars((string)$r['email']) ?></td>
                      <td><?= htmlspecialchars($name) ?></td>
                      <td><?= htmlspecialchars((string)($r['phone'] ?? '')) ?></td>
                      <td><?= htmlspecialchars((string)$r['role']) ?></td>
                      <td><?= (int)($r['is_active'] ?? 1) === 1 ? 'Yes' : 'No' ?></td>
                      <td><?= htmlspecialchars((string)($r['status'] ?? '')) ?></td>
                      <td><?= htmlspecialchars((string)($r['visibility'] ?? '')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>

          <div class="my-8" style="height:1px;background:var(--line)"></div>
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


