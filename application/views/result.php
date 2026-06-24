<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'Search', ENT_QUOTES, 'UTF-8') ?> • JobMatch</title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.7') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <style>
    :root {
      --bg: #f8fafc;
      --card: #ffffff;
      --line: #e5e7eb;
      --muted: #6b7280;
      --title: #0f172a;
      --indigo-100: #e0e7ff;
      --indigo-200: #c7d2fe;
      --indigo-300: #a5b4fc;
      --indigo-400: #818cf8;
      --indigo-500: #6366f1;
      --shadow-1: 0 6px 18px rgba(2, 6, 23, .06), 0 1px 0 rgba(2, 6, 23, .04);
      --shadow-2: 0 18px 44px rgba(2, 6, 23, .12), 0 6px 18px rgba(2, 6, 23, .08);
    }

    body {
      font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: var(--bg);
      color: var(--title)
    }

    .content-wrapper {
      padding-top: 1rem
    }

    .app {
      max-width: 1180px;
      margin: 0 auto;
      padding: 0 16px
    }

    .eyebrow {
      font-size: .85rem;
      color: var(--muted);
      font-weight: 600;
      letter-spacing: .3px;
      margin-bottom: .15rem
    }

    h4 {
      font-size: clamp(16px, 1.8vw, 20px);
      font-weight: 700;
      margin: 0
    }

    .section {
      background: #fff;
      border: 1px solid var(--indigo-200);
      border-radius: 14px;
      box-shadow: var(--shadow-1);
      padding: 16px 18px
    }

    .vlist {
      display: flex;
      flex-direction: column;
      gap: 14px
    }

    .vcard {
      display: flex;
      gap: 14px;
      align-items: flex-start;
      background: #fff;
      border: 1px solid var(--indigo-200);
      border-radius: 14px;
      box-shadow: var(--shadow-1);
      padding: 14px;
      transition: .18s
    }

    .vcard:hover {
      border-color: var(--indigo-400);
      box-shadow: var(--shadow-2);
      transform: translateY(-1px)
    }

    .vcard .avatar {
      width: 56px;
      height: 56px;
      border-radius: 9999px;
      object-fit: cover;
      border: 2px solid var(--line)
    }

    .vcard .title {
      font-weight: 800;
      line-height: 1.2;
      margin: 0
    }

    .vcard .muted {
      color: var(--muted);
      font-size: .9rem
    }

    .vcard .sub {
      font-size: .92rem
    }

    .vcard .tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 6px
    }

    .vcard .tag {
      font-size: .78rem;
      background: #eef2ff;
      border: 1px solid var(--indigo-200);
      color: #3730a3;
      border-radius: 999px;
      padding: .18rem .5rem;
      font-weight: 600
    }

    .vcard .actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap
    }

    .vcard .pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .35rem .75rem;
      border-radius: 9999px;
      border: 1px solid var(--indigo-200);
      background: #fff;
      font-size: .82rem;
      font-weight: 600;
      color: #334155;
      text-decoration: none;
      transition: all .18s ease
    }

    .vcard .pill:hover {
      background: var(--indigo-500);
      border-color: var(--indigo-500);
      color: #fff
    }

    .vcard-main {
      flex: 1 1 auto;
      min-width: 0
    }

    .vcard-aside {
      display: flex;
      flex-direction: column;
      gap: 8px;
      align-items: flex-end;
      justify-content: space-between
    }

    .rating {
      font-size: .92rem
    }

    .rating .count {
      color: var(--muted);
      font-size: .85rem;
      margin-left: 4px
    }

    @media (max-width: 768px) {
      .vcard {
        flex-direction: column;
        align-items: flex-start
      }

      .vcard-aside {
        align-items: stretch;
        width: 100%
      }

      .vcard .actions {
        width: 100%
      }

      .vcard .actions .btn {
        flex: 1
      }
    }

    .empty {
      padding: 24px;
      border: 1px dashed var(--indigo-300);
      border-radius: 14px;
      background: #fbfbff;
      color: #475569
    }

    .proj-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .proj-thumb {
      width: 84px;
      height: 84px;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
      object-fit: cover;
    }

    .proj-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .28rem .6rem;
      border-radius: 9999px;
      border: 1px solid #e5e7eb;
      background: #fff;
      font-size: .82rem;
      text-decoration: none;
      box-shadow: 0 2px 6px rgba(2, 6, 23, .06)
    }

    .proj-chip i {
      font-size: 1rem;
    }

    /* Verified badge */
    .verified-badge {
      --size: 20px;
      --ring: 1.5px;
      --blue1: #2b8cff;
      --blue2: #1877F2;
      --blue3: #0f5ed7;
      position: relative;
      display: inline-grid;
      place-items: center;
      width: var(--size);
      height: var(--size);
      border-radius: 50%;
      background: radial-gradient(120% 120% at 30% 30%, var(--blue1) 0%, var(--blue2) 50%, var(--blue3) 100%);
      box-shadow: 0 0 0 var(--ring) #fff, 0 6px 14px rgba(24, 119, 242, .20);
      margin-left: 8px;
      vertical-align: middle;
      transform: translateY(-1px);
    }

    .verified-badge svg {
      width: 12px;
      height: 12px;
      fill: #fff;
      display: block
    }

    /* Disabled really unclickable */
    .btn.disabled,
    .btn:disabled {
      pointer-events: none
    }

    /* -------------------- NEW: Modal Card UI -------------------- */
    .tw-modal .modal-dialog {
      max-width: 920px
    }

    .tw-modal .modal-content {
      border-radius: 18px;
      border: 1px solid var(--indigo-200);
      box-shadow: var(--shadow-2);
      overflow: hidden;
      background: #fff;
    }

    .tw-modal .modal-header {
      padding: 18px 22px;
      border-bottom: 1px solid var(--indigo-200);
      background: linear-gradient(180deg, #f8faff, #ffffff);
    }

    .tw-modal .modal-title {
      font-weight: 800
    }

    .tw-modal .modal-body {
      padding: 0
    }

    .tw-modal .tw-wrap {
      display: grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 16px;
      padding: 18px;
    }

    @media (max-width: 900px) {
      .tw-modal .tw-wrap {
        grid-template-columns: 1fr
      }
    }

    .tw-card {
      background: #fff;
      border: 1px solid var(--indigo-200);
      border-radius: 14px;
      box-shadow: var(--shadow-1);
      padding: 16px 16px;
    }

    .tw-card+.tw-card {
      margin-top: 12px
    }

    .tw-card h6 {
      font-weight: 800;
      font-size: 1rem;
      margin: 0 0 10px 0;
      display: flex;
      align-items: center;
      gap: 8px
    }

    .tw-muted {
      color: var(--muted)
    }

    .tw-seg {
      display: flex;
      background: #f4f6ff;
      border: 1px solid var(--indigo-200);
      border-radius: 12px;
      overflow: hidden
    }

    .tw-seg label {
      flex: 1;
      padding: 10px 12px;
      margin: 0;
      border-right: 1px solid var(--indigo-200);
      cursor: pointer;
      font-weight: 600;
      text-align: center
    }

    .tw-seg label:last-child {
      border-right: 0
    }

    .tw-seg .btn-check:checked+label {
      background: #1d4ed8;
      color: #fff;
      border-color: #1d4ed8
    }

    .tw-input {
      border-radius: 12px !important;
      border: 1px solid var(--indigo-200) !important;
    }

    .tw-help {
      font-size: .85rem;
      color: #6b7280
    }

    .tw-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 10px
    }

    .tw-preview .proj-thumb {
      width: 76px;
      height: 76px
    }

    .tw-switch .form-check-input {
      width: 3.1rem;
      height: 1.6rem;
      background-color: #e5e7eb;
      border: 1px solid #cbd5e1;
      background-image: none
    }

    .tw-switch .form-check-input:checked {
      background-color: #6366f1;
      border-color: #6366f1
    }

    .tw-disclaimer {
      border: 1px dashed var(--indigo-300);
      background: #fbfbff;
      border-radius: 14px;
      padding: 14px 14px
    }

    .tw-disc-head {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 800
    }

    .tw-disc-icon {
      width: 28px;
      height: 28px;
      border-radius: 8px;
      display: grid;
      place-items: center;
      background: linear-gradient(135deg, #a5b4fc, #6366f1);
      color: #fff
    }

    .tw-modal .modal-footer {
      border-top: 1px solid var(--indigo-200);
      padding: 16px 22px;
      background: #fff;
      display: flex;
      justify-content: flex-end
    }

    .form-switch.tw-switch,
    .form-switch.tw-switch-lg {
      padding-left: 0;
      display: flex;
      align-items: center;
      gap: .5rem;
    }

    .form-switch.tw-switch .form-check-input,
    .form-switch.tw-switch-lg .form-check-input {
      margin-left: 0;
      margin-right: .6rem;
      border-radius: 9999px;
      background-image: none;
    }

    .form-switch.tw-switch .form-check-label,
    .form-switch.tw-switch-lg .form-check-label {
      margin: 0;
      line-height: 1.4;
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
            <?php
            $__defaultAvatar = htmlspecialchars(avatar_url(''), ENT_QUOTES, 'UTF-8');
            function _rel_avatar_path($p)
            {
              $p = trim((string)$p);
              if ($p === '') return '';
              $p = str_replace('\\', '/', $p);
              $root = str_replace('\\', '/', FCPATH);
              if (stripos($p, $root) === 0) {
                $p = ltrim(substr($p, strlen($root)), '/');
              }
              return $p;
            }
            /** Check if profile is complete (client/worker) via CI models. */
            function tw_is_verified(string $mode, $userId): bool
            {
              $CI = &get_instance();
              $uid = (int)($userId ?: 0);
              if ($uid <= 0) return false;
              if ($mode === 'clients') {
                $CI->load->model('ClientProfile_model', 'cp');
                if (method_exists($CI->cp, 'get') && method_exists($CI->cp, 'is_complete')) {
                  $row = $CI->cp->get($uid);
                  return $row && $CI->cp->is_complete($row);
                }
              } else {
                $CI->load->model('WorkerProfile_model', 'wp');
                if (method_exists($CI->wp, 'get') && method_exists($CI->wp, 'is_complete')) {
                  $row = $CI->wp->get($uid);
                  return $row && $CI->wp->is_complete($row);
                }
              }
              return false;
            }
            ?>
            <?php $mode = isset($mode) ? $mode : (($this->session->userdata('role') === 'worker') ? 'clients' : 'workers'); ?>

            <div class="mb-3">
              <div class="eyebrow"> </div>
              <?php if (!empty($workers)): ?>
                <?php $shown_count = isset($shown) ? (int)$shown : count($workers);
                $total_count = isset($total) ? (int)$total : $shown_count; ?>
                <div class="muted" style="display:flex;align-items:center;gap:10px;margin-top:.35rem">
                  <span><strong><?= $shown_count ?></strong> of <strong><?= $total_count ?></strong> results</span>
                </div>
              <?php endif; ?>
            </div>

            <?php if (empty($workers)): ?>
              <div class="empty">
                <div style="font-weight:700;margin-bottom:.25rem">
                  No matching <?= $mode === 'clients' ? 'clients' : 'workers' ?> found
                  <?php if (!empty($q)): ?> for "<?= html_escape($q) ?>".<?php endif; ?>.
                </div>
                <?php if (!empty($terms)): ?>
                  <div class="mb-2">Tried terms: <?= html_escape(implode(', ', $terms)) ?></div>
                <?php endif; ?>
                <div class="mt-2" style="display:flex;gap:8px;flex-wrap:wrap">
                  <a class="pill" href="<?= site_url('search') ?>">Clear search</a>
                  <a class="pill" href="<?= site_url('dashboard/' . ($mode === 'clients' ? 'worker' : 'client')) ?>">Go back</a>
                </div>
                <div class="muted mt-2" style="font-size:.85rem">Tips: try fewer words, a broader skill (ex. "carpenter" instead of "finish carpenter").</div>
              </div>
            <?php else: ?>
              <div class="section">
                <div class="vlist">
                  <?php foreach ($workers as $w): ?>
                    <?php if ($mode === 'clients'): ?>
                      <?php
                      $first = (string)($w->cfName ?? $w->first_name ?? '');
                      $last = (string)($w->clName ?? $w->last_name  ?? '');
                      $full = $last . (($last !== '' && $first !== '') ? ', ' : '') . $first;
                      $rawAvatar = _rel_avatar_path($w->c_avatar ?? '');
                      $avatar = avatar_url($rawAvatar);
                      $loc = trim(($w->c_brgy ? $w->c_brgy . ', ' : '') . ($w->c_city ? $w->c_city . ($w->c_province ? ', ' : '') : '') . ($w->c_province ?? ''));
                      $org = trim(($w->companyName ?? '') . ' ' . ($w->employer ?? '') . ' ' . ($w->business_name ?? ''));
                      $__isVerified = tw_is_verified('clients', $w->id ?? $w->user_id ?? 0);
                      ?>
                      <div class="vcard">
                        <img class="avatar" src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar"
                          onerror="this.onerror=null;this.src='<?= $__defaultAvatar ?>';">
                        <div class="vcard-main">
                          <h5 class="title">
                            <?= html_escape($full !== '' ? $full : ($w->email ?? 'Client')) ?>
                            <?php if ($__isVerified): ?>
                              <span class="verified-badge" title="Verified" aria-label="Verified">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                  <path d="M9.0 16.2l-3.5-3.5 1.4-1.4 2.1 2.1 5.7-5.7 1.4 1.4-7.1 7.1z" />
                                </svg>
                              </span>
                            <?php endif; ?>
                          </h5>
                          <?php if ($loc): ?><div class="muted"><?= html_escape($loc) ?></div><?php endif; ?>
                          <?php if ($org !== ''): ?><div class="sub"><?= html_escape($org) ?></div><?php endif; ?>
                          <?php if (!empty($w->address)): ?><div class="sub"><strong>Address:</strong> <?= html_escape($w->address) ?></div><?php endif; ?>
                        </div>
                        <div class="vcard-aside">
                          <div class="actions">
                            <a class="btn btn-sm btn-outline-primary" href="<?= site_url('profile/client/' . $w->id) ?>">View profile</a>
                            <a class="btn btn-sm btn-primary" href="#" data-start-chat="<?= (int)$w->id ?>">Message</a>
                          </div>
                        </div>
                      </div>
                    <?php else: ?>
                      <?php
                      $first = (string)($w->first_name ?? '');
                      $last = (string)($w->last_name  ?? '');
                      $full = $last . (($last !== '' && $first !== '') ? ', ' : '') . $first;
                      $rawAvatar = _rel_avatar_path($w->w_avatar ?? '');
                      $avatar = avatar_url($rawAvatar);
                      $skills = array_filter(array_map('trim', explode(',', (string)($w->skills ?? ''))));
                      $loc = trim(($w->w_brgy ? $w->w_brgy . ', ' : '') . ($w->w_city ? $w->w_city . ($w->w_province ? ', ' : '') : '') . ($w->w_province ?? ''));
                      $recipient_id = isset($w->user_id) ? (int)$w->user_id : (int)$w->id;
                      $avg = (float)($w->w_rating ?? 0);
                      $cnt = (int)($w->w_rating_count ?? 0);
                      $__isVerified = tw_is_verified('workers', $w->id ?? $w->user_id ?? 0);
                      ?>
                      <div class="vcard">
                        <img class="avatar" src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar"
                          onerror="this.onerror=null;this.src='<?= $__defaultAvatar ?>';">
                        <div class="vcard-main">
                          <h5 class="title">
                            <?= html_escape($full !== '' ? $full : ($w->email ?? 'Worker')) ?>
                            <?php if ($__isVerified): ?>
                              <span class="verified-badge" title="Verified" aria-label="Verified">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                  <path d="M9.0 16.2l-3.5-3.5 1.4-1.4 2.1 2.1 5.7-5.7 1.4 1.4-7.1 7.1z" />
                                </svg>
                              </span>
                            <?php endif; ?>
                          </h5>
                          <?php if ($loc): ?><div class="muted"><?= html_escape($loc) ?></div><?php endif; ?>
                          <?php if (!empty($w->w_headline)): ?><div class="sub"><?= html_escape($w->w_headline) ?></div><?php endif; ?>
                          <?php if (!empty($skills)): ?>
                            <div class="tags"><?php foreach ($skills as $s): ?><span class="tag"><?= html_escape($s) ?></span><?php endforeach; ?></div>
                          <?php endif; ?>
                        </div>
                        <div class="vcard-aside">
                          <?php if ($cnt > 0): ?><div class="rating"><strong><?= number_format($avg, 1) ?></strong>/5 <span class="count">(<?= $cnt ?>)</span></div><?php endif; ?>
                          <div class="actions">
                            <a class="btn btn-sm btn-outline-primary" href="<?= site_url('profile/worker/' . $w->id) ?>">View profile</a>
                            <a class="btn btn-sm btn-outline-dark" href="#" data-start-chat="<?= (int)$recipient_id ?>">Message</a>
                            <a class="btn btn-sm btn-primary hire-btn" data-user-id="<?= (int)$recipient_id ?>" data-redirect="<?= site_url('dashboard/client') ?>" href="#">Hire</a>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>

                <!-- =============== HIRE MODAL =============== -->
                <div class="modal fade tw-modal" id="hireModal" tabindex="-1" aria-labelledby="hireModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="hireModalLabel">Create Hire Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>

                      <div class="modal-body">
                        <div class="tw-wrap">
                          <!-- Left column -->
                          <div>
                            <!-- Project -->
                            <div class="tw-card">
                              <h6><i class="mdi mdi-briefcase-outline"></i> Project (optional)</h6>
                              <select id="hireProject" class="form-select tw-input">
                                <option value="">— No specific project —</option>
                              </select>
                              <div class="tw-help mt-2" id="hireHelp"></div>
                              <div id="hirePreview" class="tw-preview mt-3"></div>
                            </div>

                            <!-- Billing -->
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

                            <!-- Rate -->
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

                          <!-- Right column -->
                          <div>
                            <!-- Invite switch -->
                            <div class="tw-card">
                              <h6><i class="mdi mdi-message-alert-outline"></i> Invitation</h6>
                              <div class="form-check form-switch tw-switch">
                                <input class="form-check-input" type="checkbox" id="hireInvite" checked>
                                <label class="form-check-label" for="hireInvite">Include hire invitation (shows Accept/Decline in chat)</label>
                              </div>
                            </div>

                            <!-- Disclaimer -->
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
                                <label class="form-check-label fw-semibold" for="hireDisclaimerAgree">
                                  I've read and agree to the disclaimer.
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Empty state -->
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
                <!-- ============= /HIRE MODAL ============= -->

                <div class="mt-3"><?= $pagination ?? '' ?></div>
              </div>
            <?php endif; ?>

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

  <script>
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

      // Disclaimer controls
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
              // send stays disabled until disclaimer is checked
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
            toast(ok ? 'Hire request sent' : (res && res.message ? res.message : 'Could not send hire request'), ok ? 'success' : 'error');
            if (ok) H.modal.hide();
            setTimeout(function() {
              window.location.href = currentRedirect;
            }, ok ? 700 : 1200);
          })
          .catch(function() {
            toast('Could not send hire request', 'error');
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
              toast(res && res.message ? res.message : 'Could not start chat', 'error');
              el.classList.remove('disabled');
              el.removeAttribute('aria-disabled');
            }
          })
          .catch(function() {
            toast('Could not start chat', 'error');
            el.classList.remove('disabled');
            el.removeAttribute('aria-disabled');
          });
      });

    })();
  </script>

</body>

</html>