<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php $page_title = $page_title ?? 'Manage Workers'; ?>
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
    :root{
      --ink:#0f172a;--muted:#6b7280;--line:#e5e7eb;--primary:#2563eb;--danger:#dc2626;--hover:rgba(2,6,23,.03);
      --card:#fff;
    }
    body{font-family:"Karla",system-ui,-apple-system,"Segoe UI",Roboto,Arial;background:#f8fafc;color:var(--ink)}
    .admin-header{position:sticky;top:0;z-index:40;background:#fff;border-bottom:1px solid var(--line)}
    .card{background:var(--card);border:1px solid var(--line);border-radius:16px;box-shadow:0 1px 2px rgba(2,6,23,.04)}
    .btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:12px;padding:.55rem .95rem;font-weight:700;border:1px solid transparent;transition:all .15s ease}
    .btn-primary{background:var(--primary);border-color:var(--primary);color:#fff}
    .btn-primary:hover{filter:brightness(.95)}
    .btn-ghost{background:#fff;border:1px solid var(--line);color:#111827}
    .btn-ghost:hover{background:var(--hover)}
    .form-control{border-radius:12px}
    .muted{color:var(--muted)}
    .table{width:100%;border-collapse:separate;border-spacing:0}
    .table thead th{font-weight:700;color:#374151;border-bottom:1px solid var(--line)!important;padding:.9rem .9rem}
    .table tbody td{padding:.85rem .9rem;border-top:1px solid #f1f5f9;vertical-align:top}
    .table tbody tr:hover{background:var(--hover)}
    .table th,.table td{white-space:normal;word-break:break-word}
    .actions{display:flex;gap:.45rem;justify-content:flex-end;flex-wrap:wrap}
    .i{--s:38px;width:var(--s);height:var(--s);display:inline-flex;align-items:center;justify-content:center;border-radius:12px;border:1px solid var(--line);background:#fff;color:#111827;position:relative;text-decoration:none;cursor:pointer;transition:background .15s ease,box-shadow .15s ease}
    .i:hover{background:var(--hover);box-shadow:0 2px 6px rgba(2,6,23,.06)}
    .i.-p{background:#eef2ff;border-color:#c7d2fe;color:#1e1b4b}
    .i.-d{background:#fee2e2;border-color:#fecaca;color:#7f1d1d}
    .i[data-tip]::after{content:attr(data-tip);position:absolute;bottom:calc(100% + 8px);left:50%;transform:translateX(-50%) translateY(4px);padding:.28rem .55rem;font-size:.75rem;font-weight:700;white-space:nowrap;background:#0f172a;color:#fff;border-radius:8px;opacity:0;pointer-events:none;box-shadow:0 6px 18px rgba(2,6,23,.18);transition:opacity .12s ease,transform .12s ease}
    .i[data-tip]:hover::after{opacity:1;transform:translateX(-50%) translateY(0)}
    .i[data-tip]::before{content:"";position:absolute;bottom:100%;left:50%;transform:translateX(-50%);border:6px solid transparent;border-top-color:#0f172a;opacity:0;transition:opacity .12s ease}
    .i[data-tip]:hover::before{opacity:1}
    .sm-hide{display:table-cell}
    @media (max-width: 768px){
      .sm-hide{display:none}
      .actions{justify-content:flex-start}
      .table thead th{font-size:.88rem}
      .table tbody td{font-size:.94rem}
      .btn{padding:.5rem .8rem}
    }
    .filters .label{font-size:.85rem;font-weight:600;margin-bottom:.35rem;color:#111827}
    .filters .row{display:grid;grid-template-columns:1fr;gap:.75rem}
    @media(min-width:768px){.filters .row{grid-template-columns:180px 180px 1fr 120px}}
  </style>
</head>
<body>
<div class="container-scroller">
  <?php $this->load->view('includes_nav'); ?>
  <div class="container-fluid page-body-wrapper">
    <?php $this->load->view('includes_nav_top'); ?>
    <div class="main-panel">
      <div class="content-wrapper pb-0">
        <div class="px-4 md:px-8 max-w-7xl mx-auto">
          <div class="admin-header">
            <div class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
              <div>
                <h1 class="text-2xl md:text-3xl font-bold"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="text-sm muted mt-1">Only roles <b>worker</b> and <b>client</b> are shown.</p>
              </div>
              <div class="flex items-center gap-2">
                <a class="btn btn-primary" href="<?= site_url('school-admin/create') ?>"><i class="mdi mdi-account-plus-outline"></i> Create</a>
                <a class="btn btn-ghost" href="<?= site_url('school-admin/bulk') ?>"><i class="mdi mdi-upload"></i> Bulk Upload</a>
              </div>
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


          <section class="card p-0 mt-4">
            <div class="table-responsive" style="padding: .25rem 0 .5rem 0; overflow-x: visible;">
              <table class="table">
                <thead>
                  <tr>
                    <th class="sm-hide" style="width:64px">#</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th class="sm-hide">Phone</th>
                    <th style="width:120px">Role</th>
                    <th class="sm-hide" style="width:90px">Active</th>
                    <th class="sm-hide" style="width:110px">Status</th>
                    <th class="text-right" style="width:160px">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($rows)): $i=1; foreach ($rows as $u):
                    $r = (string)($u->role ?? '');
                    if (!in_array($r, ['worker','client'], true)) { continue; }
                    $fn = (string)($u->first_name ?? ''); $ln = (string)($u->last_name ?? '');
                    $name = ($fn !== '' && $ln !== '') ? ($ln.', '.$fn) : ($ln !== '' ? $ln : ($fn !== '' ? $fn : ''));
                    $uid = (int)($u->id ?? 0);
                    $email = (string)($u->email ?? '');
                    $phone = (string)($u->phone ?? '');
                    $status = (string)($u->status ?? '');
                    $activeText = ((int)($u->is_active ?? 0) === 1) ? 'Yes' : 'No';
                  ?>
                  <tr>
                    <td class="sm-hide"><?= $i++; ?></td>
                    <td><?= htmlspecialchars($email) ?></td>
                    <td><?= htmlspecialchars($name) ?></td>
                    <td class="sm-hide"><?= htmlspecialchars($phone) ?></td>
                    <td><?= htmlspecialchars($r) ?></td>
                    <td class="sm-hide"><?= htmlspecialchars($activeText) ?></td>
                    <td class="sm-hide"><?= htmlspecialchars($status) ?></td>
                    <td class="text-right">
                      <div class="actions">
                        <a href="<?= site_url('school-admin/edit/'.$uid) ?>" class="i -p" data-tip="Edit" aria-label="Edit" title="Edit"><i class="mdi mdi-pencil"></i></a>
                        <a href="mailto:<?= htmlspecialchars($email) ?>" class="i" data-tip="Email" aria-label="Email" title="Email"><i class="mdi mdi-email-outline"></i></a>
                        <a href="<?= site_url('school-admin/delete/'.$uid) ?>" onclick="return confirm('Delete this user?');" class="i -d" data-tip="Delete" aria-label="Delete" title="Delete"><i class="mdi mdi-delete-outline"></i></a>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; else: ?>
                    <tr><td colspan="8" class="text-center muted" style="padding:1.25rem">No users found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </section>

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


