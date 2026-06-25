<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php
  $page_title = $page_title ?? 'PESO Dashboard';
  $forcePublic = !empty($force_public_visibility);
  ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-peso.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <?php
        $__addr_rows = $this->db
          ->select('AddID, Province, City, Brgy')
          ->from('settings_address')
          ->order_by('Province, City, Brgy', 'ASC')
          ->get()->result_array();
        ?>
        <div
          id="jmPesoConfig"
          hidden
          data-address="<?= htmlspecialchars(json_encode($__addr_rows, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>"
          data-store-url="<?= htmlspecialchars(site_url('peso/store'), ENT_QUOTES, 'UTF-8') ?>"
          data-update-url="<?= htmlspecialchars(site_url('peso/update'), ENT_QUOTES, 'UTF-8') ?>"
          data-force-public="<?= $forcePublic ? '1' : '0' ?>"></div>

        <div class="content-wrapper pb-0">
          <div class="px-4 md:px-8 max-w-7xl mx-auto">
            <div class="admin-header">
              <div class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                  <h1 class="text-2xl md:text-3xl font-bold"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
                  <p class="text-sm muted mt-1">Manage your PESO job vacancies. Only <b>OPEN</b> + <b>PUBLIC</b> posts appear on the login page feed.</p>
                </div>
                <div class="flex items-center gap-2 iconbar">
                  <button
                    type="button"
                    class="iconbtn iconbtn--primary"
                    id="openCreateModalBtn"
                    title="New Vacancy"
                    aria-label="New Vacancy">
                    <i class="mdi mdi-briefcase-plus"></i>
                    <span class="iconbtn__label">New Vacancy</span>
                  </button>
                  <a
                    class="iconbtn"
                    href="<?= site_url('dashboard/peso') ?>"
                    title="Refresh"
                    aria-label="Refresh">
                    <i class="mdi mdi-refresh"></i>
                    <span class="iconbtn__label">Refresh</span>
                  </a>
                </div>
              </div>
            </div>

            <?php
            $k_open = isset($k_open) ? (int)$k_open : 0;
            $k_closed = isset($k_closed) ? (int)$k_closed : 0;
            $k_public = isset($k_public) ? (int)$k_public : 0;
            ?>

            <section class="stats-grid">
              <article class="stat-card stat-card--open">
                <div class="stat-card__icon">
                  <i class="mdi mdi-briefcase-check"></i>
                </div>
                <div class="stat-card__meta">
                  <span class="stat-card__label">Open Positions</span>
                  <span class="stat-card__value"><?= number_format($k_open) ?></span>
                </div>
              </article>
              <article class="stat-card stat-card--closed">
                <div class="stat-card__icon">
                  <i class="mdi mdi-briefcase-remove"></i>
                </div>
                <div class="stat-card__meta">
                  <span class="stat-card__label">Closed Positions</span>
                  <span class="stat-card__value"><?= number_format($k_closed) ?></span>
                </div>
              </article>
              <article class="stat-card stat-card--public">
                <div class="stat-card__icon">
                  <i class="mdi mdi-bullhorn"></i>
                </div>
                <div class="stat-card__meta">
                  <span class="stat-card__label">Public Posts</span>
                  <span class="stat-card__value"><?= number_format($k_public) ?></span>
                </div>
              </article>
            </section>

            <div class="mt-4">
              <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <?= $this->session->flashdata('success'); ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
              <?php endif; ?>
              <?php if ($this->session->flashdata('danger')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?= $this->session->flashdata('danger'); ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
              <?php endif; ?>
            </div>

            <section class="mt-4">
              <div class="card card-panel">
                <div class="dashboard-header">
                  <div class="dashboard-header__text">
                    <h3 class="dashboard-header__title">My Job Vacancies</h3>
                    <p class="dashboard-header__sub">Latest first</p>
                  </div>
                  <form method="get" action="" class="filter-form">
                    <input
                      type="text"
                      name="q"
                      class="form-control"
                      placeholder="Search title or location"
                      value="<?= html_escape($this->input->get('q')) ?>"
                      aria-label="Search vacancies">
                    <button class="iconbtn" type="submit" title="Search" aria-label="Search">
                      <i class="mdi mdi-magnify"></i>
                      <span class="iconbtn__label">Search</span>
                    </button>
                  </form>
                </div>

                <div class="jobs-shell">
                  <?php if (!empty($list)): foreach ($list as $r): ?>
                      <?php
                      $id        = (int)$r['id'];
                      $title     = html_escape($r['title']);
                      $descRaw   = isset($r['description']) ? (string)$r['description'] : '';
                      $desc      = html_escape($descRaw);
                      $descDisplay = $desc !== '' ? nl2br($desc) : '';
                      $site      = isset($r['website_url']) ? trim((string)$r['website_url']) : '';
                      $siteSafe  = html_escape($site);
                      $loc       = !empty($r['location_text']) ? html_escape($r['location_text']) : '';
                      $min       = ($r['price_min'] !== null) ? (float)$r['price_min'] : null;
                      $max       = ($r['price_max'] !== null) ? (float)$r['price_max'] : null;
                      $minF      = $min !== null ? number_format($min, 2) : '';
                      $maxF      = $max !== null ? number_format($max, 2) : '';
                      $status    = strtolower((string)$r['status']) === 'open' ? 'open' : 'closed';
                      $post_type = isset($r['post_type']) ? strtolower((string)$r['post_type']) : 'hire';
                      $visRaw    = isset($r['visibility']) ? strtolower((string)$r['visibility']) : 'public';
                      $vis       = in_array($visRaw, ['public', 'followers'], true) ? $visRaw : 'public';
                      $postedRaw = isset($r['created_at']) ? (string)$r['created_at'] : '';
                      $postedTs  = $postedRaw !== '' ? @strtotime($postedRaw) : false;
                      $posted    = $postedTs ? html_escape(date('M j, Y', $postedTs)) : html_escape($postedRaw);
                      $isOpen    = ($status === 'open');
                      $toggleIcon = $isOpen ? 'mdi-toggle-switch' : 'mdi-toggle-switch-off-outline';
                      $toggleTip  = $isOpen ? 'Close' : 'Open';
                      $mediaRaw  = isset($r['media_json']) ? json_decode((string)$r['media_json'], true) : null;
                      $media     = null;
                      if (is_array($mediaRaw) && !empty($mediaRaw['rel_path'])) {
                        $mediaRel = ltrim(str_replace('\\', '/', (string)$mediaRaw['rel_path']), '/');
                        if (strpos($mediaRel, 'uploads/') === 0) {
                          $ext = strtolower(pathinfo($mediaRel, PATHINFO_EXTENSION));
                          $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                          $type = isset($mediaRaw['type']) && in_array($mediaRaw['type'], ['image', 'pdf'], true)
                            ? $mediaRaw['type']
                            : (in_array($ext, $imageExts, true) ? 'image' : 'pdf');
                          $viewer = site_url('media/preview?f=' . rawurlencode($mediaRel));
                          $preview = $type === 'image'
                            ? site_url('media/wm_image?f=' . rawurlencode($mediaRel))
                            : site_url('media/wm_pdf?f=' . rawurlencode($mediaRel));
                          $mediaNameRaw = isset($mediaRaw['original_name']) ? (string)$mediaRaw['original_name'] : basename($mediaRel);
                          $media = [
                            'rel'      => $mediaRel,
                            'type'     => $type,
                            'preview'  => $preview,
                            'viewer'   => $viewer,
                            'name_raw' => $mediaNameRaw,
                            'name'     => html_escape($mediaNameRaw),
                          ];
                        }
                      }
                      ?>
                      <article class="job-card">
                        <div class="job-card__body">
                          <div class="job-card__header">
                            <div class="job-card__title">
                              <span><?= $title ?></span>
                              <?php if ($post_type === 'service'): ?>
                                <span class="chip"><i class="mdi mdi-account-settings"></i> Service</span>
                              <?php endif; ?>
                            </div>
                            <div class="job-card__badges">
                              <span class="chip <?= $isOpen ? '-open' : '-closed' ?>"><i class="mdi mdi-checkbox-blank-circle"></i> <?= strtoupper($status) ?></span>
                              <span class="chip <?= $vis === 'public' ? '' : '-followers' ?>"><i class="mdi <?= $vis === 'public' ? 'mdi-earth' : 'mdi-account-multiple' ?>"></i> <?= ucfirst($vis) ?></span>
                              <?php if ($media): ?>
                                <span class="chip -attachment"><i class="mdi <?= $media['type'] === 'pdf' ? 'mdi-file-pdf-box' : 'mdi-image-outline' ?>"></i> Attachment</span>
                              <?php endif; ?>
                            </div>
                          </div>
                          <div class="job-card__meta">
                            <?php if ($loc): ?><span class="job-card__meta-item"><i class="mdi mdi-map-marker"></i><?= $loc ?></span><?php endif; ?>
                            <?php if ($minF !== '' || $maxF !== ''): ?><span class="job-card__meta-item"><i class="mdi mdi-cash"></i>&#8369; <?= $minF ?><?= ($minF !== '' && $maxF !== '') ? ' - &#8369; ' : '' ?><?= $maxF ?></span><?php endif; ?>
                            <span class="job-card__meta-item"><i class="mdi mdi-calendar-clock"></i><?= $posted ?></span>
                          </div>
                          <?php if ($descDisplay !== ''): ?>
                            <div class="job-card__desc"><?= $descDisplay ?></div>
                          <?php endif; ?>
                          <div class="job-card__links">
                            <?php if ($site): ?>
                              <a href="<?= $siteSafe ?>" target="_blank" rel="noopener" class="text-link"><i class="mdi mdi-open-in-new"></i> View external post</a>
                            <?php endif; ?>
                            <?php if ($media): ?>
                              <a href="<?= html_escape($media['viewer']) ?>" target="_blank" rel="noopener" class="text-link"><i class="mdi mdi-eye"></i> View attachment</a>
                            <?php endif; ?>
                          </div>
                        </div>
                        <?php if ($media): ?>
                          <div class="job-card__media <?= $media['type'] === 'pdf' ? 'job-card__media--pdf' : '' ?>">
                            <?php if ($media['type'] === 'image'): ?>
                              <img src="<?= html_escape($media['preview']) ?>" alt="Attachment preview for <?= $title ?>">
                            <?php else: ?>
                              <i class="mdi mdi-file-pdf-box"></i>
                              <div>
                                <div class="font-semibold">PDF attachment</div>
                                <a href="<?= html_escape($media['viewer']) ?>" target="_blank" rel="noopener" class="text-link">Open document</a>
                              </div>
                            <?php endif; ?>
                          </div>
                        <?php endif; ?>
                        <div class="job-card__footer">
                          <div class="job-card__actions iconbar">
                            <button
                              type="button"
                              class="iconbtn iconbtn--primary editVacancyBtn"
                              data-id="<?= $id ?>"
                              data-title="<?= $title ?>"
                              data-description="<?= $desc ?>"
                              data-website_url="<?= $siteSafe ?>"
                              data-post_type="<?= $post_type ?>"
                              data-visibility="<?= $vis ?>"
                              data-price_min="<?= $min !== null ? $min : '' ?>"
                              data-price_max="<?= $max !== null ? $max : '' ?>"
                              data-location_text="<?= $loc ?>"
                              title="Edit vacancy"
                              aria-label="Edit vacancy"
                              <?php if ($media): ?>
                              data-media-type="<?= $media['type'] ?>"
                              data-media-name="<?= html_escape($media['name_raw']) ?>"
                              data-media-viewer="<?= html_escape($media['viewer']) ?>"
                              <?php endif; ?>>
                              <i class="mdi mdi-pencil"></i>
                              <span class="iconbtn__label">Edit</span>
                            </button>
                            <a
                              href="<?= site_url('peso/toggle/' . $id) ?>"
                              class="iconbtn"
                              title="<?= htmlspecialchars($toggleTip, ENT_QUOTES, 'UTF-8') ?>"
                              aria-label="<?= htmlspecialchars($toggleTip, ENT_QUOTES, 'UTF-8') ?>">
                              <i class="mdi <?= $toggleIcon ?>"></i>
                              <span class="iconbtn__label"><?= htmlspecialchars($toggleTip, ENT_QUOTES, 'UTF-8') ?></span>
                            </a>
                            <a
                              href="<?= site_url('peso/delete/' . $id) ?>"
                              onclick="return confirm('Delete this posting?');"
                              class="iconbtn iconbtn--danger"
                              title="Delete"
                              aria-label="Delete">
                              <i class="mdi mdi-delete-outline"></i>
                              <span class="iconbtn__label">Delete</span>
                            </a>
                          </div>
                          <div class="job-card__stamp">Posted <?= $posted ?></div>
                        </div>
                      </article>
                    <?php endforeach;
                  else: ?>
                    <div class="empty-state">
                      <i class="mdi mdi-briefcase-search"></i>
                      <div class="mt-2 font-semibold">No postings yet.</div>
                      <p class="mt-1 text-sm muted">Publish a vacancy to have it appear here and on the landing page feed.</p>
                    </div>
                  <?php endif; ?>
                </div>

                <p class="mt-4 text-sm muted"><i class="mdi mdi-information-outline"></i> Only <strong>OPEN</strong> and <strong>PUBLIC</strong> vacancies are shown on the login screen feed.</p>
              </div>
            </section>

            <div class="my-8" style="height:1px;background:var(--line)"></div>
          </div>
        </div>

        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div id="vacancyModal" class="tw-modal-backdrop" aria-hidden="true">
    <div class="tw-modal-card" role="dialog" aria-modal="true" aria-labelledby="vacancyModalTitle">
      <div class="tw-modal-header">
        <div class="tw-modal-title" id="vacancyModalTitle">
          <i class="mdi mdi-briefcase-plus"></i>
          <span id="modalModeTitle">Create Job Vacancy</span>
        </div>
        <button type="button" class="tw-close" id="modalCloseBtn" title="Close">
          <i class="mdi mdi-close"></i>
        </button>
      </div>

      <form id="vacancyForm" method="post" action="<?= site_url('peso/store') ?>" enctype="multipart/form-data">
        <!-- NEW: Grid modal body with sticky footer/header -->
        <div class="tw-modal-body">
          <input type="hidden" name="id" id="v_id" />

          <div class="form-grid">
            <!-- LEFT COLUMN -->
            <div class="space-y-4">
              <!-- Job Basics -->
              <section class="form-card">
                <button type="button" class="form-card-toggle">
                  <span><i class="mdi mdi-clipboard-text-outline"></i> Job Basics</span>
                  <i class="mdi mdi-chevron-down toggle-icon"></i>
                </button>
                <div class="form-card-body">
                  <div class="tw-grid-2">
                    <div class="field-group" style="grid-column:1 / -1;">
                      <label class="field-label" for="v_title">Job Title *</label>
                      <input type="text" name="title" id="v_title" class="form-control" required placeholder="e.g., Junior Web Developer">
                    </div>
                    <div class="field-group">
                      <label class="field-label" for="v_website_url">Website Link</label>
                      <input type="url" name="website_url" id="v_website_url" class="form-control" placeholder="https://example.com/job/123">
                    </div>
                    <div class="field-group">
                      <label class="field-label" for="v_post_type">Post Type</label>
                      <select name="post_type" id="v_post_type" class="form-control">
                        <option value="hire">Hire</option>
                        <option value="service">Service</option>
                      </select>
                    </div>
                  </div>

                  <div class="field-group" style="margin-top:.75rem;">
                    <label class="field-label" for="v_description">Description</label>
                    <textarea name="description" id="v_description" rows="4" class="form-control" placeholder="Short role overview, qualifications, responsibilities"></textarea>
                  </div>
                </div>
              </section>

              <!-- Posting -->
              <section class="form-card">
                <button type="button" class="form-card-toggle">
                  <span><i class="mdi mdi-bullhorn-outline"></i> Posting</span>
                  <i class="mdi mdi-chevron-down toggle-icon"></i>
                </button>
                <div class="form-card-body">
                  <div class="field-group">
                    <label class="field-label" for="v_visibility">Visibility</label>
                    <select name="visibility" id="v_visibility" class="form-control" <?= $forcePublic ? 'data-force="public"' : '' ?>>
                      <option value="public">Public</option>
                      <?php if (!$forcePublic): ?>
                        <option value="followers">Followers</option>
                      <?php endif; ?>
                    </select>
                    <?php if ($forcePublic): ?>
                      <small class="muted">TESDA postings are published publicly so applicants can see them on the landing page.</small>
                    <?php endif; ?>
                  </div>
                </div>
              </section>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="space-y-4">
              <!-- Supporting Media -->
              <section class="form-card">
                <button type="button" class="form-card-toggle">
                  <span><i class="mdi mdi-paperclip"></i> Supporting Media</span>
                  <i class="mdi mdi-chevron-down toggle-icon"></i>
                </button>
                <div class="form-card-body">
                  <div class="field-group">
                    <input type="file" name="attachment" id="v_attachment" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf">
                    <small class="muted">Attach an image or PDF up to 8&nbsp;MB. Shown on the public landing page.</small>
                  </div>

                  <div id="attachmentCurrent" class="attachment-current" hidden>
                    <div class="file-pill">
                      <i id="attachmentCurrentIcon" class="mdi mdi-file-outline"></i>
                      <span id="attachmentCurrentName"></span>
                    </div>
                    <div class="attachment-actions">
                      <a id="attachmentPreviewLink" href="#" target="_blank" rel="noopener" class="text-link">Preview</a>
                      <button type="button" class="btn btn-ghost btn-sm -danger" id="attachmentRemoveBtn"><i class="mdi mdi-close-circle"></i> Remove</button>
                    </div>
                  </div>

                  <div id="attachmentSelected" class="attachment-selected" hidden>
                    <i id="attachmentSelectedIcon" class="mdi mdi-file-outline"></i>
                    <span>Selected: <strong id="attachmentSelectedName"></strong></span>
                    <button type="button" class="btn btn-ghost btn-sm" id="attachmentSelectedClear"><i class="mdi mdi-close"></i> Clear</button>
                  </div>

                  <div id="attachmentRemovalNotice" class="attachment-notice" hidden>
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <span>The current attachment will be removed after saving.</span>
                    <button type="button" class="btn btn-ghost btn-sm" id="attachmentUndoRemove"><i class="mdi mdi-undo"></i> Keep file</button>
                  </div>

                  <input type="hidden" name="remove_media" id="v_remove_media" value="">
                </div>
              </section>

              <!-- Compensation -->
              <section class="form-card">
                <button type="button" class="form-card-toggle">
                  <span><i class="mdi mdi-cash-multiple"></i> Compensation</span>
                  <i class="mdi mdi-chevron-down toggle-icon"></i>
                </button>
                <div class="form-card-body">
                  <div class="tw-grid-2">
                    <div class="field-group">
                      <label class="field-label" for="v_price_min">Salary Min</label>
                      <input type="number" step="0.01" name="price_min" id="v_price_min" class="form-control" placeholder="e.g., 15000">
                    </div>
                    <div class="field-group">
                      <label class="field-label" for="v_price_max">Salary Max</label>
                      <input type="number" step="0.01" name="price_max" id="v_price_max" class="form-control" placeholder="e.g., 25000">
                    </div>
                  </div>
                </div>
              </section>

              <!-- Location -->
              <section class="form-card">
                <button type="button" class="form-card-toggle">
                  <span><i class="mdi mdi-map-marker-radius"></i> Location</span>
                  <i class="mdi mdi-chevron-down toggle-icon"></i>
                </button>
                <div class="form-card-body">
                  <div class="tw-grid-2">
                    <div class="field-group">
                      <label class="field-label" for="v_province">Province</label>
                      <select id="v_province" class="form-control">
                        <option value="">Select Province</option>
                      </select>
                    </div>
                    <div class="field-group">
                      <label class="field-label" for="v_city">City/Municipality</label>
                      <select id="v_city" class="form-control" disabled>
                        <option value="">Select City/Municipality</option>
                      </select>
                    </div>
                  </div>
                  <div class="tw-grid-2" style="margin-top:.85rem;">
                    <div class="field-group">
                      <label class="field-label" for="v_brgy">Barangay</label>
                      <select id="v_brgy" class="form-control" disabled>
                        <option value="">Select Barangay</option>
                      </select>
                    </div>
                    <div class="field-group">
                      <label class="field-label" for="v_location_text">Location (auto)</label>
                      <input type="text" name="location_text" id="v_location_text" class="form-control" placeholder="Barangay, City, Province" readonly>
                      <input type="hidden" id="v_address_id">
                    </div>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>

        <div class="tw-modal-footer">
          <button type="button" class="btn btn-ghost" id="modalCancelBtn"><i class="mdi mdi-close"></i> Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> <span id="modalSubmitText">Post Vacancy</span></button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>
  <script src="<?= base_url('assets/js/dashboard-peso.js?v=1.0.0') ?>"></script>

</body>

</html>
