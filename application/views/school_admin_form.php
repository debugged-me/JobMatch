<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php
    // Safe defaults
    $user       = isset($user) ? $user : null;
    $isEdit     = is_object($user) && !empty($user->id);
    $page_title = $page_title ?? ($isEdit ? 'Edit Worker' : 'Create Worker');
  ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.1.1') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root{--ink:#0f172a;--muted:#6b7280;--line:#e5e7eb;--primary:#2563eb;--primary-700:#2563eb;--hover:rgba(2,6,23,.03)}
    body{font-family:"Karla",system-ui,-apple-system,"Segoe UI",Roboto,Arial;background:#f8fafc;color:var(--ink)}
    .admin-header{position:sticky;top:0;z-index:40;background:#fff;border-bottom:1px solid var(--line)}
    .card{background:#fff;border:1px solid var(--line);border-radius:18px;box-shadow:0 1px 2px rgba(0,0,0,.04)}
    .btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:12px;padding:.6rem 1rem;font-weight:700;border:1px solid transparent;transition:all .15s ease}
    .btn-primary{background:var(--primary);border-color:var(--primary);color:#fff}
    .btn-primary:hover{background:var(--primary-700);border-color:var(--primary-700)}
    .btn-ghost{background:#fff;border:1px solid var(--line);color:#111827}
    .form-control{border-radius:12px}
    .muted{color:#64748b}
    .hl{height:1px;background:var(--line)}
    .grid-2{display:grid;grid-template-columns:1fr;gap:1rem}
    @media(min-width:768px){.grid-2{grid-template-columns:1fr 1fr}}
    .input-group{display:flex;align-items:stretch}
    .input-group .form-control{border-top-right-radius:0;border-bottom-right-radius:0}
    .input-group-append .btn{border-top-left-radius:0;border-bottom-left-radius:0}
  </style>
</head>
<body>
<div class="container-scroller">
  <?php $this->load->view('includes_nav'); ?>
  <div class="container-fluid page-body-wrapper">
    <?php $this->load->view('includes_nav_top'); ?>
    <div class="main-panel">
      <div class="content-wrapper pb-0">
        <div class="px-4 md:px-8 max-w-3xl mx-auto">
          <div class="admin-header">
            <div class="py-4 flex items-center justify-between">
              <h1 class="text-2xl md:text-3xl font-bold"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
              <a class="btn btn-ghost" href="<?= site_url('school-admin/workers') ?>"><i class="mdi mdi-arrow-left"></i> Back</a>
            </div>
          </div>

          <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
              <?= $this->session->flashdata('success'); ?>
              <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
          <?php endif; ?>

          <?php if ($this->session->flashdata('danger')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
              <?= $this->session->flashdata('danger'); ?>
              <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
          <?php endif; ?>

          <?php
            $action = $isEdit ? base_url('school-admin/update/' . (int)$user->id)
                              : base_url('school-admin/store');

            $val = function($key, $fallback = '') use ($user) {
              $sv = set_value($key);
              if ($sv !== '') return $sv;
              if (is_object($user) && isset($user->$key)) return $user->$key;
              return $fallback;
            };
            $selected = function($current, $option) {
              return ((string)$current === (string)$option) ? 'selected' : '';
            };
          ?>

          <form method="post" action="<?= $action; ?>" class="card p-6 mt-6" autocomplete="off" novalidate>
            <!-- Names / Email / Phone -->
            <div class="grid-2">
              <div>
                <label class="font-semibold text-sm mb-1 block">First Name</label>
                <input type="text" name="first_name" class="form-control" required
                  value="<?= htmlspecialchars($val('first_name'), ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div>
                <label class="font-semibold text-sm mb-1 block">Last Name</label>
                <input type="text" name="last_name" class="form-control" required
                  value="<?= htmlspecialchars($val('last_name'), ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div>
                <label class="font-semibold text-sm mb-1 block">Email</label>
                <input type="email" name="email" class="form-control" required
                  value="<?= htmlspecialchars($val('email'), ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div>
                <label class="font-semibold text-sm mb-1 block">Phone</label>
                <input type="text" name="phone" class="form-control"
                  value="<?= htmlspecialchars($val('phone'), ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <!-- Passwords -->
            <div class="hl my-6"></div>
            <div class="grid-2">
              <div>
                <label class="font-semibold text-sm mb-1 block">
                  Password <?= $isEdit ? '<span class="muted">(leave blank to keep current)</span>' : '' ?>
                </label>
                <div class="input-group">
                  <input type="password" name="password" id="password" class="form-control"
                         <?= $isEdit ? '' : 'required' ?> minlength="8" autocomplete="new-password">
                  <div class="input-group-append">
                    <button class="btn btn-ghost" type="button" id="togglePw">Show</button>
                  </div>
                </div>
              </div>
              <div>
                <label class="font-semibold text-sm mb-1 block">Confirm Password</label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control"
                       <?= $isEdit ? '' : 'required' ?> minlength="8" autocomplete="new-password">
              </div>
            </div>

            <!-- Role (fixed), Active, Status, Visibility -->
            <div class="hl my-6"></div>
            <div class="grid-2">
              <div>
                <label class="font-semibold text-sm mb-1 block">Role</label>
                <input type="hidden" name="role" value="worker">
                <input type="text" class="form-control" value="worker" readonly>
              </div>

              <div>
                <label class="font-semibold text-sm mb-1 block">Active</label>
                <?php $iaVal = $val('is_active', $isEdit ? (string)($user->is_active ?? '1') : '1'); ?>
                <select name="is_active" class="form-control">
                  <option value="1" <?= $selected($iaVal, '1') ?>>Yes</option>
                  <option value="0" <?= $selected($iaVal, '0') ?>>No</option>
                </select>
              </div>

              <div>
                <label class="font-semibold text-sm mb-1 block">Status</label>
                <?php $stVal = $val('status', $isEdit ? ($user->status ?? 'active') : 'active'); ?>
                <select name="status" class="form-control">
                  <option value="active"   <?= $selected($stVal, 'active') ?>>active</option>
                  <option value="inactive" <?= $selected($stVal, 'inactive') ?>>inactive</option>
                </select>
              </div>

              <div>
                <label class="font-semibold text-sm mb-1 block">Visibility</label>
                <?php $visVal = $val('visibility', $isEdit ? ($user->visibility ?? 'private') : 'private'); ?>
                <select name="visibility" class="form-control">
                  <option value="private" <?= $selected($visVal, 'private') ?>>private</option>
                  <option value="public"  <?= $selected($visVal, 'public')  ?>>public</option>
                </select>
              </div>
            </div>

            <div class="mt-6 flex flex-col md:flex-row gap-2">
              <button class="btn btn-primary" type="submit">
                <i class="mdi mdi-content-save"></i>
                <?= $isEdit ? 'Save Changes' : 'Create &amp; Send Email' ?>
              </button>
              <a class="btn btn-ghost" href="<?= site_url('school-admin/workers') ?>">Cancel</a>
            </div>
          </form>

          <div class="my-8 hl"></div>
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
  (function(){
    var pw = document.getElementById('password');
    var pc = document.getElementById('password_confirm');
    var t  = document.getElementById('togglePw');
    if (t && pw) {
      t.addEventListener('click', function(){
        var type = pw.type === 'password' ? 'text' : 'password';
        pw.type = type;
        if (pc) pc.type = type;
        t.textContent = (type === 'password') ? 'Show' : 'Hide';
      });
    }
  })();
</script>
</body>
</html>


