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
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --silver-300: #d9dee7;
      --blue-900: #c1272d;
      --shadow-1: 0 6px 16px rgba(2, 6, 23, .08)
    }

    .app {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 12px
    }

    .eyebrow {
      font-size: 12px;
      color: #64748b;
      font-weight: 600;
      letter-spacing: .2px;
      margin: 4px 0 8px
    }

    .panel {
      background: #fff;
      border: 1px solid var(--silver-300);
      border-radius: 12px;
      box-shadow: var(--shadow-1);
      padding: 12px
    }

    .panel-head {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px
    }

    .panel-head h6 {
      margin: 0;
      font-size: 13px;
      font-weight: 800;
      color: var(--blue-900)
    }

    .badge-soft {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .25rem .5rem;
      border-radius: 9999px;
      border: 1px solid var(--silver-300);
      background: #fff;
      font-weight: 700;
      font-size: 12px
    }

    .table-r th,
    .table-r td {
      padding: 12px;
      vertical-align: middle
    }

    @media (max-width: 768px) {
      .table-responsive {
        overflow-x: visible
      }

      .table-r {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px
      }

      .table-r thead {
        display: none
      }

      .table-r tbody tr {
        display: block;
        padding: 10px;
        border: 1px solid var(--silver-300);
        border-radius: 12px;
        background: #fff;
        box-shadow: var(--shadow-1)
      }

      .table-r tbody tr+tr {
        margin-top: 8px
      }

      .table-r td {
        display: grid;
        grid-template-columns: 110px 1fr;
        gap: 8px;
        padding: 6px 0 !important;
        border: 0 !important;
        font-size: 12.5px
      }

      .table-r td::before {
        content: attr(data-label);
        font-weight: 700;
        color: #334155
      }
    }
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

            <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Hotlines', ENT_QUOTES) ?></div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
              <h4 class="mb-0">Hotline Numbers</h4>
              <a href="<?= site_url('admin/hotlines/create') ?>" class="btn btn-sm btn-primary">
                <i class="mdi mdi-plus"></i> Add
              </a>
            </div>

            <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
            <?php endif; ?>

            <section class="panel">
              <div class="panel-head">
                <i class="mdi mdi-phone-in-talk-outline" style="color:#a7afba;font-size:18px"></i>
                <h6>Directory</h6>
              </div>

              <?php if (empty($rows)): ?>
                <div class="empty">No hotlines yet.</div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-sm table-r">
                    <thead class="bg-light">
                      <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Agency</th>
                        <th>Phone</th>
                        <th>Audience</th>
                        <th>Active</th>
                        <th>Sort</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($rows as $r): ?>
                        <tr>
                          <td data-label="#"> <?= (int)$r->id ?></td>
                          <td data-label="Title" class="fw-bold"><?= htmlspecialchars($r->title, ENT_QUOTES) ?></td>
                          <td data-label="Agency"><?= htmlspecialchars($r->agency ?? '', ENT_QUOTES) ?></td>
                          <td data-label="Phone"><code><?= htmlspecialchars($r->phone, ENT_QUOTES) ?></code></td>
                          <td data-label="Audience"><span class="badge-soft"><?= htmlspecialchars($r->audience, ENT_QUOTES) ?></span></td>
                          <td data-label="Active"><?= $r->is_active ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' ?></td>
                          <td data-label="Sort"><?= (int)$r->sort_order ?></td>
                          <td data-label="Actions" class="d-flex gap-1">
                            <a class="btn btn-xs btn-outline-primary" href="<?= site_url('admin/hotlines/edit/' . $r->id) ?>"><i class="mdi mdi-pencil"></i></a>
                            <a class="btn btn-xs btn-outline-warning" href="<?= site_url('admin/hotlines/toggle/' . $r->id) ?>"><i class="mdi mdi-power"></i></a>
                            <a class="btn btn-xs btn-outline-danger" href="<?= site_url('admin/hotlines/delete/' . $r->id) ?>" onclick="return confirm('Delete this hotline?')"><i class="mdi mdi-delete"></i></a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
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