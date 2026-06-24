  <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= html_escape($page_title ?? 'Edit Worker Profile') ?> - JobMatch</title>
    <meta name="theme-color" content="#c1272d" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/logo.png') ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.css" />
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/r-3.0.2/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>

    <style>
      .select2-container .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: .5rem;
      }

      .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px;
        padding-left: .75rem;
      }

      .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
        right: .5rem;
      }

      .select2-container .select2-dropdown {
        z-index: 99999;
      }

      .form-input:focus {
        outline: none;
        border-color: #c1272d !important;
        box-shadow: 0 0 0 3px rgba(193, 39, 45, 0.1) !important;
      }

      body {
        font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      }

      .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        color: #fff;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .25);
        border-radius: 9999px;
        padding: .4rem .75rem;
        line-height: 1;
        box-shadow: 0 1px 0 rgba(255, 255, 255, .12) inset, 0 8px 24px rgba(0, 0, 0, .12);
        backdrop-filter: saturate(140%) blur(4px);
      }

      @media (max-width:768px) {
        .status-badge {
          align-self: flex-start;
        }
      }

      #toaster {
        position: fixed;
        inset: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        pointer-events: none;
        padding: 16px;
      }

      .toast {
        pointer-events: auto;
        max-width: min(92vw, 520px);
        text-align: center;
        background: #111827;
        color: #fff;
        border-radius: 12px;
        padding: .75rem 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
        font-size: .95rem;
        font-weight: 600;
        animation: toast-in .18s ease-out;
      }

      .toast--ok {
        background: #065f46;
      }

      .toast--err {
        background: #7f1d1d;
      }

      @keyframes toast-in {
        from {
          opacity: 0;
          transform: translateY(-6px) scale(.98)
        }

        to {
          opacity: 1;
          transform: translateY(0) scale(1)
        }
      }

      .wk-guide-highlight {
        outline: 3px solid #c1272d !important;
        box-shadow: 0 0 0 6px rgba(193, 39, 45, .12), 0 8px 30px rgba(193, 39, 45, .22) !important;
        border-radius: .6rem !important;
        transition: box-shadow .18s ease;
        position: relative;
      }

      .wk-guide-highlight::after {
        content: "";
        position: absolute;
        inset: -6px;
        border: 2px dashed rgba(193, 39, 45, .5);
        border-radius: .8rem;
        animation: wkPulse 1.4s ease-in-out infinite;
        pointer-events: none;
      }

      @keyframes wkPulse {
        0% {
          opacity: .5;
          transform: scale(.98)
        }

        50% {
          opacity: 1;
          transform: scale(1)
        }

        100% {
          opacity: .5;
          transform: scale(.98)
        }
      }
    </style>
    <style>
      .doc-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #fff;
      }

      .doc-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
      }

      .doc-toolbar h2 {
        margin: 0;
      }

      .doc-empty {
        padding: 40px 16px;
        text-align: center;
        color: #6b7280;
        background: linear-gradient(180deg, #fafafa, #fff);
        border-radius: 12px;
      }

      .doc-empty .art {
        width: 80px;
        height: 80px;
        margin: 0 auto 12px;
        border-radius: 16px;
        background: radial-gradient(circle at 30% 30%, #ffd9d9 0 40%, transparent 41%), #ffe5e5;
        box-shadow: inset 0 0 0 1px #ffc9c9, 0 10px 24px rgba(193, 39, 45, .12);
      }

      #documentsTable {
        font-size: .92rem;
      }

      #documentsTable thead th {
        background: #f8fafc !important;
        color: #334155;
        font-weight: 600;
        border-bottom: 1px solid #e5e7eb !important;
      }

      #documentsTable tbody tr {
        transition: background .12s ease;
      }

      #documentsTable tbody tr:hover {
        background: #f9fafb;
      }

      #documentsTable td,
      #documentsTable th {
        padding: 12px 14px !important;
      }

      .chip {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .25rem .6rem;
        border-radius: 999px;
        font-size: .75rem;
        line-height: 1;
        border: 1px solid;
      }

      .chip--type {
        color: #c1272d;
        background: #ffe5e5;
        border-color: #ffc9c9;
      }

      .chip--skill {
        color: #065f46;
        background: #ecfdf5;
        border-color: #a7f3d0;
      }

      .badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .25rem .5rem;
        border-radius: 8px;
        font-size: .72rem;
        font-weight: 600;
      }

      .badge--ok {
        color: #065f46;
        background: #ecfdf5;
      }

      .badge--warn {
        color: #b45309;
        background: #fffbeb;
      }

      .badge--danger {
        color: #7f1d1d;
        background: #fee2e2;
      }

      .file-pill {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .3rem .6rem;
        border-radius: 10px;
        background: #f1f5f9;
        color: #0f172a;
      }

      .file-pill svg {
        width: 14px;
        height: 14px;
      }

      .btn-ghost {
        background: #f3f4f6;
        color: #374151;
        border-radius: 10px;
        padding: .45rem .65rem;
        font-size: .78rem;
      }

      .btn-ghost:hover {
        background: #e5e7eb;
      }

      .btn-danger {
        background: #dc2626;
        color: #fff;
        border-radius: 10px;
        padding: .45rem .65rem;
        font-size: .78rem;
      }

      .btn-danger:hover {
        background: #b91c1c;
      }

      #docModalRoot .form-label {
        color: #374151;
      }

      #docModalRoot input[type="file"] {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 10px;
        background: #f8fafc;
      }
    </style>
    <style>
      @media (max-width: 768px) {
        .main-container {
          border: 0;
          border-radius: 0;
          box-shadow: none;
        }

        .header-gradient {
          border-radius: 0;
          padding: 16px 16px 18px;
        }

        .tabs-sticky {
          position: sticky;
          top: 0;
          z-index: 30;
          background: #fff;
          box-shadow: 0 1px 0 rgba(0, 0, 0, .06);
        }

        .section-card {
          background: #fff;
          border: 1px solid #e5e7eb;
          border-radius: 14px;
          padding: 16px;
          box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
          margin-bottom: 16px;
        }

        .m-gap-compact {
          gap: 14px !important;
        }

        .avatar-container {
          width: 72px;
          height: 72px;
        }

        .file-upload-area {
          padding: 14px;
        }

        .tab-button {
          white-space: nowrap;
        }

        .footer-sticky {
          position: sticky;
          bottom: 0;
          z-index: 25;
          background: #fff;
          border-top: 1px solid #e5e7eb;
          padding: 10px 12px !important;
          box-shadow: 0 -8px 20px rgba(0, 0, 0, .06);
        }

        .footer-sticky .primary-button,
        .footer-sticky .secondary-button {
          width: 100%;
          justify-content: center;
          padding-top: 10px;
          padding-bottom: 10px;
        }
      }

      #experiencesTable {
        font-size: .92rem;
      }

      #experiencesTable thead th {
        background: #f8fafc !important;
        color: #334155;
        font-weight: 600;
        border-bottom: 1px solid #e5e7eb !important;
      }

      #experiencesTable td,
      #experiencesTable th {
        padding: 12px 14px !important;
      }

      #experiencesTable tbody tr {
        transition: background .12s ease;
      }

      #experiencesTable tbody tr:hover {
        background: #f9fafb;
      }

      .chip--employer {
        color: #1f2937;
        background: #f3f4f6;
        border-color: #e5e7eb;
      }

      /* grey pill like â€œTypeâ€ */
      .period-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .25rem .5rem;
        border-radius: 8px;
        font-size: .72rem;
        font-weight: 600;
        color: #c1272d;
        background: #ffe5e5;
      }

      .desc-cell {
        max-width: 520px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #334155;
      }

      .desc-more {
        display: inline-block;
        margin-left: .5rem;
        font-size: .75rem;
        color: #2980b9;
      }
    </style>

    <?php
    $editCssPath = FCPATH . 'assets/css/edit.css';
    $editCssUrl  = base_url('assets/css/edit.css') . (is_file($editCssPath) ? ('?v=' . filemtime($editCssPath)) : '');
    ?>
    <link rel="stylesheet" href="<?= $editCssUrl ?>">

    <?php
    $i18nJs = base_url('assets/js/i18n.js?v=' . (is_file(FCPATH . 'assets/js/i18n.js') ? filemtime(FCPATH . 'assets/js/i18n.js') : time()));
    $scanJs = base_url('assets/js/i18n.autoscan.js?v=' . (is_file(FCPATH . 'assets/js/i18n.autoscan.js') ? filemtime(FCPATH . 'assets/js/i18n.autoscan.js') : time()));
    ?>
    <script src="<?= $i18nJs ?>"></script>
    <script src="<?= $scanJs ?>"></script>
  </head>

  <body
    class="bg-gray-50"
    data-api-address="<?= site_url('address/api') ?>"
    data-limit-province=""
    data-i18n-base="<?= base_url('assets/i18n') ?>">

    <?php
    $p = $profile ?? ($p ?? null);

    $first_name = $p->first_name ?? ($this->session->userdata('first_name') ?? '');
    $last_name  = $p->last_name  ?? '';
    $seed = trim($first_name . ' ' . $last_name) ?: 'Worker';

    $normalize_rel = function ($rel) {
      $rel = str_replace('\\', '/', (string)$rel);
      return ltrim($rel, '/');
    };

    $DEFAULT_AVATAR_REL = 'uploads/avatars/avatar.png';

    $avatarField = trim((string)($p->avatar ?? ''));
    $avatarLocalRel = null;

    if ($avatarField !== '' && preg_match('#^https?://#i', $avatarField)) {
      $avatarUrl = $avatarField;
    } else {
      $rel = $avatarField !== '' ? $normalize_rel($avatarField) : $DEFAULT_AVATAR_REL;
      if (!is_file(FCPATH . $rel)) {
        if (is_file(FCPATH . $DEFAULT_AVATAR_REL)) {
          $rel = $DEFAULT_AVATAR_REL;
        } else {
          $rel = null;
        }
      }
      if ($rel) {
        $avatarLocalRel = $rel;
        $avatarUrl = base_url($rel);
      } else {
        $avatarUrl = 'https://api.dicebear.com/9.x/initials/svg?seed=' . urlencode($seed);
      }
    }

    if (!function_exists('viewer_url_from_abs')) {
      function viewer_url_from_abs($absUrl)
      {
        if (!$absUrl) return null;
        $pathOnly = parse_url($absUrl, PHP_URL_PATH);
        if (!$pathOnly) return null;
        $fParam   = ltrim($pathOnly, '/');
        $basePath = trim(parse_url(base_url(), PHP_URL_PATH), '/');
        if ($basePath !== '' && strpos($fParam, $basePath . '/') === 0) {
          $fParam = substr($fParam, strlen($basePath) + 1);
        }
        return site_url('media/preview') . '?f=' . rawurlencode($fParam);
      }
    }

    $avatarViewerUrl = $avatarLocalRel ? viewer_url_from_abs(base_url($avatarLocalRel)) : null;

    // Skills list for Skills tab
    if (!isset($allSkills)) {
      $allSkills = $this->db->order_by('Title', 'ASC')->get('skills')->result();
    }
    $skillOptions = array_values(array_unique(array_filter(array_map(function ($x) {
      if (is_object($x)) return trim((string)($x->Title ?? ''));
      if (is_array($x))  return trim((string)($x['Title'] ?? ''));
      return trim((string)$x);
    }, (array)$allSkills))));

    $currentSkills = array_values(array_filter(array_map('trim', explode(',', (string)($p->skills ?? '')))));

    // NC (kept only for potential future use)
    $currentNCs = [];
    $mapByText = [];
    if (!empty($ncOptions)) {
      foreach ($ncOptions as $opt) {
        $t = mb_strtolower(trim((string)($opt['text'] ?? '')));
        if ($t !== '') $mapByText[$t] = (string)$opt['id'];
      }
    }
    if (!empty($p->tesda_certs)) {
      $rows = is_string($p->tesda_certs) ? json_decode($p->tesda_certs, true) : (array)$p->tesda_certs;
      if (is_array($rows)) {
        foreach ($rows as $row) {
          $q = mb_strtolower(trim((string)($row['qualification'] ?? '')));
          if ($q !== '' && isset($mapByText[$q])) {
            $currentNCs[] = $mapByText[$q];
          }
        }
      }
    }
    $ncOptionsJson  = json_encode($ncOptions ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    $currentNCsJson = json_encode($currentNCs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>

    <?php $this->load->view('partials_translate_banner'); ?>

    <div id="toaster" aria-live="polite"></div>

    <div class="min-h-screen py-8 px-4">
      <div class="max-w-5xl mx-auto">
        <div class="main-container bg-white rounded-2xl shadow border border-gray-200 overflow-hidden">

          <div class="header-gradient px-8 py-6 bg-gradient-to-r from-red-700 to-red-500">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div class="flex items-center gap-4 sm:gap-6">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center shrink-0">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    Edit Professional Profile <span class="w-2 h-2 rounded-full bg-white/40"></span>
                  </h1>
                  <p class="text-white/80 text-sm font-medium mt-1">Build your professional presence</p>
                  <button id="openTranslate" class="status-badge mt-2" type="button" title="Translate">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path d="M4 5h16v2H5v4H3V5a1 1 0 011-1zm3 6h6v2H9.83A12 12 0 0013 18.17V20h-2a10 10 0 01-4-9zM20 11h-2.06A8 8 0 0111 17.94V20h-2v-2.06A10 10 0 009.94 13H8v-2h4V7h2v4h4v2z" />
                    </svg>
                    <span data-i18n="nav.translate">Translate</span>
                  </button>
                </div>
              </div>
            </div>

            <?php if ($this->session->flashdata('error')): ?>
              <div class="mt-4 bg-red-50 text-red-800 px-3 py-2 rounded"><?= $this->session->flashdata('error'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
              <div class="mt-4 bg-emerald-50 text-emerald-800 px-3 py-2 rounded"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
          </div>

          <div class="px-8 py-6 border-b border-gray-100">
            <div class="flex flex-col md:flex-row md:items-start gap-6">
              <div class="flex items-center gap-4">
                <div class="avatar-container w-[88px] h-[88px] rounded-full overflow-hidden ring-4 ring-white shadow">
                  <img id="avatarPreview" src="<?= $avatarUrl ?>" alt="Profile Avatar" class="w-full h-full object-cover">
                </div>
                <div class="text-sm text-gray-600">
                  <div class="font-semibold text-gray-800 mb-1">Profile Photo</div>
                  <div class="text-xs">PNG, JPG, or WebP format</div>
                  <div class="text-xs text-gray-500">Clear headshot recommended</div>
                </div>
              </div>

              <form id="avatarForm" class="w-full md:max-w-lg md:ml-auto" method="post" action="<?= site_url('profile/update') ?>" enctype="multipart/form-data" target="uploadFrame">
                <?php if ($this->config->item('csrf_protection')): ?>
                  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <?php endif; ?>
                <input type="hidden" name="__partial" value="avatar">
                <input type="hidden" name="next" value="edit">
                <input type="hidden" name="ajax" value="1">

                <label class="form-label block mb-2 font-medium text-gray-800">Upload new photo</label>
                <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-5 text-center bg-gray-50 hover:bg-gray-100 transition">
                  <svg class="mx-auto h-9 w-9 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="text-sm font-medium text-gray-700">Choose photo or drag & drop</div>
                  <div class="text-xs text-gray-500 mt-1">High quality images work best</div>
                  <input id="avatar" class="mt-3 block w-full text-sm" type="file" name="avatar" accept=".png,.jpg,.jpeg,.webp">
                </div>

                <div class="flex items-center gap-3 mt-4">
                  <button class="primary-button inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg" type="submit">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 6L9 17l-5-5" />
                    </svg>
                    Upload Photo
                  </button>
                  <?php if (!empty($p->avatar) && !empty($avatarViewerUrl)): ?>
                    <a class="text-sm font-medium text-red-600 hover:text-red-700" href="<?= htmlspecialchars($avatarViewerUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View current</a>
                  <?php endif; ?>
                </div>
                <div id="preview-avatar" class="upload-previews mt-3"></div>
              </form>
              <iframe id="uploadFrame" name="uploadFrame" class="hidden"></iframe>
            </div>
          </div>

          <nav class="px-8 py-4 border-b border-gray-100 bg-white tabs-sticky">
            <div class="flex gap-3 overflow-x-auto pb-2">
              <button data-tab="info" class="tab-button active px-3 py-2 rounded-lg bg-gray-900 text-white text-sm"><span data-i18n="tabs.basic">Basic Info</span></button>
              <button data-tab="availability" class="tab-button px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm"><span data-i18n="tabs.availability">Availability</span></button>
              <button data-tab="skills" class="tab-button px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm"><span data-i18n="tabs.skills">Skills</span></button>
              <button data-tab="experience" class="tab-button px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm"><span data-i18n="tabs.experience">Experience</span></button>
              <button data-tab="education" class="tab-button px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm"><span data-i18n="tabs.education">Education</span></button>
              <button data-tab="docs" class="tab-button px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm"><span data-i18n="tabs.docs">Documents</span></button>
            </div>
          </nav>

          <form id="mainForm" method="post" action="<?= site_url('profile/update') ?>" enctype="multipart/form-data">
            <?php if ($this->config->item('csrf_protection')): ?>
              <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <?php endif; ?>
            <input type="hidden" name="next" value="dashboard">

            <div class="px-8 py-6">
              <!-- Basic Info -->
              <div data-panel="info" class="tab-panel section-card">
                <h2 class="section-header text-lg font-semibold text-gray-900 mb-4">Professional Information</h2>

                <div class="space-y-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="first_name">First Name</label>
                      <input id="first_name" type="text" name="first_name" value="<?= set_value('first_name', $first_name) ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="Enter your first name">
                    </div>
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="last_name">Last Name</label>
                      <input id="last_name" type="text" name="last_name" value="<?= set_value('last_name', $last_name) ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="Enter your last name">
                    </div>
                  </div>

                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="headline">Professional Headline</label>
                    <input id="headline" type="text" name="headline" maxlength="160" value="<?= set_value('headline', $p->headline ?? '') ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="e.x. TESDA NC II Electrician - Industrial wiring specialist - 6+ years">
                    <div class="form-hint text-xs text-gray-500 mt-1">A brief, compelling summary of your expertise (max 160 characters)</div>
                  </div>

                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="bio">Professional Summary</label>
                    <textarea id="bio" name="bio" rows="5" maxlength="600" class="form-input w-full border-gray-300 rounded-lg min-h-[120px] resize-y" placeholder="Describe your experience, specializations, certifications, and standout projects..."><?= set_value('bio', $p->bio ?? '') ?></textarea>
                    <div class="flex justify-between items-center mt-2">
                      <div class="form-hint text-xs text-gray-500">Mention tools, standards, and notable credentials</div>
                      <span id="bioCount" class="text-xs text-gray-500">0 / 600</span>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="province">Province</label>
                      <select id="province" name="province" class="form-input w-full border-gray-300 rounded-lg" data-pre="<?= html_escape($p->province ?? '') ?>">
                        <option value="">- Select Province -</option>
                      </select>
                    </div>
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="city">City / Municipality</label>
                      <select id="city" name="city" class="form-input w-full border-gray-300 rounded-lg" data-pre="<?= html_escape($p->city ?? '') ?>" disabled>
                        <option value="">- Select City -</option>
                      </select>
                    </div>
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="brgy">Barangay</label>
                      <select id="brgy" name="brgy" class="form-input w-full border-gray-300 rounded-lg" data-pre="<?= html_escape($p->brgy ?? '') ?>" disabled>
                        <option value="">- Select Barangay -</option>
                      </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="years_experience">Years of Experience</label>
                      <input id="years_experience" type="number" min="0" step="1" name="years_experience" value="<?= set_value('years_experience', (int)($p->years_experience ?? 0)) ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="6">
                    </div>
                    <div class="md:col-span-2">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="phoneNo">Phone Number</label>
                      <div class="flex">
                        <span class="phone-prefix inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-600">+63</span>
                        <input id="phoneNo" type="text" name="phoneNo" value="<?= set_value('phoneNo', $p->phoneNo ?? '') ?>" class="form-input w-full border-gray-300 rounded-r-lg" placeholder="9xx xxx xxxx">
                      </div>
                      <div class="form-hint text-xs text-gray-500 mt-1">Shown to clients after you accept a job</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Availability -->
              <div data-panel="availability" class="tab-panel hidden section-card">
                <h2 class="section-header text-lg font-semibold text-gray-900 mb-4">Work Schedule</h2>
                <?php
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                $picked = array_filter(array_map('trim', explode(',', set_value('availability_days', $p->availability_days ?? ''))));
                ?>
                <div class="space-y-4">
                  <div>
                    <label class="form-label block text-sm font-medium text-gray-700">Available Days</label>
                    <div class="flex flex-wrap gap-3 mt-3">
                      <?php foreach ($days as $d):
                        $id  = 'day_' . $d;
                        $chk = in_array($d, $picked) ? 'checked' : ''; ?>
                        <label for="<?= $id ?>" class="inline-flex items-center gap-2">
                          <input id="<?= $id ?>" type="checkbox" name="availability_days[]" value="<?= $d ?>" class="day-selector rounded" <?= $chk ?>>
                          <span class="day-label text-sm"><?= $d ?></span>
                        </label>
                      <?php endforeach; ?>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-4">
                      <button type="button" id="pickWeekdays" class="quick-select-button px-3 py-1.5 rounded-lg bg-gray-100">Weekdays Only</button>
                      <button type="button" id="pickAll" class="quick-select-button px-3 py-1.5 rounded-lg bg-gray-100">All Days</button>
                      <button type="button" id="clearDays" class="quick-select-button px-3 py-1.5 rounded-lg bg-gray-100">Clear</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Skills -->
              <div data-panel="skills" class="tab-panel hidden skills-section section-card">
                <h2 class="section-header text-lg font-semibold text-gray-900 mb-4">Professional Skills</h2>
                <div class="space-y-5">
                  <div>
                    <label class="form-label block text-sm font-medium text-gray-700">Search for skills to add</label>
                    <div class="skills-search-wrap mt-1">
                      <div class="flex items-center border border-gray-200 rounded-xl bg-white px-3 py-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z" />
                        </svg>
                        <input id="skillsSearch" type="text" class="flex-1 border-none outline-none text-sm" placeholder="Search for skills to add...">
                        <button id="skillsToggle" type="button" class="p-1 text-gray-500 hover:text-gray-700">
                          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 10l5 5 5-5z" />
                          </svg>
                        </button>
                      </div>
                      <div id="skillsPanel" class="dropdown-panel static mt-2 w-full hidden">
                        <?php if (empty($skillOptions)): ?>
                          <div class="px-4 py-3 text-sm text-gray-500">No skills available</div>
                        <?php else: ?>
                          <ul id="skillsList" role="listbox" aria-label="Skills"></ul>
                          <div data-empty class="px-4 py-3 text-sm text-gray-500 hidden">No matches found</div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="form-label block text-sm font-medium text-gray-700">Selected skills</label>
                    <div id="skillsSelected" class="flex flex-wrap gap-2 mt-2">
                      <?php foreach ($currentSkills as $s): if (!$s) continue; ?>
                        <span class="skill-chip inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-red-50 text-red-700 border border-red-200" data-skill-chip="<?= html_escape($s) ?>">
                          <svg class="w-3.5 h-3.5 text-red-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 12l2 2 4-4 1.5 1.5L11 17l-3.5-3.5L9 12z" />
                          </svg>
                          <span class="text-xs font-medium"><?= html_escape($s) ?></span>
                          <button type="button" class="remove-skill" data-remove-skill="<?= html_escape($s) ?>">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                              <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                          </button>
                        </span>
                      <?php endforeach; ?>
                    </div>
                  </div>

                  <input id="skills" name="skills" type="hidden" value="<?= html_escape(implode(', ', $currentSkills)) ?>">
                  <div class="form-hint text-xs text-gray-500">Select your professional skills (use the NC section to attach proof files).</div>
                </div>
              </div>

              <!-- Experience -->
              <div data-panel="experience" class="tab-panel hidden section-card">
                <h2 class="section-header text-lg font-semibold text-gray-900 mb-2">Work Experience</h2>

                <div class="doc-toolbar mb-4">
                  <p class="text-sm text-gray-600">
                    List your relevant roles and projects. Most recent at the top is recommended.
                  </p>
                  <button type="button" id="btnAddExp"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Experience
                  </button>
                </div>

                <div class="doc-card p-3">
                  <!-- Empty state -->
                  <div id="expEmpty" class="doc-empty">
                    <div class="art"></div>
                    <p>No experience added yet.</p>
                  </div>

                  <!-- Table wrapper -->
                  <div id="expTableWrap" class="overflow-x-auto hidden">
                    <table id="experiencesTable" class="min-w-full text-sm display nowrap" style="width:100%">
                      <thead>
                        <tr>
                          <th>Role / Title</th>
                          <th>Employer / Project</th>
                          <th>Period</th>
                          <th>Description</th>
                          <th style="width:120px;">Action</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>

                  </div>
                </div>
              </div>


              <!-- Education -->
              <div data-panel="education" class="tab-panel hidden section-card">
                <h2 class="section-header text-lg font-semibold text-gray-900 mb-4">Education &amp; Credentials</h2>
                <?php
                $edu = set_value('education_level', $p->education_level ?? '');
                $eduOptions = [
                  'Elementary / Primary School',
                  'Senior High School / K–12',
                  'Technical / Vocational',
                  'College (Undergraduate,In Progress)',
                  'College Graduate (Bachelor’s Degree)',
                  'Postgraduate (Master’s Degree)',
                  'Doctorate / PhD / Professional Doctorate',
                ];
                ?>
                <div class="space-y-6">
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="education_level">Highest Education</label>
                      <select id="education_level" name="education_level" class="form-input w-full border-gray-300 rounded-lg">
                        <option value="">Select level</option>
                        <?php foreach ($eduOptions as $opt): ?>
                          <option value="<?= html_escape($opt) ?>" <?= $edu === $opt ? 'selected' : '' ?>><?= html_escape($opt) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="course">Course</label>
                      <input id="course" type="text" name="course" value="<?= set_value('course', $p->course ?? '') ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="ex. BS Electrical Engineering">
                    </div>
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="school">School/Institution</label>
                      <input id="school" type="text" name="school" value="<?= set_value('school', $p->school ?? '') ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="University/School name">
                    </div>
                    <div class="input-group">
                      <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="year_graduated">Year Graduated</label>
                      <input id="year_graduated" type="number" min="1950" max="2100" step="1" name="year_graduated" value="<?= set_value('year_graduated', $p->year_graduated ?? '') ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="2020">
                    </div>
                  </div>

                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="credentials">Professional Credentials &amp; Licenses</label>
                    <textarea id="credentials" name="credentials" rows="4" class="form-input w-full border-gray-300 rounded-lg resize-y" placeholder="List your certifications and licenses (one per line, include year/license when applicable)"><?= set_value('credentials', $p->credentials ?? '') ?></textarea>
                  </div>
                </div>
              </div>

              <!-- Documents -->
              <div data-panel="docs" class="tab-panel hidden section-card">
                <h2 class="section-header text-lg font-semibold text-gray-900 mb-2">Documents/Clearance/TESDA Certificates</h2>

                <div class="doc-toolbar mb-4">
                  <p class="text-sm text-gray-600">
                    Keep your certifications and IDs organized. Expiring items are highlighted.
                  </p>
                  <button type="button" id="btnAddDoc"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Document
                  </button>
                </div>

                <div class="doc-card p-3">
                  <!-- Empty state -->
                  <div id="docsEmpty" class="doc-empty">
                    <div class="art"></div>
                    <p>No documents yet.</p>
                  </div>

                  <!-- Table wrapper -->
                  <div id="docsTableWrap" class="overflow-x-auto hidden">
                    <table id="documentsTable" class="min-w-full text-sm display nowrap" style="width:100%">
                      <thead>
                        <tr>
                          <th>Document</th>
                          <th>Type</th>
                          <th>Certificate</th>
                          <th>Expiry</th>
                          <th>File</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-6">
                  <div>
                    <label class="form-label block text-sm font-medium text-gray-700 mb-2" for="portfolio_url">Portfolio/Work Samples</label>
                    <input id="portfolio_url" type="url" name="portfolio_url" value="<?= set_value('portfolio_url', $p->portfolio_url ?? '') ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="https://drive.google.com/...">
                    <div class="form-hint text-xs text-gray-500 mt-1">Link to your portfolio, Google Drive, or work samples</div>
                  </div>
                  <div>
                    <label class="form-label block text-sm font-medium text-gray-700 mb-2" for="facebook_url">Facebook Profile</label>
                    <input id="facebook_url" type="url" name="facebook_url" value="<?= set_value('facebook_url', $p->facebook_url ?? '') ?>" class="form-input w-full border-gray-300 rounded-lg" placeholder="https://facebook.com/username">
                    <div class="form-hint text-xs text-gray-500 mt-1">Your professional Facebook page or profile</div>
                  </div>
                </div>
              </div>

              <!-- Footer actions -->
              <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                  <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="primary-button inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                      </svg>
                      Save Profile
                    </button>
                    <a class="secondary-button inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100" href="<?= site_url('dashboard/worker') ?>">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      Cancel
                    </a>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Your data is securely encrypted
                  </div>
                </div>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>

    <!-- Guided Steps Widget -->
    <div id="wkGuide" class="fixed bottom-4 right-4 z-[9998] hidden">
      <button id="wkGuideBeacon" class="shadow-lg rounded-full px-4 py-2 bg-red-600 text-white text-sm font-semibold flex items-center gap-2">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
        </svg>
        Guided steps
      </button>
      <div id="wkGuideCard" class="hidden w-[320px] max-w-[92vw] bg-white border border-gray-200 rounded-xl shadow-xl p-4">
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="text-xs text-gray-500 font-semibold tracking-wide uppercase">Profile setup</div>
            <div id="wkGuideTitle" class="text-sm font-bold text-gray-900 mt-0.5">Step</div>
          </div>
          <button id="wkGuideDismiss" class="text-gray-400 hover:text-gray-600" title="Hide forever">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <p id="wkGuideMsg" class="text-sm text-gray-700 mt-2">Message</p>

        <div class="flex items-center justify-between mt-3">
          <div id="wkGuideDots" class="flex gap-1.5"></div>
          <div class="flex gap-2">
            <button id="wkGuideSkip" class="px-2 py-1.5 text-sm rounded-lg bg-gray-100">Skip</button>
            <button id="wkGuideGo" class="px-3 py-1.5 text-sm rounded-lg bg-red-600 text-white font-semibold">Proceed</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Add/Edit Document Modal -->
    <div class="fixed inset-0 z-[9999] hidden" id="docModalRoot" aria-hidden="true">
      <div class="absolute inset-0 bg-black/50"></div>
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
              <h3 class="text-base font-semibold text-gray-900" id="docModalTitle">Add Document</h3>
              <p class="text-xs text-gray-500 mt-1">Attach certificates (PDF/JPG/PNG/WEBP). Expiry highlighting is automatic.</p>
            </div>
            <button type="button" class="p-2 text-gray-500 hover:text-gray-700" id="docModalClose" aria-label="Close">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form id="docForm" class="px-5 py-4" enctype="multipart/form-data">
            <?php if ($this->config->item('csrf_protection')): ?>
              <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <?php endif; ?>

            <input type="hidden" name="id" id="doc_id" value="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="doc_name">Document Name</label>
                <input id="doc_name" name="doc_name" type="text" class="form-input w-full border-gray-300 rounded-lg" placeholder="e.x: NBI Clearance" required>
              </div>

              <div>
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="doc_type_id">Document Type</label>
                <select id="doc_type_id" name="doc_type_id" class="w-full"></select>
              </div>

              <div id="skillWrap" class="hidden">
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="skill_id">Certificate / Qualification</label>
                <select id="skill_id" name="skill_id" class="w-full"></select>
              </div>
              <!-- NEW: Others-only mini dropdown -->
              <div id="otherWrap" class="hidden">
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="other_choice">
                  Category
                </label>
                <select id="other_choice" name="other_choice" class="form-input w-full border-gray-300 rounded-lg">
                  <option value="">-- Select --</option>
                  <option value="document">Document</option>
                  <option value="certificate">Certificate</option>
                </select>
                <div class="text-[11px] text-gray-500 mt-1">Shown for non-TESDA types</div>
              </div>

              <div id="expiryWrap" class="hidden">
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="expiry_date">Expiry Date</label>
                <input id="expiry_date" name="expiry_date" type="date" class="form-input w-full border-gray-300 rounded-lg">
              </div>

              <div class="md:col-span-2">
                <label class="form-label block text-sm font-medium text-gray-700 mb-1">Attach File</label>
                <input type="file" name="doc_file" id="doc_file" accept=".pdf,.jpg,.jpeg,.png,.webp">
                <div class="text-[11px] text-gray-500 mt-1">Max ~2MB recommended — PDF/JPG/PNG/WEBP</div>
              </div>


            </div>


            <div class="px-0 py-4 border-t border-gray-200 mt-4 flex items-center justify-end gap-2">
              <button type="button" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700" id="docModalCancel">Cancel</button>
              <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Add/Edit Experience Modal -->
    <div class="fixed inset-0 z-[9999] hidden" id="expModalRoot" aria-hidden="true">
      <div class="absolute inset-0 bg-black/50"></div>
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
              <h3 class="text-base font-semibold text-gray-900" id="expModalTitle">Add Experience</h3>
              <p class="text-xs text-gray-500 mt-1">Use clear, concise role titles and highlight key outcomes.</p>
            </div>
            <button type="button" class="p-2 text-gray-500 hover:text-gray-700" id="expModalClose" aria-label="Close">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form id="expForm" class="px-5 py-4">
            <?php if ($this->config->item('csrf_protection')): ?>
              <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <?php endif; ?>

            <input type="hidden" name="id" id="exp_id" value="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="exp_role">Role / Title</label>
                <input id="exp_role" name="role" type="text" class="form-input w-full border-gray-300 rounded-lg" placeholder="e.x: Web Developer" required>
              </div>

              <div>
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="exp_employer">Employer / Project</label>
                <input id="exp_employer" name="employer" type="text" class="form-input w-full border-gray-300 rounded-lg" placeholder="e.x: ABC Solutions" required>
              </div>

              <div>
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="exp_from">Start (YYYY-MM)</label>
                <input id="exp_from" name="from" type="month" class="form-input w-full border-gray-300 rounded-lg">
              </div>

              <div>
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="exp_to">End (YYYY-MM)</label>
                <div class="flex items-center gap-2">
                  <input id="exp_to" name="to" type="month" class="form-input w-full border-gray-300 rounded-lg">
                  <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" id="exp_present" class="rounded"> Present
                  </label>
                </div>
              </div>

              <div class="md:col-span-2">
                <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="exp_desc">Description</label>
                <textarea id="exp_desc" name="desc" rows="4" class="form-input w-full border-gray-300 rounded-lg resize-y" placeholder="Key responsibilities, tools used, major achievements..."></textarea>
              </div>
            </div>

            <div class="px-0 py-4 border-t border-gray-200 mt-4 flex items-center justify-end gap-2">
              <button type="button" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700" id="expModalCancel">Cancel</button>
              <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php
    $skillOptionsJson = json_encode($skillOptions ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>
    <script id="skillOptionsJson" type="application/json">
      <?= $skillOptionsJson ?>
    </script>
    <script id="ncOptionsJson" type="application/json">
      <?= $ncOptionsJson ?>
    </script>
    <script id="currentNCsJson" type="application/json">
      <?= $currentNCsJson ?>
    </script>

    <?php
    $workerJsPath = FCPATH . 'assets/js/workerEdit.js';
    $workerJsUrl  = base_url('assets/js/workerEdit.js?v=' . (is_file($workerJsPath) ? filemtime($workerJsPath) : time()));
    ?>
    <script defer src="<?= $workerJsUrl ?>"></script>
    <script>
      (function(w) {
        if (!w.htmlEscape) {
          w.htmlEscape = function(s) {
            return String(s || '').replace(/[&<>"'`=\/]/g, function(c) {
              return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
              })[c];
            });
          };
        }
      })(window);
    </script>
    <script>
      (function DocsTableAndModal() {
        function openDocModal(edit = false, row = null) {
          document.getElementById('docModalTitle').textContent =
            edit ? 'Edit Document' : 'Add Document';

          resetDocForm();

          const showOrHideOtherWrap = (selected) => {
            const text = selected?.text || '';
            const code = selected?.code || '';
            if (isNonTesda(text, code)) {
              $('#otherWrap').removeClass('hidden');
              // prefer saved value from API when editing
              $('#other_choice').val(row?.other_choice || '');
            } else {
              $('#otherWrap').addClass('hidden');
              $('#other_choice').val(''); // clear when TESDA is chosen
            }
          };

          if (edit && row) {
            $('#doc_id').val(row.id);
            $('#doc_name').val(row.doc_name);
            $('#is_active').val(row.is_active ? '1' : '0');

            ensureDocTypes().then(async () => {
              if (row.doc_type_id) {
                // set the type and fire change so dependent UI resets
                $('#doc_type_id').val(String(row.doc_type_id)).trigger('change');
              }

              // now that select2 is ready + value set, inspect current selection
              const sel = $('#doc_type_id').select2('data')[0] || null;
              showOrHideOtherWrap(sel);

              if (row.skill_id) {
                await ensureSkills();
                const opt = new Option(row.skill || '', row.skill_id, true, true);
                $('#skill_id').append(opt).trigger('change');
              }

              if (row.expiry_date) $('#expiry_date').val(row.expiry_date);
            });
          } else {
            ensureDocTypes().then(() => {
              // fresh modal: hide "Others" until user picks a non-TESDA type
              $('#otherWrap').addClass('hidden');
              $('#other_choice').val('');
            });
          }

          document.getElementById('docModalRoot').classList.remove('hidden');
        }

        function closeDocModal() {
          document.getElementById('docModalRoot').classList.add('hidden');
        }

        function resetDocForm() {
          const frm = document.getElementById('docForm');
          frm.reset();
          $('#doc_id').val('');
          if ($('#doc_type_id').data('select2')) $('#doc_type_id').val(null).trigger('change');
          if ($('#skill_id').data('select2')) $('#skill_id').val(null).trigger('change');
          document.getElementById('skillWrap').classList.add('hidden');
          document.getElementById('expiryWrap').classList.add('hidden');
        }
        let docTypesLoaded = false;

        function ensureDocTypes() {
          const $sel = $('#doc_type_id');
          if (docTypesLoaded && $sel.data('select2')) {
            $sel.val(null).trigger('change');
            return Promise.resolve();
          }

          return fetch('<?= site_url('profile/doc_types_json') ?>', {
              credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(list => {
              const data = (list || []).map(x => ({
                id: String(x.id),
                text: x.doc_type || x.text || '',
                code: (x.code || '').toUpperCase()
              }));

              $sel.empty();
              $sel.append(new Option('', '', true, false));

              $sel.select2({
                data,
                placeholder: '-- Select Type --',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#docModalRoot')
              });

              $sel.val(null).trigger('change');

              docTypesLoaded = true;
              return true;
            });
        }

        function isTesdaType(text, code) {
          const t = (text || '').toLowerCase();
          const c = (code || '').toLowerCase();
          // treat anything that clearly references TESDA / NC / Qualification as TESDA
          return t.includes('tesda') || t.includes('nc ') || t.endsWith(' nc') || t.includes('qualification') ||
            c === 'tesda' || c === 'tesda_cert' || c === 'tesda certificate' || c === 'nc' || c === 'qualification';
        }

        // Non-TESDA = everything else
        function isNonTesda(text, code) {
          return !isTesdaType(text, code);
        }
        $('#doc_type_id').on('change', async function() {
          const sel = $('#doc_type_id').select2('data')[0] || null;
          const text = sel?.text || '';
          const code = sel?.code || '';
          // reset all sections
          $('#skillWrap').addClass('hidden');
          $('#expiryWrap').addClass('hidden');
          $('#otherWrap').addClass('hidden');

          if (isTesdaType(text, code)) {
            // TESDA flow: show TESDA cert picker + expiry
            await ensureSkills();
            $('#skillWrap, #expiryWrap').removeClass('hidden');
            $('#other_choice').val('');
          } else {
            // NON-TESDA flow: show the Document/Certificate mini dropdown
            $('#otherWrap').removeClass('hidden');
            // clear TESDA-specific widgets
            $('#skill_id').val(null).trigger('change');
            $('#expiry_date').val('');
          }
        });


        let skillsInited = false;

        function ensureSkills() {
          if (skillsInited) return Promise.resolve();
          const $form = $('#docForm');
          const csrfName = $form.find('input[type="hidden"]').first().attr('name');
          const csrfVal = $form.find(`input[name="${csrfName}"]`).val();

          $('#skill_id').select2({
            placeholder: '--Select Tesda Cert--',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#docModalRoot'),
            minimumInputLength: 0,
            ajax: {
              url: '<?= site_url('profile/skills_json') ?>',
              dataType: 'json',
              delay: 200,
              headers: {
                'X-Requested-With': 'XMLHttpRequest'
              }, // <-- add this
              data: params => ({
                q: params.term || '',
                page: params.page || 1
              }),
              processResults: (data, params) => ({
                results: data?.results || [],
                pagination: {
                  more: !!data?.pagination?.more
                }
              }),
              cache: true
            }
          });

          skillsInited = true;
          return Promise.resolve();
        }


        const docsWrap = document.getElementById('docsTableWrap');
        const docsEmpty = document.getElementById('docsEmpty');

        function toggleDocsVisibility(count) {
          if (count > 0) {
            docsWrap.classList.remove('hidden');
            docsEmpty.classList.add('hidden');
          } else {
            docsWrap.classList.add('hidden');
            docsEmpty.classList.remove('hidden');
          }
        }
        const table = $('#documentsTable').DataTable({
          responsive: true,
          processing: true,
          serverSide: false,
          paging: false,
          info: false,
          searching: false,

          lengthChange: false,
          ajax: {
            url: '<?= site_url('profile/documents_json') ?>',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            },
            cache: false,
            timeout: 15000,
            dataSrc: function(json) {
              if (!json || typeof json !== 'object') return [];
              const rows = Array.isArray(json.data) ? json.data : [];
              toggleDocsVisibility(rows.length);
              return rows;
            },
            error: function(xhr, status, err) {
              console.error('documents_json error:', status, err, xhr && xhr.responseText);
              toggleDocsVisibility(0);
              if (window.toast) toast('Failed to load documents', false);
            }
          },
          columns: [{
              data: null,
              title: 'Document',
              render: (row) => {
                const name = row.doc_name || '(Untitled)';
                return `<div class="font-medium text-slate-800">${htmlEscape(name)}</div>`;
              }
            },
            {
              data: 'doc_type',
              title: 'Type',
              render: (d, _, row) => {
                const t = d || row.type || '';
                return t ? `<span class="chip chip--type">${htmlEscape(t)}</span>` : '';
              }
            },
            {
              data: 'skill',
              title: 'Certificate',
              defaultContent: '',
              render: (d, _type, row) => {
                // TESDA rows: show the qualification name
                if (d) {
                  return `<span class="chip chip--skill">${htmlEscape(d)}</span>`;
                }

                // Non-TESDA rows: show mini dropdown value if the API returns it
                const oc = (row?.other_choice || '').toString().toLowerCase();
                const label =
                  oc === 'certificate' ? 'Certificate' :
                  oc === 'document' ? 'Document' :
                  '';

                return label ?
                  `<span class="chip chip--skill">${htmlEscape(label)}</span>` :
                  '<span class="text-slate-400">—</span>';
              }
            },

            {
              data: 'expiry_date',
              title: 'Expiry',
              render: d => {
                if (!d) return '<span class="text-slate-400">—</span>';
                const today = moment().startOf('day');
                const exp = moment(d, ['YYYY-MM-DD', 'YYYY/MM/DD', 'MM/DD/YYYY'], true);
                if (!exp.isValid()) return htmlEscape(d);
                const diff = exp.diff(today, 'days');
                let cls = 'badge--ok',
                  txt = exp.format('YYYY-MM-DD');
                if (diff < 0) {
                  cls = 'badge--danger';
                  txt = `Expired ${exp.fromNow()}`;
                } else if (diff <= 30) {
                  cls = 'badge--warn';
                  txt = `In ${diff} day${diff===1?'':'s'} (${exp.format('YYYY-MM-DD')})`;
                }
                return `<span class="badge ${cls}">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 8v5l3 3"/><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
         ${htmlEscape(txt)}
        </span>`;
              }
            },
            {
              data: 'file_path',
              title: 'File',
              render: d => {
                if (!d) return '<span class="text-slate-400">—</span>';
                const rel = String(d).replace(/^\/+/, '');
                const isAbsolute = /^https?:\/\//i.test(rel);
                const isUploads = /^uploads\//i.test(rel);
                const isPdf = /\.pdf(\?|$)/i.test(rel);
                const label = isPdf ? 'PDF' : 'File';
                const href = (isUploads) ?
                  '<?= site_url('media/preview') ?>' + '?f=' + encodeURIComponent(rel) :
                  (isAbsolute ? rel : '<?= base_url() ?>' + rel);

                return `<a class="file-pill" href="${htmlEscape(href)}" target="_blank" rel="noopener">
      <svg viewBox="0 0 24 24"><path d="M7 3h7l5 5v13H7z" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
      ${label}
    </a>`;
              }
            },

            {
              data: null,
              orderable: false,
              searchable: false,
              render: (row) => {
                const dataAttr = encodeURIComponent(JSON.stringify(row));
                return `
          <div class="flex items-center gap-2 justify-end">
            <button type="button" class="btn-ghost" data-doc-edit='${dataAttr}'>Edit</button>
            <button type="button" class="btn-danger" data-doc-del='${row.id}'>Delete</button>
          </div>`;
              }
            }
          ],
          order: [
            [3, 'asc']
          ]
        });
        table.on('xhr.dt', function() {
          const json = table.ajax.json();
          const len = (json && Array.isArray(json.data)) ? json.data.length : 0;
          toggleDocsVisibility(len);
        });



        $('#btnAddDoc').on('click', function() {
          openDocModal(false, null);
        });
        $('#documentsTable').on('click', '[data-doc-edit]', async function() {
          const row = JSON.parse(decodeURIComponent(this.getAttribute('data-doc-edit') || '%7B%7D'));
          await ensureSkills();
          openDocModal(true, row);
        });

        $('#documentsTable').on('click', '[data-doc-del]', async function() {
          const id = this.getAttribute('data-doc-del');
          if (!id) return;
          if (!confirm('Delete this document?')) return;
          try {
            const res = await fetch('<?= site_url('profile/delete_document') ?>/' + id, {
              method: 'POST',
              credentials: 'same-origin',
              headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const json = await res.json();
            if (!res.ok || !json?.ok) throw new Error(json?.error || 'Delete failed');
            table.ajax.reload(null, false);
            if (window.toast) toast('Document deleted', true);
          } catch (err) {
            console.error(err);
            alert('Delete failed: ' + err.message);
            if (window.toast) toast('Delete failed', false);
          }
        });

        document.getElementById('docModalClose').addEventListener('click', closeDocModal);
        document.getElementById('docModalCancel').addEventListener('click', closeDocModal);
        document.getElementById('docModalRoot').addEventListener('click', (e) => {
          if (e.target.id === 'docModalRoot') closeDocModal();
        });

        document.getElementById('docForm').addEventListener('submit', async (e) => {
          e.preventDefault();
          const fd = new FormData(e.currentTarget);
          try {
            const res = await fetch('<?= site_url('profile/save_document') ?>', {
              method: 'POST',
              credentials: 'same-origin',
              body: fd,
              headers: {
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const ct = (res.headers.get('Content-Type') || '').toLowerCase();
            const json = ct.includes('application/json') ? await res.json() : {
              ok: false,
              error: 'Non-JSON response'
            };
            if (!res.ok || !json.ok) throw new Error(json.error || 'Save failed');
            closeDocModal();
            table.ajax.reload(null, false);
            if (window.toast) toast(json.mode === 'updated' ? 'Document updated' : 'Document added', true);
          } catch (err) {
            console.error(err);
            alert('Save failed: ' + err.message);
            if (window.toast) toast('Save failed', false);
          }
        });
      })();
    </script>
    <script>
      (function ExperiencesTableAndModal() {
        const expWrap = document.getElementById('expTableWrap');
        const expEmpty = document.getElementById('expEmpty');

        function toggleExpVisibility(n) {
          (n > 0 ? expWrap : expEmpty).classList.remove('hidden');
          (n > 0 ? expEmpty : expWrap).classList.add('hidden');
        }

        // period formatter (YYYY / YYYY-MM supported)
        function fmtYM(s) {
          if (!s) return '';
          const m = moment(s, ['YYYY-MM', 'YYYY', 'YYYY-MM-DD'], true);
          if (!m.isValid()) return s;
          const inFmt = m.creationData().format;
          return inFmt === 'YYYY' ? m.format('YYYY') : m.format('MMM YYYY');
        }

        function renderPeriod(row) {
          const a = fmtYM(row.from);
          const b = row.to_present ? '' : fmtYM(row.to);
          if (!a && !b) return '<span class="text-slate-400">—</span>';
          const txt = a && b ? `${a} — ${b}` : `${a || ''} — <span class="text-slate-500">Present</span>`;
          return `<span class="period-badge">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 8v5l3 3"></path><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.5"></circle></svg>
      ${txt}
    </span>`;
        }

        // clamp description & allow “More”
        function renderDesc(txt) {
          if (!txt) return '<span class="text-slate-400">—</span>';
          const safe = htmlEscape(txt);
          if (safe.length <= 220) return `<div class="desc-cell" title="${safe}">${safe}</div>`;
          const short = safe.slice(0, 220) + '…';
          return `<div class="desc-cell" title="${safe}">${short}</div>
            <button type="button" class="desc-more" data-show-full='${encodeURIComponent(safe)}'>More</button>`;
        }

        const expTable = $('#experiencesTable').DataTable({
          responsive: true,
          processing: true,
          serverSide: false,
          paging: false,
          info: false,
          searching: false,
          lengthChange: false,
          ajax: {
            url: '<?= site_url('profile/experience_json') ?>',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            },
            cache: false,
            timeout: 15000,
            dataSrc: json => {
              const rows = (json && Array.isArray(json.data)) ? json.data : [];
              toggleExpVisibility(rows.length);
              return rows;
            },
            error: () => {
              toggleExpVisibility(0);
              window.toast && toast('Failed to load experiences', false);
            }
          },
          columns: [{
              data: 'role',
              title: 'Role / Title',
              responsivePriority: 1,
              render: d => d ? `<div class="font-medium text-slate-800">${htmlEscape(d)}</div>` : '(Untitled)'
            },
            {
              data: 'employer',
              title: 'Employer / Project',
              responsivePriority: 2,
              render: d => d ? `<span class="chip chip--employer">${htmlEscape(d)}</span>` : '<span class="text-slate-400">—</span>'
            },
            {
              data: null,
              title: 'Period',
              responsivePriority: 3,
              render: renderPeriod
            },
            {
              data: 'desc',
              title: 'Description',
              responsivePriority: 4,
              render: renderDesc
            },
            {
              data: null,
              orderable: false,
              searchable: false,
              responsivePriority: 1,
              render: (row) => {
                const dataAttr = encodeURIComponent(JSON.stringify(row));
                return `<div class="flex items-center gap-2 justify-end">
            <button type="button" class="btn-ghost"  data-exp-edit='${dataAttr}'>Edit</button>
            <button type="button" class="btn-danger" data-exp-del='${row.id}'>Delete</button>
          </div>`;
              }
            }
          ],
          order: [
            [2, 'desc']
          ], // newest period first
          columnDefs: [{
              targets: 4,
              width: 120
            } // Action
          ]
        });

        expTable.on('xhr.dt', function() {
          const json = expTable.ajax.json();
          const len = (json && Array.isArray(json.data)) ? json.data.length : 0;
          toggleExpVisibility(len);
        });

        // open/close modal – your existing code already does this
        const root = document.getElementById('expModalRoot');
        const title = document.getElementById('expModalTitle');
        const form = document.getElementById('expForm');
        const closeB = document.getElementById('expModalClose');
        const cancel = document.getElementById('expModalCancel');

        const idEl = document.getElementById('exp_id');
        const roleEl = document.getElementById('exp_role');
        const empEl = document.getElementById('exp_employer');
        const fromEl = document.getElementById('exp_from');
        const toEl = document.getElementById('exp_to');
        const presEl = document.getElementById('exp_present');
        const descEl = document.getElementById('exp_desc');

        function openExpModal(edit = false, row = null) {
          title.textContent = edit ? 'Edit Experience' : 'Add Experience';
          form.reset();
          idEl.value = '';
          if (edit && row) {
            idEl.value = row.id || '';
            roleEl.value = row.role || '';
            empEl.value = row.employer || '';
            fromEl.value = (row.from && /^\d{4}-\d{2}$/.test(row.from)) ? row.from : (row.from && /^\d{4}$/.test(row.from)) ? `${row.from}-01` : '';
            const isPresent = !!row.to_present || !row.to;
            presEl.checked = isPresent;
            toEl.disabled = isPresent;
            toEl.value = (!isPresent && row.to && /^\d{4}-\d{2}$/.test(row.to)) ? row.to : (!isPresent && row.to && /^\d{4}$/.test(row.to)) ? `${row.to}-01` : '';
            descEl.value = row.desc || '';
          } else {
            presEl.checked = false;
            toEl.disabled = false;
          }
          root.classList.remove('hidden');
        }

        function closeExpModal() {
          root.classList.add('hidden');
        }

        document.getElementById('btnAddExp').addEventListener('click', () => openExpModal(false, null));
        document.getElementById('experiencesTable').addEventListener('click', (e) => {
          const more = e.target.closest('[data-show-full]');
          if (more) {
            const full = decodeURIComponent(more.getAttribute('data-show-full') || '');
            alert(full); // simple viewer; replace with a nice modal if you want
            return;
          }
          const editBtn = e.target.closest('[data-exp-edit]');
          const delBtn = e.target.closest('[data-exp-del]');
          if (editBtn) {
            const row = JSON.parse(decodeURIComponent(editBtn.getAttribute('data-exp-edit') || '%7B%7D'));
            openExpModal(true, row);
          } else if (delBtn) {
            const id = delBtn.getAttribute('data-exp-del');
            if (!id) return;
            if (!confirm('Delete this experience?')) return;
            (async () => {
              try {
                const res = await fetch('<?= site_url('profile/delete_experience') ?>/' + id, {
                  method: 'POST',
                  credentials: 'same-origin',
                  headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                  }
                });
                const json = await res.json();
                if (!res.ok || !json?.ok) throw new Error(json?.error || 'Delete failed');
                expTable.ajax.reload(null, false);
                window.toast && toast('Experience deleted', true);
              } catch (err) {
                console.error(err);
                alert('Delete failed: ' + err.message);
                window.toast && toast('Delete failed', false);
              }
            })();
          }
        });

        presEl.addEventListener('change', () => {
          toEl.disabled = presEl.checked;
          if (presEl.checked) toEl.value = '';
        });

        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const fd = new FormData(form);
          if (presEl.checked) {
            fd.set('to_present', '1');
            fd.set('to', '');
          } else {
            fd.delete('to_present');
          }
          try {
            const res = await fetch('<?= site_url('profile/save_experience') ?>', {
              method: 'POST',
              body: fd,
              credentials: 'same-origin',
              headers: {
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const ct = (res.headers.get('Content-Type') || '').toLowerCase();
            const json = ct.includes('application/json') ? await res.json() : {
              ok: false,
              error: 'Non-JSON response'
            };
            if (!res.ok || !json.ok) throw new Error(json.error || 'Save failed');
            closeExpModal();
            expTable.ajax.reload(null, false);
            window.toast && toast(json.mode === 'updated' ? 'Experience updated' : 'Experience added', true);
          } catch (err) {
            console.error(err);
            alert('Save failed: ' + err.message);
            window.toast && toast('Save failed', false);
          }
        });

        closeB.addEventListener('click', closeExpModal);
        cancel.addEventListener('click', closeExpModal);
        document.getElementById('expModalRoot').addEventListener('click', e => {
          if (e.target.id === 'expModalRoot') closeExpModal();
        });
      })();
    </script>


    <script>
      (function() {
        const $ = sel => document.querySelector(sel);
        const $$ = sel => Array.from(document.querySelectorAll(sel));
        window.jQuery(function($) {
          const $prov = $('#province');
          if ($prov.length) {
            $prov.select2({
              placeholder: '- Select Province -',
              allowClear: true,
              width: '100%',
              dropdownParent: $prov.closest('.input-group')
            });
          }
        });

        window.toast = function(msg, ok = true) {
          const box = document.getElementById('toaster');
          if (!box) return;
          const t = document.createElement('div');
          t.className = 'toast ' + (ok ? 'toast--ok' : 'toast--err');
          t.textContent = msg;
          box.appendChild(t);
          setTimeout(() => {
            t.style.transition = 'opacity .18s ease, transform .18s ease';
            t.style.opacity = '0';
            t.style.transform = 'translateY(-8px) scale(.98)';
          }, 2200);
          setTimeout(() => t.remove(), 2400);
        };

        (function initTabs() {
          const tabButtons = $$('.tab-button');
          const panels = $$('.tab-panel');

          function showTab(key) {
            tabButtons.forEach(b => b.classList.remove('active', 'bg-gray-900', 'text-white'));
            panels.forEach(p => p.classList.add('hidden'));
            const btn = tabButtons.find(b => b.dataset.tab === key) || tabButtons[0];
            const panel = document.querySelector('[data-panel="' + btn.dataset.tab + '"]');
            btn.classList.add('active', 'bg-gray-900', 'text-white');
            panel.classList.remove('hidden');
            localStorage.setItem('worker_edit_tab', btn.dataset.tab);
          }
          tabButtons.forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));
          showTab(localStorage.getItem('worker_edit_tab') || 'info');
        })();

        (function initBio() {
          const bio = $('#bio'),
            cnt = $('#bioCount');

          function upd() {
            if (bio && cnt) cnt.textContent = (bio.value.length || 0) + ' / 600';
          }
          bio && bio.addEventListener('input', upd);
          upd();
        })();

        (function initAvatar() {
          const avatarInp = $('#avatar');
          const avatarImg = $('#avatarPreview');
          const avatarPrevWrap = $('#preview-avatar');

          function previewAvatar(file) {
            if (!file || !file.type.match(/^image\//)) return;
            const r = new FileReader();
            r.onload = e => {
              if (avatarImg) avatarImg.src = e.target.result;
              if (avatarPrevWrap) {
                avatarPrevWrap.innerHTML = '';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                img.className = 'w-[140px] h-auto rounded-xl border border-gray-200';
                avatarPrevWrap.appendChild(img);
              }
            };
            r.readAsDataURL(file);
          }
          avatarInp && avatarInp.addEventListener('change', e => previewAvatar(e.target.files?.[0]));

          const avatarForm = $('#avatarForm');
          if (avatarForm) {
            avatarForm.addEventListener('submit', async (e) => {
              e.preventDefault();
              const fd = new FormData(avatarForm);
              try {
                const res = await fetch(avatarForm.action, {
                  method: 'POST',
                  body: fd,
                  credentials: 'same-origin'
                });
                const ct = (res.headers.get('Content-Type') || '').toLowerCase();
                let data = null;
                if (ct.includes('application/json')) {
                  data = await res.json();
                } else {
                  const text = await res.text();
                  console.warn('Unexpected response content-type:', ct, 'Body:', text);
                  try {
                    data = JSON.parse(text);
                  } catch (e) {
                    console.error('Failed to parse response:', e);
                  }
                }
                if (!res.ok) throw new Error('HTTP ' + res.status + (data?.error ? ': ' + data.error : ''));
                if (!data?.ok) throw new Error(data?.error || 'Upload failed');
                if (data.avatar_url && avatarImg) {
                  avatarImg.src = data.avatar_url + '?t=' + Date.now();
                  console.log('Avatar updated:', data.avatar_url);
                }
                if (data.csrf_name && data.csrf_hash) {
                  document.querySelectorAll('input[name="' + data.csrf_name + '"]').forEach(el => el.value = data.csrf_hash);
                }
                toast('Photo uploaded successfully!', true);
              } catch (err) {
                console.error('Avatar upload error:', err);
                toast(err.message || 'Upload failed. Please try again.', false);
              }
            });
          }
        })();

        (function initAvailability() {
          const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          const pick = id => $('#' + id);
          pick('pickWeekdays')?.addEventListener('click', () => {
            days.forEach((d, i) => $('#day_' + d).checked = i < 5);
          });
          pick('pickAll')?.addEventListener('click', () => {
            days.forEach(d => $('#day_' + d).checked = true);
          });
          pick('clearDays')?.addEventListener('click', () => {
            days.forEach(d => $('#day_' + d).checked = false);
          });
        })();
        (async function initAddressCascade() {
          const provinceSel = $('#province'),
            citySel = $('#city'),
            brgySel = $('#brgy');
          if (!provinceSel || !citySel || !brgySel) return;

          const apiBase = document.body.dataset.apiAddress || '<?= site_url('address/api') ?>';
          const LIMIT_PROVINCE = (document.body.dataset.limitProvince || '').trim();
          const lockProvince = LIMIT_PROVINCE.length > 0;

          let preProvince = (provinceSel.dataset.pre ?? '').trim() || <?= json_encode((string)($p->province ?? '')) ?>;
          let preCity = (citySel.dataset.pre ?? '').trim() || <?= json_encode((string)($p->city ?? '')) ?>;
          let preBrgy = (brgySel.dataset.pre ?? '').trim() || <?= json_encode((string)($p->brgy ?? '')) ?>;
          if (lockProvince) preProvince = LIMIT_PROVINCE;

          function fillOptions(selectEl, items, placeholder) {
            const isjQ = typeof window.jQuery !== 'undefined';
            const $sel = isjQ ? window.jQuery(selectEl) : null;
            const ph = placeholder || 'Select';

            if ($sel && $sel.data('select2')) {
              $sel.empty();
              $sel.append(new Option(ph, '', true, false));
              items.forEach(v => $sel.append(new Option(v, v, false, false)));

              // ✅ trigger REAL change so your cascade reacts
              $sel.trigger('change');
            } else {
              selectEl.innerHTML = '';
              const opt0 = document.createElement('option');
              opt0.value = '';
              opt0.textContent = ph;
              selectEl.appendChild(opt0);
              items.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v;
                opt.textContent = v;
                selectEl.appendChild(opt);
              });

              // ✅ optional but safe
              selectEl.dispatchEvent(new Event('change', {
                bubbles: true
              }));
            }
          }


          async function api(params) {
            const url = apiBase + '?' + new URLSearchParams(params).toString();
            const res = await fetch(url, {
              credentials: 'same-origin'
            });
            const text = await res.text();
            if (!res.ok) throw new Error('HTTP ' + res.status + ' ' + text.slice(0, 120));
            let json;
            try {
              json = JSON.parse(text);
            } catch {
              throw new Error('API did not return JSON');
            }
            if (!json.ok) throw new Error(json.msg || 'API error');
            return json.items || [];
          }

          async function loadProvinces() {
            citySel.disabled = true;
            brgySel.disabled = true;

            // If DAVAO ORIENTAL is forced, lock province UI and auto-load cities
            if (lockProvince) {
              fillOptions(provinceSel, [LIMIT_PROVINCE], '- Select Province -');

              // set value + disable so user can't change it
              provinceSel.value = LIMIT_PROVINCE;
              provinceSel.disabled = true;

              // If select2 is active, set it properly and disable select2 too
              const $prov = window.jQuery && window.jQuery('#province');
              if ($prov && $prov.length && $prov.data('select2')) {
                $prov.val(LIMIT_PROVINCE).trigger('change'); // not change.select2
                $prov.prop('disabled', true);
              }

              // now load cities for DAVAO ORIENTAL (and apply preCity/preBrgy if any)
              await onProvinceChange(true);
              return;
            }

            // Normal behavior (all provinces)
            fillOptions(provinceSel, [], 'Loading provinces…');
            try {
              let items = await api({
                scope: 'province'
              });
              fillOptions(provinceSel, items, '- Select Province -');
            } catch (e) {
              console.error(e);
              fillOptions(provinceSel, [], 'Unavailable — refresh to retry');
              toast('Address service error: ' + e.message, false);
              return;
            }

            if (preProvince && Array.from(provinceSel.options).some(o => o.value === preProvince)) {
              const $prov = window.jQuery && window.jQuery('#province');
              if ($prov && $prov.length && $prov.data('select2')) {
                $prov.val(preProvince).trigger('change');
              } else {
                provinceSel.value = preProvince;
              }
              await onProvinceChange(true);
            }
          }


          async function onProvinceChange(isInit = false) {
            const province = lockProvince ? LIMIT_PROVINCE : provinceSel.value;
            citySel.disabled = true;
            brgySel.disabled = true;
            fillOptions(citySel, [], '- Select City -');
            fillOptions(brgySel, [], '- Select Barangay -');
            if (!province) return;
            try {
              const items = await api({
                scope: 'city',
                province
              });
              fillOptions(citySel, items, '- Select City -');
            } catch (e) {
              console.error(e);
              fillOptions(citySel, [], 'Unavailable — refresh to retry');
              toast('City load error: ' + e.message, false);
              return;
            } finally {
              citySel.disabled = false;
            }
            if (isInit && preCity && Array.from(citySel.options).some(o => o.value === preCity)) {
              citySel.value = preCity;
              await onCityChange(true);
            }
          }

          async function onCityChange(isInit = false) {
            const province = lockProvince ? LIMIT_PROVINCE : provinceSel.value;
            const city = citySel.value;
            brgySel.disabled = true;
            fillOptions(brgySel, [], '- Select Barangay -');
            if (!province || !city) return;
            try {
              const items = await api({
                scope: 'brgy',
                province,
                city
              });
              fillOptions(brgySel, items, '- Select Barangay -');
            } catch (e) {
              console.error(e);
              fillOptions(brgySel, [], 'Unavailable — refresh to retry');
              toast('Barangay load error: ' + e.message, false);
              return;
            } finally {
              brgySel.disabled = false;
            }
            if (isInit && preBrgy && Array.from(brgySel.options).some(o => o.value === preBrgy)) {
              brgySel.value = preBrgy;
            }
          }

          if (window.jQuery) {
            window.jQuery(provinceSel).on('change', () => onProvinceChange(false));
            window.jQuery(citySel).on('change', () => onCityChange(false));
          } else {
            provinceSel.addEventListener('change', () => onProvinceChange(false));
            citySel.addEventListener('change', () => onCityChange(false));
          }

          await loadProvinces();
        })();
      })();
    </script>
    <script>
      (function WorkerGuidePersistent() {
        const $ = s => document.querySelector(s);
        const $$ = s => Array.from(document.querySelectorAll(s));

        const guide = $('#wkGuide');
        if (!guide) return;
        const beacon = $('#wkGuideBeacon');
        const card = $('#wkGuideCard');
        const titleEl = $('#wkGuideTitle');
        const msgEl = $('#wkGuideMsg');
        const dotsEl = $('#wkGuideDots');
        const goBtn = $('#wkGuideGo');
        const skipBtn = $('#wkGuideSkip');
        const close = $('#wkGuideDismiss');

        function openTab(key) {
          const btn = document.querySelector('.tab-button[data-tab="' + key + '"]');
          if (btn) btn.click();
        }

        const steps = [{
            key: 'photo',
            title: 'Add a clear profile photo',
            msg: 'Clients respond more to profiles with a clear headshot. Proceed to upload.',
            go: () => {
              openTab('info');
              focusTarget($('#avatarForm .file-upload-area') || $('#avatarPreview'));
            }
          },

          {
            key: 'info',
            title: 'Fill your basic information',
            msg: 'Enter your name, headline and a short professional summary.',
            go: () => {
              openTab('info');
              focusTarget($('#first_name') || $('#headline') || $('#bio'), true);
            }
          },

          {
            key: 'skills',
            title: 'Add your skills',
            msg: 'Add at least 3 skills that match your work.',
            go: () => {
              openTab('skills');
              focusTarget($('#skillsSearch') || $('#skillsSelected'), true);
            }
          },

          {
            key: 'docs',
            title: 'Add your documents',
            msg: 'Click “Add Document”, choose a type. If you pick TESDA/NC/Qualification, you can select a certificate and set an expiry.',
            go: () => {
              openTab('docs');
              focusTarget(document.querySelector('#btnAddDoc'), true);
            }
          },

          {
            key: 'availability',
            title: 'Set your availability',
            msg: 'Pick the days you are available to work.',
            go: () => {
              openTab('availability');
              focusTarget($('#pickWeekdays') || $('[name="availability_days[]"]'), true);
            }
          },

          {
            key: 'save',
            title: 'Save your profile',
            msg: 'All done? Save to update your public profile.',
            go: () => {
              focusTarget(document.querySelector('button[type="submit"]'), true);
            }
          }
        ];

        const LS_STEP = 'worker_edit_guide_step';
        let idx = +localStorage.getItem(LS_STEP) || 0;
        idx = Math.min(Math.max(idx, 0), steps.length);

        function showBeacon() {
          beacon.classList.remove('hidden');
          card.classList.add('hidden');
        }

        function showCard() {
          beacon.classList.add('hidden');
          card.classList.remove('hidden');
        }

        function minimize() {
          showBeacon();
          removeHL();
        }

        function paint() {
          dotsEl.innerHTML = '';
          const done = idx >= steps.length;
          if (done) {
            titleEl.textContent = 'Guide complete';
            msgEl.textContent = 'You can reopen this guide anytime. Click “Restart” to review the steps again.';
            for (let i = 0; i < steps.length; i++) {
              const d = document.createElement('span');
              d.className = 'w-2 h-2 rounded-full bg-red-600';
              dotsEl.appendChild(d);
            }
            goBtn.textContent = 'Restart';
            skipBtn.textContent = 'Close';
            return;
          }
          const s = steps[idx];
          titleEl.textContent = `Step ${idx+1} of ${steps.length}: ${s.title}`;
          msgEl.textContent = s.msg;
          for (let i = 0; i < steps.length; i++) {
            const d = document.createElement('span');
            d.className = 'w-2 h-2 rounded-full ' + (i <= idx ? 'bg-red-600' : 'bg-gray-300');
            dotsEl.appendChild(d);
          }
          goBtn.textContent = 'Proceed';
          skipBtn.textContent = 'Skip';
        }

        let lastHL = null;

        function focusTarget(t, focusInput) {
          removeHL();
          if (!t) return;
          t.classList.add('wk-guide-highlight');
          lastHL = t;
          t.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
          });
          if (focusInput && typeof t.focus === 'function') setTimeout(() => t.focus({
            preventScroll: true
          }), 450);
        }

        function removeHL() {
          if (lastHL) {
            lastHL.classList.remove('wk-guide-highlight');
            lastHL = null;
          }
        }

        beacon.addEventListener('click', () => {
          showCard();
          paint();
        });
        goBtn.addEventListener('click', () => {
          if (idx >= steps.length) {
            idx = 0;
            localStorage.setItem(LS_STEP, '0');
            paint();
            return;
          }
          const s = steps[idx];
          s.go && s.go();
          if (idx < steps.length) {
            idx++;
            localStorage.setItem(LS_STEP, String(idx));
          }
          paint();
        });
        skipBtn.addEventListener('click', () => {
          if (idx < steps.length) {
            idx++;
            localStorage.setItem(LS_STEP, String(idx));
            paint();
          } else {
            minimize();
          }
        });
        close.addEventListener('click', minimize);

        guide.classList.remove('hidden');
        showBeacon();
      })();
    </script>

    <script>
      document.addEventListener('DOMContentLoaded', async () => {
        const saved = localStorage.getItem('lang_pref') || 'en';
        try {
          if (window.I18N) {
            await I18N.init({
              defaultLang: saved
            });
          }
          if (window.I18NAutoScan) {
            I18NAutoScan.init();
          }
          document.documentElement.setAttribute('lang', (I18N?.currentLang?.() || saved));
        } catch (e) {}

        const btn = document.getElementById('openTranslate');
        if (btn) {
          btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (I18N?.openPicker) return I18N.openPicker();
            if (I18N?.openBanner) return I18N.openBanner();
            document.dispatchEvent(new CustomEvent('i18n:open'));
          });
        }
      });
    </script>


  </body>

  </html>