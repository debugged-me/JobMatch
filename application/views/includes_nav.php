<?php
defined('BASEPATH') or exit('No direct script access allowed');

$pf    = isset($profile) ? $profile : null;
$first = (string)(($pf->first_name ?? $pf->fName ?? null) ?? $this->session->userdata('first_name') ?? '');
$last  = (string)(($pf->last_name  ?? $pf->lName  ?? null) ?? $this->session->userdata('last_name')  ?? '');
$email = (string)($this->session->userdata('email') ?? '');

$display_name = ($first !== '' && $last !== '') ? ($last . ', ' . $first) : ($last !== '' ? $last : ($first !== '' ? $first : ($email !== '' ? $email : 'User')));
$lastUpper = $last !== '' ? (function_exists('mb_strtoupper') ? mb_strtoupper($last, 'UTF-8') : strtoupper($last)) : '';

$roleRaw = (string)$this->session->userdata('role');
$role    = strtolower($roleRaw);
$allowed = ['admin', 'worker', 'client', 'tesda_admin', 'peso', 'school admin'];
if (!in_array($role, $allowed, true)) {
  $role = 'guest';
}

$isAdmin        = ($role === 'admin');
$isWorker       = ($role === 'worker');
$isClient       = ($role === 'client');
$isTesda        = ($role === 'tesda_admin');
$isPeso         = ($role === 'peso');
$isSchoolAdmin  = ($role === 'school admin');

$portalLabel =
  $isAdmin        ? 'Admin Portal'   : ($isWorker      ? 'Worker Portal' : ($isClient      ? 'Client Portal' : ($isTesda       ? 'TESDA Portal'  : ($isPeso        ? 'PESO Portal'   : ($isSchoolAdmin ? 'School Admin'  : 'Portal')))));

$dashboard_url =
  $isAdmin        ? 'dashboard/admin'  : ($isWorker      ? 'dashboard/worker' : ($isClient      ? 'dashboard/client' : ($isTesda       ? 'dashboard/tesda'  : ($isPeso        ? 'dashboard/peso'   : ($isSchoolAdmin ? 'school-admin'     : 'dashboard/user')))));

$CI = &get_instance();
$CI->load->database();

$sessionAvatar = (string)($this->session->userdata('avatar') ?? '');
$profileAvatar = isset($pf->avatar) ? (string)$pf->avatar : '';
$dbAvatar = '';
$meId = (int)($this->session->userdata('id') ?: $this->session->userdata('user_id') ?: 0);

if ($meId > 0) {
  $wp = $CI->db->get_where('worker_profile', ['workerID' => $meId])->row();
  if ($wp && !empty($wp->avatar)) {
    $dbAvatar = base_url($wp->avatar);
  } else {
    $cp = $CI->db->get_where('client_profile', ['clientID' => $meId])->row();
    if ($cp && !empty($cp->avatar)) {
      $dbAvatar = base_url($cp->avatar);
    }
  }
}

if (!function_exists('avatar_url')) {
  function avatar_url($path = '')
  {
    if ($path === '' || $path === null) return base_url('uploads/avatars/avatar.png');
    if (preg_match('#^https?://#i', $path)) return $path;
    return base_url($path);
  }
}
$avatarUrl = avatar_url(($profileAvatar !== '' ? $profileAvatar : ($sessionAvatar !== '' ? $sessionAvatar : ($dbAvatar !== '' ? $dbAvatar : ''))));

$openComplaintsCount = 0;
if ($isAdmin) {
  $openComplaintsCount = (int) $CI->db->where('status', 'open')->count_all_results('complaints');
}

$current = uri_string();

$active = function (string $href) use ($current): string {
  $path = $href;
  $qpos = strpos($path, '?');
  if ($qpos !== false) {
    $path = substr($path, 0, $qpos);
  }
  return ($current === $path) ? 'active' : '';
};

$starts_with_any = function (array $prefixes) use ($current): bool {
  foreach ($prefixes as $p) {
    $path = $p;
    $qpos = strpos($path, '?');
    if ($qpos !== false) {
      $path = substr($path, 0, $qpos);
    }
    if ($path !== '' && strpos($current, $path) === 0) return true;
  }
  return false;
};

$submenu_state = function (array $prefixes) use ($starts_with_any): array {
  $open = $starts_with_any($prefixes);
  return [$open ? 'true' : 'false', $open ? ' show' : '', $open ? '' : ' collapsed'];
};

list($ariaProjects, $showProjects, $collapsedProjects)   = $submenu_state(['projects']);
list($ariaProjW, $showProjW, $collapsedProjW)            = $submenu_state(['projects/active', 'projects/history']);
list($ariaSchAcc, $showSchAcc, $collapsedSchAcc) = $submenu_state(['school-admin']);
?>
<link rel="stylesheet" href="<?= base_url('assets/css/nav-shell.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/nav-shell.css') ?: '2' ?>">
<nav class="sidebar sidebar-offcanvas <?= ($isAdmin || $isTesda) ? 'sidebar-textonly' : '' ?>" id="sidebar">
  <ul class="nav">
    <li class="sidebar-brand-block">
      <span class="brand-icon">
        <img src="<?= base_url('assets/images/logo.png') ?>" alt="PESO" onerror="this.style.display='none'">
      </span>
      <span class="brand-text">
        <?= htmlspecialchars($portalLabel, ENT_QUOTES, 'UTF-8') ?>
        <small>PESO Davao Oriental</small>
      </span>
    </li>

    <li class="nav-item nav-profile">
      <div class="nav-link d-flex align-items-center">
        <div class="nav-profile-image position-relative">
          <img src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="profile" class="rounded-circle" onerror="this.onerror=null;this.src='<?= htmlspecialchars(base_url('uploads/avatars/avatar.png'), ENT_QUOTES, 'UTF-8') ?>';">
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column pe-3">
          <?php if ($lastUpper !== '' || $first !== ''): ?>
            <?php if ($lastUpper !== ''): ?>
              <span class="fw-medium mb-0 text-truncate nav-profile-truncate"><?= htmlspecialchars($lastUpper, ENT_QUOTES, 'UTF-8') ?><?= $first !== '' ? ',' : '' ?></span>
            <?php endif; ?>
            <?php if ($first !== ''): ?>
              <span class="fw-normal text-truncate text-dark nav-profile-truncate"><?= htmlspecialchars($first, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
          <?php else: ?>
            <span class="fw-medium mb-0 text-truncate nav-profile-truncate"><?= htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8') ?></span>
          <?php endif; ?>
        </div>
      </div>
    </li>

    <li class="nav-item <?= $active($dashboard_url) ?>">
      <a class="nav-link" href="<?= site_url($dashboard_url) ?>">
        <i class="mdi mdi-home-variant menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <?php if ($isClient): ?>
      <li class="nav-item">
        <a class="nav-link<?= $collapsedProjects ?>" href="#" data-bs-toggle="collapse" data-bs-target="#clientProjects" aria-expanded="<?= $ariaProjects ?>" aria-controls="clientProjects" role="button">
          <i class="mdi mdi-clipboard-check-outline menu-icon"></i>
          <span class="menu-title">Projects</span>
        </a>
        <div class="collapse<?= $showProjects ?>" id="clientProjects">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link <?= $active('projects/active') ?>" href="<?= site_url('projects/active') ?>">My Projects</a></li>
            <li class="nav-item"><a class="nav-link <?= $active('payments') ?>" href="<?= site_url('payments') ?>">Payments</a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item <?= $active('personnel/hired') ?>">
        <a class="nav-link" href="<?= site_url('personnel/hired') ?>">
          <i class="mdi mdi-account-group-outline menu-icon"></i>
          <span class="menu-title">Personnel</span>
        </a>
      </li>

      <li class="nav-item <?= $active('complaints') ?>">
        <a class="nav-link" href="<?= site_url('complaints') ?>">
          <i class="mdi mdi-shield-alert-outline menu-icon"></i>
          <span class="menu-title">Report a Scam</span>
        </a>
      </li>

      <li class="nav-item <?= $active('hotlines') ?>">
        <a class="nav-link" href="<?= site_url('hotlines') ?>">
          <i class="mdi mdi-phone-in-talk-outline menu-icon"></i>
          <span class="menu-title">Hotlines</span>
        </a>
      </li>
    <?php endif; ?>
    <!-- Common: Timeline (role route) -->
    <?php if ($isClient): ?>
      <li class="nav-item <?= $active('client/feed') ?>">
        <a class="nav-link" href="<?= site_url('client/feed') ?>">
          <i class="mdi mdi-forum-outline menu-icon"></i>
          <span class="menu-title">News Feed</span>
        </a>
      </li>
    <?php elseif ($isWorker): ?>
      <li class="nav-item <?= $active('worker/feed') ?>">
        <a class="nav-link" href="<?= site_url('worker/feed') ?>">
          <i class="mdi mdi-forum-outline menu-icon"></i>
          <span class="menu-title">News Feed</span>
        </a>
      </li>
    <?php endif; ?>
    <?php if ($isWorker): ?>
      <li class="nav-item">
        <a class="nav-link<?= $collapsedProjW ?>" href="#" data-bs-toggle="collapse" data-bs-target="#workerProjects" aria-expanded="<?= $ariaProjW ?>" aria-controls="workerProjects" role="button">
          <i class="mdi mdi-clipboard-check-outline menu-icon"></i>
          <span class="menu-title">Projects</span>
        </a>
        <div class="collapse<?= $showProjW ?>" id="workerProjects">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link <?= $active('projects/active') ?>" href="<?= site_url('projects/active') ?>">Active</a></li>
            <li class="nav-item"><a class="nav-link <?= $active('projects/history') ?>" href="<?= site_url('projects/history') ?>">History</a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item <?= $active('complaints') ?>">
        <a class="nav-link" href="<?= site_url('complaints') ?>">
          <i class="mdi mdi-shield-alert-outline menu-icon"></i>
          <span class="menu-title">Report a Scam</span>
        </a>
      </li>

      <li class="nav-item <?= $active('hotlines') ?>">
        <a class="nav-link" href="<?= site_url('hotlines') ?>">
          <i class="mdi mdi-phone-in-talk-outline menu-icon"></i>
          <span class="menu-title">Hotlines</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($isTesda): ?>
      <li class="nav-item <?= $active('tesda/workers/upload') ?>">
        <a class="nav-link" href="<?= site_url('tesda/workers/upload') ?>">
          <i class="mdi mdi-account-multiple-plus menu-icon"></i>
          <span class="menu-title">Upload Workers</span>
        </a>
      </li>
      <li class="nav-item <?= $active('tesda/trainings') ?>">
        <a class="nav-link" href="<?= site_url('tesda/trainings') ?>">
          <i class="mdi mdi-school-outline menu-icon"></i>
          <span class="menu-title">Trainings</span>
        </a>
      </li>
      <li class="nav-item <?= $active('tesda/reports') ?>">
        <a class="nav-link" href="<?= site_url('tesda/reports') ?>">
          <i class="mdi mdi-chart-box-outline menu-icon"></i>
          <span class="menu-title">Reports</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($isPeso): ?>
      <li class="nav-item <?= $active('nsrp/encode') ?>">
        <a class="nav-link" href="<?= site_url('nsrp/encode') ?>">
          <i class="mdi mdi-account-plus-outline menu-icon"></i>
          <span class="menu-title">NSRP Form 1 (Jobseeker)</span>
        </a>
      </li>
      <li class="nav-item <?= $active('nsrp/encode_establishment') ?>">
        <a class="nav-link" href="<?= site_url('nsrp/encode_establishment') ?>">
          <i class="mdi mdi-domain menu-icon"></i>
          <span class="menu-title">NSRP Form 2 (Establishment)</span>
        </a>
      </li>
      <li class="nav-item <?= $active('nsrp/records') ?>">
        <a class="nav-link" href="<?= site_url('nsrp/records') ?>">
          <i class="mdi mdi-clipboard-list-outline menu-icon"></i>
          <span class="menu-title">NSRP Records</span>
        </a>
      </li>
      <li class="nav-item <?= $active('peso/reports/hired-workers') ?>">
        <a class="nav-link" href="<?= site_url('peso/reports/hired-workers') ?>">
          <i class="mdi mdi-chart-box-outline menu-icon"></i>
          <span class="menu-title">Reports</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($isSchoolAdmin): ?>
      <li class="nav-item">
        <a class="nav-link<?= $collapsedSchAcc ?>" href="#" data-bs-toggle="collapse" data-bs-target="#schoolAccounts" aria-expanded="<?= $ariaSchAcc ?>" aria-controls="schoolAccounts" role="button">
          <i class="mdi mdi-school-outline menu-icon"></i>
          <span class="menu-title">School Accounts</span>
        </a>
        <div class="collapse<?= $showSchAcc ?>" id="schoolAccounts">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="nav-link <?= $active('school-admin') ?>" href="<?= site_url('school-admin') ?>">Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $active('school-admin/workers') ?>" href="<?= site_url('school-admin/workers') ?>">Manage Workers</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $active('school-admin/create') ?>" href="<?= site_url('school-admin/create') ?>">Create Worker</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $active('school-admin/bulk') ?>" href="<?= site_url('school-admin/bulk') ?>">Bulk Upload</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $active('school-admin/reports') ?>" href="<?= site_url('school-admin/reports') ?>">Reports</a>
            </li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
      <li class="nav-item <?= $active('users') ?>">
        <a class="nav-link" href="<?= site_url('users') ?>">
          <i class="mdi mdi-account-cog-outline menu-icon"></i>
          <span class="menu-title">Manage Users</span>
        </a>
      </li>

      <li class="nav-item <?= $active('admin/workers/upload') ?>">
        <a class="nav-link" href="<?= site_url('admin/workers/upload') ?>">
          <i class="mdi mdi-account-multiple-plus menu-icon"></i>
          <span class="menu-title">Upload Workers</span>
        </a>
      </li>

      <li class="nav-item <?= $active('admin/skills') ?>">
        <a class="nav-link" href="<?= site_url('admin/skills') ?>">
          <i class="mdi mdi-hammer-wrench menu-icon"></i>
          <span class="menu-title">Skills</span>
        </a>
      </li>

      <li class="nav-item <?= $active('admin/hotlines') ?>">
        <a class="nav-link" href="<?= site_url('admin/hotlines') ?>">
          <i class="mdi mdi-phone-in-talk-outline menu-icon"></i>
          <span class="menu-title">Hotlines</span>
        </a>
      </li>

      <li class="nav-item <?= $active('admin/reports') ?>">
        <a class="nav-link" href="<?= site_url('admin/reports') ?>">
          <i class="mdi mdi-chart-box-outline menu-icon"></i>
          <span class="menu-title">Reports</span>
        </a>
      </li>

      <li class="nav-item <?= $active('admin/complaints') ?>">
        <a class="nav-link" href="<?= site_url('admin/complaints') ?>">
          <i class="mdi mdi-shield-account-outline menu-icon"></i>
          <span class="menu-title">Complaints</span>
          <?php if ($openComplaintsCount > 0): ?>
            <span class="badge bg-danger ms-2"><?= (int)$openComplaintsCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li class="nav-item <?= $active('admin/change_password') ?>">
        <a class="nav-link" href="<?= site_url('admin/change_password') ?>">
          <i class="mdi mdi-lock-reset menu-icon"></i>
          <span class="menu-title">Change Password</span>
        </a>
      </li>
    <?php endif; ?>

    <li class="nav-item logout-item">
      <a class="nav-link text-danger" href="<?= site_url('auth/logout') ?>">
        <i class="mdi mdi-logout menu-icon"></i>
        <span class="menu-title">Logout</span>
      </a>
    </li>

    <li class="sidebar-foot">&copy; <?= date('Y') ?> PESO Davao Oriental</li>
  </ul>
</nav>
<script src="<?= base_url('assets/js/nav.js') ?>"></script>
