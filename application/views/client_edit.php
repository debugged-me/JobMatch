<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="x-ua-compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= html_escape($page_title ?? 'Edit Client Profile') ?> - JobMatch DavOr</title>
  <meta name="theme-color" content="#1d4ed8" />
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/logo.png') ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <?php
  $editCssPath = FCPATH . 'assets/css/edit.css';
  $editCssUrl  = base_url('assets/css/edit.css') . (is_file($editCssPath) ? ('?v=' . filemtime($editCssPath)) : '');
  ?>
  <link rel="stylesheet" href="<?= $editCssUrl ?>">

  <style>
    body {
      font-family: "Inter", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial
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
      backdrop-filter: saturate(140%) blur(4px)
    }

    .upload-previews {
      display: grid;
      grid-template-columns: repeat(1, minmax(0, 1fr));
      gap: 1rem
    }

    @media (min-width:640px) {
      .upload-previews {
        grid-template-columns: repeat(2, minmax(0, 1fr))
      }
    }

    @media (min-width:1280px) {
      .upload-previews {
        grid-template-columns: repeat(3, minmax(0, 1fr))
      }
    }

    .doc-card-xl {
      aspect-ratio: 5/4
    }

    .doc-pdf-pane {
      background: linear-gradient(180deg, #f8fafc 0%, #eef1f5 38%, #d2d6dc 60%, #9aa0a6 100%)
    }

    .doc-pdf-badge {
      font-size: 11px;
      line-height: 1;
      padding: .35rem .55rem;
      border-radius: .5rem;
      background: #b91c1c;
      color: #fff;
      font-weight: 700;
      box-shadow: 0 1px 0 rgba(0, 0, 0, .12) inset, 0 6px 16px rgba(0, 0, 0, .15)
    }

    .doc-view-btn {
      position: absolute;
      left: 50%;
      bottom: 14px;
      transform: translateX(-50%);
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .5rem .9rem;
      border-radius: 9999px;
      background: #2563eb;
      color: #fff;
      font-weight: 700;
      font-size: .85rem;
      box-shadow: 0 8px 20px rgba(37, 99, 235, .35)
    }

    .doc-view-btn:hover {
      filter: brightness(1.05);
      transform: translateX(-50%) translateY(-1px)
    }
  </style>
</head>

<body class="bg-gray-50" data-api-address="<?= site_url('address/api') ?>">
  <?php $this->load->view('partials_translate_banner'); ?>

  <?php
  $p = $profile ?? null;

  $seed = trim(($p->fName ?? '') . ' ' . ($p->lName ?? '')) ?: ($this->session->userdata('first_name') ?? 'Client');
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
      $rel = is_file(FCPATH . $DEFAULT_AVATAR_REL) ? $DEFAULT_AVATAR_REL : null;
    }
    if ($rel) {
      $avatarLocalRel = $rel;
      $avatarUrl = base_url($rel);
    } else {
      $avatarUrl = 'https://api.dicebear.com/9.x/initials/svg?seed=' . urlencode($seed);
    }
    if ($avatarLocalRel && is_file(FCPATH . $avatarLocalRel)) {
      $avatarUrl = base_url($avatarLocalRel) . '?v=' . filemtime(FCPATH . $avatarLocalRel);
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

  $certs = [];
  if (!empty($p->certificates)) {
    $tmp = is_string($p->certificates) ? json_decode($p->certificates, true) : (array)$p->certificates;
    if (is_array($tmp)) $certs = array_values(array_filter($tmp));
  }
  $idViewerUrl     = !empty($p->id_image) ? (viewer_url_from_abs(base_url($p->id_image)) ?: base_url($p->id_image)) : null;
  $permitViewerUrl = !empty($p->business_permit) ? (viewer_url_from_abs(base_url($p->business_permit)) ?: base_url($p->business_permit)) : null;
  $idRel     = !empty($p->id_image) ? ltrim(str_replace('\\', '/', $p->id_image), '/') : null;
  $permitRel = !empty($p->business_permit) ? ltrim(str_replace('\\', '/', $p->business_permit), '/') : null;

  $isIndividualEmployer = client_is_individual_employer($p);
  $currentOrgLabel = client_org_label($p);
  $hasCompanyPosition = client_has_company_position_field();
  $companyNameCurrent = trim((string)set_value('companyName', $p->companyName ?? ''));
  $employerCurrent    = trim((string)set_value('employer', $p->employer ?? ''));
  $showCompanyPosition = $hasCompanyPosition && $companyNameCurrent !== '' && $employerCurrent !== '';

  ?>

  <div class="min-h-screen py-8 px-4">
    <div class="max-w-5xl mx-auto">
      <div class="main-container bg-white rounded-2xl shadow border border-gray-200 overflow-hidden">

        <!-- HEADER -->
        <div class="header-gradient px-8 py-6 bg-gradient-to-r from-blue-700 to-blue-500">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4 sm:gap-6">
              <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                  Edit Client Profile <span class="w-2 h-2 rounded-full bg-white/40"></span>
                </h1>
                <p class="text-white/80 text-sm font-medium mt-1">Keep your details up to date</p>
              </div>
            </div>

          </div>

          <?php if ($this->session->flashdata('error')): ?>
            <div class="mt-4 bg-red-50 text-red-800 px-3 py-2 rounded"><?= $this->session->flashdata('error'); ?></div>
          <?php endif; ?>
          <?php if ($this->session->flashdata('success')): ?>
            <div class="mt-4 bg-emerald-50 text-emerald-800 px-3 py-2 rounded"><?= $this->session->flashdata('success'); ?></div>
          <?php endif; ?>
          <div id="liveFlash" class="mt-4"></div>

        </div>

        <!-- AVATAR ROW-->
        <div class="px-8 py-6 border-b border-gray-100">
          <div class="flex flex-col md:flex-row md:items-start gap-6">
            <div class="flex items-center gap-4">
              <div class="w-[88px] h-[88px] rounded-full overflow-hidden ring-4 ring-white shadow">
                <img id="avatarPreview" src="<?= $avatarUrl ?>" alt="Client Avatar" class="w-full h-full object-cover">
              </div>
              <div class="text-sm text-gray-600">
                <div class="font-semibold text-gray-800 mb-1">Profile Photo</div>
                <div class="text-xs">PNG, JPG, or WebP format</div>
                <div class="text-xs text-gray-500">Clear headshot recommended</div>
              </div>
            </div>
            <form id="avatarForm" class="w-full md:max-w-lg md:ml-auto" method="post"
              action="<?= site_url('client/update') ?>" enctype="multipart/form-data">
              <?php if ($this->config->item('csrf_protection')): ?>
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
              <?php endif; ?>
              <input type="hidden" name="__partial" value="avatar">
              <input type="hidden" name="next" value="edit">
              <input type="hidden" name="ajax" value="1"> <!-- ✅ ADD THIS -->


              <label class="block mb-2 font-medium text-gray-800">Upload new photo</label>
              <div id="avatarDrop" class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center bg-gray-50 hover:bg-gray-100 transition cursor-pointer">
                <svg class="mx-auto h-9 w-9 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="text-sm font-medium text-gray-700">Choose photo or drag &amp; drop</div>
                <div class="text-xs text-gray-500 mt-1">High quality images work best</div>
                <input id="avatar" class="hidden"
                  type="file" name="avatar" accept=".png,.jpg,.jpeg,.webp">
              </div>

              <div id="avatarStatus" class="text-xs text-gray-500 mt-2 hidden">Uploading…</div>

              <?php if (!empty($avatarViewerUrl)): ?>
                <a class="text-sm font-medium text-blue-600 hover:text-blue-700 inline-block mt-3"
                  href="<?= htmlspecialchars($avatarViewerUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View current</a>
              <?php endif; ?>

              <div id="preview-avatar" class="upload-previews mt-3"></div>
            </form>

          </div>
        </div>

        <!-- TABS -->
        <nav class="px-8 py-4 border-b border-gray-100 bg-white">
          <div class="flex gap-3 overflow-x-auto pb-2">
            <button data-tab="info" class="tab-button active px-3 py-2 rounded-lg bg-gray-900 text-white text-sm">Basic Info</button>
            <button data-tab="business" class="tab-button px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">Business</button>
            <button data-tab="docs" class="tab-button px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">Documents</button>
          </div>
        </nav>

        <!-- MAIN FORM -->
        <form method="post" action<?= "=\"" . site_url('client/update') . "\"" ?> enctype="multipart/form-data">
          <?php if ($this->config->item('csrf_protection')): ?>
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <?php endif; ?>
          <input type="hidden" name="next" value="dashboard">

          <div class="px-8 py-6">

            <!-- INFO -->
            <div data-panel="info" class="tab-panel">
              <h2 class="section-header text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>

              <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1">First name <span class="text-amber-500">*</span></label>
                    <input class="form-input w-full border-gray-300 rounded-lg" name="fName" required maxlength="100"
                      value="<?= set_value('fName', $p->fName ?? ($this->session->userdata('first_name') ?? '')) ?>" placeholder="First name">
                  </div>
                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1">Last name <span class="text-amber-500">*</span></label>
                    <input class="form-input w-full border-gray-300 rounded-lg" name="lName" required maxlength="100"
                      value="<?= set_value('lName', $p->lName ?? ($this->session->userdata('last_name') ?? '')) ?>" placeholder="Last name">
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1">Middle name</label>
                    <input class="form-input w-full border-gray-300 rounded-lg" name="mName" maxlength="45" value="<?= set_value('mName', $p->mName ?? '') ?>" placeholder="Middle name">
                  </div>
                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="phoneNo">Cellphone</label>
                    <div class="flex">
                      <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-600">+63</span>
                      <input id="phoneNo" type="text" name="phoneNo" value="<?= set_value('phoneNo', $p->phoneNo ?? '') ?>" class="form-input w-full border-gray-300 rounded-r-lg" placeholder="9xx xxx xxxx">
                    </div>
                    <div class="form-hint text-xs text-gray-500 mt-1">Shown to workers after you accept a job</div>
                  </div>
                </div>

                <!-- Address cascade (Province → City → Barangay) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="province">Province</label>
                    <select id="province" name="province" class="form-input w-full border-gray-300 rounded-lg"
                      data-pre="<?= html_escape($p->province ?? '') ?>">
                      <option value="">- Select Province -</option>
                    </select>
                  </div>

                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="city">City / Municipality</label>
                    <select id="city" name="city" class="form-input w-full border-gray-300 rounded-lg"
                      data-pre="<?= html_escape($p->city ?? '') ?>" disabled>
                      <option value="">- Select City -</option>
                    </select>
                  </div>

                  <div class="input-group">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1" for="brgy">Barangay</label>
                    <select id="brgy" name="brgy" class="form-input w-full border-gray-300 rounded-lg"
                      data-pre="<?= html_escape($p->brgy ?? '') ?>" disabled>
                      <option value="">- Select Barangay -</option>
                    </select>
                  </div>
                </div>

              </div>
            </div>

            <!-- BUSINESS -->
            <div data-panel="business" class="tab-panel hidden">
              <h2 class="section-header text-lg font-semibold text-gray-900 mb-4">Business Information <span class="text-sm text-gray-500 font-normal"></span></h2>
              <?php if ($isIndividualEmployer): ?>
                <div class="mb-6 text-sm text-gray-600">You are currently listed as <strong><?= htmlspecialchars($currentOrgLabel, ENT_QUOTES, 'UTF-8') ?></strong>. Add an Employer or Company name to update your business profile.</div>
              <?php else: ?>
                <div class="mb-6 text-xs text-gray-500">Currently displayed as <strong><?= htmlspecialchars($currentOrgLabel, ENT_QUOTES, 'UTF-8') ?></strong>. Leave the Employer and Company Name fields empty if you are hiring as an Individual Employer.</div>
              <?php endif; ?>
              <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div class="input-group">
                  <label class="form-label block text-sm font-medium text-gray-700 mb-1">Employer</label>
                  <input class="form-input w-full border-gray-300 rounded-lg" name="employer" maxlength="120" value="<?= set_value('employer', $p->employer ?? '') ?>" placeholder="Employer name">
                </div>
                <div class="input-group">
                  <label class="form-label block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                  <input class="form-input w-full border-gray-300 rounded-lg" name="companyName" maxlength="45" value="<?= set_value('companyName', $p->companyName ?? '') ?>" placeholder="Company name">
                </div>
                <?php if ($hasCompanyPosition): ?>
                  <div id="companyPositionGroup" class="input-group<?= $showCompanyPosition ? '' : ' hidden' ?>">
                    <label class="form-label block text-sm font-medium text-gray-700 mb-1">Position in Company</label>
                    <input class="form-input w-full border-gray-300 rounded-lg" name="company_position" maxlength="120" value="<?= set_value('company_position', isset($p->company_position) ? $p->company_position : '') ?>" placeholder="e.x HR Manager" <?= $showCompanyPosition ? '' : ' disabled' ?>>
                    <p class="form-hint text-xs text-gray-500 mt-1">Shown to workers so they know who will manage their engagement.</p>
                  </div>
                <?php endif; ?>
                <div class="input-group">
                  <label class="form-label block text-sm font-medium text-gray-700 mb-1">Business / Project Name</label>
                  <input class="form-input w-full border-gray-300 rounded-lg" name="business_name" maxlength="160" value="<?= set_value('business_name', $p->business_name ?? '') ?>" placeholder="Business or project name">
                </div>
                <div class="input-group">
                  <label class="form-label block text-sm font-medium text-gray-700 mb-1">Business Location</label>
                  <input class="form-input w-full border-gray-300 rounded-lg" name="business_location" maxlength="160" value="<?= set_value('business_location', $p->business_location ?? '') ?>" placeholder="Business location">
                </div>
              </div>
            </div>

            <!-- DOCS (merged Gov ID + Certificates + Permit; same tiles/preview) -->
            <div data-panel="docs" class="tab-panel hidden">
              <h2 class="section-header text-lg font-semibold text-gray-900 mb-4">Documents &amp; Verification</h2>

              <div class="space-y-6">
                <div>
                  <label class="form-label block text-sm font-medium text-gray-700 mb-2">Government ID <span class="text-amber-500">*</span></label>
                  <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-5 text-center bg-gray-50">
                    <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                      <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="text-sm font-medium text-gray-700">Click to upload or drag &amp; drop</div>
                    <div class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP, PDF up to 10MB</div>
                    <input id="id_image" type="file" name="id_image" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-2">
                  </div>
                  <div id="preview-id_image" class="upload-previews mt-2"></div>

                  <?php if ($idViewerUrl): ?>
                    <a class="text-sm font-medium text-blue-600 hover:text-blue-700 inline-flex items-center mt-2"
                      target="_blank" rel="noopener" href="<?= htmlspecialchars($idViewerUrl, ENT_QUOTES, 'UTF-8') ?>">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View Current ID
                    </a>
                    <?php if ($idRel): ?>
                      <button type="button"
                        class="doc-del-field text-xs text-red-700 hover:text-red-800 font-medium ml-2"
                        data-doc-path="<?= htmlspecialchars($idRel, ENT_QUOTES, 'UTF-8') ?>"
                        data-doc-field="id_image">
                        Delete
                      </button>
                    <?php endif; ?>
                  <?php endif; ?>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Certificates (multiple) -->
                  <div>
                    <label class="form-label block text-sm font-medium text-gray-700 mb-2">Certificates</label>
                    <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-5 text-center bg-gray-50">
                      <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="text-sm font-medium text-gray-700">Upload certificates</div>
                      <div class="text-xs text-gray-500">Multiple files allowed (PNG, JPG, WEBP, PDF)</div>
                      <input id="certificates" type="file" name="certificates[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-2">
                    </div>
                    <div class="text-xs text-gray-500 mt-2">After selecting, add a <strong>Document Name</strong> per file below.</div>
                    <div id="preview-certificates" class="upload-previews mt-2"></div>

                    <?php if (!empty($certs)): ?>
                      <div class="mt-3">

                        <div id="clientCurrentDocs" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                          <?php foreach ($certs as $c):
                            $path  = is_array($c) ? (string)($c['path'] ?? '') : (string)$c;
                            if ($path === '') continue;
                            $title = is_array($c) ? trim((string)($c['title'] ?? '')) : '';
                            if ($title === '') $title = pathinfo($path, PATHINFO_FILENAME);
                            $abs    = preg_match('#^https?://#i', $path) ? $path : base_url($path);
                            $viewer = viewer_url_from_abs($abs) ?: $abs;
                            $ext    = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                            $isImg  = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
                          ?>
                            <div class="doc-tile">
                              <div class="relative w-full overflow-hidden rounded-xl border border-gray-200 bg-white doc-card-xl">

                                <button type="button"
                                  class="doc-del absolute top-2 right-2 z-20 px-2.5 py-1.5 rounded-md bg-white/90 text-red-600 border border-red-200 hover:bg-red-50"
                                  data-doc-path="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>"
                                  title="Delete this document">
                                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M9 3h6a1 1 0 0 1 1 1v1h4v2H4V5h4V4a1 1 0 0 1 1-1zm1 6h2v9h-2V9zm4 0h2v9h-2V9zM7 9h2v9H7V9z" />
                                  </svg>
                                </button>


                                <a href="<?= htmlspecialchars($viewer, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="absolute inset-0 block z-10" aria-label="Open"></a>
                                <div class="w-full h-full flex items-center justify-center">
                                  <?php if ($isImg): ?>
                                    <img src="<?= htmlspecialchars($abs, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full object-cover pointer-events-none">
                                  <?php else: ?>
                                    <div class="w-full h-full doc-pdf-pane relative flex items-center justify-center">
                                      <span class="absolute top-2 left-2 doc-pdf-badge">PDF</span>
                                      <div class="w-12 h-12 rounded-md bg-red-700 flex items-center justify-center shadow-md">
                                        <span class="text-white font-extrabold text-xs tracking-widest">PDF</span>
                                      </div>
                                    </div>
                                  <?php endif; ?>
                                </div>
                              </div>
                              <div class="mt-1 text-xs font-medium text-gray-700 truncate" title="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    <?php endif; ?>

                  </div>

                  <!-- Business Permit (single) -->
                  <div>
                    <label class="form-label block text-sm font-medium text-gray-700 mb-2">Business Permit</label>
                    <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-5 text-center bg-gray-50">
                      <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="text-sm font-medium text-gray-700">Upload permit</div>
                      <input id="business_permit" type="file" name="business_permit" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-2">
                    </div>
                    <div id="preview-business_permit" class="upload-previews mt-2"></div>

                    <?php if ($permitViewerUrl): ?>
                      <a class="text-sm font-medium text-blue-600 hover:text-blue-700 inline-flex items-center mt-2"
                        target="_blank" rel="noopener" href="<?= htmlspecialchars($permitViewerUrl, ENT_QUOTES, 'UTF-8') ?>">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Current Permit
                      </a>

                      <?php if ($permitRel): ?>
                        <button type="button"
                          class="doc-del-field text-xs text-red-700 hover:text-red-800 font-medium ml-2"
                          data-doc-path="<?= htmlspecialchars($permitRel, ENT_QUOTES, 'UTF-8') ?>"
                          data-doc-field="business_permit">
                          Delete
                        </button>
                      <?php endif; ?>

                    <?php endif; ?>

                  </div>
                </div>
              </div>
            </div>
            <!-- /DOCS -->
          </div>

          <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
              <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="primary-button inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5" />
                  </svg>
                  Save Profile
                </button>
                <a class="secondary-button inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100" href="<?= site_url('dashboard/client') ?>">
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
                All data is securely encrypted
              </div>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
  <script>
    (function() {
      const form = document.getElementById('avatarForm');
      const drop = document.getElementById('avatarDrop');
      const inp = document.getElementById('avatar');
      const img = document.getElementById('avatarPreview');
      const status = document.getElementById('avatarStatus');
      const live = document.getElementById('liveFlash');

      if (!form || !drop || !inp) return;

      // Make the drop area open file picker
      drop.addEventListener('click', () => inp.click());

      function showFlash(type, text) {
        if (!live) return;
        const cls = type === 'success' ?
          'bg-emerald-50 text-emerald-800 border border-emerald-200' :
          'bg-red-50 text-red-800 border border-red-200';
        live.innerHTML = `<div class="px-3 py-2 rounded ${cls}">${text}</div>`;
        setTimeout(() => {
          live.innerHTML = '';
        }, 3500);
      }

      async function uploadAvatar(file) {
        const fd = new FormData(form);
        fd.set('avatar', file);

        if (status) {
          status.classList.remove('hidden');
          status.textContent = 'Uploading…';
        }

        try {
          const res = await fetch(form.action, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const data = await res.json().catch(() => ({}));

          if (!res.ok || !data.ok) {
            throw new Error(data.message || 'Upload failed.');
          }

          // Update CSRF token in BOTH forms (avatar form + main form)
          if (data.csrf_name && data.csrf_hash) {
            document.querySelectorAll(`input[name="${data.csrf_name}"]`)
              .forEach(el => el.value = data.csrf_hash);
          }

          // Update avatar preview with cache-buster
          if (data.avatar_url && img) {
            img.src = data.avatar_url + (data.avatar_url.includes('?') ? '&' : '?') + 't=' + Date.now();
          }

          if (status) status.textContent = 'Uploaded ✅';
          showFlash('success', (data.flash && data.flash.text) ? data.flash.text : 'Profile photo updated.');

          setTimeout(() => {
            if (status) status.classList.add('hidden');
          }, 1200);

        } catch (err) {
          if (status) {
            status.textContent = 'Upload failed';
            setTimeout(() => status.classList.add('hidden'), 1200);
          }
          showFlash('error', err.message || 'Upload failed.');
        }
      }

      inp.addEventListener('change', (e) => {
        const f = e.target.files && e.target.files[0];
        if (!f) return;

        // quick local preview
        if (f.type && f.type.startsWith('image/') && img) {
          const r = new FileReader();
          r.onload = (ev) => {
            img.src = ev.target.result;
          };
          r.readAsDataURL(f);
        }

        uploadAvatar(f);
      });

      // Optional: drag & drop
      drop.addEventListener('dragover', (e) => {
        e.preventDefault();
        drop.classList.add('ring-2', 'ring-blue-300');
      });
      drop.addEventListener('dragleave', () => {
        drop.classList.remove('ring-2', 'ring-blue-300');
      });
      drop.addEventListener('drop', (e) => {
        e.preventDefault();
        drop.classList.remove('ring-2', 'ring-blue-300');
        const f = e.dataTransfer.files && e.dataTransfer.files[0];
        if (f) uploadAvatar(f);
      });

    })();
  </script>

  <script>
    (function() {
      const btns = [...document.querySelectorAll('.tab-button')];
      const panels = [...document.querySelectorAll('.tab-panel')];

      function show(key) {
        btns.forEach(b => b.classList.remove('active', 'bg-gray-900', 'text-white'));
        panels.forEach(p => p.classList.add('hidden'));
        const btn = btns.find(b => b.dataset.tab === key) || btns[0];
        const panel = document.querySelector('[data-panel="' + btn.dataset.tab + '"]');
        btn.classList.add('active', 'bg-gray-900', 'text-white');
        panel.classList.remove('hidden');
        localStorage.setItem('client_edit_tab', btn.dataset.tab);
      }
      btns.forEach(b => b.addEventListener('click', () => show(b.dataset.tab)));
      show(localStorage.getItem('client_edit_tab') || 'info');
    })();

    (function() {
      const inp = document.getElementById('avatar');
      const img = document.getElementById('avatarPreview');
      const wrap = document.getElementById('preview-avatar');
      if (!inp) return;
      inp.addEventListener('change', e => {
        const f = e.target.files?.[0];
        if (!f || !f.type.startsWith('image/')) return;
        const r = new FileReader();
        r.onload = ev => {
          if (img) img.src = ev.target.result;
          if (wrap) {
            wrap.innerHTML = '';
            const i = document.createElement('img');
            i.src = ev.target.result;
            i.className = 'w-[140px] h-auto rounded-xl border border-gray-200';
            wrap.appendChild(i);
          }
        };
        r.readAsDataURL(f);
      });
    })();

    (function() {
      const $ = s => document.querySelector(s);
      const provinceSel = $('#province'),
        citySel = $('#city'),
        brgySel = $('#brgy');
      if (!provinceSel || !citySel || !brgySel) return;
      const apiBase = document.body.dataset.apiAddress || '<?= site_url('address/api') ?>';

      const preProvince = (provinceSel.dataset.pre || '').trim() || <?= json_encode((string)($p->province ?? '')) ?>;
      const preCity = (citySel.dataset.pre || '').trim() || <?= json_encode((string)($p->city ?? '')) ?>;
      const preBrgy = (brgySel.dataset.pre || '').trim() || <?= json_encode((string)($p->brgy ?? '')) ?>;

      // Fills a <select>. If Select2 is attached (optional), refreshes it too.
      function fill(el, items, ph) {
        const isjQ = typeof window.jQuery !== 'undefined';
        const $sel = isjQ ? window.jQuery(el) : null;
        const placeholder = ph || 'Select';

        if ($sel && $sel.data && $sel.data('select2')) {
          $sel.empty();
          $sel.append(new Option(placeholder, '', false, false));
          items.forEach(v => $sel.append(new Option(v, v, false, false)));
          $sel.trigger('change.select2');
        } else {
          el.innerHTML = '';
          const o = document.createElement('option');
          o.value = '';
          o.textContent = placeholder;
          el.appendChild(o);
          items.forEach(v => {
            const opt = document.createElement('option');
            opt.value = v;
            opt.textContent = v;
            el.appendChild(opt);
          });
        }
      }

      async function api(params) {
        const url = apiBase + '?' + new URLSearchParams(params).toString();
        const res = await fetch(url, {
          credentials: 'same-origin'
        });
        const txt = await res.text();
        if (!res.ok) throw new Error('HTTP ' + res.status + ' ' + txt.slice(0, 120));
        const j = JSON.parse(txt);
        if (!j.ok) throw new Error(j.msg || 'Address API error');
        return j.items || [];
      }

      async function loadProvinces() {
        citySel.disabled = true;
        brgySel.disabled = true;
        fill(provinceSel, [], 'Loading provinces…');
        try {
          const items = await api({
            scope: 'province'
          });
          fill(provinceSel, items, '- Select Province -');
        } catch (e) {
          console.error(e);
          fill(provinceSel, [], 'Unavailable — refresh to retry');
          return;
        }
        if (preProvince && [...provinceSel.options].some(o => o.value === preProvince)) {
          // If Select2 is present, set via jQuery; otherwise plain assign.
          const $prov = (typeof window.jQuery !== 'undefined') && window.jQuery('#province');
          if ($prov && $prov.length && $prov.data('select2')) {
            $prov.val(preProvince).trigger('change.select2');
          } else {
            provinceSel.value = preProvince;
          }
          await onProvince(true);
        }
      }

      async function onProvince(init = false) {
        const pv = provinceSel.value;
        citySel.disabled = true;
        brgySel.disabled = true;
        fill(citySel, [], '- Select City -');
        fill(brgySel, [], '- Select Barangay -');
        if (!pv) return;
        try {
          const items = await api({
            scope: 'city',
            province: pv
          });
          fill(citySel, items, '- Select City -');
        } catch (e) {
          console.error(e);
          fill(citySel, [], 'Unavailable — refresh to retry');
          return;
        } finally {
          citySel.disabled = false;
        }
        if (init && preCity && [...citySel.options].some(o => o.value === preCity)) {
          citySel.value = preCity;
          await onCity(true);
        }
      }
      async function onCity(init = false) {
        const pv = provinceSel.value,
          ct = citySel.value;
        brgySel.disabled = true;
        fill(brgySel, [], '- Select Barangay -');
        if (!pv || !ct) return;
        try {
          const items = await api({
            scope: 'brgy',
            province: pv,
            city: ct
          });
          fill(brgySel, items, '- Select Barangay -');
        } catch (e) {
          console.error(e);
          fill(brgySel, [], 'Unavailable — refresh to retry');
          return;
        } finally {
          brgySel.disabled = false;
        }
        if (init && preBrgy && [...brgySel.options].some(o => o.value === preBrgy)) {
          brgySel.value = preBrgy;
        }
      }

      provinceSel.addEventListener('change', () => onProvince(false));
      citySel.addEventListener('change', () => onCity(false));
      loadProvinces();
    })();

    (function docPreviews() {
      const KB = 1024;
      const fmtSize = s => (s < 1024 * KB ? Math.ceil(s / KB) + ' KB' : (Math.ceil(s / KB / KB * 10) / 10) + ' MB');
      const baseName = n => {
        const i = n.lastIndexOf('.');
        return i > 0 ? n.slice(0, i) : n;
      };

      function makeTile(file, withTitle) {
        const isImg = file.type && file.type.startsWith('image/');
        const ext = (file.name.split('.').pop() || '').toUpperCase();

        const card = document.createElement('div');
        card.className = 'doc-card-preview p-3 border border-gray-200 rounded-xl bg-white flex items-start gap-3';

        const thumb = document.createElement('div');
        thumb.className = 'doc-thumb-fixed relative w-32 h-40 rounded-lg overflow-hidden flex items-center justify-center border ' +
          (isImg ? 'bg-gray-50' : 'doc-pdf-pane');

        const badge = document.createElement('span');
        badge.className = 'absolute top-2 left-2 doc-pdf-badge';
        badge.textContent = isImg ? (ext || 'IMG') : 'PDF';
        thumb.appendChild(badge);

        if (isImg) {
          const img = document.createElement('img');
          img.src = URL.createObjectURL(file);
          img.onload = () => URL.revokeObjectURL(img.src);
          img.className = 'w-full h-full object-cover';
          thumb.appendChild(img);
        } else {
          const emblem = document.createElement('div');
          emblem.className = 'w-12 h-12 rounded-md bg-red-700 flex items-center justify-center shadow-md';
          emblem.innerHTML = '<span class="text-white font-extrabold text-xs tracking-widest">PDF</span>';
          thumb.appendChild(emblem);
        }

        const meta = document.createElement('div');
        meta.className = 'flex-1 min-w-[220px]';
        if (withTitle) {
          const label = document.createElement('label');
          label.className = 'block text-xs font-medium text-gray-700 mb-1';
          label.textContent = 'Document Name';
          const inputTitle = document.createElement('input');
          inputTitle.type = 'text';
          inputTitle.name = 'cert_titles[]';
          inputTitle.value = baseName(file.name);
          inputTitle.placeholder = 'ex. TESDA NC II, Diploma, Company ID';
          inputTitle.className = 'form-input w-full border-gray-300 rounded-lg text-sm';
          meta.appendChild(label);
          meta.appendChild(inputTitle);
        }
        const note = document.createElement('div');
        note.className = 'text-[11px] text-gray-500 mt-1 truncate';
        note.textContent = file.name + ' • ' + fmtSize(file.size);
        meta.appendChild(note);

        card.appendChild(thumb);
        card.appendChild(meta);
        return card;
      }

      function init(inputId, previewId, withTitle = false) {
        const input = document.getElementById(inputId);
        const wrap = document.getElementById(previewId);
        if (!input || !wrap) return;

        wrap.classList.add('grid', 'gap-4');
        wrap.style.gridTemplateColumns = 'repeat(1, minmax(0, 1fr))';

        input.addEventListener('change', () => {
          wrap.innerHTML = '';
          Array.from(input.files || []).forEach(file => {
            wrap.appendChild(makeTile(file, withTitle));
          });
        });
      }

      init('certificates', 'preview-certificates', true);
      init('business_permit', 'preview-business_permit');
      init('id_image', 'preview-id_image');
    })();
  </script>
  <?php if ($hasCompanyPosition): ?>
    <script>
      (function clientPositionToggle() {
        const employerInput = document.querySelector('input[name="employer"]');
        const companyInput = document.querySelector('input[name="companyName"]');
        const positionGroup = document.getElementById('companyPositionGroup');
        const positionInput = positionGroup ? positionGroup.querySelector('input[name="company_position"]') : null;
        if (!employerInput || !companyInput || !positionGroup) return;

        const toggle = () => {
          const hasEmployer = employerInput.value.trim() !== '';
          const hasCompany = companyInput.value.trim() !== '';
          const shouldShow = hasEmployer && hasCompany;
          positionGroup.classList.toggle('hidden', !shouldShow);
          if (positionInput) {
            positionInput.disabled = !shouldShow;
          }
        };

        employerInput.addEventListener('input', toggle);
        employerInput.addEventListener('blur', toggle);
        companyInput.addEventListener('input', toggle);
        companyInput.addEventListener('blur', toggle);
        toggle();
      })();
    </script>
  <?php endif; ?>
  <script>
    (function avatarAutoUpload() {
      const form = document.getElementById('avatarForm');
      const input = document.getElementById('avatar');
      const img = document.getElementById('avatarPreview');
      const previewWrap = document.getElementById('preview-avatar');
      const status = document.getElementById('avatarStatus');
      if (!form || !input) return;

      function preview(file) {
        if (!file || !file.type.match(/^image\//)) return;
        const r = new FileReader();
        r.onload = e => {
          if (img) img.src = e.target.result;
          if (previewWrap) {
            previewWrap.innerHTML = '';
            const p = document.createElement('img');
            p.src = e.target.result;
            p.className = 'w-[140px] h-auto rounded-xl border border-gray-200';
            previewWrap.appendChild(p);
          }
        };
        r.readAsDataURL(file);
      }

      async function upload() {
        const fd = new FormData(form);
        status?.classList.remove('hidden');
        try {
          const res = await fetch(form.action, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          });

          const ct = (res.headers.get('Content-Type') || '').toLowerCase();
          const data = ct.includes('application/json') ? await res.json() : null;
          if (!res.ok) throw new Error('Upload failed');
          if (data?.ok && data?.avatar_url && img) img.src = data.avatar_url + '?t=' + Date.now();
          if (data?.csrf_name && data?.csrf_hash) {
            document.querySelectorAll('input[name="' + data.csrf_name + '"]').forEach(el => el.value = data.csrf_hash);
          }
        } catch (e) {
          console.error(e);
        } finally {
          status?.classList.add('hidden');
        }
      }

      input.addEventListener('change', e => {
        const f = e.target.files && e.target.files[0];
        if (f) {
          preview(f);
          upload();
        }
      });
    })();
  </script>
  <script>
    (function makeUploadAreasClickable() {
      document.querySelectorAll('input[type="file"]').forEach(input => {
        const area =
          input.closest('.file-upload-area') ||
          input.closest('.border-2.border-dashed');

        if (!area) return;

        area.style.cursor = 'pointer';
        area.setAttribute('role', 'button');
        area.setAttribute('tabindex', '0');

        const openPicker = () => input.click();
        area.addEventListener('click', e => {
          if (e.target === input) return;
          openPicker();
        });

        area.addEventListener('keydown', e => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openPicker();
          }
        });

        area.addEventListener('dragover', e => {
          e.preventDefault();
        });
        area.addEventListener('drop', e => {
          e.preventDefault();
          if (!e.dataTransfer || !e.dataTransfer.files?.length) return;
          const dt = new DataTransfer();
          [...e.dataTransfer.files].forEach(f => dt.items.add(f));
          input.files = dt.files;
          input.dispatchEvent(new Event('change', {
            bubbles: true
          }));
        });
      });
    })();
  </script>
  <script>
    (function() {
      function toast(msg, ok = true) {
        (ok ? console.log : console.error)(msg);
      }

      function showFlash(flash) {
        if (!flash) return;
        const host = document.getElementById('liveFlash');
        if (!host) return;

        const cls = flash.type === 'error' ?
          'bg-red-50 text-red-800' :
          (flash.type === 'info' ? 'bg-blue-50 text-blue-800' : 'bg-emerald-50 text-emerald-800');

        host.innerHTML = `<div class="px-3 py-2 rounded ${cls}">${flash.text || 'Done.'}</div>`;
        setTimeout(() => {
          host.innerHTML = '';
        }, 4000);
      }

      const certGrid = document.getElementById('clientCurrentDocs');
      if (certGrid) {
        certGrid.addEventListener('click', async (e) => {
          const btn = e.target.closest('.doc-del');
          if (!btn) return;
          e.preventDefault();
          e.stopPropagation();
          if (!confirm('Delete this document? This cannot be undone.')) return;

          const fd = new FormData();
          fd.append('path', btn.dataset.docPath);
          <?php if ($this->config->item('csrf_protection')): ?>
            fd.append('<?= $this->security->get_csrf_token_name(); ?>', '<?= $this->security->get_csrf_hash(); ?>');
          <?php endif; ?>

          btn.disabled = true;
          try {
            const res = await fetch('<?= site_url('client/delete_doc') ?>', {
              method: 'POST',
              body: fd,
              credentials: 'same-origin',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
              }
            });
            const ct = (res.headers.get('Content-Type') || '').toLowerCase();
            const data = ct.includes('application/json') ? await res.json() : null;
            if (!res.ok || !data?.ok) throw new Error((data && data.msg) || ('HTTP ' + res.status));

            if (data.csrf_name && data.csrf_hash) {
              document.querySelectorAll('input[name="' + data.csrf_name + '"]').forEach(el => el.value = data.csrf_hash);
            }
            btn.closest('.doc-tile')?.remove();
            toast('Document deleted', true);
            showFlash(data.flash || {
              type: 'success',
              text: 'Document deleted.'
            });

          } catch (err) {
            toast('Delete failed: ' + err.message, false);
            alert('Delete failed: ' + err.message);
            btn.disabled = false;
          }
        });
      }

      // Single fields (id_image / business_permit)
      document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.doc-del-field');
        if (!btn) return;

        if (!confirm('Delete this file? This cannot be undone.')) return;

        const fd = new FormData();
        fd.append('path', btn.dataset.docPath);
        fd.append('field', btn.dataset.docField || '');
        <?php if ($this->config->item('csrf_protection')): ?>
          fd.append('<?= $this->security->get_csrf_token_name(); ?>', '<?= $this->security->get_csrf_hash(); ?>');
        <?php endif; ?>

        btn.disabled = true;
        try {
          const res = await fetch('<?= site_url('client/delete_doc') ?>', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          });
          const ct = (res.headers.get('Content-Type') || '').toLowerCase();
          const data = ct.includes('application/json') ? await res.json() : null;
          if (!res.ok || !data?.ok) throw new Error((data && data.msg) || ('HTTP ' + res.status));

          if (data.csrf_name && data.csrf_hash) {
            document.querySelectorAll('input[name="' + data.csrf_name + '"]').forEach(el => el.value = data.csrf_hash);
          }
          const link = btn.previousElementSibling;
          if (link && link.tagName === 'A') link.remove();
          btn.remove();
          toast('File deleted', true);
          showFlash(data.flash || {
            type: 'success',
            text: 'File deleted.'
          });

        } catch (err) {
          toast('Delete failed: ' + err.message, false);
          alert('Delete failed: ' + err.message);
          btn.disabled = false;
        }
      });
    })();
  </script>

</body>

</html>