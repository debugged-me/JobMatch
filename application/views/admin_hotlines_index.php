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
      --brand: #c1272d;
      --brand-700: #9e1b21;
      --shadow-1: 0 6px 16px rgba(2, 6, 23, .08)
    }

    body {
      background: linear-gradient(180deg, #f6f8fc, #eef2f7 60%, #e9edf3 100%);
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

    /* Hero header */
    .page-hero {
      position: relative;
      overflow: hidden;
      background: linear-gradient(135deg, var(--brand) 0%, var(--brand-700) 100%);
      border-radius: 18px;
      padding: 22px 24px;
      color: #fff;
      box-shadow: 0 14px 30px rgba(193, 39, 45, .26);
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 14px;
    }

    .page-hero::after {
      content: "";
      position: absolute;
      right: -40px;
      top: -60px;
      width: 220px;
      height: 220px;
      background: radial-gradient(circle, rgba(255, 255, 255, .16), transparent 70%);
      pointer-events: none;
    }

    .page-hero .hero-left {
      display: flex;
      align-items: center;
      gap: 16px;
      position: relative;
      z-index: 1
    }

    .page-hero .hero-ic {
      width: 54px;
      height: 54px;
      border-radius: 14px;
      background: rgba(255, 255, 255, .16);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      backdrop-filter: blur(2px)
    }

    .page-hero h1 {
      margin: 0;
      font-size: 22px;
      font-weight: 800;
      letter-spacing: -.2px
    }

    .page-hero p {
      margin: 2px 0 0;
      font-size: 13px;
      opacity: .9
    }

    .page-hero .hero-right {
      display: flex;
      align-items: center;
      gap: 10px;
      position: relative;
      z-index: 1
    }

    .hero-stat {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(255, 255, 255, .15);
      border: 1px solid rgba(255, 255, 255, .25);
      color: #fff;
      border-radius: 9999px;
      padding: .5rem .85rem;
      font-weight: 700;
      font-size: 13px
    }

    .btn-hero {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #fff;
      color: var(--brand);
      border: 0;
      border-radius: 10px;
      padding: .55rem .95rem;
      font-weight: 800;
      font-size: 13px;
      text-decoration: none;
      box-shadow: 0 6px 14px rgba(0, 0, 0, .12);
      transition: transform .15s, box-shadow .15s
    }

    .btn-hero:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, .18);
      color: var(--brand-700)
    }

    /* Audience / status pills */
    .pill-aud {
      text-transform: capitalize;
      background: #eef2ff;
      color: #1b5e9f;
      border: 1px solid #c7d2fe
    }

    .pill-on {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: .25rem .55rem;
      border-radius: 9999px;
      font-weight: 700;
      font-size: 12px;
      background: rgba(22, 163, 74, .1);
      color: #166534;
      border: 1px solid rgba(22, 163, 74, .25)
    }

    .pill-off {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: .25rem .55rem;
      border-radius: 9999px;
      font-weight: 700;
      font-size: 12px;
      background: #f1f5f9;
      color: #64748b;
      border: 1px solid #e2e8f0
    }

    .dot {
      width: 7px;
      height: 7px;
      border-radius: 50%
    }

    .iconbtn {
      width: 34px;
      height: 34px;
      border-radius: 9px;
      border: 1px solid var(--silver-300);
      background: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #475569;
      cursor: pointer;
      transition: .15s
    }

    .iconbtn:hover {
      transform: translateY(-1px);
      border-color: #cbd5e1
    }

    .iconbtn.edit:hover {
      color: #2563eb;
      border-color: #bfdbfe;
      background: #eff6ff
    }

    .iconbtn.warn:hover {
      color: #b45309;
      border-color: #fcd34d;
      background: #fffbeb
    }

    .iconbtn.del:hover {
      color: #dc2626;
      border-color: #fecaca;
      background: #fef2f2
    }

    table.table-r thead th {
      text-transform: uppercase;
      letter-spacing: .04em;
      font-size: .72rem;
      color: #64748b;
      background: #fbfdff;
      border-bottom: 1px solid var(--silver-300)
    }

    table.table-r tbody tr:hover {
      background: #f8fafc
    }

    table.table-r tbody td {
      border-bottom: 1px solid #f1f5f9
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

    .chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .3rem .6rem;
      border-radius: 9999px;
      border: 1px solid var(--silver-300);
      background: #fff;
      font-weight: 700;
      font-size: 12px;
      color: #334155
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #64748b
    }

    .empty-state .empty-icon {
      font-size: 48px;
      color: #cbd5e1;
      margin-bottom: 12px
    }

    .empty-state h5 {
      font-weight: 700;
      color: #475569;
      margin-bottom: 4px
    }

    .empty-state p {
      font-size: .9rem;
      margin-bottom: 16px
    }

    .table-r th,
    .table-r td {
      padding: 12px;
      vertical-align: middle
    }

    .pagination-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 12px;
      padding-top: 10px;
      border-top: 1px solid var(--silver-300)
    }

    .pagination-bar .page-info {
      font-size: .85rem;
      color: #64748b;
      font-weight: 600
    }

    .pagination-bar .page-links {
      display: flex;
      gap: 4px
    }

    .pagination-bar .page-links a,
    .pagination-bar .page-links span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      height: 34px;
      border-radius: 8px;
      border: 1px solid var(--silver-300);
      background: #fff;
      font-weight: 700;
      font-size: .85rem;
      color: #334155;
      text-decoration: none;
      padding: 0 8px
    }

    .pagination-bar .page-links a:hover {
      background: #f8fafc;
      border-color: #cbd5e1
    }

    .pagination-bar .page-links .current {
      background: var(--blue-900);
      border-color: var(--blue-900);
      color: #fff
    }

    .breadcrumb-bar {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: .82rem;
      color: #64748b;
      margin-bottom: 8px
    }

    .breadcrumb-bar a {
      color: #64748b;
      text-decoration: none;
      font-weight: 600
    }

    .breadcrumb-bar a:hover {
      color: var(--blue-900)
    }

    .breadcrumb-bar .sep {
      color: #cbd5e1
    }

    .breadcrumb-bar .current {
      color: #334155;
      font-weight: 700
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

            <div class="breadcrumb-bar">
              <a href="<?= site_url('dashboard/admin') ?>"><i class="mdi mdi-home-outline"></i> Dashboard</a>
              <span class="sep">/</span>
              <span class="current">Hotlines</span>
            </div>

            <div class="page-hero">
              <div class="hero-left">
                <div class="hero-ic"><i class="mdi mdi-phone-in-talk-outline"></i></div>
                <div>
                  <h1>Emergency Hotlines</h1>
                  <p>Manage the directory of hotline numbers shown across the app.</p>
                </div>
              </div>
              <div class="hero-right">
                <span class="hero-stat"><i class="mdi mdi-format-list-bulleted"></i> <?= (int)($total ?? count($rows)) ?> total</span>
                <a href="<?= site_url('admin/hotlines/create') ?>" class="btn-hero">
                  <i class="mdi mdi-plus"></i> Add Hotline
                </a>
              </div>
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
                <div class="empty-state">
                  <div class="empty-icon"><i class="mdi mdi-phone-off"></i></div>
                  <h5>No hotlines found</h5>
                  <p>Add your first hotline number to get started.</p>
                  <a href="<?= site_url('admin/hotlines/create') ?>" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-plus"></i> Add Hotline
                  </a>
                </div>
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
                          <td data-label="Phone"><a href="tel:<?= htmlspecialchars($r->phone, ENT_QUOTES) ?>" style="text-decoration:none"><code><?= htmlspecialchars($r->phone, ENT_QUOTES) ?></code></a></td>
                          <td data-label="Audience"><span class="badge-soft pill-aud"><?= htmlspecialchars($r->audience, ENT_QUOTES) ?></span></td>
                          <td data-label="Active">
                            <?php if ($r->is_active): ?>
                              <span class="pill-on"><span class="dot" style="background:#16a34a"></span> Active</span>
                            <?php else: ?>
                              <span class="pill-off"><span class="dot" style="background:#94a3b8"></span> Inactive</span>
                            <?php endif; ?>
                          </td>
                          <td data-label="Sort"><?= (int)$r->sort_order ?></td>
                          <td data-label="Actions">
                            <div class="d-flex gap-1">
                              <a class="iconbtn edit" href="<?= site_url('admin/hotlines/edit/' . $r->id) ?>" title="Edit"><i class="mdi mdi-pencil"></i></a>
                              <form method="post" action="<?= site_url('admin/hotlines/toggle/' . $r->id) ?>" style="display:inline">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                <button type="submit" class="iconbtn warn" title="Toggle active"><i class="mdi mdi-power"></i></button>
                              </form>
                              <form method="post" action="<?= site_url('admin/hotlines/delete/' . $r->id) ?>" style="display:inline" class="hotline-delete-form">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                <button type="submit" class="iconbtn del" title="Delete"><i class="mdi mdi-delete"></i></button>
                              </form>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                  <div class="pagination-bar">
                    <div class="page-info">
                      Showing <?= $pagination['from'] ?>–<?= $pagination['to'] ?> of <?= $pagination['total'] ?> hotlines
                    </div>
                    <div class="page-links">
                      <?php
                        $cur  = (int)$pagination['page'];
                        $last = (int)$pagination['total_pages'];
                        $mk = function ($p) {
                          return site_url('admin/hotlines') . '?page=' . $p;
                        };
                        $pages = [];
                        for ($p = 1; $p <= $last; $p++) {
                          if ($p === 1 || $p === $last || ($p >= $cur - 1 && $p <= $cur + 1)) $pages[] = $p;
                        }
                      ?>
                      <?php if ($cur > 1): ?><a href="<?= $mk($cur - 1) ?>"><i class="mdi mdi-chevron-left"></i></a><?php endif; ?>
                      <?php $prev = 0; foreach ($pages as $p): ?>
                        <?php if ($prev && $p - $prev > 1): ?><span style="border:0;background:transparent;color:#94a3b8">…</span><?php endif; ?>
                        <?php if ($p === $cur): ?>
                          <span class="current"><?= $p ?></span>
                        <?php else: ?>
                          <a href="<?= $mk($p) ?>"><?= $p ?></a>
                        <?php endif; ?>
                        <?php $prev = $p; endforeach; ?>
                      <?php if ($cur < $last): ?><a href="<?= $mk($cur + 1) ?>"><i class="mdi mdi-chevron-right"></i></a><?php endif; ?>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
            </section>

          </div>
        </div>
        <?php $this->load->view('includes_footer'); ?>

      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
  <script>
    document.querySelectorAll('.hotline-delete-form').forEach(function(form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Delete this hotline?',
          text: 'This action cannot be undone.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc2626',
          cancelButtonColor: '#64748b',
          confirmButtonText: 'Yes, delete it'
        }).then(function(result) {
          if (result.isConfirmed) form.submit();
        });
      });
    });
    setTimeout(function() {
      document.querySelectorAll('.alert').forEach(function(el) {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(function() { el.remove(); }, 400);
      });
    }, 4000);
  </script>
</body>

</html>