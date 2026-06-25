<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Privacy / Visibility', ENT_QUOTES, 'UTF-8') ?> - JobMatch</title>

  <!-- match global assets from your design -->
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --blue-900: #1e3a8a;
      --blue-700: #1d4ed8;
      --blue-600: #2563eb;
      --blue-500: #2563eb;
      --gold-700: #c89113;
      --gold-600: #f0b429;
      --silver-600: #a7afba;
      --silver-500: #c0c6d0;
      --silver-300: #d9dee7;
      --silver-200: #e7ebf2;
      --silver-100: #f6f8fc;
      --radius: 12px;
      --pad-panel: 12px;
      --fs-title: 20px;
      --fs-sub: 12.5px;
      --fs-body: 13px;
      --shadow-1: 0 6px 16px rgba(2, 6, 23, .08);
    }

    html,
    body {
      height: 100%;
    }

    body {
      font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      font-size: var(--fs-body);
      background: linear-gradient(180deg, var(--silver-100), #eef2f7 60%, #e9edf3 100%);
      color: #0f172a;
    }

    .content-wrapper {
      padding-top: .6rem
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
      border-radius: var(--radius);
      box-shadow: var(--shadow-1);
      padding: var(--pad-panel)
    }

    .panel-head {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px
    }

    .panel-head i {
      font-size: 18px;
      color: var(--silver-600)
    }

    .panel-head h6 {
      margin: 0;
      font-size: 13px;
      font-weight: 800;
      color: var(--blue-900)
    }

    .muted {
      color: #64748b
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px
    }

    @media (max-width:992px) {
      .grid-2 {
        grid-template-columns: 1fr
      }
    }

    .cta {
      border: 1px solid var(--silver-300);
      border-radius: 12px;
      padding: 12px;
      background: linear-gradient(180deg, #fff, #fbfcff)
    }

    .cta h5 {
      margin: 0 0 4px;
      font-weight: 800;
      color: var(--blue-900);
      font-size: 14px
    }

    .cta .muted {
      font-size: 12.5px
    }

    .pill {
      display: inline-flex;
      gap: .5rem;
      align-items: center;
      padding: .35rem .7rem;
      border-radius: 9999px;
      border: 1px solid var(--silver-300);
      background: #fff;
      font-weight: 700;
      font-size: 12px
    }

    .pill--warn {
      background: #fffbeb;
      border-color: #fde68a;
      color: #b45309
    }

    .divider {
      height: 1px;
      background: var(--silver-200);
      margin: 10px 0
    }

    .btn-brand {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .45rem .8rem;
      border-radius: 10px;
      border: 1px solid var(--blue-600);
      background: #f5f8ff;
      font-weight: 700;
      color: #1e3a8a;
      text-decoration: none;
      transition: all .25s ease;
    }

    .btn-brand:hover {
      background: var(--gold-600);
      border-color: var(--gold-700);
      color: #111;
      transform: translateY(-1px);
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

            <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Privacy / Visibility', ENT_QUOTES, 'UTF-8') ?></div>

            <!-- Header -->
            <section class="panel" style="margin-bottom:12px">
              <div class="panel-head">
                <i class="mdi mdi-eye-settings-outline"></i>
                <h6>Control how others see your profile</h6>
              </div>
              <div class="muted">
                Switch to <b>Private</b> to hide your profile from search, listings, and suggestions — without disabling your login.
                Existing chats and jobs remain usable.
              </div>

              <?php $vis = strtolower((string)($me->visibility ?? 'public')); ?>
              <div class="mt-2">
                <span class="pill">
                  <i class="mdi <?= $vis === 'private' ? 'mdi-eye-off-outline' : 'mdi-eye-outline' ?>"></i>
                  Current: <?= $vis === 'private' ? 'Private' : 'Public' ?>
                </span>
                <?php if ((int)($me->is_active ?? 1) !== 1): ?>
                  <span class="pill pill--warn" style="margin-left:.5rem">
                    <i class="mdi mdi-alert-outline"></i> Inactive
                  </span>
                <?php endif; ?>
              </div>
            </section>

            <!-- Actions -->
            <div class="grid-2">
              <section class="panel">
                <div class="cta">
                  <h5><i class="mdi mdi-eye-outline"></i> Public</h5>
                  <p class="muted mb-2">Your profile appears in search results, listings, and suggestions.</p>
                  <button id="btnPublic" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-check-circle-outline"></i> Set Public
                  </button>
                </div>
                <div class="divider"></div>
                <div class="muted" style="font-size:12px">
                  Tip: staying public increases your chances of being discovered by clients.
                </div>
              </section>

              <section class="panel">
                <div class="cta">
                  <h5><i class="mdi mdi-eye-off-outline"></i> Private</h5>
                  <p class="muted mb-2">
                    Hide your profile from discovery. You can still log in, message existing threads, and work on active jobs.
                  </p>
                  <button id="btnPrivate" class="btn btn-sm btn-outline-secondary">
                    <i class="mdi mdi-eye-off-outline"></i> Set Private
                  </button>
                </div>
                <div class="divider"></div>
                <div class="muted" style="font-size:12px">
                  You can switch back to <b>Public</b> anytime.
                </div>
              </section>
            </div>

            <!-- Help -->
            <section class="panel" style="margin-top:12px">
              <div class="panel-head">
                <i class="mdi mdi-help-circle-outline"></i>
                <h6>How visibility works</h6>
              </div>
              <ul class="muted mb-0" style="padding-left:18px">
                <li><b>Public</b>: visible in search, browse cards, and suggestions.</li>
                <li><b>Private</b>: hidden across discovery; only existing counterparties can see you in current threads/jobs.</li>
                <li>Visibility does not affect your ability to log in (that's controlled by activation).</li>
              </ul>
            </section>

          </div>
          <?php $this->load->view('includes_footer'); ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function() {
      const csrf = {
        name: '<?= $this->security->get_csrf_token_name(); ?>',
        hash: '<?= $this->security->get_csrf_hash(); ?>'
      };
      async function setVis(v) {
        try {
          const fd = new FormData();
          fd.append('visibility', v);
          fd.append(csrf.name, csrf.hash);
          const res = await fetch('<?= site_url('visibility/set') ?>', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
          const j = await res.json();
          alert(j.message || (j.ok ? 'Updated' : 'Failed'));
          if (j.ok) location.reload();
        } catch (e) {
          alert('Failed');
        }
      }
      document.getElementById('btnPublic')?.addEventListener('click', () => setVis('public'));
      document.getElementById('btnPrivate')?.addEventListener('click', () => setVis('private'));
    })();
  </script>

  <!-- keep vendor shell behavior -->
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>