<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$page_title = 'Pending Users';
$users = is_array($users ?? null) ? $users : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view('partials/head', ['page_title' => $page_title]); ?>
  <style>
    body { font-family: "Karla", ui-sans-serif; background: #f9fafb; }
    .app { max-width: 960px; margin: 0 auto; padding: 0 12px; }
    .panel { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px; }
    table { width:100%; border-collapse:collapse; }
    th, td { text-align:left; padding:10px 8px; border-bottom:1px solid #eef2f7; font-size:14px; }
    th { font-weight:700; color:#374151; }
    .btn-approve { display:inline-flex; align-items:center; gap:6px; background:#c1272d; color:#fff;
      border:0; border-radius:8px; padding:7px 14px; font-size:13px; font-weight:600; cursor:pointer; }
    .btn-approve:hover { background:#a51f24; }
    .empty { padding:24px; text-align:center; color:#6b7280; }
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
            <div class="panel">
              <h1 style="font-size:20px;margin:0 0 16px">Pending Users</h1>
              <?php if (empty($users)): ?>
                <p class="empty">No pending users right now.</p>
              <?php else: ?>
                <div style="overflow-x:auto">
                  <table>
                    <thead>
                      <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                      <tr>
                        <td><?= (int) $u->id ?></td>
                        <td><?= htmlspecialchars(trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u->email ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucfirst((string)($u->role ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                          <form method="post" action="<?= site_url('admin/approve/' . (int)$u->id) ?>" style="display:inline" onsubmit="return confirm('Approve this account?');">
                            <button type="submit" class="btn-approve"><i class="mdi mdi-check"></i> Approve</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>
</body>
</html>
