<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Timeline Worker', ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Brand assets -->
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.7') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/css/worker_feed.css?v=1.0.9') ?>">
</head>

<body>
  <?php $this->load->view('partials_translate_banner'); ?>

  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="container-fluid px-0">

            <?php
            $fallback = base_url('uploads/avatars/avatar.png');

            $first_name = (string)($this->session->userdata('first_name') ?? '');
            $rawAvatar  = (string)($this->session->userdata('avatar') ?? '');

            if ($rawAvatar === '') {
              $meId = (int)($this->session->userdata('id') ?: $this->session->userdata('user_id') ?: 0);
              if ($meId > 0) {
                $row = $this->db
                  ->select("COALESCE(NULLIF(wp.avatar,''), NULLIF(cp.avatar,'')) AS a", false)
                  ->from('users u')
                  ->join('worker_profile wp', 'wp.workerID = u.id', 'left')
                  ->join('client_profile cp', 'cp.clientID = u.id', 'left')
                  ->where('u.id', $meId)
                  ->get()->row();
                if ($row && !empty($row->a)) $rawAvatar = (string)$row->a;
              }
            }
            $avatarAbs = function_exists('avatar_url') ? avatar_url($rawAvatar) : ($rawAvatar ?: $fallback);

            ?>
            <div class="d-flex gap-2 mb-2">
              <a href="<?= current_url() . '?scope=all' ?>"
                class="btn btn-sm btn-scope <?= $scope === 'all' ? 'active' : '' ?>"
                aria-current="<?= $scope === 'all' ? 'page' : 'false' ?>">All</a>

              <a href="<?= current_url() . '?scope=mine' ?>"
                class="btn btn-sm btn-scope <?= $scope === 'mine' ? 'active' : '' ?>"
                aria-current="<?= $scope === 'mine' ? 'page' : 'false' ?>">My posts</a>
            </div>

            <section class="panel">
              <div class="panel-head">
                <i class="mdi mdi-pencil-outline" style="color:var(--silver-600)"></i>
                <h6>Create a Post</h6>
              </div>
              <div class="panel-body">
                <div class="composer">
                  <img class="avatar"
                    src="<?= htmlspecialchars($avatarAbs, ENT_QUOTES, 'UTF-8') ?>"
                    onerror="this.onerror=null;this.src='<?= htmlspecialchars($fallback, ENT_QUOTES) ?>';"
                    alt="Me">
                  <div class="composer-input" data-bs-toggle="modal" data-bs-target="#composeModal">
                    What's on your mind, <?= htmlspecialchars($first_name ?: 'Worker', ENT_QUOTES, 'UTF-8') ?>?
                  </div>
                </div>
                <hr class="my-3">
                <div class="composer-actions">
                  <span class="chip" data-bs-toggle="modal" data-bs-target="#composeModal"><i class="mdi mdi-image-outline"></i> Photo</span>
                  <span class="chip" data-bs-toggle="modal" data-bs-target="#composeModal"><i class="mdi mdi-emoticon-happy-outline"></i> Activity</span>
                  <span class="chip" data-bs-toggle="modal" data-bs-target="#composeModal"><i class="mdi mdi-briefcase-outline"></i> Post Job</span>
                </div>
              </div>
            </section>

            <!-- â–¼â–¼ FIX #1: stable anchor for auto-prepend when there is no first post -->
            <div id="feedAnchor"></div>
            <!-- â–²â–² FIX #1 -->

            <?php if (!empty($posts)): foreach ($posts as $post): ?>
                <?php
                $name = trim(($post->first_name ?? '') . ' ' . ($post->last_name ?? ''));
                $dt = new DateTime($post->created_at ?: 'now', new DateTimeZone('UTC'));
                $iso = $dt->format(DateTime::ATOM);
                $ts  = $dt->getTimestamp() * 1000; // ms

                $mediaArr = [];
                if (!empty($post->media)) {
                  $tmp = json_decode($post->media, true);
                  if (is_array($tmp)) $mediaArr = array_values(array_filter($tmp));
                }
                $authorPhoto = function_exists('avatar_url')
                  ? avatar_url($post->author_avatar ?? '')
                  : ($post->author_avatar ?: $fallback);
                ?>
                <article class="post" data-post-id="<?= (int)$post->id ?>">

                  <div class="head">
                    <div class="who">
                      <img class="avatar"
                        src="<?= htmlspecialchars($authorPhoto, ENT_QUOTES) ?>"
                        onerror="this.onerror=null;this.src='<?= htmlspecialchars($fallback, ENT_QUOTES) ?>';"
                        alt="avatar">
                      <div>
                        <div class="name"><?= html_escape($name ?: 'User') ?></div>
                        <div class="meta">
                          <time class="when" datetime="<?= htmlspecialchars($iso, ENT_QUOTES) ?>" data-ts="<?= (int)$ts ?>">
                            <!-- fallback text (UTC) will be replaced by JS -->
                            <?= htmlspecialchars($dt->format('M d, Y Â· h:i A'), ENT_QUOTES) ?>
                          </time>
                        </div>
                      </div>
                    </div>
                    <?php
                    $canDelete = isset($me_id) && ((int)$post->worker_id === (int)$me_id);
                    ?>
                    <div class="dropdown">
                      <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
                        <i class="mdi mdi-dots-horizontal"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($canDelete): ?>
                          <li>
                            <a class="dropdown-item text-danger"
                              onclick="return confirm('Delete this post?')"
                              href="<?= site_url(($delete_action ?? 'worker/feed/delete') . '/' . (int)$post->id) ?>">
                              <i class="mdi mdi-trash-can-outline"></i> Delete
                            </a>
                          </li>
                        <?php else: ?>
                          <li><span class="dropdown-item disabled text-muted small">Actions</span></li>
                        <?php endif; ?>
                      </ul>
                    </div>

                  </div>
                  <div class="body">
                    <?php if (!empty($post->body)): ?>
                      <div class="mb-2"><?= nl2br(html_escape($post->body)) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($mediaArr)): ?>
                      <div class="<?= count($mediaArr) > 1 ? 'grid-2' : '' ?>">
                        <?php foreach ($mediaArr as $m):
                          $abs = preg_match('#^https?://#i', $m) ? $m : base_url($m); ?>
                          <div class="img mb-2"><img src="<?= htmlspecialchars($abs, ENT_QUOTES) ?>" alt=""></div>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  <?php
                  $viewerRole = strtolower((string)($this->session->userdata('role') ?? ''));
                  $authorId   = (int)($post->worker_id ?? 0);
                  $meIdView   = (int)($me_id ?? 0);
                  $redirect   = site_url('dashboard/client');
                  ?>
                  <div class="foot">
                    <?php if ($viewerRole === 'client'): ?>
                      <?php if ($authorId !== $meIdView): ?>
                        <a href="#" class="btn btn-brand btn-sm btn-pill" data-start-chat="<?= $authorId ?>">
                          <i class="mdi mdi-message-text-outline"></i> Message
                        </a>
                        <a href="#" class="btn btn-primary btn-sm btn-pill hire-btn"
                          data-user-id="<?= $authorId ?>"
                          data-redirect="<?= $redirect ?>">
                          <i class="mdi mdi-briefcase-check-outline"></i> Hire
                        </a>
                      <?php else: ?>
                        <button class="btn btn-light btn-sm btn-pill" disabled title="You can't hire yourself">
                          <i class="mdi mdi-briefcase-off-outline"></i> Hire
                        </button>
                      <?php endif; ?>
                    <?php else: ?>
                      <button class="btn btn-light btn-sm btn-pill"><i class="mdi mdi-thumb-up-outline"></i> Like</button>
                      <button class="btn btn-light btn-sm btn-pill"><i class="mdi mdi-comment-outline"></i> Comment</button>
                      <button class="btn btn-light btn-sm btn-pill"><i class="mdi mdi-share-outline"></i> Share</button>
                    <?php endif; ?>
                  </div>


                </article>
              <?php endforeach;
            else: ?>
              <div class="panel" style="margin-top:12px">
                <div class="panel-body text-muted text-center">No posts yet. Share your first update!</div>
              </div>
            <?php endif; ?>


          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="composeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <?= form_open_multipart(site_url($post_action ?? 'worker/feed/post')) ?>
        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800;color:var(--blue-900)">Create post</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <?= validation_errors('<div class="alert alert-danger py-2">', '</div>') ?>

          <div class="d-flex align-items-center gap-2 mb-3">
            <img class="rounded-circle" style="width:44px;height:44px;object-fit:cover"
              src="<?= htmlspecialchars($avatarAbs, ENT_QUOTES, 'UTF-8') ?>"
              onerror="this.onerror=null;this.src='<?= htmlspecialchars($fallback, ENT_QUOTES) ?>';" alt="">
            <span class="text-muted small">Posting publicly</span>
          </div>

          <div class="mb-2">
            <textarea name="body" class="form-control brand-input" rows="4" placeholder="What's on your mind?" required></textarea>
          </div>

          <div class="mt-2">
            <input type="file" name="photo" class="form-control brand-input" accept="image/*">
            <small class="text-muted">Optional. JPG, PNG, GIF, WEBP up to 4MB.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-brand btn-pill"><i class="mdi mdi-send"></i> Post</button>
        </div>
        <?= form_close() ?>
      </div>
    </div>
  </div>
  <div class="toast-container">
    <div id="postToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body"><i class="mdi mdi-check-circle-outline me-1"></i> Your post is live.</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <div class="modal fade tw-modal" id="hireModal" tabindex="-1" aria-labelledby="hireModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="hireModalLabel">Create Hire Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="tw-wrap">
            <div>
              <div class="tw-card">
                <h6><i class="mdi mdi-briefcase-outline"></i> Project (optional)</h6>
                <select id="hireProject" class="form-select tw-input">
                  <option value="">— No specific project —</option>
                </select>
                <div class="tw-help mt-2" id="hireHelp"></div>
                <div id="hirePreview" class="tw-preview mt-3"></div>
              </div>

              <div class="tw-card">
                <h6><i class="mdi mdi-cash-multiple"></i> Billing</h6>
                <div class="tw-seg mb-2">
                  <input class="btn-check" type="radio" name="hireRateUnit" id="ruHour" value="hour" autocomplete="off">
                  <label for="ruHour">Per hour</label>
                  <input class="btn-check" type="radio" name="hireRateUnit" id="ruDay" value="day" autocomplete="off">
                  <label for="ruDay">Per day</label>
                  <input class="btn-check" type="radio" name="hireRateUnit" id="ruProject" value="project" autocomplete="off" checked>
                  <label for="ruProject">Per project</label>
                </div>
                <div class="tw-help">If you pick a project above, we'll prefill from that project's billing when possible.</div>
              </div>

              <div class="tw-card">
                <h6><i class="mdi mdi-currency-php"></i> Proposed rate</h6>
                <div class="input-group">
                  <span class="input-group-text">â‚±</span>
                  <input type="number" step="0.01" min="0" class="form-control tw-input" id="hireRate" placeholder="ex. 500.00">
                  <span class="input-group-text">per <span id="hireRateUnitSuffix">project</span></span>
                </div>
                <div class="tw-help mt-2">Leave empty if you'd rather discuss inside chat.</div>
              </div>
            </div>

            <div>
              <div class="tw-card">
                <h6><i class="mdi mdi-message-alert-outline"></i> Invitation</h6>
                <div class="form-check form-switch tw-switch">
                  <input class="form-check-input" type="checkbox" id="hireInvite" checked>
                  <label class="form-check-label" for="hireInvite">Include hire invitation (shows Accept/Decline in chat)</label>
                </div>
              </div>

              <div class="tw-card tw-disclaimer">
                <div class="tw-disc-head">
                  <div class="tw-disc-icon"><i class="mdi mdi-shield-check"></i></div>
                  <div>Disclaimer</div>
                </div>
                <details class="mt-1 tw-muted">
                  <summary>Read full disclaimer</summary>
                  <div class="mt-2">
                    • Keep all hiring, messages, and payments on JobMatch for safety and support.<br>
                    • Do not send advance payments outside the platform.<br>
                    • Report suspicious behavior via Help &amp; Support.<br>
                    • By sending a Hire request you agree to our Terms &amp; Policies.
                  </div>
                </details>
                <div class="form-check form-switch tw-switch mt-3">
                  <input class="form-check-input" type="checkbox" id="hireDisclaimerAgree">
                  <label class="form-check-label fw-semibold" for="hireDisclaimerAgree">I've read and agree to the disclaimer.</label>
                </div>
              </div>
            </div>
          </div>

          <div id="hireEmpty" class="d-none" style="padding:18px;">
            <div class="alert alert-info mb-0">You don't have any active projects yet.</div>
          </div>
        </div>

        <div class="modal-footer">
          <button id="hireSendBtn" type="button" class="btn btn-primary disabled" disabled>
            <i class="mdi mdi-send"></i> Send Hire Request
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php $this->load->view('includes_footer'); ?>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>

  <script>
    /* =========================================================================
   Unified script block (toast + hire modal + feed poll + time localizer)
   Includes the fix: renderPostCard writes visible time text immediately.
   ========================================================================= */

    /* ---------- 1) Post success toast ---------- */
    (function() {
      var posted = <?= json_encode((bool)$this->session->flashdata('post_ok') || (isset($_GET['posted']) && $_GET['posted'] == '1')) ?>;
      if (posted && window.bootstrap) {
        new bootstrap.Toast(document.getElementById('postToast'), {
          delay: 2500
        }).show();
      }
    })();

    /* ---------- 2) Hire modal + chat bindings ---------- */
    (function() {
      if (window.__TW_HIRE_CHAT_BOUND__) return;
      window.__TW_HIRE_CHAT_BOUND__ = true;

      function csrfPair() {
        <?php if ($this->security->get_csrf_token_name()): ?>
          return {
            name: '<?= $this->security->get_csrf_token_name(); ?>',
            hash: '<?= $this->security->get_csrf_hash(); ?>'
          };
        <?php else: ?>
          return null;
        <?php endif; ?>
      }

      function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
      }

      function toast(msg, kind) {
        if (window.showToast) window.showToast(msg, kind);
      }

      function renderPreview(meta) {
        if (!H.preview) return;
        if (!meta || !Array.isArray(meta.files) || !meta.files.length) {
          H.preview.innerHTML = '<div class="text-muted small">No files attached.</div>';
        } else {
          var imgs = meta.files.filter(function(f) {
            return f.type === 'image';
          }).slice(0, 6);
          var pdfs = meta.files.filter(function(f) {
            return f.type === 'pdf';
          }).slice(0, 6);
          var html = '';
          imgs.forEach(function(f) {
            html += '<img class="proj-thumb" src="' + esc(f.url) + '" alt="' + esc(f.name) + '">';
          });
          pdfs.forEach(function(f) {
            html += '<a class="proj-chip" href="' + esc(f.url) + '" target="_blank" rel="noopener"><i class="mdi mdi-file-pdf-box"></i> ' + esc(f.name) + '</a>';
          });
          H.preview.innerHTML = '<div class="tw-preview">' + html + '</div>';
        }
        applyProjectBillingToModal(meta);
      }

      function applyProjectBillingToModal(meta) {
        var ru = (meta && meta.rate_unit) ? String(meta.rate_unit).toLowerCase() : '';
        if (ru === 'hour' && H.ruHour) H.ruHour.checked = true;
        else if (ru === 'day' && H.ruDay) H.ruDay.checked = true;
        else if (H.ruProject) H.ruProject.checked = true;
        setRateSuffix((ru === 'hour' || ru === 'day') ? ru : 'project');
      }

      function setRateSuffix(val) {
        if (!H.rateSuffix) return;
        H.rateSuffix.textContent = (val === 'hour' ? 'hour' : (val === 'day' ? 'day' : 'project'));
      }

      function ensureHireModal() {
        var el = document.getElementById('hireModal');
        return {
          el: el,
          modal: new bootstrap.Modal(el),
          projectSel: el.querySelector('#hireProject'),
          sendBtn: el.querySelector('#hireSendBtn'),
          help: el.querySelector('#hireHelp'),
          bodyWrap: el.querySelector('.tw-wrap'),
          emptyWrap: el.querySelector('#hireEmpty'),
          preview: el.querySelector('#hirePreview'),
          inviteChk: el.querySelector('#hireInvite'),
          ruHour: el.querySelector('#ruHour'),
          ruDay: el.querySelector('#ruDay'),
          ruProject: el.querySelector('#ruProject'),
          rateInput: el.querySelector('#hireRate'),
          rateSuffix: el.querySelector('#hireRateUnitSuffix')
        };
      }

      var H = ensureHireModal();
      var currentWorkerId = null;
      var currentRedirect = null;

      var disclaimerChk = document.getElementById('hireDisclaimerAgree');
      var REQUIRE_DISCLAIMER = true;

      function updateSendEnabled() {
        if (!H.sendBtn) return;
        var allow = true;
        if (REQUIRE_DISCLAIMER && disclaimerChk) {
          allow = !!disclaimerChk.checked;
        }
        H.sendBtn.disabled = !allow;
        H.sendBtn.classList.toggle('disabled', !allow);
      }

      function resetDisclaimer() {
        if (disclaimerChk) disclaimerChk.checked = false;
        updateSendEnabled();
      }
      if (disclaimerChk) {
        disclaimerChk.addEventListener('change', updateSendEnabled);
      }

      ['ruHour', 'ruDay', 'ruProject'].forEach(function(id) {
        if (!H[id]) return;
        H[id].addEventListener('change', function() {
          if (this.checked) setRateSuffix(this.value);
        });
      });

      function fetchAndPreview(pid) {
        if (!pid) {
          if (H.preview) H.preview.innerHTML = '';
          applyProjectBillingToModal(null);
          return;
        }
        fetch('<?= site_url("projects/api/one/") ?>' + pid, {
            credentials: 'same-origin'
          })
          .then(function(r) {
            return r.json();
          })
          .then(function(res) {
            if (!res || !res.ok) throw 0;
            renderPreview(res);
          })
          .catch(function() {
            if (H.preview) H.preview.innerHTML = '<div class="text-muted small">Could not load files.</div>';
            applyProjectBillingToModal(null);
          });
      }

      function loadProjectsIntoModal() {
        if (!H.projectSel) return;
        H.projectSel.innerHTML = '<option value="">Loading…</option>';
        H.help.textContent = '';
        H.emptyWrap.classList.add('d-none');
        H.bodyWrap.classList.remove('d-none');
        if (H.preview) H.preview.innerHTML = '';
        if (H.inviteChk) H.inviteChk.checked = true;
        if (H.ruProject) H.ruProject.checked = true;
        setRateSuffix('project');
        if (H.rateInput) H.rateInput.value = '';

        fetch('<?= site_url("projects/api/active-min") ?>', {
            credentials: 'same-origin'
          })
          .then(function(r) {
            return r.json();
          })
          .then(function(res) {
            if (!res || !res.ok) throw 0;
            var items = res.items || [];
            if (!items.length) {
              H.bodyWrap.classList.add('d-none');
              H.emptyWrap.classList.remove('d-none');
              if (H.sendBtn) {
                H.sendBtn.textContent = 'Go to Projects';
                H.sendBtn.setAttribute('data-redirect', '<?= site_url("projects/active") ?>');
                H.sendBtn.classList.remove('btn-primary');
                H.sendBtn.classList.add('btn-outline-primary');
                H.sendBtn.disabled = false;
              }
              return;
            }
            var html = '<option value="">— No specific project —</option>';
            items.forEach(function(p) {
              html += '<option value="' + p.id + '">' + esc(p.title) + '</option>';
            });
            H.projectSel.innerHTML = html;
            H.help.textContent = 'You have ' + items.length + ' active project' + (items.length > 1 ? 's' : '') + '.';
            if (H.sendBtn) {
              H.sendBtn.innerHTML = '<i class="mdi mdi-send"></i> Send Hire Request';
              H.sendBtn.removeAttribute('data-redirect');
              H.sendBtn.classList.remove('btn-outline-primary');
              H.sendBtn.classList.add('btn-primary');
            }
          })
          .catch(function() {
            H.projectSel.innerHTML = '<option value="">— No specific project —</option>';
            H.help.textContent = 'Could not load projects right now.';
            if (H.sendBtn) {
              H.sendBtn.textContent = 'Go to Projects';
              H.sendBtn.setAttribute('data-redirect', '<?= site_url("projects/active") ?>');
              H.sendBtn.classList.remove('btn-primary');
              H.sendBtn.classList.add('btn-outline-primary');
              H.sendBtn.disabled = false;
            }
          });
      }

      document.querySelectorAll('.hire-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          currentWorkerId = this.getAttribute('data-user-id');
          currentRedirect = this.getAttribute('data-redirect') || '<?= site_url("dashboard/client") ?>';
          if (!currentWorkerId) return;
          loadProjectsIntoModal();
          resetDisclaimer();
          H.modal.show();
        });
      });

      if (H.projectSel) {
        H.projectSel.addEventListener('change', function() {
          fetchAndPreview(this.value);
        });
      }

      H.sendBtn.addEventListener('click', function() {
        var redirectTo = this.getAttribute('data-redirect');
        if (redirectTo) {
          window.location.href = redirectTo;
          return;
        }
        if (REQUIRE_DISCLAIMER && disclaimerChk && !disclaimerChk.checked) {
          toast('Please agree to the disclaimer first.', 'error');
          return;
        }
        if (!currentWorkerId) return;

        var fd = new FormData();
        fd.append('user_id', currentWorkerId);
        var pid = H.projectSel ? (H.projectSel.value || '') : '';
        if (pid) fd.append('project_id', pid);
        if (H.inviteChk && H.inviteChk.checked) fd.append('invite', '1');
        var ru = (H.ruHour && H.ruHour.checked) ? 'hour' : (H.ruDay && H.ruDay.checked) ? 'day' : 'project';
        fd.append('rate_unit', ru);
        var rateVal = H.rateInput && H.rateInput.value ? H.rateInput.value : '';
        if (rateVal !== '') fd.append('rate', rateVal);
        var csrf = csrfPair();
        if (csrf) {
          fd.append(csrf.name, csrf.hash);
        }

        fetch('<?= site_url("notifications/notify_hire") ?>', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          })
          .then(function(r) {
            return r.ok ? r.json() : {
              ok: false,
              message: 'HTTP ' + r.status
            };
          })
          .then(function(res) {
            var ok = !!(res && res.ok);
            if (window.showToast) window.showToast(ok ? 'Hire request sent' : (res && res.message ? res.message : 'Could not send hire request'), ok ? 'success' : 'error');
            if (ok) H.modal.hide();
            setTimeout(function() {
              window.location.href = currentRedirect;
            }, ok ? 700 : 1200);
          })
          .catch(function() {
            if (window.showToast) window.showToast('Could not send hire request', 'error');
          });
      });

      document.addEventListener('click', function(e) {
        var el = e.target.closest('[data-start-chat]');
        if (!el) return;
        e.preventDefault();
        var to = el.getAttribute('data-start-chat');
        if (!to) return;
        el.classList.add('disabled');
        el.setAttribute('aria-disabled', 'true');
        var fd = new FormData();
        fd.append('to', to);
        var c = csrfPair();
        if (c) {
          fd.append(c.name, c.hash);
        }

        fetch('<?= site_url('messages/api/start') ?>', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          })
          .then(function(r) {
            return r.ok ? r.json() : {
              ok: false,
              message: 'HTTP ' + r.status
            };
          })
          .then(function(res) {
            if (res && res.ok && res.link) {
              window.location.href = res.link;
            } else {
              if (window.showToast) window.showToast(res && res.message ? res.message : 'Could not start chat', 'error');
              el.classList.remove('disabled');
              el.removeAttribute('aria-disabled');
            }
          })
          .catch(function() {
            if (window.showToast) window.showToast('Could not start chat', 'error');
            el.classList.remove('disabled');
            el.removeAttribute('aria-disabled');
          });
      });

      function openFor(uid, redirectUrl) {
        currentWorkerId = uid;
        currentRedirect = redirectUrl || '<?= site_url("dashboard/client") ?>';
        loadProjectsIntoModal();
        resetDisclaimer();
        H.modal.show();
      }
      window.TW_HIRE = {
        openFor: openFor,
        H: H,
        loadProjectsIntoModal: loadProjectsIntoModal,
        resetDisclaimer: resetDisclaimer
      };
    })();

    /* ---------- 3) Helper: click proxy for .hire-btn ---------- */
    document.addEventListener('click', function(e) {
      var btn = e.target.closest('.hire-btn');
      if (!btn) return;
      e.preventDefault();

      var uid = btn.getAttribute('data-user-id');
      var redirectUrl = btn.getAttribute('data-redirect') || '<?= site_url("dashboard/client") ?>';
      if (!uid) return;

      if (!window.TW_HIRE || typeof window.TW_HIRE.openFor !== 'function') return;
      window.TW_HIRE.openFor(uid, redirectUrl);
    });

    /* ---------- 4) Time localizer (available before poller uses it) ---------- */
    (function() {
      function localizeTimes(root) {
        (root || document).querySelectorAll('time.when[data-ts]').forEach(function(t) {
          var ms = parseInt(t.getAttribute('data-ts'), 10);
          if (!isFinite(ms)) return;
          var d = new Date(ms);
          t.textContent = d.toLocaleString([], {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
          });
        });
      }
      document.addEventListener('DOMContentLoaded', function() {
        localizeTimes(document);
      });
      window.__localizeTimes = localizeTimes;
    })();

    /* ---------- 5) Feed poller (with immediate time text fix) ---------- */
    (function() {
      var POLL_MS = 12000;
      var API_URL = "<?= site_url($api_url ?? 'worker/feed/api_new') ?>";
      var BASE_URL = "<?= rtrim(base_url(), '/') ?>/";
      var SCOPE = "<?= isset($scope) ? $scope : 'all' ?>";

      window.__viewerRole = '<?= strtolower((string)$this->session->userdata('role') ?: '') ?>';
      window.__meId = <?= json_encode((int)($me_id ?? 0)) ?>;
      window.__deleteBase = <?= json_encode((string)($delete_action ?? 'worker/feed/delete')) ?>;

      function isHttp(u) {
        return /^https?:\/\//i.test(String(u || ''));
      }

      function absUrl(p) {
        return isHttp(p) ? p : (p ? BASE_URL + p.replace(/^\/+/, '') : "");
      }

      function esc(s) {
        return String(s == null ? '' : s)
          .replace(/&/g, '&amp;').replace(/</g, '&lt;')
          .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
      }

      function topPostId() {
        var first = document.querySelector('.post[data-post-id]');
        return first ? (parseInt(first.getAttribute('data-post-id'), 10) || 0) : 0;
      }

      // âœ… FIX APPLIED: time text is inserted immediately
      function renderPostCard(p) {
        var name = esc(((p.first_name || '') + ' ' + (p.last_name || '')).trim()) || 'User';

        var ts = (p.created_ts ? parseInt(p.created_ts, 10) : (new Date(p.created_at)).getTime());
        var iso = p.created_at || new Date(ts).toISOString();

        // Render a visible string now (local time); localizer can overwrite later
        var whenText = (function() {
          try {
            var d = new Date(ts);
            return d.toLocaleString([], {
              year: 'numeric',
              month: 'short',
              day: '2-digit',
              hour: '2-digit',
              minute: '2-digit'
            });
          } catch (_) {
            return '';
          }
        })();

        var avatar = absUrl(p.author_photo || p.author_avatar || 'uploads/avatars/avatar.png');

        var canDelete = (parseInt(window.__meId || 0, 10) === parseInt(p.worker_id || 0, 10));
        var menuHtml = canDelete ?
          '<li><a class="dropdown-item text-danger" onclick="return confirm(\'Delete this post?\')" ' +
          'href="' + esc((window.__deleteBase || 'worker/feed/delete') + '/' + p.id) + '">' +
          '<i class="mdi mdi-trash-can-outline"></i> Delete</a></li>' :
          '<li><span class="dropdown-item disabled text-muted small">Actions</span></li>';

        var mediaArr = Array.isArray(p.media) ? p.media.filter(Boolean) : [];
        var mediaHtml = '';
        if (mediaArr.length) {
          var grid = mediaArr.length > 1 ? 'grid-2' : '';
          mediaHtml = '<div class="' + grid + '">' + mediaArr.map(function(m) {
            return '<div class="img mb-2"><img src="' + esc(absUrl(m)) + '" alt=""></div>';
          }).join('') + '</div>';
        }

        var role = (window.__viewerRole || '').toLowerCase();
        var workerId = p.worker_id || 0;
        var isSelf = (parseInt(window.__meId || 0, 10) === parseInt(workerId || 0, 10)); // self-hire guard

        var footHtml;
        if (role === 'client') {
          if (!isSelf) {
            footHtml =
              '<a href="#" class="btn btn-brand btn-sm btn-pill" data-start-chat="' + workerId + '"><i class="mdi mdi-message-text-outline"></i> Message</a> ' +
              '<a href="#" class="btn btn-primary btn-sm btn-pill hire-btn" data-user-id="' + workerId + '" data-redirect="<?= site_url('dashboard/client') ?>"><i class="mdi mdi-briefcase-check-outline"></i> Hire</a>';
          } else {
            footHtml =
              '<button class="btn btn-light btn-sm btn-pill" disabled title="You can’t hire yourself"><i class="mdi mdi-briefcase-off-outline"></i> Hire</button>';
          }
        } else {
          footHtml =
            '<button class="btn btn-light btn-sm btn-pill"><i class="mdi mdi-thumb-up-outline"></i> Like</button> ' +
            '<button class="btn btn-light btn-sm btn-pill"><i class="mdi mdi-comment-outline"></i> Comment</button> ' +
            '<button class="btn btn-light btn-sm btn-pill"><i class="mdi mdi-share-outline"></i> Share</button>';
        }

        return '' +
          '<article class="post" data-post-id="' + p.id + '">' +
          '<div class="head">' +
          '<div class="who">' +
          '<img class="avatar" src="' + esc(avatar) + '" alt="avatar" onerror="this.onerror=null;this.src=\'' + esc(absUrl('uploads/avatars/avatar.png')) + '\';">' +
          '<div>' +
          '<div class="name">' + name + '</div>' +
          '<div class="meta"><time class="when" datetime="' + esc(iso) + '" data-ts="' + esc(String(ts)) + '">' + esc(whenText) + '</time></div>' +
          '</div>' +
          '</div>' +
          '<div class="dropdown">' +
          '<button class="btn btn-light btn-sm" data-bs-toggle="dropdown"><i class="mdi mdi-dots-horizontal"></i></button>' +
          '<ul class="dropdown-menu dropdown-menu-end">' + menuHtml + '</ul>' +
          '</div>' +
          '</div>' +
          '<div class="body">' + (p.body ? '<div class="mb-2">' + esc(p.body).replace(/\n/g, '<br>') + '</div>' : '') + mediaHtml + '</div>' +
          '<div class="foot">' + footHtml + '</div>' +
          '</article>';
      }

      function prependBatchDESC(items) {
        if (!items || !items.length) return;
        var anchor = document.querySelector('#feedAnchor');
        var firstPost = document.querySelector('.post');
        var parent = (firstPost ? firstPost.parentNode : (anchor ? anchor.parentNode : (document.querySelector('.app') || document.body)));

        for (var i = items.length - 1; i >= 0; i--) {
          var it = items[i];
          if (document.querySelector('.post[data-post-id="' + it.id + '"]')) continue;

          var tmp = document.createElement('div');
          tmp.innerHTML = renderPostCard(it);
          var card = tmp.firstElementChild;

          if (firstPost) {
            parent.insertBefore(card, firstPost);
          } else if (anchor) {
            anchor.insertAdjacentElement('afterend', card);
          } else {
            parent.insertBefore(card, parent.firstChild);
          }

          if (window.__localizeTimes) window.__localizeTimes(card);
        }
      }

      function poll() {
        if (window.__FEED_POLL_RUNNING__) return;
        window.__FEED_POLL_RUNNING__ = true;
        (function loop() {
          var after = topPostId();
          var url = API_URL + '?after=' + encodeURIComponent(after) + '&scope=' + encodeURIComponent(SCOPE) + '&_=' + Date.now();
          fetch(url, {
              credentials: 'same-origin',
              headers: {
                'Accept': 'application/json'
              },
              cache: 'no-store'
            })
            .then(function(r) {
              return r.ok ? r.json() : {
                ok: false
              };
            })
            .then(function(res) {
              if (res && res.ok && Array.isArray(res.items) && res.items.length) {
                prependBatchDESC(res.items);
              }
            })
            .catch(function() {
              /* ignore */
            })
            .finally(function() {
              setTimeout(loop, POLL_MS);
            });
        })();
      }

      document.addEventListener('DOMContentLoaded', poll);
    })();
  </script>

</body>

</html>