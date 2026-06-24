<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title ?? 'JobMatch', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.6') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --ink: #0f172a;
      --muted: #6b7280;
      --line: #e5e7eb;
      --card: #fff;
      --indigo-200: #c7d2fe;
      --indigo-300: #a5b4fc;
      --indigo-400: #818cf8;
      --indigo-500: #6366f1;
      --blue-focus: #1e3a8a;
      --shadow-1: 0 6px 18px rgba(2, 6, 23, .06), 0 1px 0 rgba(2, 6, 23, .04);
      --shadow-2: 0 16px 36px rgba(2, 6, 23, .12), 0 3px 10px rgba(2, 6, 23, .08)
    }

    body {
      background: #f6f7fb;
      color: var(--ink);
      font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial
    }

    .content-wrapper {
      padding-top: 1rem
    }

    .app {
      max-width: 1120px;
      margin: 0 auto;
      padding: 0 16px
    }

    .eyebrow {
      font-size: .85rem;
      color: var(--muted);
      font-weight: 600;
      letter-spacing: .3px
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      padding: .5rem .9rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: .9rem;
      transition: all .18s ease
    }

    .btn i {
      font-size: 1.05rem
    }

    .btn-brand {
      background: var(--indigo-500);
      border: 1px solid var(--indigo-500);
      color: #fff
    }

    .btn-brand:hover {
      background: var(--indigo-400);
      border-color: var(--indigo-400)
    }

    .btn-light {
      background: #fff;
      border: 1px solid var(--line);
      color: var(--ink)
    }

    .btn-light:hover {
      background: #f1f5f9
    }

    .form-control,
    select.form-control {
      width: 100%;
      background: #fff;
      border: 1px solid #111827;
      border-radius: 10px;
      padding: .70rem .9rem;
      font-size: 1rem;
      transition: border-color .18s ease, box-shadow .18s ease
    }

    .form-control:focus,
    select.form-control:focus {
      outline: 0;
      border-color: var(--blue-focus);
      box-shadow: 0 0 0 3px rgba(43, 77, 165, .15)
    }

    .form-text {
      color: var(--muted)
    }

    .card-flat {
      background: var(--card);
      border: 1px solid var(--indigo-200);
      border-radius: 14px;
      box-shadow: var(--shadow-1);
      transition: transform .16s ease, box-shadow .18s ease, border-color .18s ease
    }

    .card-flat:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-2);
      border-color: var(--indigo-400)
    }

    .stat {
      display: flex;
      align-items: center;
      gap: .75rem;
      padding: 14px
    }

    .stat .icon {
      font-size: 22px;
      opacity: .9
    }

    .stat .label {
      color: var(--muted);
      font-size: .85rem
    }

    .stat .val {
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--ink)
    }

    .pgrid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 18px
    }

    .p-card {
      overflow: hidden;
      display: flex;
      flex-direction: column
    }

    .p-cover {
      position: relative;
      height: 160px;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: inset 0 -12px 22px rgba(2, 6, 23, .05);
      overflow: hidden
    }

    .p-cover.hasimg {
      background-size: cover;
      background-position: center;
      box-shadow: inset 0 -38px 54px rgba(2, 6, 23, .18)
    }

    .p-overlay {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding: 14px;
      background: linear-gradient(to top, rgba(15, 23, 42, .55), rgba(15, 23, 42, .15) 45%, rgba(15, 23, 42, 0));
      color: #fff;
      text-decoration: none;
      font-weight: 700;
      gap: .5rem;
      opacity: 0;
      transform: translateY(6px);
      transition: opacity .18s ease, transform .18s ease
    }

    .p-overlay .tag {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .35rem .6rem;
      border-radius: 9999px;
      background: rgba(255, 255, 255, .12);
      backdrop-filter: saturate(140%);
      font-weight: 700
    }

    .p-overlay .more {
      font-style: normal;
      font-weight: 600;
      opacity: .9;
      margin-left: .25rem;
      background: rgba(255, 255, 255, .18);
      padding: .05rem .4rem;
      border-radius: 9999px;
      font-size: .8rem
    }

    .p-card:hover .p-overlay {
      opacity: 1;
      transform: translateY(0)
    }

    .p-overlay:focus {
      outline: 2px solid #fff;
      outline-offset: -2px;
      opacity: 1
    }

    .section {
      padding: 16px
    }

    .badge-soft {
      display: inline-flex;
      align-items: center;
      border-radius: 9999px;
      padding: .2rem .55rem;
      font-weight: 600;
      font-size: .8rem;
      border: 1px solid var(--indigo-200);
      background: #eef2ff;
      color: #3730a3;
      transition: all .18s ease
    }

    .badge-muted {
      border-color: #cbd5e1;
      background: #f1f5f9;
      color: #334155
    }

    .chips {
      display: flex;
      flex-wrap: wrap;
      gap: .4rem
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .28rem .6rem;
      border-radius: 9999px;
      border: 1px solid var(--line);
      background: #fff;
      font-size: .8rem;
      color: #334155;
      text-decoration: none;
      transition: all .18s ease;
      box-shadow: 0 2px 6px rgba(2, 6, 23, .06)
    }

    .chip:hover {
      border-color: var(--indigo-300);
      color: #1f2937
    }

    .meta {
      color: #94a3b8;
      font-size: .85rem
    }

    .table.align-middle td,
    .table.align-middle th {
      vertical-align: middle;
    }

    .table thead.bg-light th {
      border-bottom: 1px solid var(--line);
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

            <div class="mb-3">
              <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
              <div class="alert alert-success d-flex align-items-center"><i class="mdi mdi-check-circle-outline me-2"></i><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
              <div class="alert alert-danger d-flex align-items-center"><i class="mdi mdi-alert-circle-outline me-2"></i><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>

            <?php
            if (!function_exists('is_img')) {
              function is_img($f)
              {
                $ext = strtolower(pathinfo(is_string($f) ? $f : '', PATHINFO_EXTENSION));
                return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
              }
            }

            if (!function_exists('first_img')) {
              function first_img($arr)
              {
                if (!is_array($arr)) return null;
                foreach ($arr as $f) {
                  if (is_img($f)) return $f;
                }
                return null;
              }
            }

            if (!function_exists('first_file')) {
              function first_file($arr)
              {
                if (!is_array($arr) || empty($arr)) return null;
                foreach ($arr as $f) {
                  if (is_string($f) && $f !== '') return $f;
                }
                return null;
              }
            }

            if (!function_exists('file_label')) {
              function file_label($f)
              {
                if (!$f) return '';
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) return 'View image';
                if ($ext === 'pdf') return 'Open PDF';
                return 'Open file';
              }
            }

            if (!function_exists('file_icon')) {
              function file_icon($f)
              {
                if (!$f) return 'mdi-file-outline';
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) return 'mdi-eye-outline';
                if ($ext === 'pdf') return 'mdi-file-pdf-box';
                return 'mdi-file-outline';
              }
            }

            if (!function_exists('chip')) {
              function chip($f)
              {
                $ext = strtolower(pathinfo(is_string($f) ? $f : '', PATHINFO_EXTENSION));
                $ico = ($ext === 'pdf')
                  ? 'mdi-file-pdf-box'
                  : (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? 'mdi-image-outline' : 'mdi-file-outline');

                $absUrl   = preg_match('#^https?://#i', $f) ? $f : base_url($f);
                $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                $fParam   = ltrim($pathOnly, '/');
                $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                  $fParam = substr($fParam, strlen($basePath) + 1);
                }
                $viewerUrl = site_url('media/preview') . '?f=' . rawurlencode($fParam);

                return '<a class="chip" href="' . $viewerUrl . '" target="_blank" title="' . html_escape(basename($f)) . '"><i class="mdi ' . $ico . '"></i>' . html_escape(basename($f)) . '</a>';
              }
            }

            if (!function_exists('cert_norm')) {
              function cert_norm($item)
              {
                if (is_array($item)) {
                  $p = (string)($item['path'] ?? '');
                  $t = trim((string)($item['title'] ?? ''));
                  if ($t === '' && $p !== '') $t = pathinfo($p, PATHINFO_FILENAME);
                  return ['path' => $p, 'title' => $t];
                }
                $p = (string)$item;
                return ['path' => $p, 'title' => pathinfo($p, PATHINFO_FILENAME)];
              }
            }

            if (!function_exists('chip_cert')) {
              function chip_cert($item)
              {
                $n   = cert_norm($item);
                $p   = $n['path'];
                $ttl = ($n['title'] !== '') ? $n['title'] : basename($p);

                $absUrl   = preg_match('#^https?://#i', $p) ? $p : base_url($p);
                $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                $fParam   = ltrim($pathOnly, '/');
                $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                  $fParam = substr($fParam, strlen($basePath) + 1);
                }
                $viewerUrl = site_url('media/preview') . '?f=' . rawurlencode($fParam);

                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                $ico = ($ext === 'pdf') ? 'mdi-file-pdf-box'
                  : (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? 'mdi-image-outline' : 'mdi-file-outline');

                return '<a class="chip" href="' . $viewerUrl . '" target="_blank" title="' . html_escape($ttl) . '"><i class="mdi ' . $ico . '"></i>' . html_escape($ttl) . '</a>';
              }
            }
            ?>



            <div class="d-flex align-items-center gap-2 mb-3">
              <span class="text-muted ms-2">Showcase your past work, this will be visible to the clients.</span>
              <?php if ($mode === 'list'): ?>
                <a class="btn btn-brand ms-auto" href="<?= site_url('portfolio/create') ?>"><i class="mdi mdi-plus"></i> Add Item</a>
              <?php else: ?>
              <?php endif; ?>
            </div>

            <?php if ($mode === 'list'): ?>

              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <div class="card-flat stat">
                    <i class="mdi mdi-briefcase-check-outline icon text-success"></i>
                    <div>
                      <div class="label">Times Hired</div>
                      <div class="val"><?= (int)$times_hired ?></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card-flat stat">
                    <i class="mdi mdi-star-circle-outline icon text-warning"></i>
                    <div>
                      <div class="label">Average Rating</div>
                      <div class="val d-inline-block me-2"><?= number_format($reviews['avg'] ?? 0, 2) ?></div>
                      <span class="meta">
                        <?php $avg = (float)($reviews['avg'] ?? 0);
                        for ($i = 1; $i <= 5; $i++) {
                          echo '<i class="mdi mdi-star' . ($avg >= $i ? '' : '-outline') . ' text-warning"></i>';
                        } ?>
                      </span>
                      <div class="label mt-1"><?= (int)($reviews['count'] ?? 0) ?> review<?= ((int)($reviews['count'] ?? 0) === 1 ? '' : 's') ?></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card-flat stat">
                    <i class="mdi mdi-folder-multiple-image icon text-info"></i>
                    <div>
                      <div class="label">Portfolio Items</div>
                      <div class="val"><?= is_array($items ?? []) ? count($items) : 0 ?></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card-flat section mb-3">
                <div class="d-flex align-items-center mb-2">
                  <h6 class="mb-0">Certificates</h6>
                  <a class="btn btn-light btn-sm ms-auto" href="<?= site_url('portfolio/certificates') ?>">
                    <i class="mdi mdi-upload"></i> Upload / Manage
                  </a>
                </div>

                <?php if (empty($certs)): ?>
                  <div class="text-muted">No certificates uploaded yet.</div>
                <?php else: ?>
                  <?php
                  $tiles = [];
                  foreach ($certs as $it) {
                    $n = cert_norm($it);
                    $p = $n['path'] ?? '';
                    if ($p === '') continue;

                    $absUrl   = preg_match('#^https?://#i', $p) ? $p : base_url($p);
                    $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                    $fParam   = ltrim($pathOnly, '/');
                    $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                    if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                      $fParam = substr($fParam, strlen($basePath) + 1);
                    }
                    $viewerUrl = site_url('media/preview') . '?f=' . rawurlencode($fParam);

                    $ext   = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                    $title = ($n['title'] ?? '') !== '' ? $n['title'] : pathinfo($p, PATHINFO_FILENAME);

                    $tiles[] = [
                      'viewer' => $viewerUrl,
                      'abs'    => $absUrl,
                      'isImg'  => $isImg,
                      'title'  => $title,
                    ];
                  }
                  ?>

                  <?php if (empty($tiles)): ?>
                    <div class="text-muted">No certificates uploaded yet.</div>
                  <?php else: ?>
                    <div class="row g-2 mb-2">
                      <?php foreach ($tiles as $t): ?>
                        <div class="col-6 col-md-3">
                          <a href="<?= htmlspecialchars($t['viewer'], ENT_QUOTES, 'UTF-8') ?>"
                            target="_blank" rel="noopener"
                            class="d-block text-decoration-none">
                            <div class="border rounded-3 overflow-hidden ratio ratio-4x3" style="background:#0b1220">
                              <?php if ($t['isImg']): ?>
                                <img src="<?= htmlspecialchars($t['abs'], ENT_QUOTES, 'UTF-8') ?>"
                                  alt="<?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?>"
                                  class="w-100 h-100 object-fit-cover">
                              <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                  <i class="mdi mdi-file-pdf-box" style="font-size:56px;color:#ef4444"></i>
                                </div>
                              <?php endif; ?>
                            </div>
                          </a>
                          <div class="fw-semibold mt-2 text-truncate"
                            title="<?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>


              </div>

              <?php if (empty($items)): ?>
                <div class="card-flat section text-center">
                  <i class="mdi mdi-folder-outline" style="font-size:42px;color:#94a3b8"></i>
                  <p class="mb-2">No portfolio items yet</p>
                  <a class="btn btn-light" href="<?= site_url('portfolio/create') ?>"><i class="mdi mdi-plus"></i> Add your first item</a>
                </div>
              <?php else: ?>
                <div class="pgrid">
                  <?php foreach ($items as $it):
                    $cover = first_img($it['files'] ?? []);
                    $first = first_file($it['files'] ?? []);
                    $label = file_label($first);
                    $icon  = file_icon($first);
                    $more  = max(0, count($it['files'] ?? []) - 1);
                  ?>
                    <div class="card-flat p-card">
                      <div class="p-cover <?= $cover ? 'hasimg' : '' ?>" style="<?= $cover ? 'background-image:url(' . htmlspecialchars(base_url($cover), ENT_QUOTES, 'UTF-8') . ')' : '' ?>">
                        <?php if (!$cover): ?><i class="mdi mdi-image-multiple-outline" style="font-size:40px;color:#cbd5e1"></i><?php endif; ?>
                        <?php if (!empty($first)): ?>
                          <a class="p-overlay" href="<?= site_url('media/preview?f=' . rawurlencode($first)) ?>" target="_blank" rel="noopener" aria-label="<?= html_escape($label) ?>">
                            <span class="tag"><i class="mdi <?= $icon ?>"></i> <?= html_escape($label) ?><?= $more > 0 ? '<em class="more">+' . (int)$more . ' more</em>' : '' ?></span>
                          </a>
                        <?php endif; ?>
                      </div>

                      <div class="section pt-3">
                        <div class="d-flex align-items-start mb-2">
                          <h6 class="mb-0 me-2 flex-grow-1"><?= html_escape($it['title']) ?></h6>
                          <span class="badge-soft <?= ($it['visibility'] === 'private') ? 'badge-muted' : '' ?>"><?= $it['visibility'] === 'private' ? 'Private' : 'Public' ?></span>
                        </div>

                        <?php if (!empty($it['description'])): ?>
                          <p class="text-muted mb-2"><?= nl2br(html_escape($it['description'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($it['files'])): ?>
                          <div class="meta mb-1">Files</div>
                          <div class="chips mb-2"><?php foreach ($it['files'] as $f) echo chip($f); ?></div>
                        <?php endif; ?>

                        <div class="meta">
                          <i class="mdi mdi-calendar-blank me-1"></i>
                          <?= !empty($it['created_at']) ? date('M d, Y', strtotime($it['created_at'])) : '—' ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <?php if (!empty($latest_reviews)): ?>
                <div class="card-flat section mt-3">
                  <div class="d-flex align-items-center mb-2">
                    <h6 class="mb-0">Latest Reviews</h6>
                    <span class="meta ms-2">(<?= (int)($reviews['count'] ?? 0) ?> total)</span>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="width:100%">
                      <thead class="bg-light">
                        <tr>
                          <th style="white-space:nowrap">Client</th>
                          <th>Job</th>
                          <th>Rating</th>
                          <th style="white-space:nowrap">When</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($latest_reviews as $r): ?>
                          <?php
                          $name   = trim((string)($r->client_name ?? '—'));
                          $job    = trim((string)($r->job_title   ?? '—'));
                          $rating = (int)($r->rating ?? 0);
                          $when   = trim((string)($r->time_ago    ?? ''));
                          ?>
                          <tr>
                            <td class="fw-medium"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($job, ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                              <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="mdi <?= $i <= $rating ? 'mdi-star text-warning' : 'mdi-star-outline text-muted' ?>"></i>
                              <?php endfor; ?>
                            </td>
                            <td class="text-muted"><?= $when !== '' ? htmlspecialchars($when, ENT_QUOTES, 'UTF-8') : '—' ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php endif; ?>





            <?php elseif ($mode === 'cert-form'): ?>

              <div class="card-flat section">
                <div class="d-flex align-items-center mb-2">
                  <h6 class="mb-0">Upload Certificates</h6>
                  <a class="btn btn-light ms-auto" href="<?= site_url('portfolio') ?>"><i class="mdi mdi-arrow-left"></i> Back to Portfolio</a>
                </div>

                <form method="post" action="<?= site_url('portfolio/cert_store') ?>" enctype="multipart/form-data">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Certificates (PDF / Images)</label>
                    <input type="file" name="cert_files[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
                    <div id="certTitlesWrap" class="mt-2"></div>
                    <script>
                      (function() {
                        const fileInput = document.querySelector('input[name="cert_files[]"]');
                        const wrap = document.getElementById('certTitlesWrap');
                        if (!fileInput || !wrap) return;
                        fileInput.addEventListener('change', function() {
                          wrap.innerHTML = '';
                          const files = this.files || [];
                          if (!files.length) return;
                          const list = document.createElement('div');
                          for (let i = 0; i < files.length; i++) {
                            const row = document.createElement('div');
                            row.className = 'mb-2';
                            const lbl = document.createElement('label');
                            lbl.className = 'form-label fw-semibold d-block';
                            lbl.textContent = 'Title for: ' + files[i].name;
                            const inp = document.createElement('input');
                            inp.type = 'text';
                            inp.name = 'cert_titles[]';
                            inp.maxLength = 150;
                            inp.placeholder = 'ex. TESDA NC II';
                            inp.className = 'form-control';
                            row.appendChild(lbl);
                            row.appendChild(inp);
                            list.appendChild(row);
                          }
                          wrap.appendChild(list);
                        });
                      })();
                    </script>

                    <div class="form-text">You can upload multiple files (max 10MB each). Images will show as tiles; PDFs as file chips.</div>

                  </div>
                  <div class="d-flex gap-2 justify-content-end">
                    <a class="btn btn-light" href="<?= site_url('portfolio') ?>">Cancel</a>
                    <button class="btn btn-brand"><i class="mdi mdi-upload"></i> Upload</button>
                  </div>
                </form>

                <hr class="my-3">

                <div class="d-flex align-items-center mb-2">
                  <h6 class="mb-0">Currently Uploaded</h6>
                </div>

                <?php if (empty($certs)): ?>
                  <div class="text-muted">No certificates uploaded yet.</div>
                <?php else: ?>
                  <?php
                  // Build unified tiles (images & PDFs shown the same, with titles)
                  $tiles = [];
                  foreach ($certs as $it) {
                    $n = cert_norm($it);
                    $p = $n['path'] ?? '';
                    if ($p === '') continue;

                    // Build preview URL
                    $absUrl   = preg_match('#^https?://#i', $p) ? $p : base_url($p);
                    $pathOnly = parse_url($absUrl, PHP_URL_PATH);
                    $fParam   = ltrim($pathOnly, '/');
                    $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
                    if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
                      $fParam = substr($fParam, strlen($basePath) + 1);
                    }
                    $viewerUrl = site_url('media/preview') . '?f=' . rawurlencode($fParam);

                    $ext   = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                    $title = ($n['title'] ?? '') !== '' ? $n['title'] : pathinfo($p, PATHINFO_FILENAME);

                    $tiles[] = [
                      'viewer' => $viewerUrl,
                      'abs'    => $absUrl,
                      'isImg'  => $isImg,
                      'title'  => $title,
                    ];
                  }
                  ?>

                  <?php if (empty($tiles)): ?>
                    <div class="text-muted">No certificates uploaded yet.</div>
                  <?php else: ?>
                    <div class="row g-2 mb-2">
                      <?php foreach ($tiles as $t): ?>
                        <div class="col-6 col-md-3">
                          <a href="<?= htmlspecialchars($t['viewer'], ENT_QUOTES, 'UTF-8') ?>"
                            target="_blank" rel="noopener"
                            class="d-block border rounded-3 overflow-hidden"
                            style="background:#0b1220">
                            <div class="ratio ratio-4x3">
                              <?php if ($t['isImg']): ?>
                                <img src="<?= htmlspecialchars($t['abs'], ENT_QUOTES, 'UTF-8') ?>"
                                  alt="<?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?>"
                                  class="w-100 h-100 object-fit-cover">
                              <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                  <i class="mdi mdi-file-pdf-box" style="font-size:48px;color:#ef4444"></i>
                                </div>
                              <?php endif; ?>
                            </div>
                          </a>
                          <div class="mt-1 small fw-semibold text-truncate"
                            title="<?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>

              </div>

            <?php else: ?>

              <div class="card-flat section">
                <form method="post" action="<?= site_url('portfolio/store') ?>" enctype="multipart/form-data">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required maxlength="150" placeholder="ex. Kitchen renovation">
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="4" maxlength="5000" placeholder="Briefly describe this work—"></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Files (PDF / Images)</label>
                    <input type="file" name="files[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.webp">
                    <div class="form-text">You can upload multiple files (max 10MB each).</div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Visibility</label>
                    <select name="visibility" class="form-control">
                      <option value="public">Public</option>
                      <option value="private">Private</option>
                    </select>
                  </div>
                  <div class="d-flex gap-2 justify-content-end">
                    <a class="btn btn-light" href="<?= site_url('portfolio') ?>">Cancel</a>
                    <button class="btn btn-brand">Post Work</button>
                  </div>
                </form>
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
</body>

</html>