<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * NSRP Form 1 (Rev.3) — Jobseeker Registration  (PESO only)
 * Vars: $p (object|null), $target_id (int)
 */
$v = function ($k, $d = '') use ($p) {
    return (isset($p->$k) && $p->$k !== null && !is_array($p->$k)) ? html_escape($p->$k) : $d;
};
$rows = function ($k) use ($p) { $val = $p->$k ?? null; return is_array($val) ? $val : []; };
$csv = function ($k) use ($p) {
    $val = $p->$k ?? '';
    if (is_array($val)) return $val;
    $val = trim((string)$val);
    return $val === '' ? [] : array_map('trim', explode(',', $val));
};
$is  = function ($k, $e) use ($p) { return (string)($p->$k ?? '') === (string)$e ? 'selected' : ''; };
$on  = function ($k) use ($p) { return !empty($p->$k) ? 'checked' : ''; };
$has = function ($arr, $n) { return in_array($n, $arr, true) ? 'checked' : ''; };

$save_action   = 'nsrp/save/' . (int)$target_id;
$assess_action = 'nsrp/assess/' . (int)$target_id;
$full_name     = trim(($v('first_name')) . ' ' . ($v('last_name')));

$occRows  = $rows('pref_occupations') ?: [[]];
$eligRows = $rows('eligibilities') ?: [[]];
$tesRows  = $rows('tesda_certs') ?: [[]];
$langCert = $rows('language_certs') ?: [[]];
$expRows  = $rows('exp') ?: [[]];
$localPref    = $csv('pref_locations_local')    ?: [''];
$overseasPref = $csv('pref_locations_overseas') ?: [''];

$century = ['Innovation','Team Work','Multitasking','Work Ethics','Self Motivation','Creative Problem Solving','Problem Solving','Critical Thinking','Decision Making','Stress Tolerance','Planning and Organizing','Social Perceptiveness','English Functional Skills','English Comprehension','Math Functional Skill'];
$techSkills = ['Carpentry','Masonry','Welding','Auto Mechanic','Plumbing','Driving','Gardening','Tailoring','Photography','Hairdressing','Cooking','Baking'];
$disList = ['Visual','Hearing','Speech','Physical','Mental'];
$selCentury = $csv('century_skills');
$selTech    = $csv('tech_skills_informal');
$selDis     = $csv('disability');
$selLang    = $csv('languages');
$selElig    = $csv('peso_eligibility');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= html_escape($page_title ?? 'NSRP Form 1') ?> - JobMatch</title>
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <style>
    .nsrp .sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:18px;overflow:hidden}
    .nsrp .sec-head{background:#dbe7f3;color:#1e3a5f;font-weight:700;padding:.6rem 1rem;font-size:.9rem;letter-spacing:.02em}
    .nsrp .sec-body{padding:1rem}
    .nsrp .form-label{font-size:.78rem;font-weight:600;color:#475569;margin-bottom:.2rem}
    .nsrp .form-control,.nsrp .form-select{font-size:.9rem}
    .nsrp .subhead{font-size:.82rem;font-weight:700;color:#64748b;margin:.4rem 0}
    .nsrp .peso-box{border-top:2px dashed #cbd5e1;background:#fffdf3}
    .nsrp .repeater .row{margin-bottom:.4rem}
    .nsrp .page-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1rem}
    .nsrp .badge-status{background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe}
    /* Force native checkboxes (admin template hides them by default) */
    .nsrp input[type=checkbox],.nsrp input[type=radio]{appearance:auto;-webkit-appearance:auto;opacity:1!important;position:static!important;float:none!important;margin:0 .4rem 0 0!important;width:16px;height:16px;vertical-align:-2px;pointer-events:auto}
    .nsrp .form-check{padding-left:0;min-height:auto;display:inline-flex;align-items:center}
    .nsrp .form-check .form-check-input{margin-left:0}
    .nsrp .form-check-inline{margin-right:1rem}
  </style>
</head>
<body>
  <?php $this->load->view('partials_translate_banner'); ?>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-4 nsrp">

          <div class="page-head">
            <div>
              <div class="text-muted" style="font-size:.75rem">Department of Labor and Employment · PESO Davao Oriental</div>
              <h4 class="mb-0 fw-bold">NSRP Form 1 — Jobseeker Registration</h4>
              <div class="text-danger fw-semibold" style="font-size:.85rem">Registrant: <?= html_escape($full_name ?: $v('email')) ?></div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="badge badge-status">Status: <?= html_escape(ucfirst($v('nsrp_status', 'draft'))) ?></span>
              <a href="<?= site_url('nsrp/print_jobseeker/' . (int)$target_id) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-printer"></i> Print</a>
              <a href="<?= site_url('nsrp/send_jobseeker/' . (int)$target_id) ?>" class="btn btn-sm btn-outline-primary" onclick="return confirm('Send a confidential copy of this NSRP form to the worker via Messages?')"><i class="mdi mdi-send"></i> Send to user</a>
            </div>
          </div>

          <?php foreach (['success' => 'success', 'danger' => 'danger'] as $key => $cls): ?>
            <?php if ($msg = $this->session->flashdata($key)): ?>
              <div class="alert alert-<?= $cls ?> py-2"><?= $msg ?></div>
            <?php endif; ?>
          <?php endforeach; ?>

          <?= form_open($save_action) ?>

          <!-- I. PERSONAL INFORMATION -->
          <div class="sec">
            <div class="sec-head">I. PERSONAL INFORMATION</div>
            <div class="sec-body">
              <div class="row g-3">
                <div class="col-md-4"><label class="form-label">First Name *</label><input name="first_name" class="form-control" value="<?= $v('first_name') ?>" required></div>
                <div class="col-md-4"><label class="form-label">Last Name *</label><input name="last_name" class="form-control" value="<?= $v('last_name') ?>" required></div>
                <div class="col-md-4"><label class="form-label">Sex</label>
                  <select name="sex" class="form-select"><option value="">—</option>
                    <option value="male" <?= $is('sex','male') ?>>Male</option>
                    <option value="female" <?= $is('sex','female') ?>>Female</option>
                  </select></div>
                <div class="col-md-4"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" class="form-control" value="<?= $v('date_of_birth') ?>"></div>
                <div class="col-md-4"><label class="form-label">Place of Birth</label><input name="place_of_birth" class="form-control" value="<?= $v('place_of_birth') ?>"></div>
                <div class="col-md-4"><label class="form-label">Civil Status</label>
                  <select name="civil_status" class="form-select">
                    <?php foreach (['','Single','Married','Widowed','Separated','Others'] as $cs): ?>
                      <option value="<?= $cs ?>" <?= $is('civil_status',$cs) ?>><?= $cs ?: '—' ?></option>
                    <?php endforeach; ?>
                  </select></div>
                <div class="col-md-4"><label class="form-label">Citizenship</label><input name="citizenship" class="form-control" value="<?= $v('citizenship') ?>"></div>
                <div class="col-md-4"><label class="form-label">Religion</label><input name="religion" class="form-control" value="<?= $v('religion') ?>"></div>
                <div class="col-md-2"><label class="form-label">Height (cm)</label><input name="height_cm" class="form-control" value="<?= $v('height_cm') ?>"></div>
                <div class="col-md-2"><label class="form-label">Weight (kg)</label><input name="weight_kg" class="form-control" value="<?= $v('weight_kg') ?>"></div>
                <div class="col-md-4"><label class="form-label">Mobile (Primary)</label><input name="phoneNo" class="form-control" value="<?= $v('phoneNo') ?>"></div>
                <div class="col-md-4"><label class="form-label">Mobile (Secondary)</label><input name="mobile_secondary" class="form-control" value="<?= $v('mobile_secondary') ?>"></div>
                <div class="col-md-4"><label class="form-label">Landline</label><input name="landline" class="form-control" value="<?= $v('landline') ?>"></div>
                <div class="col-md-12"><label class="form-label">Email</label><input class="form-control bg-light" value="<?= $v('email') ?>" disabled></div>
              </div>

              <div class="subhead mt-3">Present Address</div>
              <div class="row g-3">
                <div class="col-md-3"><label class="form-label">House/Street/Village</label><input name="present_street" class="form-control" value="<?= $v('present_street') ?>"></div>
                <div class="col-md-3"><label class="form-label">Barangay</label><input name="brgy" class="form-control" value="<?= $v('brgy') ?>"></div>
                <div class="col-md-3"><label class="form-label">Municipality/City</label><input name="city" class="form-control" value="<?= $v('city') ?>"></div>
                <div class="col-md-3"><label class="form-label">Province</label><input name="province" class="form-control" value="<?= $v('province') ?>"></div>
              </div>

              <div class="subhead mt-3">Permanent Address
                <span class="ms-3 fw-normal"><input type="checkbox" id="permSame" name="perm_same_as_present" value="1" <?= $on('perm_same_as_present') ?>> <label for="permSame" class="form-label d-inline">same as present</label></span>
              </div>
              <div class="row g-3">
                <div class="col-md-3" data-perm><label class="form-label">House/Street/Village</label><input name="perm_street" class="form-control" value="<?= $v('perm_street') ?>"></div>
                <div class="col-md-3" data-perm><label class="form-label">Barangay</label><input name="perm_brgy" class="form-control" value="<?= $v('perm_brgy') ?>"></div>
                <div class="col-md-3" data-perm><label class="form-label">Municipality/City</label><input name="perm_city" class="form-control" value="<?= $v('perm_city') ?>"></div>
                <div class="col-md-3" data-perm><label class="form-label">Province</label><input name="perm_province" class="form-control" value="<?= $v('perm_province') ?>"></div>
              </div>

              <div class="mt-3">
                <label class="form-label">Disability (if any)</label><br>
                <?php foreach ($disList as $d): ?>
                  <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="disability[]" value="<?= $d ?>" <?= $has($selDis,$d) ?>><label class="form-check-label"><?= $d ?></label></div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <!-- EMPLOYMENT STATUS -->
          <div class="sec">
            <div class="sec-head">EMPLOYMENT STATUS</div>
            <div class="sec-body">
              <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Status</label>
                  <select name="employment_status" class="form-select">
                    <?php foreach (['','Employed','Unemployed'] as $es): ?><option value="<?= $es ?>" <?= $is('employment_status',$es) ?>><?= $es ?: '—' ?></option><?php endforeach; ?>
                  </select></div>
                <div class="col-md-6"><label class="form-label">Sub-status</label>
                  <select name="employment_substatus" class="form-select">
                    <?php foreach (['','Wage Employed','Self Employed','New Entrant/Fresh Graduate','Finished Contract','Resigned','Retired','Terminated/Laid-off (local)','Terminated/Laid-off (abroad)'] as $ss): ?><option value="<?= $ss ?>" <?= $is('employment_substatus',$ss) ?>><?= $ss ?: '—' ?></option><?php endforeach; ?>
                  </select></div>
                <div class="col-md-6 d-flex align-items-end gap-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="actively_looking" value="1" <?= $on('actively_looking') ?>><label class="form-check-label">Actively looking?</label></div><input name="looking_duration" class="form-control" placeholder="how long?" value="<?= $v('looking_duration') ?>"></div>
                <div class="col-md-6 d-flex align-items-end gap-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="willing_immediate" value="1" <?= $on('willing_immediate') ?>><label class="form-check-label">Work immediately?</label></div><input name="available_when" class="form-control" placeholder="if no, when?" value="<?= $v('available_when') ?>"></div>
                <div class="col-md-6 d-flex align-items-end gap-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_4ps" value="1" <?= $on('is_4ps') ?>><label class="form-check-label">4Ps beneficiary?</label></div><input name="fourps_household_id" class="form-control" placeholder="Household ID No." value="<?= $v('fourps_household_id') ?>"></div>
                <div class="col-md-6 d-flex align-items-end gap-3">
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="is_ofw" value="1" <?= $on('is_ofw') ?>><label class="form-check-label">OFW?</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="ofw_returning" value="1" <?= $on('ofw_returning') ?>><label class="form-check-label">Returning to PH to work?</label></div>
                </div>
              </div>
            </div>
          </div>

          <!-- II. JOB PREFERENCE -->
          <div class="sec">
            <div class="sec-head">II. JOB PREFERENCE</div>
            <div class="sec-body">
              <label class="form-label">Preferred Occupation &amp; Industry</label>
              <div class="repeater" data-repeater="occ">
                <?php foreach ($occRows as $r): ?>
                  <div class="row g-2" data-row>
                    <div class="col-md-6"><input name="pref_occupation[]" class="form-control" placeholder="e.g. call center agent" value="<?= html_escape($r['occupation'] ?? '') ?>"></div>
                    <div class="col-md-6"><input name="pref_industry[]" class="form-control" placeholder="e.g. IT-BPM" value="<?= html_escape($r['industry'] ?? '') ?>"></div>
                  </div>
                <?php endforeach; ?>
                <button type="button" class="btn btn-link btn-sm p-0 text-danger" data-add="occ">+ add occupation</button>
              </div>
              <div class="row g-3 mt-1">
                <div class="col-md-6">
                  <label class="form-label">Preferred Local Locations</label>
                  <div class="repeater" data-repeater="local">
                    <?php foreach ($localPref as $loc): ?><div data-row class="mb-1"><input name="pref_local[]" class="form-control" placeholder="city/municipality" value="<?= html_escape($loc) ?>"></div><?php endforeach; ?>
                    <button type="button" class="btn btn-link btn-sm p-0 text-danger" data-add="local">+ add</button>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Preferred Overseas Locations</label>
                  <div class="repeater" data-repeater="overseas">
                    <?php foreach ($overseasPref as $loc): ?><div data-row class="mb-1"><input name="pref_overseas[]" class="form-control" placeholder="country" value="<?= html_escape($loc) ?>"></div><?php endforeach; ?>
                    <button type="button" class="btn btn-link btn-sm p-0 text-danger" data-add="overseas">+ add</button>
                  </div>
                </div>
                <div class="col-md-4"><label class="form-label">Salary Expectation (PHP)</label><input name="salary_expectation" class="form-control" value="<?= $v('salary_expectation') ?>"></div>
              </div>
            </div>
          </div>

          <!-- III. EDUCATION -->
          <div class="sec">
            <div class="sec-head">III. EDUCATIONAL BACKGROUND</div>
            <div class="sec-body"><div class="row g-3">
              <div class="col-md-6"><label class="form-label">Highest Educational Level</label>
                <select name="education_level" class="form-select">
                  <?php foreach (['','No formal education','Elementary Level','Elementary Graduate','High School Level','High School Graduate','College level','College Graduate','Technical-vocational graduate','Post Graduate'] as $el): ?><option value="<?= $el ?>" <?= $is('education_level',$el) ?>><?= $el ?: '—' ?></option><?php endforeach; ?>
                </select></div>
              <div class="col-md-6"><label class="form-label">Course / Program / SHS Strand</label><input name="course" class="form-control" value="<?= $v('course') ?>"></div>
              <div class="col-md-6"><label class="form-label">School / University</label><input name="school" class="form-control" value="<?= $v('school') ?>"></div>
              <div class="col-md-6"><label class="form-label">Year Graduated</label><input name="year_graduated" class="form-control" value="<?= $v('year_graduated') ?>"></div>
            </div></div>
          </div>

          <!-- IV. TRAINING -->
          <div class="sec">
            <div class="sec-head">IV. TECHNICAL / VOCATIONAL TRAINING</div>
            <div class="sec-body repeater" data-repeater="tesda">
              <?php foreach ($tesRows as $r): ?>
                <div class="row g-2" data-row>
                  <div class="col-md-5"><input name="tesda_qual[]" class="form-control" placeholder="Qualification / NC" value="<?= html_escape($r['qualification'] ?? '') ?>"></div>
                  <div class="col-md-4"><input name="tesda_no[]" class="form-control" placeholder="Certificate No." value="<?= html_escape($r['number'] ?? '') ?>"></div>
                  <div class="col-md-3"><input type="date" name="tesda_exp[]" class="form-control" value="<?= html_escape($r['expiry'] ?? '') ?>"></div>
                </div>
              <?php endforeach; ?>
              <button type="button" class="btn btn-link btn-sm p-0 text-danger" data-add="tesda">+ add training</button>
            </div>
          </div>

          <!-- V. ELIGIBILITY -->
          <div class="sec">
            <div class="sec-head">V. ELIGIBILITY &amp; LANGUAGES</div>
            <div class="sec-body">
              <label class="form-label">Career Service / Board / Bar</label>
              <div class="repeater" data-repeater="elig">
                <?php foreach ($eligRows as $r): ?>
                  <div class="row g-2" data-row>
                    <div class="col-md-5"><input name="elig_service[]" class="form-control" placeholder="Career service / board" value="<?= html_escape($r['career_service'] ?? '') ?>"></div>
                    <div class="col-md-4"><input name="elig_license[]" class="form-control" placeholder="License number" value="<?= html_escape($r['license_no'] ?? '') ?>"></div>
                    <div class="col-md-3"><input type="date" name="elig_expiry[]" class="form-control" value="<?= html_escape($r['expiry'] ?? '') ?>"></div>
                  </div>
                <?php endforeach; ?>
                <button type="button" class="btn btn-link btn-sm p-0 text-danger" data-add="elig">+ add eligibility</button>
              </div>
              <label class="form-label mt-2">Language Proficiency Certifications</label>
              <div class="repeater" data-repeater="lang">
                <?php foreach ($langCert as $r): ?>
                  <div class="row g-2" data-row>
                    <div class="col-md-6"><input name="lang_cert_name[]" class="form-control" placeholder="e.g. IELTS / JLPT" value="<?= html_escape($r['name'] ?? '') ?>"></div>
                    <div class="col-md-6"><input name="lang_cert_validity[]" class="form-control" placeholder="Validity date" value="<?= html_escape($r['validity'] ?? '') ?>"></div>
                  </div>
                <?php endforeach; ?>
                <button type="button" class="btn btn-link btn-sm p-0 text-danger" data-add="lang">+ add certification</button>
              </div>
              <div class="mt-2"><label class="form-label">Dialects / Languages Spoken</label><br>
                <?php foreach (['Tagalog','Ilocano','Ilonggo','Bikol','Cebuano'] as $lo): ?>
                  <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="languages[]" value="<?= $lo ?>" <?= $has($selLang,$lo) ?>><label class="form-check-label"><?= $lo ?></label></div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <!-- VI. WORK EXPERIENCE -->
          <div class="sec">
            <div class="sec-head">VI. WORK EXPERIENCE (last 10 years, most recent first)</div>
            <div class="sec-body repeater" data-repeater="exp">
              <?php foreach ($expRows as $r): ?>
                <div class="row g-2" data-row>
                  <div class="col-md-3"><input name="exp_company[]" class="form-control" placeholder="Company" value="<?= html_escape($r['company'] ?? ($r['employer'] ?? '')) ?>"></div>
                  <div class="col-md-3"><input name="exp_address[]" class="form-control" placeholder="Address" value="<?= html_escape($r['address'] ?? '') ?>"></div>
                  <div class="col-md-2"><input name="exp_position[]" class="form-control" placeholder="Position" value="<?= html_escape($r['position'] ?? ($r['role'] ?? '')) ?>"></div>
                  <div class="col-md-2"><input name="exp_dates[]" class="form-control" placeholder="Inclusive dates" value="<?= html_escape($r['dates'] ?? '') ?>"></div>
                  <div class="col-md-2"><input name="exp_status[]" class="form-control" placeholder="Appt. status" value="<?= html_escape($r['status'] ?? '') ?>"></div>
                </div>
              <?php endforeach; ?>
              <button type="button" class="btn btn-link btn-sm p-0 text-danger" data-add="exp">+ add experience</button>
            </div>
          </div>

          <!-- VII / IX. SKILLS -->
          <div class="sec">
            <div class="sec-head">VII. 21st CENTURY SKILLS &amp; IX. TECHNICAL SKILLS</div>
            <div class="sec-body">
              <label class="form-label">21st Century Skills (check up to 5)</label>
              <div class="row"><?php foreach ($century as $s): ?><div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="century_skills[]" value="<?= $s ?>" <?= $has($selCentury,$s) ?>><label class="form-check-label"><?= $s ?></label></div></div><?php endforeach; ?></div>
              <label class="form-label mt-3">Technical Skills (no formal training)</label>
              <div class="row"><?php foreach ($techSkills as $s): ?><div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="tech_skills_informal[]" value="<?= $s ?>" <?= $has($selTech,$s) ?>><label class="form-check-label"><?= $s ?></label></div></div><?php endforeach; ?></div>
              <div class="mt-3"><label class="form-label">Other skills (comma-separated)</label><input name="skills" class="form-control" value="<?= $v('skills') ?>"></div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="<?= site_url('peso') ?>" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-danger"><i class="mdi mdi-content-save"></i> Save NSRP Form 1</button>
          </div>
          <?= form_close() ?>

          <!-- FOR USE OF PESO ONLY -->
          <div class="sec">
            <div class="sec-head">FOR USE OF PESO ONLY</div>
            <div class="sec-body peso-box">
              <?= form_open($assess_action) ?>
              <label class="form-label">Eligible for public employment services</label><br>
              <?php foreach (['SPES','GIP','TUPAD','JobStart'] as $pe): ?>
                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="peso_eligibility[]" value="<?= $pe ?>" <?= $has($selElig,$pe) ?>><label class="form-check-label"><?= $pe ?></label></div>
              <?php endforeach; ?>
              <div class="row g-3 mt-1">
                <div class="col-md-3"><label class="form-label">Assessed by</label><input name="assessed_by" class="form-control" value="<?= $v('assessed_by') ?>"></div>
                <div class="col-md-3"><label class="form-label">Date assessed</label><input type="date" name="assessed_at" class="form-control" value="<?= $v('assessed_at') ?>"></div>
                <div class="col-md-3"><label class="form-label">Registration Ref. No.</label><input name="nsrp_reference" class="form-control" value="<?= $v('nsrp_reference') ?>"></div>
                <div class="col-md-3"><label class="form-label">Status</label>
                  <select name="nsrp_status" class="form-select">
                    <?php foreach (['draft','submitted','assessed'] as $st): ?><option value="<?= $st ?>" <?= $is('nsrp_status',$st) ?>><?= ucfirst($st) ?></option><?php endforeach; ?>
                  </select></div>
              </div>
              <div class="d-flex justify-content-end mt-3"><button class="btn btn-dark btn-sm"><i class="mdi mdi-clipboard-check"></i> Save Assessment</button></div>
              <?= form_close() ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/nav.js') ?>"></script>
  <script>
    document.querySelectorAll('[data-add]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var box = document.querySelector('[data-repeater="' + btn.dataset.add + '"]');
        var rows = box.querySelectorAll('[data-row]');
        var clone = rows[rows.length - 1].cloneNode(true);
        clone.querySelectorAll('input').forEach(function (i) { i.value = ''; });
        rows[rows.length - 1].after(clone);
      });
    });
    var permSame = document.getElementById('permSame');
    function syncPerm() {
      document.querySelectorAll('[data-perm] input').forEach(function (i) {
        i.disabled = permSame.checked; if (permSame.checked) i.value = '';
      });
    }
    if (permSame) { permSame.addEventListener('change', syncPerm); syncPerm(); }
  </script>
</body>
</html>
