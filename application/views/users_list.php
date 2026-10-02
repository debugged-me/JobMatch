<!doctype html>
<html lang="en">

<head>
  <?php $this->load->view('partials/head', [
    'page_title' => 'Manage Users',
    'css' => [
      'assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css',
      'assets/css/tw-utils.css?v=1',
    ],
  ]); ?>
  <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
  <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">
  <style>
  :root {
  --ink: var(--jm-slate-900);
  --muted: var(--jm-slate-500);
  --line: var(--jm-gray-200);
  --soft: var(--jm-slate-50);
  --brand: var(--jm-primary);
  --brand-600: var(--jm-primary);
  --warn: var(--jm-info);
  --ok: var(--jm-success);
  --bad: var(--jm-red-500);
  --role: var(--jm-blue-steel-dark);
  --icon: var(--jm-slate-600);
  --icon-h: var(--jm-slate-900);
  }
  body {
  font-family: "Karla", system-ui, -apple-system, "Segoe UI", Roboto, Arial;
  color: var(--ink)
  }
  .card {
  background: #fff;
  border: 1px solid #e6e6e6;
  border-radius: 16px;
  box-shadow: 0 6px 18px rgba(2, 6, 23, .06)
  }
  .btn-silver {
  background: #fff;
  border: 1px solid var(--jm-gray-200);
  color: var(--jm-gray-900);
  border-radius: 12px;
  padding: .6rem 1rem;
  font-weight: 700
  }
  .divider {
  height: 1px;
  background: var(--line)
  }
  .input,
  .select {
  border: 1px solid var(--jm-gray-200);
  border-radius: 12px;
  padding: .6rem .8rem;
  background: #fff;
  outline: none
  }
  .pill {
  display: inline-flex;
  align-items: center;
  gap: .38rem;
  font-weight: 700;
  line-height: 1;
  border-radius: 9999px;
  padding: .26rem .52rem;
  font-size: .75rem;
  border: 1px solid transparent;
  white-space: nowrap
  }
  .pill-ok {
  background: rgba(22, 163, 74, .10);
  color: var(--jm-emerald-800);
  border-color: rgba(22, 163, 74, .22)
  }
  .pill-bad {
  background: rgba(239, 68, 68, .10);
  color: var(--jm-red-800);
  border-color: rgba(239, 68, 68, .22)
  }
  .pill-warn {
  background: rgba(245, 158, 11, .12);
  color: var(--jm-amber-800);
  border-color: rgba(245, 158, 11, .28)
  }
  .pill-role {
  background: var(--jm-indigo-50);
  color: var(--role);
  border-color: var(--jm-indigo-200)
  }
  table.users {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0
  }
  table.users thead th {
  text-transform: uppercase;
  letter-spacing: .04em;
  font-size: .72rem;
  color: var(--muted);
  padding: 12px;
  border-bottom: 1px solid var(--line);
  background: #fbfdff;
  position: sticky;
  top: 0;
  z-index: 1
  }
  table.users tbody td {
  padding: 14px 12px;
  border-bottom: 1px solid var(--jm-slate-100);
  vertical-align: middle
  }
  table.users tbody tr:nth-child(odd) {
  background: #fff
  }
  table.users tbody tr:nth-child(even) {
  background: #fcfdff
  }
  table.users tbody tr:hover {
  background: var(--jm-slate-50)
  }
  .u-main {
  display: flex;
  align-items: center;
  gap: 12px
  }
  .u-ava {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid var(--jm-gray-200);
  background: #f3f4f6
  }
  .actbar {
  display: flex;
  align-items: center;
  gap: .45rem;
  flex-wrap: wrap;
  justify-content: center
  }
  .icon-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1px solid var(--jm-gray-200);
  background: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: var(--icon);
  cursor: pointer;
  transition: .16s border-color, .16s transform, .16s color;
  position: relative;
  }
  .icon-btn:hover {
  border-color: #d1d5db;
  color: var(--icon-h);
  transform: translateY(-1px)
  }
  .icon-btn.ok:hover {
  border-color: rgba(22, 163, 74, .35);
  color: var(--jm-emerald-800)
  }
  .icon-btn.bad:hover {
  border-color: rgba(239, 68, 68, .35);
  color: var(--jm-red-800)
  }
  .icon-btn.info:hover,
  .icon-btn.brand:hover {
  border-color: #bfdbfe;
  color: var(--brand)
  }
  .icon-btn[data-tip]:after {
  content: attr(data-tip);
  position: absolute;
  bottom: 110%;
  left: 50%;
  transform: translateX(-50%);
  background: var(--jm-gray-900);
  color: #fff;
  font-size: .72rem;
  line-height: 1;
  padding: .32rem .46rem;
  border-radius: 8px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: .15s;
  box-shadow: 0 8px 20px rgba(2, 6, 23, .25)
  }
  .icon-btn[data-tip]:before {
  content: "";
  position: absolute;
  bottom: 102%;
  left: 50%;
  transform: translateX(-50%);
  border: 6px solid transparent;
  border-top-color: var(--jm-gray-900);
  opacity: 0;
  transition: .15s
  }
  .icon-btn:hover:after,
  .icon-btn:hover:before {
  opacity: 1
  }
  @media (max-width:992px) {
  .card .overflow-x-auto {
  overflow: visible
  }
  table.users thead {
  display: none
  }
  table.users,
  table.users tbody,
  table.users tr,
  table.users td {
  display: block;
  width: 100%
  }
  table.users tbody tr {
  border: 1px solid var(--jm-gray-200);
  border-radius: 14px;
  box-shadow: 0 4px 10px rgba(2, 6, 23, .05);
  padding: 12px;
  margin-bottom: 12px;
  background: #fff
  }
  table.users tbody td {
  border: 0;
  padding: 6px 0
  }
  table.users tbody td[data-th]::before {
  content: attr(data-th);
  display: block;
  font: 700 11px/1 Inter, system-ui;
  color: var(--jm-slate-500);
  text-transform: uppercase;
  margin-bottom: 4px
  }
  .actbar {
  gap: .35rem;
  justify-content: flex-start
  }
  }
  table.users tbody td.email a {
  font-size: .92rem;
  font-weight: 500;
  line-height: 1.3;
  color: var(--brand-600);
  text-decoration: none;
  }
  table.users tbody td.email a .mdi {
  font-size: 16px;
  margin-right: 6px;
  vertical-align: -2px
  }
  table.users thead th:nth-child(1),
  table.users thead th:nth-child(2) {
  background: linear-gradient(0deg, #fff, var(--jm-amber-50))
  }
  table.users thead th:nth-child(3),
  table.users thead th:nth-child(4),
  table.users thead th:nth-child(5) {
  background: linear-gradient(0deg, #fff, var(--jm-blue-50))
  }
  #create-admin-modal {
  position: fixed;
  inset: 0;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  background: rgba(2, 6, 23, .55);
  backdrop-filter: saturate(140%) blur(3px);
  }
  .cam-card {
  width: 100%;
  max-width: 560px;
  background: #fff;
  border: 1px solid #e6e6e6;
  border-radius: 16px;
  box-shadow: 0 22px 60px rgba(2, 6, 23, .18);
  padding: 20px;
  }
  .cam-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 6px
  }
  .cam-title {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0;
  font: 700 1.1rem/1.2 Inter, system-ui
  }
  .cam-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 12px;
  background: var(--jm-indigo-50);
  color: #3730a3;
  border: 1px solid var(--jm-indigo-200)
  }
  .cam-line {
  height: 1px;
  background: var(--line);
  margin: 12px 0
  }
  .cam-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px
  }
  .cam-field {
  margin-top: 12px
  }
  .cam-label {
  display: block;
  font-size: .86rem;
  color: var(--jm-slate-600);
  margin-bottom: 6px;
  font-weight: 600
  }
  .cam-ctl {
  position: relative
  }
  .cam-input {
  border: 1px solid var(--jm-gray-200);
  border-radius: 12px;
  width: 100%;
  padding: .68rem .9rem .68rem 2.25rem;
  background: #fff;
  outline: 0
  }
  .cam-input:focus {
  border-color: #93c5fd;
  box-shadow: 0 0 0 4px rgba(43, 77, 165, .12)
  }
  .cam-ico {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: var(--jm-slate-500)
  }
  .cam-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 16px
  }
  .btn-primary {
  background: var(--brand);
  border: 1px solid var(--brand);
  color: #fff;
  border-radius: 12px;
  padding: .65rem 1rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  box-shadow: 0 6px 18px rgba(37, 99, 235, .18);
  transition: .15s transform;
  }
  .btn-primary:hover {
  transform: translateY(-1px)
  }
  .btn-silver {
  background: #fff;
  border: 1px solid var(--jm-gray-200);
  color: var(--jm-gray-900);
  border-radius: 12px;
  padding: .65rem 1rem;
  font-weight: 700
  }
  .btn-silver:hover {
  border-color: #d1d5db
  }
  .icon-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1px solid var(--jm-gray-200);
  background: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: var(--jm-slate-600);
  cursor: pointer
  }
  .icon-btn:hover {
  border-color: var(--jm-slate-300);
  color: var(--jm-slate-900)
  }
  .cam-help {
  margin-top: 6px;
  font-size: .78rem;
  color: var(--jm-slate-500)
  }
  .cam-eye {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1px solid var(--jm-gray-200);
  background: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: var(--jm-slate-600);
  cursor: pointer;
  }
  .cam-eye:hover {
  border-color: var(--jm-slate-300);
  color: var(--jm-slate-900);
  }
  .searchbar {
  display: flex;
  align-items: center;
  gap: .6rem;
  background: #fff;
  border: 1px solid var(--jm-gray-200);
  border-radius: 12px;
  padding: .35rem .5rem;
  box-shadow: 0 4px 12px rgba(2, 6, 23, .04);
  }
  .searchbar .icon {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: var(--jm-slate-500);
  }
  .searchbar input {
  border: 0;
  outline: 0;
  width: 100%;
  padding: .5rem .2rem;
  border-radius: 10px;
  font: 500 .95rem/1.2 Inter, system-ui;
  }
  .searchbar input::placeholder {
  color: var(--jm-slate-400);
  }
  .select.compact,
  .btn-compact {
  height: 38px;
  padding: 0 .9rem;
  border-radius: 12px;
  }
  .btn-compact {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: .45rem;
  background: var(--jm-blue-600);
  border: 1px solid var(--jm-blue-600);
  color: #fff;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(37, 99, 235, .20);
  }
  @media (max-width: 768px) {
  .searchbar {
  padding: .3rem .45rem;
  }
  .select.compact,
  .btn-compact {
  height: 36px;
  }
  }
  /* Global loading overlay */
  .busy {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  background: rgba(2, 6, 23, .55);
  backdrop-filter: saturate(140%) blur(3px);
  }
  .busy-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 14px;
  background: #fff;
  border: 1px solid var(--jm-gray-200);
  box-shadow: 0 22px 60px rgba(2, 6, 23, .18);
  font-weight: 600;
  color: var(--jm-slate-900);
  }
  .busy-spinner {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 3px solid var(--jm-gray-200);
  border-top-color: var(--jm-blue-600);
  animation: spin .8s linear infinite;
  }
  @keyframes spin {
  to {
  transform: rotate(360deg);
  }
  }
  .busy-text {
  font-size: .95rem
  }
  /* keep no horizontal scrollbar but allow tooltips to overflow vertically */
  .card .overflow-x-auto {
  overflow-x: hidden;
  overflow-y: visible;
  }
  /* we earlier used ellipsis globally; let Action column breathe */
  table.users {
  table-layout: fixed;
  width: 100%;
  }
  table.users th,
  table.users td {
  white-space: nowrap;
  overflow: hidden;
  /* keeps other columns neat */
  text-overflow: ellipsis;
  }
  /* allow tooltips to overflow in the Action column */
  table.users th.col-actions,
  table.users td.td-actions {
  overflow: visible !important;
  }
  /* make sure the tooltip sits on top and isn't clipped by the button */
  .icon-btn {
  position: relative;
  z-index: 1;
  overflow: visible;
  }
  .icon-btn:hover:after,
  .icon-btn:hover:before {
  z-index: 20;
  }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>

      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="px-4 md:px-8 max-w-7xl mx-auto">

            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
              <a href="<?= site_url('dashboard/admin') ?>" class="font-semibold text-gray-500 hover:text-red-600 no-underline"><i class="mdi mdi-home-outline"></i> Dashboard</a>
              <span class="text-gray-300">/</span>
              <span class="font-bold text-gray-700">Manage Users</span>
            </div>

            <div class="flex items-center justify-between mb-3 gap-2">
              <div class="flex items-center gap-3">
                <h1 class="text-xl md:text-2xl font-bold"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                <?php if (isset($total_users)): ?>
                  <span class="pill pill-role"><i class="mdi mdi-account-group-outline"></i> <?= number_format((int)$total_users) ?> total</span>
                <?php endif; ?>
              </div>
              <div class="flex items-center gap-2">
                <button type="button" id="btn-open-create-admin" class="btn-silver" style="display:inline-flex;align-items:center;gap:8px;">
                  <i class="mdi mdi-shield-account-outline"></i>
                  <span>Create Admin</span>
                </button>
                <a class="btn-silver" href="<?= site_url('users') ?>"><i class="mdi mdi-refresh"></i> Refresh</a>
              </div>
            </div>

            <div class="card p-4 mb-4">
              <form class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end" method="get" action="<?= site_url('users') ?>">
                <div class="md:col-span-2">
                  <div class="searchbar">
                    <span class="icon"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" name="q"
                      value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>"
                      placeholder="Search name, username, or email">
                  </div>
                </div>
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Role</label>
                  <?php $roleNow = strtolower((string)($role ?? '')); ?>
                  <select class="select compact w-full" name="role">
                    <option value="" <?= $roleNow === '' ? 'selected' : ''; ?>>All</option>
                    <option value="worker" <?= $roleNow === 'worker' ? 'selected' : ''; ?>>Worker</option>
                    <option value="client" <?= $roleNow === 'client' ? 'selected' : ''; ?>>Client</option>
                    <option value="admin" <?= $roleNow === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="tesda_admin" <?= $roleNow === 'tesda_admin' ? 'selected' : ''; ?>>TESDA Admin</option>
                    <option value="school_admin" <?= $roleNow === 'school_admin' ? 'selected' : ''; ?>>School Admin</option>
                    <option value="peso" <?= $roleNow === 'peso' ? 'selected' : ''; ?>>PESO</option>
                    <option value="other" <?= $roleNow === 'other' ? 'selected' : ''; ?>>Other</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Status</label>
                  <?php $statusNow = strtolower((string)($status ?? '')); ?>
                  <select class="select compact w-full" name="status">
                    <option value="" <?= $statusNow === '' ? 'selected' : ''; ?>>All</option>
                    <option value="active" <?= $statusNow === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?= $statusNow === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="pending" <?= $statusNow === 'pending' ? 'selected' : ''; ?>>Pending</option>
                  </select>
                </div>
                <div class="flex items-center gap-2">
                  <button type="submit" class="btn-compact w-full">
                    <i class="mdi mdi-magnify"></i> Search
                  </button>
                  <?php if (!empty($q) || !empty($role) || !empty($status)): ?>
                    <a class="btn-compact" href="<?= site_url('users') ?>" style="background:#fff;border:1px solid var(--jm-gray-200);color:var(--jm-slate-500)">
                      <i class="mdi mdi-filter-remove-outline"></i> Clear
                    </a>
                  <?php endif; ?>
                </div>
              </form>
            </div>

            <div class="card p-4">
              <div class="overflow-x-auto">
                <table class="users">
                  <thead>
                    <tr>
                      <th style="min-width:220px"><?php $sort = $sort ?? 'created_at'; $dir = $dir ?? 'desc'; $params = (!empty($q) ? '&q=' . urlencode($q) : '') . (!empty($role) ? '&role=' . urlencode($role) : '') . (!empty($status) ? '&status=' . urlencode($status) : ''); ?>
                        <a href="<?= site_url('users') ?>?sort=first_name&dir=<?= $sort === 'first_name' && $dir === 'asc' ? 'desc' : 'asc' ?><?= $params ?>" style="text-decoration:none;color:inherit">User <?php if ($sort === 'first_name'): ?><i class="mdi mdi-arrow-<?= $dir === 'asc' ? 'up' : 'down' ?>"></i><?php endif; ?></a>
                      </th>
                      <th>
                        <a href="<?= site_url('users') ?>?sort=email&dir=<?= $sort === 'email' && $dir === 'asc' ? 'desc' : 'asc' ?><?= $params ?>" style="text-decoration:none;color:inherit">Email <?php if ($sort === 'email'): ?><i class="mdi mdi-arrow-<?= $dir === 'asc' ? 'up' : 'down' ?>"></i><?php endif; ?></a>
                      </th>
                      <th>
                        <a href="<?= site_url('users') ?>?sort=role&dir=<?= $sort === 'role' && $dir === 'asc' ? 'desc' : 'asc' ?><?= $params ?>" style="text-decoration:none;color:inherit">Role <?php if ($sort === 'role'): ?><i class="mdi mdi-arrow-<?= $dir === 'asc' ? 'up' : 'down' ?>"></i><?php endif; ?></a>
                      </th>
                      <th>Status</th>
                      <th>National ID</th>
                      <th class="col-actions" style="text-align:center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $qNow       = trim((string)($q ?? ''));
                    $roleNow    = strtolower((string)($role ?? ''));
                    $adminRoles = ['admin', 'tesda_admin', 'school_admin', 'peso', 'other'];
                    $hideAdmins = false;

                    ?>
                    <?php
                    if (!function_exists('avatar_url_safe')) {
                      function avatar_url_safe($raw)
                      {
                        $raw = trim((string)$raw);
                        if ($raw !== '' && preg_match('#^https?://#i', $raw)) return $raw;
                        if ($raw !== '') return base_url(str_replace('\\', '/', $raw));
                        return base_url('uploads/avatars/avatar.png');
                      }
                    }
                    ?>
                    <?php foreach (($users ?? []) as $u):
                      $ln = trim((string)($u->last_name   ?? ''));
                      $fn = trim((string)($u->first_name  ?? ''));
                      $full = ($ln !== '' || $fn !== '') ? ($ln . ($ln && $fn ? ', ' : '') . $fn) : '';
                      if ($full === '') $full = ($u->email ?? ('User #' . $u->id));
                      $isActive    = (int)$u->is_active === 1;
                      $roleLower   = strtolower((string)($u->role ?? ''));
                      $statusLower = strtolower((string)($u->status ?? ''));
                      $ava = avatar_url_safe($u->avatar ?? '');
                      $isPendingClient = in_array($roleLower, ['client', 'employer'], true) && $statusLower === 'pending';
                      $pillClass  = $isPendingClient ? 'pill-warn' : ($isActive ? 'pill-ok' : 'pill-bad');
                      $pillText   = $isPendingClient ? 'Pending' : ($isActive ? 'Active' : 'Inactive');
                      $profileUrl = ($roleLower === 'client' || $roleLower === 'employer')
                        ? site_url('profile/client/' . (int)$u->id)
                        : site_url('profile/worker/' . (int)$u->id);
                    ?>
                      <tr data-id="<?= (int)$u->id ?>">
                        <!-- User -->
                        <td data-th="User">
                          <div class="u-main">
                            <img class="u-ava" src="<?= htmlspecialchars($ava, ENT_QUOTES, 'UTF-8') ?>"
                              alt="avatar" onerror="this.onerror=null;this.src='<?= base_url('uploads/avatars/avatar.png') ?>'">
                            <div>
                              <div class="fw-semibold"><?= htmlspecialchars($full, ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                          </div>
                        </td>
                        <!-- Email -->
                        <td class="email" data-th="Email">
                          <?php if (filter_var((string)$u->email, FILTER_VALIDATE_EMAIL)): ?>
                            <a href="mailto:<?= htmlspecialchars($u->email, ENT_QUOTES, 'UTF-8') ?>">
                              <i class="mdi mdi-email-outline"></i><?= htmlspecialchars($u->email, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                          <?php else: ?>
                            <span class="font-medium"><?= htmlspecialchars($u->email, ENT_QUOTES, 'UTF-8') ?></span>
                          <?php endif; ?>
                        </td>

                        <!-- Role -->
                        <td data-th="Role">
                          <span class="pill pill-role">
                            <i class="mdi mdi-badge-account-outline"></i>
                            <?= strtoupper(htmlspecialchars($u->role, ENT_QUOTES, 'UTF-8')) ?>
                          </span>
                        </td>

                        <!-- Status -->
                        <td data-th="Status">
                          <span class="pill pill-status <?= $pillClass ?>">
                            <i class="mdi <?= $isPendingClient ? 'mdi-timer-sand' : ($isActive ? 'mdi-check-circle-outline' : 'mdi-close-circle-outline') ?>"></i>
                            <?= $pillText ?>
                          </span>
                        </td>

                        <!-- National ID (PhilSys) -->
                        <td data-th="National ID" class="td-psys">
                          <?php
                            $psStatus = (string)($u->philsys_status ?? '');
                            $psName   = trim((string)($u->philsys_name ?? ''));
                            $psTip    = $psName !== ''
                              ? 'ID: ' . $psName . (!empty($u->philsys_dob) ? ' · DOB ' . $u->philsys_dob : '')
                              : '';
                            $psMap = [
                              'verified' => ['pill-ok',   'mdi-shield-check',            'Verified'],
                              'pending'  => ['pill-warn', 'mdi-timer-sand',              'Pending'],
                              'failed'   => ['pill-bad',  'mdi-shield-alert-outline',    'Failed'],
                            ];
                          ?>
                          <?php if ($psStatus === ''): ?>
                            <span class="pill" style="color:var(--jm-slate-400);border-color:var(--jm-gray-200)"><i class="mdi mdi-minus"></i> —</span>
                          <?php else: ?>
                            <span class="pill pill-psys <?= $psMap[$psStatus][0] ?? '' ?>" title="<?= htmlspecialchars($psTip, ENT_QUOTES, 'UTF-8') ?>">
                              <i class="mdi <?= $psMap[$psStatus][1] ?? 'mdi-card-account-details-outline' ?>"></i>
                              <?= $psMap[$psStatus][2] ?? $psStatus ?>
                            </span>
                            <?php if (!empty($u->philsys_id_type)): ?>
                              <span style="font-size:.72rem;color:var(--jm-slate-400)"><?= htmlspecialchars($u->philsys_id_type, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <?php if ($psStatus === 'pending'): ?>
                              <span class="actbar" style="display:inline-flex;margin-left:6px">
                                <button type="button" class="icon-btn ok psys-act" data-id="<?= (int)$u->id ?>" data-action="verify" data-tip="Confirm ID match">
                                  <i class="mdi mdi-check"></i>
                                </button>
                                <button type="button" class="icon-btn bad psys-act" data-id="<?= (int)$u->id ?>" data-action="reject" data-tip="Reject ID">
                                  <i class="mdi mdi-close"></i>
                                </button>
                              </span>
                            <?php endif; ?>
                          <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td class="td-actions" data-th="Action">
                          <div class="actbar">
                            <?php if ($isPendingClient): ?>
                              <button type="button" class="icon-btn ok btn-approve-js" data-id="<?= (int)$u->id ?>" data-tip="Approve">
                                <i class="mdi mdi-check-decagram-outline"></i>
                              </button>
                              <button type="button" class="icon-btn info btn-resend-js" data-id="<?= (int)$u->id ?>" data-tip="Resend activation">
                                <i class="mdi mdi-send"></i>
                              </button>
                            <?php endif; ?>

                            <a class="icon-btn info" href="mailto:<?= htmlspecialchars($u->email, ENT_QUOTES, 'UTF-8') ?>" data-tip="Email">
                              <i class="mdi mdi-email-outline"></i>
                            </a>
                            <button type="button" class="icon-btn brand" data-copy="<?= htmlspecialchars($u->email, ENT_QUOTES, 'UTF-8') ?>" data-tip="Copy email">
                              <i class="mdi mdi-content-copy"></i>
                            </button>
                            <a class="icon-btn brand" href="<?= htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" data-tip="Open profile">
                              <i class="mdi mdi-open-in-new"></i>
                            </a>

                            <?php if (!$isPendingClient): ?>
                              <?php if ($isActive): ?>
                                <button type="button" class="icon-btn bad deactivate-only" data-id="<?= (int)$u->id ?>" data-tip="Deactivate">
                                  <i class="mdi mdi-account-cancel-outline"></i>
                                </button>
                              <?php else: ?>
                                <button type="button" class="icon-btn ok activate-only" data-id="<?= (int)$u->id ?>" data-tip="Activate">
                                  <i class="mdi mdi-account-check-outline"></i>
                                </button>
                              <?php endif; ?>
                            <?php endif; ?>

                            <!-- NEW: Hard Delete button -->
                            <button type="button"
                              class="icon-btn bad btn-delete-user"
                              data-id="<?= (int)$u->id ?>"
                              data-name="<?= htmlspecialchars($full, ENT_QUOTES, 'UTF-8') ?>"
                              data-tip="Delete user">
                              <i class="mdi mdi-trash-can-outline"></i>
                            </button>

                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>

                    <?php if (empty($users)): ?>
                      <tr>
                        <td colspan="6" class="text-center py-10">
                          <div style="font-size:48px;color:var(--jm-slate-300);margin-bottom:8px"><i class="mdi mdi-account-search-outline"></i></div>
                          <h5 style="font-weight:700;color:var(--jm-slate-600);margin-bottom:4px">No users found</h5>
                          <p style="color:var(--jm-slate-500);font-size:.9rem;margin-bottom:12px">Try adjusting your search or filters.</p>
                          <a href="<?= site_url('users') ?>" class="btn-silver" style="display:inline-flex;align-items:center;gap:6px">
                            <i class="mdi mdi-filter-remove-outline"></i> Clear filters
                          </a>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="flex items-center justify-between flex-wrap gap-3 mt-4 pt-3" style="border-top:1px solid var(--jm-gray-200)">
                  <div style="font-size:.85rem;color:var(--jm-slate-500);font-weight:600">
                    Showing <?= $pagination['from'] ?>–<?= $pagination['to'] ?> of <?= number_format($pagination['total']) ?> users
                  </div>
                  <div class="flex gap-1">
                    <?php
                      $cur   = (int)$pagination['page'];
                      $last  = (int)$pagination['total_pages'];

                      // Build URL for a given page, preserving active filters.
                      $mkUrl = function ($p) use ($q, $role, $status) {
                        $url = site_url('users') . '?page=' . $p;
                        if (!empty($q))      $url .= '&q=' . urlencode($q);
                        if (!empty($role))   $url .= '&role=' . urlencode($role);
                        if (!empty($status)) $url .= '&status=' . urlencode($status);
                        return $url;
                      };

                      // Windowed list of pages: 1 … (cur-1, cur, cur+1) … last
                      $window = 1;
                      $pages  = [];
                      for ($p = 1; $p <= $last; $p++) {
                        if ($p === 1 || $p === $last || ($p >= $cur - $window && $p <= $cur + $window)) {
                          $pages[] = $p;
                        }
                      }

                      $btnBase = 'display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;border-radius:8px;font-weight:700;font-size:.85rem;padding:0 8px';
                      $linkSt  = $btnBase . ';border:1px solid var(--jm-gray-200);background:#fff;color:var(--jm-slate-700);text-decoration:none';
                      $curSt   = $btnBase . ';background:var(--jm-primary);color:#fff';
                      $gapSt   = $btnBase . ';color:var(--jm-slate-400)';
                      $disSt   = $linkSt . ';opacity:.45;pointer-events:none';
                    ?>

                    <?php if ($cur > 1): ?>
                      <a href="<?= $mkUrl($cur - 1) ?>" style="<?= $linkSt ?>" aria-label="Previous"><i class="mdi mdi-chevron-left"></i></a>
                    <?php else: ?>
                      <span style="<?= $disSt ?>"><i class="mdi mdi-chevron-left"></i></span>
                    <?php endif; ?>

                    <?php $prev = 0; foreach ($pages as $p): ?>
                      <?php if ($prev && $p - $prev > 1): ?>
                        <span style="<?= $gapSt ?>">…</span>
                      <?php endif; ?>
                      <?php if ($p === $cur): ?>
                        <span style="<?= $curSt ?>"><?= $p ?></span>
                      <?php else: ?>
                        <a href="<?= $mkUrl($p) ?>" style="<?= $linkSt ?>"><?= $p ?></a>
                      <?php endif; ?>
                      <?php $prev = $p; endforeach; ?>

                    <?php if ($cur < $last): ?>
                      <a href="<?= $mkUrl($cur + 1) ?>" style="<?= $linkSt ?>" aria-label="Next"><i class="mdi mdi-chevron-right"></i></a>
                    <?php else: ?>
                      <span style="<?= $disSt ?>"><i class="mdi mdi-chevron-right"></i></span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>

            <div class="my-6 divider"></div>

          </div>
        </div>

        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <!-- Create Admin Modal -->
  <div id="create-admin-modal">
    <div class="cam-card">
      <div class="cam-head">
        <h3 class="cam-title">
          <span class="cam-badge"><i class="mdi mdi-shield-account-outline"></i></span>
          <span>Create Admin</span>
        </h3>
        <button type="button" id="btn-close-create-admin" class="icon-btn" aria-label="Close">
          <i class="mdi mdi-close"></i>
        </button>
      </div>

      <div class="cam-line"></div>

      <form id="create-admin-form" autocomplete="off">
        <div class="cam-grid">
          <div class="cam-field">
            <label class="cam-label">First Name</label>
            <div class="cam-ctl">
              <i class="mdi mdi-account-outline cam-ico"></i>
              <input type="text" name="first_name" class="cam-input input" required>
            </div>
          </div>

          <div class="cam-field">
            <label class="cam-label">Last Name</label>
            <div class="cam-ctl">
              <i class="mdi mdi-account-outline cam-ico"></i>
              <input type="text" name="last_name" class="cam-input input" required>
            </div>
          </div>
        </div>

        <div class="cam-field">
          <label class="cam-label">Email / Username</label>
          <div class="cam-ctl">
            <i class="mdi mdi-email-outline cam-ico"></i>
            <input type="text" name="email" class="cam-input input" required>
          </div>
          <div class="cam-help">For TESDA, School Admin, or PESO roles you may enter a username without an @ symbol.</div>
        </div>

        <!-- Role (admin, tesda_admin, school_admin, peso, other) -->
        <div class="cam-field">
          <label class="cam-label">Role</label>
          <div class="cam-ctl">
            <i class="mdi mdi-badge-account-outline cam-ico"></i>
            <select name="role" class="cam-input input" required style="padding-left:2.25rem">
              <option value="admin">Admin (default)</option>
              <option value="tesda_admin">TESDA Admin</option>
              <option value="school_admin">School Admin</option>
              <option value="peso">PESO</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="cam-help">Choose the correct admin/staff role.</div>
        </div>

        <div class="cam-field">
          <label class="cam-label">Password</label>
          <div class="cam-ctl">
            <i class="mdi mdi-lock-outline cam-ico"></i>
            <input type="password" name="password" class="cam-input input" minlength="6" required>
            <button type="button" class="cam-eye" data-eye="password"><i class="mdi mdi-eye-outline"></i></button>
          </div>
        </div>

        <div class="cam-field">
          <label class="cam-label">Confirm Password</label>
          <div class="cam-ctl">
            <i class="mdi mdi-lock-check-outline cam-ico"></i>
            <input type="password" name="confirm" class="cam-input input" minlength="6" required>
            <button type="button" class="cam-eye" data-eye="confirm"><i class="mdi mdi-eye-outline"></i></button>
          </div>
        </div>

        <div class="cam-line"></div>

        <div class="cam-actions">
          <button type="button" id="btn-cancel-create-admin" class="btn-silver">Cancel</button>
          <button type="submit" id="btn-submit-create-admin" class="btn-primary">
            <i class="mdi mdi-shield-account-outline"></i>
            <span>Create Admin</span>
          </button>
        </div>
      </form>
    </div>
  </div>
  <!-- Global loading overlay -->
  <div id="busy" class="busy" hidden>
    <div class="busy-card" role="status" aria-live="polite" aria-atomic="true">
      <span class="busy-spinner" aria-hidden="true"></span>
      <div class="busy-text" id="busy-text">Working…</div>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script>
    (function() {
      const URL_TOGGLE = '<?= site_url('users/toggle')  ?>';
      const URL_APPROVE = '<?= site_url('users/approve') ?>';
      const URL_RESEND = '<?= site_url('users/resend')  ?>';
      const URL_CREATE = '<?= site_url('users/create_admin') ?>';
      const URL_DELETE = '<?= site_url('users/delete') ?>'; // NEW
      const URL_PSYS   = '<?= site_url('philsys/review') ?>';

      // CSRF helpers
      const metaName = document.querySelector('meta[name="csrf-token-name"]');
      const metaHash = document.querySelector('meta[name="csrf-token-hash"]');
      const getCSRF = () => ({
        name: metaName.getAttribute('content'),
        hash: metaHash.getAttribute('content')
      });
      const setCSRF = (n, h) => {
        if (n && h) {
          metaName.setAttribute('content', n);
          metaHash.setAttribute('content', h);
        }
      };
      // Global busy overlay helpers
      const busyEl = document.getElementById('busy');
      const busyText = document.getElementById('busy-text');

      function showBusy(text) {
        if (busyEl) {
          if (busyText) busyText.textContent = text || 'Working…';
          busyEl.hidden = false;
          document.body.style.cursor = 'wait';
        }
      }

      function hideBusy() {
        if (busyEl) {
          busyEl.hidden = true;
          document.body.style.cursor = '';
        }
      }

      async function post(url, payload) {
        const csrf = getCSRF();
        const fd = new FormData();
        Object.entries(payload || {}).forEach(([k, v]) => fd.append(k, v));
        fd.append(csrf.name, csrf.hash);
        const res = await fetch(url, {
          method: 'POST',
          body: fd,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const json = await res.json().catch(() => ({}));
        if (json && json.csrf_name && json.csrf_hash) setCSRF(json.csrf_name, json.csrf_hash);
        if (!res.ok || json.ok === false) throw new Error(json.msg || 'Request failed');
        return json;
      }

      async function copy(text) {
        try {
          await navigator.clipboard.writeText(text);
        } catch {
          alert('Copied: ' + text);
        }
      }

      function setStatusPillTo(tr, state) {
        const pill = tr.querySelector('.pill-status');
        if (!pill) return;
        pill.classList.remove('pill-ok', 'pill-bad', 'pill-warn');
        if (state === 'active') {
          pill.classList.add('pill-ok');
          pill.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Active';
        } else if (state === 'inactive') {
          pill.classList.add('pill-bad');
          pill.innerHTML = '<i class="mdi mdi-close-circle-outline"></i> Inactive';
        } else {
          pill.classList.add('pill-warn');
          pill.innerHTML = '<i class="mdi mdi-timer-sand"></i> Pending';
        }
      }

      function toActivateButton(btn) {
        btn.classList.remove('bad', 'deactivate-only');
        btn.classList.add('ok', 'activate-only');
        btn.setAttribute('data-tip', 'Activate');
        btn.innerHTML = '<i class="mdi mdi-account-check-outline"></i>';
      }

      function toDeactivateButton(btn) {
        btn.classList.remove('ok', 'activate-only');
        btn.classList.add('bad', 'deactivate-only');
        btn.setAttribute('data-tip', 'Deactivate');
        btn.innerHTML = '<i class="mdi mdi-account-cancel-outline"></i>';
      }

      // '/' keyboard shortcut to focus search
      document.addEventListener('keydown', function(e) {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'SELECT' && document.activeElement.tagName !== 'TEXTAREA') {
          e.preventDefault();
          const searchInput = document.querySelector('input[name="q"]');
          if (searchInput) searchInput.focus();
        }
      });

      // Create Admin modal wiring
      const modal = document.getElementById('create-admin-modal');
      const openBtn = document.getElementById('btn-open-create-admin');
      const xBtn = document.getElementById('btn-close-create-admin');
      const cancel = document.getElementById('btn-cancel-create-admin');
      const form = document.getElementById('create-admin-form');
      const submit = document.getElementById('btn-submit-create-admin');

      const toggleModal = (show) => {
        if (modal) modal.style.display = show ? 'flex' : 'none';
      };

      openBtn && openBtn.addEventListener('click', () => toggleModal(true));
      xBtn && xBtn.addEventListener('click', () => toggleModal(false));
      cancel && cancel.addEventListener('click', () => toggleModal(false));
      modal && modal.addEventListener('click', (e) => {
        if (e.target === modal) toggleModal(false);
      });

      document.querySelectorAll('.cam-eye').forEach(btn => {
        btn.addEventListener('click', () => {
          const which = btn.getAttribute('data-eye'); // "password" or "confirm"
          const inp = form?.elements[which];
          if (!inp) return;
          const isPw = inp.type === 'password';
          inp.type = isPw ? 'text' : 'password';
          btn.innerHTML = `<i class="mdi ${isPw ? 'mdi-eye-off-outline' : 'mdi-eye-outline'}"></i>`;
        });
      });
      form && form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const fn = form.first_name.value.trim();
        const ln = form.last_name.value.trim();
        const em = form.email.value.trim();
        const role = (form.role?.value || 'admin').trim().toLowerCase();
        const pw = form.password.value;
        const pw2 = form.confirm.value;

        if (!fn || !ln || !em || !pw || !pw2 || !role) {
          JM.alert('Please complete the form.', 'warning', 'Incomplete form');
          return;
        }
        if (pw !== pw2) {
          JM.alert('Passwords do not match.', 'error', 'Mismatch');
          return;
        }
        if (pw.length < 8) {
          JM.alert('Password must be at least 8 characters.', 'warning', 'Too short');
          return;
        }

        submit && (submit.disabled = true);
        showBusy('Creating admin & sending email…');

        try {
          const res = await post(URL_CREATE, {
            first_name: fn,
            last_name: ln,
            email: em,
            role: role,
            password: pw,
            confirm: pw2
          });
          JM.toast(res.msg || 'Admin account created.', 'success');
          setTimeout(function() { location.reload(); }, 1500);
        } catch (err) {
          JM.alert(err.message || 'Failed to create admin.', 'error', 'Failed');
        } finally {
          hideBusy();
          submit && (submit.disabled = false);
        }
      });


      // Table actions
      document.addEventListener('click', async (ev) => {
        const copyBtn = ev.target.closest('[data-copy]');
        if (copyBtn) {
          copy(copyBtn.getAttribute('data-copy') || '');
          return;
        }

        const psysBtn = ev.target.closest('button.psys-act');
        if (psysBtn) {
          const tr = psysBtn.closest('tr');
          const id = parseInt(psysBtn.getAttribute('data-id'), 10);
          const action = psysBtn.getAttribute('data-action');
          const isVerify = action === 'verify';
          JM.confirm({
            title: isVerify ? 'Confirm National ID?' : 'Reject National ID?',
            text: isVerify
              ? 'Confirm the name and birthdate on the scanned ID match this account holder, then mark it verified.'
              : 'Flag this scanned National ID as failed. The user can rescan a valid ID.',
            danger: !isVerify,
            confirmText: isVerify ? 'Yes, verify' : 'Yes, reject'
          }).then(function(result) {
            if (!result) return;
            post(URL_PSYS, { user_id: id, action: action }).then(function(res) {
              JM.toast(res.msg || 'Updated.', 'success');
              const cell = tr && tr.querySelector('.td-psys');
              if (cell) {
                const ok = res.status === 'verified';
                cell.innerHTML =
                  '<span class="pill pill-psys ' + (ok ? 'pill-ok' : 'pill-bad') + '">' +
                  '<i class="mdi ' + (ok ? 'mdi-shield-check' : 'mdi-shield-alert-outline') + '"></i> ' +
                  (ok ? 'Verified' : 'Failed') + '</span>';
              }
            }).catch(function(e) {
              JM.alert(e.message || 'Update failed', 'error', 'Failed');
            });
          });
          return;
        }

        const deBtn = ev.target.closest('button.deactivate-only');
        if (deBtn) {
          const tr = deBtn.closest('tr');
          const id = parseInt(tr.getAttribute('data-id'), 10);
          JM.confirm({
            title: 'Deactivate user?',
            text: 'This user will lose access immediately.',
            danger: true,
            confirmText: 'Yes, deactivate'
          }).then(function(result) {
            if (!result) return;
            deBtn.disabled = true;
            post(URL_TOGGLE, { id, active: '0' }).then(function() {
              setStatusPillTo(tr, 'inactive');
              toActivateButton(deBtn);
            }).catch(function(e) {
              JM.alert(e.message || 'Failed to deactivate', 'error', 'Failed');
            }).finally(function() { deBtn.disabled = false; });
          });
          return;
        }

        const actBtn = ev.target.closest('button.activate-only');
        if (actBtn) {
          const tr = actBtn.closest('tr');
          const id = parseInt(tr.getAttribute('data-id'), 10);
          JM.confirm({
            title: 'Activate user?',
            text: 'This user will regain access.',
            icon: 'success',
            confirmText: 'Yes, activate'
          }).then(function(result) {
            if (!result) return;
            actBtn.disabled = true;
            post(URL_TOGGLE, { id, active: '1' }).then(function() {
              setStatusPillTo(tr, 'active');
              toDeactivateButton(actBtn);
            }).catch(function(e) {
              JM.alert(e.message || 'Failed to activate', 'error', 'Failed');
            }).finally(function() { actBtn.disabled = false; });
          });
          return;
        }

        const approveBtn = ev.target.closest('.btn-approve-js');
        if (approveBtn) {
          const tr = approveBtn.closest('tr');
          const id = parseInt(tr.getAttribute('data-id'), 10);
          JM.confirm({
            title: 'Approve user?',
            text: 'This will manually approve the user account.',
            icon: 'success',
            confirmText: 'Yes, approve'
          }).then(function(result) {
            if (!result) return;
            approveBtn.disabled = true;
            post(URL_APPROVE, { id }).then(function() {
              setStatusPillTo(tr, 'active');
              tr.querySelectorAll('.btn-approve-js, .btn-resend-js').forEach(function(b) { b.remove(); });
              var bar = tr.querySelector('.actbar');
              var btn = document.createElement('button');
              btn.type = 'button';
              btn.className = 'icon-btn bad deactivate-only';
              btn.dataset.id = String(id);
              btn.setAttribute('data-tip', 'Deactivate');
              btn.innerHTML = '<i class="mdi mdi-account-cancel-outline"></i>';
              bar.appendChild(btn);
            }).catch(function(e) {
              JM.alert(e.message || 'Approve failed', 'error', 'Failed');
            }).finally(function() { approveBtn.disabled = false; });
          });
          return;
        }

        const resendBtn = ev.target.closest('.btn-resend-js');
        if (resendBtn) {
          const tr = resendBtn.closest('tr');
          const id = parseInt(tr.getAttribute('data-id'), 10);
          resendBtn.disabled = true;
          post(URL_RESEND, { id }).then(function(res) {
            // The endpoint no longer returns activation links (bearer-token leak fix).
            JM.toast(res.msg || 'Activation email sent', 'success');
          }).catch(function(e) {
            JM.alert(e.message || 'Send failed', 'error', 'Failed');
          }).finally(function() { resendBtn.disabled = false; });
          return;
        }

        // NEW: hard delete handler
        const delBtn = ev.target.closest('.btn-delete-user');
        if (delBtn) {
          const tr = delBtn.closest('tr');
          const id = parseInt(tr.getAttribute('data-id'), 10);
          const name = delBtn.getAttribute('data-name') || ('User #' + id);

          JM.confirm({
            title: 'Permanently delete ' + name + '?',
            text: 'This cannot be undone. All related records will be removed.',
            danger: true,
            confirmText: 'Yes, delete',
            requireText: 'DELETE'
          }).then(function(result) {
            if (!result) return;
            delBtn.disabled = true;
            post(URL_DELETE, { id }).then(function(res) {
              JM.toast(res.msg || 'User deleted permanently.', 'success');
              tr.style.opacity = 0.25;
              setTimeout(function() { tr.remove(); }, 180);
            }).catch(function(e) {
              JM.alert(e.message || 'Delete failed', 'error', 'Failed');
            }).finally(function() { delBtn.disabled = false; });
          });
          return;
        }
      });
    })();
  </script>

</body>

</html>