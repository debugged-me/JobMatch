<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('partials_translate_banner'); ?>

<?php
$CI = &get_instance();
$CI = &get_instance();

$roleRaw = (string)($CI->session->userdata('role') ?? '');
$roleKey = strtolower(trim($roleRaw));
$roleMap = [
  'admin'        => 'admin',
  'administrator' => 'admin',
  'admins'       => 'admin',

  'worker'       => 'worker',
  'workers'      => 'worker',
  'staff'        => 'worker',

  'client'       => 'client',
  'clients'      => 'client',
  'customer'     => 'client',
  'customers'    => 'client',

  'tesda_admin'  => 'tesda_admin',
  'tesda-admin'  => 'tesda_admin',
  'tesda'        => 'tesda_admin',
];

$role = $roleMap[$roleKey] ?? 'guest';

$seg0 = strtolower((string)$CI->uri->segment(1));
$seg1 = strtolower((string)$CI->uri->segment(2));

if ($seg0 === 'admin' || ($seg0 === 'dashboard' && $seg1 === 'admin')) {
  $dashPath = 'dashboard/admin';
} elseif ($seg0 === 'tesda' || $seg0 === 'tesda_admin' || ($seg0 === 'dashboard' && $seg1 === 'tesda')) {
  $dashPath = 'dashboard/tesda';
} else {
  if ($role === 'admin') {
    $dashPath = 'dashboard/admin';
  } elseif ($role === 'client') {
    $dashPath = 'dashboard/client';
  } elseif ($role === 'worker') {
    $dashPath = 'dashboard/worker';
  } elseif ($role === 'tesda_admin') {
    $dashPath = 'dashboard/tesda';
  } else {
    $dashPath = 'auth/login';
  } // 
}
$dashboard_url = site_url($dashPath);

$CI->load->model('ClientProfile_model');
$CI->load->model('Message_model', 'mm');

$current_uid = $CI->session->userdata('id') ?: $CI->session->userdata('user_id');
$current_uid = is_numeric($current_uid) ? (int)$current_uid : 0;

$msg_unread   = 0;
$msg_threads  = [];
if ($current_uid > 0) {
  $msg_unread  = (int)$CI->mm->unread_messages_count($current_uid);
  $msg_threads = $CI->mm->latest_threads($current_uid, 8);
}

$unread_count  = 0;
$initial_notes = [];
if ($current_uid > 0) {
  if (method_exists($CI->ClientProfile_model, 'unread_count')) {
    $unread_count = (int)$CI->ClientProfile_model->unread_count($current_uid);
  }
  if (method_exists($CI->ClientProfile_model, 'get_notifications')) {
    $initial_notes = $CI->ClientProfile_model->get_notifications($current_uid, 10, 0);
  }
}
?>

<nav class="navbar col-lg-12 col-12 p-lg-0 fixed-top d-flex flex-row"
  id="jmNavTop"
  data-msg-count-url="<?= site_url('messages/api_unread') ?>"
  data-msg-feed-url="<?= site_url('messages/api_feed') ?>"
  data-notif-feed-url="<?= site_url('notifications/feed') ?>"
  data-notif-count-url="<?= site_url('notifications/count') ?>"
  data-notif-mark-url="<?= site_url('notifications/mark_read') ?>"
  data-default-avatar="<?= htmlspecialchars(base_url('uploads/avatars/avatar.png'), ENT_QUOTES, 'UTF-8') ?>"
  data-csrf-name="<?= $this->security->get_csrf_token_name(); ?>"
  data-csrf-hash="<?= $this->security->get_csrf_hash(); ?>"
  data-user-id="<?= (int)($this->session->userdata('id') ?: $this->session->userdata('user_id') ?: 0) ?>">
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between">
    <button class="navbar-toggler navbar-toggler align-items-center" type="button" data-bs-toggle="minimize">
      <i class="mdi mdi-menu"></i>
    </button>

    <ul class="navbar-nav navbar-nav-right ms-lg-auto">
      <?php if ($role === 'client'): ?>
        <li class="nav-item nav-search border-0 me-1 me-md-3 me-lg-5 d-none d-md-flex">
          <form id="topSearchForm" class="nav-link form-inline mt-2 mt-md-0"
            action="<?= site_url('search') ?>" method="get"
            onsubmit="return this.q.value.trim().length >= 2;">
            <div class="input-group tw-searchbar">
              <div class="input-group-append">
                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
              </div>
              <input id="topSearchInput" type="text" class="form-control" name="q"
                placeholder="Search skills you need."
                value="<?= html_escape($CI->input->get('q')) ?>"
                required minlength="2" enterkeyhint="search" autocomplete="off">
            </div>
          </form>
        </li>
      <?php endif; ?>

      <!-- Translate button visible to ALL roles -->
      <li class="nav-item ms-2">
        <a href="#" id="openTranslate" class="nav-link" title="Translate">
          <i class="mdi mdi-translate"></i>
          <span class="d-none d-md-inline" data-i18n="nav.translate">Translate</span>
        </a>
      </li>

      <li class="nav-item dropdown dropleft">
        <a class="nav-link position-relative" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
          <i class="mdi mdi-bell-outline"></i>
          <span id="notifBadge"
            class="badge rounded-pill bg-danger position-absolute d-none nav-badge">0</span>

        </a>

        <div class="dropdown-menu navbar-dropdown navbar-dropdown-large preview-list dropdown-menu-right nav-dropdown-large"
          aria-labelledby="notificationDropdown">
          <h6 class="p-3 mb-0">Notifications</h6>
          <div id="notifList">
            <?php if (empty($initial_notes)): ?>
              <div class="px-3 pb-3 text-muted small">No notifications yet.</div>
              <?php else: foreach ($initial_notes as $n):
                $actor = trim(($n->actor_fname ?? '') . ' ' . ($n->actor_lname ?? '')) ?: (string)$n->actor_id;

                $DEFAULT_AVATAR_REL = 'uploads/avatars/avatar.png';
                $toAbs = function ($raw) {
                  $raw = trim((string)$raw);
                  if ($raw === '') return '';
                  if (preg_match('#^https?://#i', $raw)) return $raw;
                  return base_url(str_replace('\\', '/', $raw));
                };
                $is_default_avatar = function ($raw) use ($DEFAULT_AVATAR_REL) {
                  if ($raw === null) return false;
                  $raw = trim((string)$raw);
                  if ($raw === '') return false;
                  $rel = str_replace(base_url(), '', $raw);
                  $rel = ltrim(str_replace('\\', '/', $rel), '/');
                  return (strcasecmp($rel, $DEFAULT_AVATAR_REL) === 0) || (basename($rel) === basename($DEFAULT_AVATAR_REL));
                };

                $avatar = '';
                $rawAvatar = (string)($n->actor_avatar ?? $n->avatar ?? '');
                if ($rawAvatar !== '' && !$is_default_avatar($rawAvatar)) {
                  $avatar = function_exists('avatar_url') ? avatar_url($rawAvatar) : $toAbs($rawAvatar);
                }

                if (!$avatar) {
                  $wp = $CI->db->get_where('worker_profile', ['workerID' => (int)$n->actor_id])->row();
                  $cp = $CI->db->get_where('client_profile', ['clientID' => (int)$n->actor_id])->row();
                  if ($wp && !empty($wp->avatar) && !$is_default_avatar($wp->avatar)) $avatar = function_exists('avatar_url') ? avatar_url($wp->avatar) : $toAbs($wp->avatar);
                  elseif ($cp && !empty($cp->avatar) && !$is_default_avatar($cp->avatar)) $avatar = function_exists('avatar_url') ? avatar_url($cp->avatar) : $toAbs($cp->avatar);
                }
                if (!$avatar) $avatar = base_url($DEFAULT_AVATAR_REL);
              ?>

                <a href="<?= $n->link ?: 'javascript:void(0)' ?>"
                  class="dropdown-item preview-item d-flex align-items-start notif-item"
                  data-id="<?= (int)$n->id ?>">
                  <img
                    src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>"
                    class="me-2 rounded-circle nav-avatar" alt="avatar"
                    onerror="this.onerror=null;this.src='<?= htmlspecialchars(base_url('uploads/avatars/avatar.png'), ENT_QUOTES, 'UTF-8') ?>';">
                  <div class="preview-item-content flex-grow">
                    <p class="preview-subject mb-0 <?= !$n->is_read ? 'fw-bold' : '' ?>">
                      <?= html_escape($n->title) ?>
                    </p>
                    <?php if (!empty($n->body)): ?>
                      <p class="small text-muted mb-0"><?= html_escape($n->body) ?></p>
                    <?php endif; ?>
                    <p class="small text-muted mb-0"><?= date('M d, Y h:i A', strtotime($n->created_at)) ?></p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
            <?php endforeach;
            endif; ?>
          </div>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link position-relative" id="messageDropdown" href="#" data-bs-toggle="dropdown">
          <i class="mdi mdi-email-outline"></i>
          <?php if ($msg_unread > 0): ?>
            <span id="msgBadge" class="badge rounded-pill bg-danger position-absolute nav-badge"><?= $msg_unread ?></span>
          <?php else: ?>
            <span id="msgBadge" class="badge rounded-pill bg-danger position-absolute d-none nav-badge">0</span>
          <?php endif; ?>
        </a>

        <div class="dropdown-menu navbar-dropdown navbar-dropdown-large preview-list dropdown-menu-right nav-dropdown-large"
          aria-labelledby="messageDropdown">
          <h6 class="p-3 mb-0">Messages</h6>
          <div id="msgList">
            <?php if (empty($msg_threads)): ?>
              <div class="px-3 pb-3 text-muted small">No conversations yet.</div>
              <?php else: foreach ($msg_threads as $t):
                $other_id = ($t->a_id == $current_uid) ? (int)$t->b_id : (int)$t->a_id;
                $name     = trim(($t->first_name ?? '') . ' ' . ($t->last_name ?? '')) ?: ('User #' . $other_id);
                $DEFAULT_AVATAR_REL = 'uploads/avatars/avatar.png';

                $DEFAULT_AVATAR_REL = 'uploads/avatars/avatar.png';

                $toAbs = function ($raw) {
                  $raw = trim((string)$raw);
                  if ($raw === '') return '';
                  if (preg_match('#^https?://#i', $raw)) return $raw;
                  return base_url(str_replace('\\', '/', $raw));
                };

                $rawAvatar = $n->actor_avatar ?? $n->avatar ?? '';
                $avatar = '';
                if ($rawAvatar) {
                  $avatar = function_exists('avatar_url') ? avatar_url($rawAvatar)
                    : (preg_match('#^https?://#i', $rawAvatar) ? $rawAvatar
                      : base_url(str_replace('\\', '/', $rawAvatar)));
                }
                if (!$avatar) {
                  $wp = $CI->db->get_where('worker_profile', ['workerID' => (int)$n->actor_id])->row();
                  $cp = $CI->db->get_where('client_profile', ['clientID' => (int)$n->actor_id])->row();
                  if ($wp && !empty($wp->avatar)) $avatar = function_exists('avatar_url') ? avatar_url($wp->avatar) : base_url(str_replace('\\', '/', $wp->avatar));
                  elseif ($cp && !empty($cp->avatar)) $avatar = function_exists('avatar_url') ? avatar_url($cp->avatar) : base_url(str_replace('\\', '/', $cp->avatar));
                }
                if (!$avatar) $avatar = base_url('uploads/avatars/avatar.png');

                $link = site_url('messages/t/' . $t->thread_id);
              ?>
                <a href="<?= $link ?>" class="dropdown-item preview-item d-flex align-items-start msg-item"
                  data-thread="<?= (int)$t->thread_id ?>">
                  <?php $def = base_url($DEFAULT_AVATAR_REL); ?>
                  <img
                    src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>"
                    class="me-2 rounded-circle nav-avatar" alt="avatar"
                    onerror="this.onerror=null;this.src='<?= htmlspecialchars($def, ENT_QUOTES, 'UTF-8') ?>';">

                  <div class="preview-item-content flex-grow">
                    <p class="preview-subject mb-0"><?= html_escape($name) ?></p>
                    <p class="small text-muted mb-0"><?= html_escape(mb_strimwidth((string)$t->last_body, 0, 80, '…')) ?></p>
                  </div>
                  <?php if (!empty($t->unread)): ?>
                    <span class="badge bg-primary ms-2"><?= (int)$t->unread ?></span>
                  <?php endif; ?>
                </a>
                <div class="dropdown-divider"></div>
            <?php endforeach;
            endif; ?>
          </div>
        </div>
      </li>

      <!-- HOME ICON -->
      <li class="nav-item"><a class="nav-link" href="<?= $dashboard_url; ?>"><i class="mdi mdi-home-outline"></i></a></li>
    </ul>

    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
<script src="<?= base_url('assets/js/nav-top.js') ?>"></script>
