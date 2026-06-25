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
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
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

    .btn-edit-soft {
      background: #eff6ff;
      color: #2563eb;
      border: 1px solid #bfdbfe;
      border-radius: 8px;
      font-size: .85rem;
      padding: .25rem .6rem
    }

    .btn-edit-soft:hover {
      background: #dbeafe
    }

    .skill-desc {
      font-size: .8rem;
      color: #64748b;
      font-weight: 400;
      margin-top: 2px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      max-width: 400px;
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
      border: 1px solid var(--silver-200) !important;
      border-radius: 10px;
      box-shadow: none !important;
    }

    .modal .form-control:focus {
      border-color: var(--blue-700) !important;
      box-shadow: 0 0 0 .25rem rgba(193, 39, 45, .15) !important;
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

            <div class="breadcrumb-bar">
              <a href="<?= site_url('dashboard/admin') ?>"><i class="mdi mdi-home-outline"></i> Dashboard</a>
              <span class="sep">/</span>
              <span class="current">Skills</span>
            </div>

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
                    $d    = trim($s->Description ?? '');
                  ?>
                    <li data-title="<?= htmlspecialchars(mb_strtolower($t, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>">
                      <div>
                        <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($d !== ''): ?>
                          <div class="skill-desc"><?= htmlspecialchars($d, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                      </div>
                      <div class="skill-actions">
                        <button type="button" class="btn-edit-soft btn-edit-skill" data-id="<?= $id ?>" data-title="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>" data-desc="<?= htmlspecialchars($d, ENT_QUOTES, 'UTF-8') ?>" title="Edit">
                          <i class="mdi mdi-pencil-outline"></i>
                        </button>
                        <form method="post"
                          action="<?= site_url('admin/deleteSkill/' . $id) ?>"
                          class="m-0 p-0 skill-delete-form">
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
                <div id="skillsPager" class="skills-pager" style="display:none;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:14px;padding-top:12px;border-top:1px solid #e5e7eb">
                  <div id="skillsPagerInfo" style="font-size:.85rem;color:#64748b;font-weight:600"></div>
                  <div id="skillsPagerNav" class="d-flex" style="gap:4px"></div>
                </div>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-icon"><i class="mdi mdi-hammer-wrench"></i></div>
                  <h5>No skills yet</h5>
                  <p>Click <b>Add Skill</b> to create your first one.</p>
                  <button id="btnAddSkillEmpty" type="button" class="btn btn-sm btn-primary">
                    <i class="mdi mdi-plus"></i> Add Skill
                  </button>
                </div>
              <?php endif; ?>
            </section>
          </div>
        </div>

        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <!-- Add Skill Modal -->
  <div class="modal" id="addSkillModal" tabindex="-1" aria-labelledby="addSkillLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="<?= site_url('admin/saveSkill') ?>" autocomplete="off">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

          <div class="modal-header">
            <h5 class="modal-title" id="addSkillLabel"><i class="mdi mdi-plus mr-1"></i> Add Skill</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="mb-2">
              <label class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" name="Title" class="form-control" required>
            </div>
            <div class="mb-0">
              <label class="form-label">Description</label>
              <textarea name="Description" rows="3" class="form-control" placeholder="Description of the skill."></textarea>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-primary" type="submit"><i class="mdi mdi-content-save-outline mr-1"></i> Save</button>
            <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Skill Modal -->
  <div class="modal" id="editSkillModal" tabindex="-1" aria-labelledby="editSkillLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="editSkillForm" method="post" autocomplete="off">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

          <div class="modal-header">
            <h5 class="modal-title" id="editSkillLabel"><i class="mdi mdi-pencil-outline mr-1"></i> Edit Skill</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="mb-2">
              <label class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" name="Title" id="editTitle" class="form-control" required>
            </div>
            <div class="mb-0">
              <label class="form-label">Description</label>
              <textarea name="Description" id="editDesc" rows="3" class="form-control" placeholder="Description of the skill."></textarea>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-primary" type="submit"><i class="mdi mdi-content-save-outline mr-1"></i> Update</button>
            <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Vendor JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

      // Filter list by title + client-side pagination
      var PER_PAGE = 20;
      var curPage = 1;
      var curTerm = '';

      function getRows() {
        var list = document.getElementById('skillsList');
        return list ? Array.prototype.slice.call(list.querySelectorAll('li')) : [];
      }

      function matched() {
        return getRows().filter(function(li) {
          var t = li.getAttribute('data-title') || '';
          return !curTerm || t.indexOf(curTerm) > -1;
        });
      }

      function pagerBtn(label, page, opts) {
        opts = opts || {};
        var base = 'display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;border-radius:8px;font-weight:700;font-size:.85rem;padding:0 8px;border:1px solid #e5e7eb;background:#fff;color:#334155;cursor:pointer';
        if (opts.active)   base = 'display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;border-radius:8px;font-weight:700;font-size:.85rem;padding:0 8px;background:#c1272d;color:#fff;border:1px solid #c1272d';
        if (opts.gap)      base = 'display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;font-weight:700;font-size:.85rem;color:#94a3b8';
        if (opts.disabled) base += ';opacity:.45;pointer-events:none';
        if (opts.gap) {
          var span = document.createElement('span');
          span.style.cssText = base; span.textContent = '…';
          return span;
        }
        var b = document.createElement('button');
        b.type = 'button'; b.style.cssText = base; b.innerHTML = label;
        if (!opts.active && !opts.disabled) {
          b.addEventListener('click', function() { curPage = page; render(); });
        }
        return b;
      }

      function render() {
        var rows = getRows();
        var visible = matched();
        var total = visible.length;
        var totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if (curPage > totalPages) curPage = totalPages;
        if (curPage < 1) curPage = 1;

        var start = (curPage - 1) * PER_PAGE;
        var end = start + PER_PAGE;

        // Hide everything, then show only this page's slice of matched rows
        rows.forEach(function(li) { li.style.display = 'none'; });
        visible.forEach(function(li, i) {
          li.style.display = (i >= start && i < end) ? '' : 'none';
        });

        var pager = document.getElementById('skillsPager');
        var info  = document.getElementById('skillsPagerInfo');
        var nav   = document.getElementById('skillsPagerNav');
        if (!pager) return;

        if (total <= PER_PAGE) { pager.style.display = 'none'; return; }
        pager.style.display = 'flex';

        info.textContent = 'Showing ' + (start + 1) + '–' + Math.min(end, total) + ' of ' + total + ' skills';

        nav.innerHTML = '';
        nav.appendChild(pagerBtn('<i class="mdi mdi-chevron-left"></i>', curPage - 1, { disabled: curPage <= 1 }));

        var win = 1, prev = 0;
        for (var p = 1; p <= totalPages; p++) {
          if (p === 1 || p === totalPages || (p >= curPage - win && p <= curPage + win)) {
            if (prev && p - prev > 1) nav.appendChild(pagerBtn('', 0, { gap: true }));
            nav.appendChild(pagerBtn(String(p), p, { active: p === curPage }));
            prev = p;
          }
        }

        nav.appendChild(pagerBtn('<i class="mdi mdi-chevron-right"></i>', curPage + 1, { disabled: curPage >= totalPages }));
      }

      function filterList(q) {
        curTerm = (q || '').trim().toLowerCase();
        curPage = 1;
        render();
      }
      document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'skillSearch') {
          filterList(e.target.value);
        }
      });
      render();

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
      // SweetAlert2 delete confirmation
      document.querySelectorAll('.skill-delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          Swal.fire({
            title: 'Delete this skill?',
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

      // Edit skill modal
      document.querySelectorAll('.btn-edit-skill').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var id    = this.getAttribute('data-id');
          var title = this.getAttribute('data-title');
          var desc  = this.getAttribute('data-desc');
          var modal = document.getElementById('editSkillModal');
          var form  = document.getElementById('editSkillForm');
          document.getElementById('editTitle').value = title;
          document.getElementById('editDesc').value = desc;
          form.action = '<?= site_url('admin/skills/update/') ?>' + id;
          var bsModal = (typeof bootstrap !== 'undefined') ? new bootstrap.Modal(modal) : null;
          if (bsModal) { bsModal.show(); }
          else { modal.style.display = 'block'; modal.classList.add('show'); }
        });
      });

      // Empty state add button
      var btnEmpty = document.getElementById('btnAddSkillEmpty');
      if (btnEmpty) {
        btnEmpty.addEventListener('click', function() {
          var btn = document.getElementById('btnAddSkill');
          if (btn) btn.click();
        });
      }

      // Flash auto-dismiss
      setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(el) {
          el.style.transition = 'opacity .4s';
          el.style.opacity = '0';
          setTimeout(function() { el.remove(); }, 400);
        });
      }, 4000);
    })();
  </script>
</body>

</html>