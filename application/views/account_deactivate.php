<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Account Controls', ENT_QUOTES, 'UTF-8') ?> - JobMatch</title>

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

    .btn-brand {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .45rem .8rem;
      border-radius: 10px;
      border: 1px solid var(--blue-600);
      background: #f5f8ff;
      font-weight: 700;
      color: var(--blue-900);
      text-decoration: none;
      transition: all .25s ease;
    }

    .btn-brand:hover {
      background: var(--gold-600);
      border-color: var(--gold-700);
      color: #111;
      transform: translateY(-1px);
    }

    .divider {
      height: 1px;
      background: var(--silver-200);
      margin: 10px 0
    }

    .confirm-row {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap
    }

    .confirm-row code {
      font-weight: 800
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

            <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Account Controls', ENT_QUOTES, 'UTF-8') ?></div>

            <!-- Header panel -->
            <section class="panel" style="margin-bottom:12px">
              <div class="panel-head">
                <i class="mdi mdi-shield-account-outline"></i>
                <h6>Manage account visibility & lifecycle</h6>
              </div>
              <div class="muted">
                Hide your profile from search and disable login. Reactivation can only be done by an administrator.
              </div>

            </section>

            <!-- Actions -->
            <div class="grid-2">
              <section class="panel">
                <div class="cta">
                  <h5><i class="mdi mdi-eye-off-outline"></i> Deactivate (Hide & Disable)</h5>
                  <p class="muted mb-2">
                    Sets your account to <strong>deactivated</strong>: hidden from search and sign-in is disabled.
                    You can re-activate later.
                  </p>
                  <button id="btnDeactivate" class="btn btn-outline-warning btn-sm">
                    <i class="mdi mdi-account-off-outline"></i> Deactivate my account
                  </button>
                </div>

                <div class="divider"></div>

              </section>

              <section class="panel">
                <div class="cta">
                  <h5><i class="mdi mdi-delete-alert-outline"></i> Delete Account</h5>
                  <p class="muted">
                    Marks your account as <strong>deleted</strong>, hides it, and anonymizes personal data.
                    Your messages/transactions remain for integrity. This action signs you out.
                  </p>
                  <div class="confirm-row mb-2">
                    <span>Type <code>DELETE</code> to confirm:</span>
                    <input id="delConfirm" type="text" class="form-control form-control-sm" style="max-width:240px">
                  </div>
                  <button id="btnDelete" class="btn btn-danger btn-sm">
                    <i class="mdi mdi-trash-can-outline"></i> Delete my account
                  </button>
                </div>
              </section>
            </div>

            <!-- Help / guidance -->
            <section class="panel" style="margin-top:12px">
              <div class="panel-head">
                <i class="mdi mdi-help-circle-outline"></i>
                <h6>What happens next?</h6>
              </div>
              <ul class="muted mb-0" style="padding-left:18px">
                <li>Deactivate will immediately sign you out and hide your profile. Reactivation restores access.</li>
                <li>Delete performs a soft delete (keeps job records for integrity; anonymizes personal identifiers).</li>
                <li>If you need a full export before deletion, contact support.</li>
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

      function post(action, extra) {
        const fd = new FormData();
        fd.append('action', action);
        fd.append(csrf.name, csrf.hash);
        if (extra) Object.entries(extra).forEach(([k, v]) => fd.append(k, v));

        return fetch('<?= site_url('deactivate/do_action') ?>', {
          method: 'POST',
          credentials: 'same-origin',
          body: fd
        }).then(r => r.json());
      }

      const $dec = document.getElementById('btnDeactivate');
      const $rea = document.getElementById('btnReactivate');
      const $del = document.getElementById('btnDelete');

      $dec && $dec.addEventListener('click', function() {
        if (!confirm('Deactivate your account now? You will be signed out.')) return;
        post('deactivate').then(res => {
          alert(res.message || 'Done');
          if (res.ok) location.href = '<?= site_url('auth/login') ?>';
        }).catch(() => alert('Failed'));
      });


      $del && $del.addEventListener('click', function() {
        const v = (document.getElementById('delConfirm').value || '').trim();
        if (v !== 'DELETE') {
          alert('Please type DELETE to confirm.');
          return;
        }
        if (!confirm('This will hide and anonymize your account. Continue?')) return;
        post('delete', {
          confirm: 'DELETE'
        }).then(res => {
          alert(res.message || 'Done');
          if (res.ok) location.href = '<?= site_url('auth/login') ?>';
        }).catch(() => alert('Failed'));
      });
    })();
  </script>

  <!-- keep your vendor shell behavior -->
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
</body>

</html>