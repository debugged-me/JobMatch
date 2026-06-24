<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$page_title = 'Skills';
$openAdd = ($this->input->get('add') === '1');
$skills = $skills ?? [];
$count = is_array($skills) ? count($skills) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'JobMatch — Admin', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">

  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.9') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --blue-700: #d63031;
      --blue-900: #c1272d;
      --silver-200: #e5e7eb;
      --silver-100: #f9fafb;
    }

    body {
      font-family: "Karla", ui-sans-serif;
      background: #f9fafb;
    }

    .app {
      max-width: 900px;
      margin: 0 auto;
      padding: 0 12px
    }

    .panel {
      background: #fff;
      border: 1px solid var(--silver-200);
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(2, 6, 23, .08);
      padding: 16px
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .3rem .6rem;
      border-radius: 9999px;
      border: 1px solid var(--silver-200);
      background: #fff;
      font-weight: 700;
      font-size: 12px;
      color: #334155
    }

    /* List style */
    .skill-list {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .skill-list li {
      padding: 10px 12px;
      border-bottom: 1px solid var(--silver-200);
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-weight: 600;
      color: #1e3a8a;
      transition: background .15s ease;
    }

    .skill-list li:last-child {
      border-bottom: none;
    }

    .skill-list li:hover {
      background: var(--silver-100);
    }

    .skill-actions {
      display: flex;
      gap: 8px;
    }

    .btn-danger-soft {
      background: #fff5f5;
      color: #dc2626;
      border: 1px solid #fecaca;
      border-radius: 8px;
      font-size: .85rem;
      padding: .25rem .6rem
    }

    .btn-danger-soft:hover {
      background: #fee2e2
    }

    /* Search box */
    .toolbar .input-group {
      border-radius: 12px;
      overflow: hidden;
      max-width: 360px;
    }

    .toolbar .input-group .input-group-text {
      background: #fff;
      border: 1px solid var(--silver-200);
      border-right: 0
    }

    .toolbar .input-group .form-control {
      border: 1px solid var(--silver-200);
      border-left: 0
    }

    .btn-primary {
      background: var(--blue-700);
      border-color: var(--blue-700);
      border-radius: 10px;
      font-weight: 700
    }

    .btn-primary:hover {
      filter: brightness(.95)
    }

    /* Modal polish */
    .modal .form-control {
      border: 2px solid var(--blue-700) !important;
      border-radius: 10px;
      box-shadow: none !important;
    }

    .modal .form-control:focus {
      border-color: #2980b9 !important;
      box-shadow: 0 0 0 .25rem rgba(41, 128, 185, .30) !important;
      outline: 0;
    }
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

            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <h5 class="mb-1" style="font-weight:800;color:var(--blue-900)">
                  <i class="mdi mdi-hammer-wrench mr-1" style="color:var(--blue-700)"></i>
                  <?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?>
                </h5>
              </div>
              <span class="chip"><i class="mdi mdi-format-list-bulleted"></i> <?= (int)$count ?> total</span>
            </div>

            <!-- Flash messages -->
            <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger" role="alert"><i class="mdi mdi-alert-circle-outline mr-1"></i><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success" role="alert"><i class="mdi mdi-check-circle-outline mr-1"></i><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>

            <!-- Panel -->
            <section class="panel">
              <div class="d-flex align-items-center justify-content-between mb-3 toolbar">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                  </div>
                  <input id="skillSearch" type="search" class="form-control" placeholder="Search skills (press / to focus)" aria-label="Search skills">
                </div>
                <button id="btnAddSkill" type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addSkillModal">
                  <i class="mdi mdi-plus"></i> Add Skill
                </button>
              </div>

              <?php if (!empty($skills)): ?>
                <ul id="skillsList" class="skill-list">
                  <?php foreach ($skills as $s):
                    $id   = isset($s->skillID) ? (int)$s->skillID : 0;
                    $t    = trim($s->Title ?? '');
                  ?>
                    <li data-title="<?= htmlspecialchars(mb_strtolower($t, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>">
                      <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
                      <div class="skill-actions">
                        <form method="post"
                          action="<?= site_url('admin/deleteSkill/' . $id) ?>"
                          onsubmit="return confirm('Delete this skill?');"
                          class="m-0 p-0">
                          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                            value="<?= $this->security->get_csrf_hash(); ?>">
                          <button type="submit" class="btn-danger-soft" title="Delete">
                            <i class="mdi mdi-delete-outline"></i>
                          </button>
                        </form>

                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <div class="text-muted"><i class="mdi mdi-information-outline"></i> No skills yet. Click <b>Add Skill</b> to create your first one.</div>
              <?php endif; ?>
            </section>
          </div>
        </div>

        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <!-- Add Skill Modal -->
  <div class="modal" id="addSkillModal" tabindex="-1" role="dialog" aria-labelledby="addSkillLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="post" action="<?= site_url('admin/saveSkill') ?>" autocomplete="off">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

          <div class="modal-header">
            <h5 class="modal-title" id="addSkillLabel"><i class="mdi mdi-plus mr-1"></i> Add Skill</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>

          <div class="modal-body">
            <div class="form-group mb-2">
              <label>Title <span class="text-danger">*</span></label>
              <input type="text" name="Title" class="form-control" required>
            </div>
            <div class="form-group mb-0">
              <label>Description</label>
              <textarea name="Description" rows="3" class="form-control" placeholder="Description of the skill."></textarea>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-primary" type="submit"><i class="mdi mdi-content-save-outline mr-1"></i> Save</button>
            <button class="btn btn-light" type="button" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Vendor JS -->
  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>

  <script>
    (function() {
      // Focus search with "/"
      document.addEventListener('keydown', function(e) {
        if (e.key === '/' && !/input|textarea|select/i.test((e.target || {}).tagName || '')) {
          e.preventDefault();
          var s = document.getElementById('skillSearch');
          if (s) s.focus();
        }
      });

      // Filter list by title (keeps your search)
      function filterList(q) {
        var list = document.getElementById('skillsList');
        if (!list) return;
        var term = (q || '').trim().toLowerCase();
        var rows = list.querySelectorAll('li');
        rows.forEach(function(li) {
          var t = li.getAttribute('data-title') || '';
          li.style.display = (!term || t.indexOf(term) > -1) ? '' : 'none';
        });
      }
      document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'skillSearch') {
          filterList(e.target.value);
        }
      });

      // Robust modal open (fixes "can't add" when overlays conflict)
      function moveModalToBody(modal) {
        if (modal && modal.parentNode !== document.body) document.body.appendChild(modal);
      }

      function removeStaleOverlays() {
        if (!window.jQuery) return;
        jQuery('.modal-backdrop, .overlay, .sidebar-overlay, .fullscreen-overlay').remove();
        jQuery('body').removeClass('modal-open').css('padding-right', '');
      }

      function hardOpen() {
        var bd = document.querySelector('.modal-backdrop');
        if (!bd) {
          bd = document.createElement('div');
          bd.className = 'modal-backdrop show';
          document.body.appendChild(bd);
        }
        document.body.classList.add('modal-open');
        var m = document.getElementById('addSkillModal');
        m.setAttribute('aria-hidden', 'false');
        m.style.display = 'block';
        m.classList.add('show');
        var inp = m.querySelector('input, textarea, button');
        if (inp) setTimeout(function() {
          try {
            inp.focus();
          } catch (e) {}
        }, 0);
      }

      function hardClose() {
        var m = document.getElementById('addSkillModal');
        m.classList.remove('show');
        m.style.display = 'none';
        m.setAttribute('aria-hidden', 'true');
        var bd = document.querySelector('.modal-backdrop');
        if (bd) bd.parentNode.removeChild(bd);
        document.body.classList.remove('modal-open');
      }

      document.addEventListener('DOMContentLoaded', function() {
        // ensure single modal instance
        var dups = document.querySelectorAll('#addSkillModal');
        if (dups.length > 1) {
          for (var i = 0; i < dups.length - 1; i++) {
            dups[i].remove();
          }
        }
        var modal = document.getElementById('addSkillModal');
        moveModalToBody(modal);

        var canBS = !!(window.jQuery && jQuery.fn && jQuery.fn.modal);
        if (canBS) {
          jQuery(modal).on('hidden.bs.modal', function() {
            removeStaleOverlays();
          });
        }

        // button open
        var btn = document.getElementById('btnAddSkill');
        if (btn) {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            removeStaleOverlays();
            if (canBS) {
              jQuery('#addSkillModal').modal({
                backdrop: true,
                keyboard: true,
                show: true
              });
              setTimeout(function() {
                if (!modal.classList.contains('show') || modal.style.display === 'none') {
                  hardOpen();
                }
              }, 50);
            } else {
              hardOpen();
            }
          });
        }

        // close buttons fallback
        document.addEventListener('click', function(ev) {
          var t = ev.target;
          if (t.matches('[data-dismiss="modal"], .modal .close, .modal [type="button"].btn-light')) {
            ev.preventDefault();
            if (canBS) {
              jQuery('#addSkillModal').modal('hide');
            }
            hardClose();
          }
        });

        // auto-open via ?add=1
        <?php if ($openAdd): ?>
          if (canBS) {
            jQuery('#addSkillModal').modal('show');
          }
          setTimeout(function() {
            hardOpen();
          }, 60);
        <?php endif; ?>
      });
    })();
  </script>
</body>

</html>