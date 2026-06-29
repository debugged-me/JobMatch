<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * NSRP Form 2 — Establishment Registration Form  (PESO only)
 * Vars: $p (object|null), $vacancies (array), $edit_job (array|null), $target_id (int)
 */
$v = function ($k, $d = '') use ($p) {
    return (isset($p->$k) && $p->$k !== null) ? html_escape($p->$k) : $d;
};
$is = function ($k, $e) use ($p) { return (string)($p->$k ?? '') === (string)$e ? 'selected' : ''; };

$j = function ($k, $d = '') use ($edit_job) {
    return ($edit_job && isset($edit_job[$k]) && $edit_job[$k] !== null) ? html_escape($edit_job[$k]) : $d;
};
$jsel = function ($k, $e) use ($edit_job) { return ($edit_job && (string)($edit_job[$k] ?? '') === (string)$e) ? 'selected' : ''; };
$jon  = function ($k) use ($edit_job) { return ($edit_job && !empty($edit_job[$k])) ? 'checked' : ''; };
$jpwd = function ($n) use ($edit_job) {
    $types = $edit_job ? array_map('trim', explode(',', (string)($edit_job['pwd_types'] ?? ''))) : [];
    return in_array($n, $types, true) ? 'checked' : '';
};

$save_action   = 'nsrp/save_establishment/' . (int)$target_id;
$assess_action = 'nsrp/establishment_assess/' . (int)$target_id;
$new_url       = site_url('nsrp/establishment/' . (int)$target_id);
$business      = $v('business_name') ?: $v('companyName');
$natures = ['Permanent','Contractual','Project-based','Internship / OJT','Part-time','Work from home / online job'];
$pwdList = ['Visual','Hearing','Speech','Physical','Mental'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= html_escape($page_title ?? 'NSRP Form 2') ?> - JobMatch</title>
  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <style>
    .nsrp .sec{background:#fff;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:18px;overflow:hidden}
    .nsrp .sec-head{background:#dbe7f3;color:#1e3a5f;font-weight:700;padding:.6rem 1rem;font-size:.9rem}
    .nsrp .sec-body{padding:1rem}
    .nsrp .form-label{font-size:.78rem;font-weight:600;color:#475569;margin-bottom:.2rem}
    .nsrp .form-control,.nsrp .form-select{font-size:.9rem}
    .nsrp .peso-box{background:#fffdf3}
    .nsrp .page-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1rem}
    .nsrp .badge-status{background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe}
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
              <h4 class="mb-0 fw-bold">NSRP Form 2 — Establishment Registration</h4>
              <div class="text-danger fw-semibold" style="font-size:.85rem">Establishment: <?= html_escape($business ?: $v('email')) ?></div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="badge badge-status">Status: <?= html_escape(ucfirst($v('nsrp_status', 'draft'))) ?></span>
              <a href="<?= site_url('nsrp/print_establishment/' . (int)$target_id) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-printer"></i> Print</a>
              <a href="<?= site_url('nsrp/send_establishment/' . (int)$target_id) ?>" class="btn btn-sm btn-outline-primary" onclick="return confirm('Send a confidential copy of this NSRP form to the establishment via Messages?')"><i class="mdi mdi-send"></i> Send to user</a>
            </div>
          </div>

          <?php foreach (['success' => 'success', 'danger' => 'danger'] as $key => $cls): ?>
            <?php if ($msg = $this->session->flashdata($key)): ?>
              <div class="alert alert-<?= $cls ?> py-2"><?= $msg ?></div>
            <?php endif; ?>
          <?php endforeach; ?>

          <?= form_open($save_action) ?>
          <input type="hidden" name="job_id" value="<?= $edit_job ? (int)$edit_job['id'] : '' ?>">

          <!-- I. ESTABLISHMENT DETAILS -->
          <div class="sec">
            <div class="sec-head">I. ESTABLISHMENT DETAILS</div>
            <div class="sec-body"><div class="row g-3">
              <div class="col-md-8"><label class="form-label">Business Name *</label><input name="business_name" class="form-control" value="<?= $business ?>" required></div>
              <div class="col-md-4"><label class="form-label">Trade Name</label><input name="trade_name" class="form-control" value="<?= $v('trade_name') ?>"></div>
              <div class="col-md-4"><label class="form-label">Acronym / Abbreviation</label><input name="acronym" class="form-control" value="<?= $v('acronym') ?>"></div>
              <div class="col-md-4"><label class="form-label">Office</label>
                <select name="office_type" class="form-select"><option value="">—</option>
                  <option value="main" <?= $is('office_type','main') ?>>Main office</option>
                  <option value="branch" <?= $is('office_type','branch') ?>>Branch</option>
                </select></div>
              <div class="col-md-4"><label class="form-label">Tax Identification Number</label><input name="tin" class="form-control" value="<?= $v('tin') ?>"></div>
              <div class="col-md-4"><label class="form-label">Employer Type</label>
                <select name="employer_type" class="form-select"><option value="">—</option>
                  <option value="public" <?= $is('employer_type','public') ?>>Public</option>
                  <option value="private" <?= $is('employer_type','private') ?>>Private</option>
                </select></div>
              <div class="col-md-8"><label class="form-label">Employer Sub-type</label>
                <select name="employer_subtype" class="form-select">
                  <?php foreach (['','National Government Agency','Local Government Unit','Government-owned and Controlled Corporation','State/Local University or College','Direct Hire','Local Recruitment Agency','Overseas Recruitment Agency','D.O. 174'] as $s): ?>
                    <option value="<?= $s ?>" <?= $is('employer_subtype',$s) ?>><?= $s ?: '—' ?></option>
                  <?php endforeach; ?>
                </select></div>
              <div class="col-md-4"><label class="form-label">Total Work Force</label>
                <select name="workforce_size" class="form-select"><option value="">—</option>
                  <option value="micro" <?= $is('workforce_size','micro') ?>>Micro (1-9)</option>
                  <option value="small" <?= $is('workforce_size','small') ?>>Small (10-99)</option>
                  <option value="medium" <?= $is('workforce_size','medium') ?>>Medium (100-199)</option>
                  <option value="large" <?= $is('workforce_size','large') ?>>Large (200+)</option>
                </select></div>
              <div class="col-md-8"><label class="form-label">Line of Business / Industry (per BIR 2303)</label><input name="line_of_business" class="form-control" value="<?= $v('line_of_business') ?>"></div>
              <div class="col-md-3"><label class="form-label">Street / Village</label><input name="street_village" class="form-control" value="<?= $v('street_village') ?>"></div>
              <div class="col-md-3"><label class="form-label">Barangay</label><input name="brgy" class="form-control" value="<?= $v('brgy') ?>"></div>
              <div class="col-md-3"><label class="form-label">Municipality / City</label><input name="city" class="form-control" value="<?= $v('city') ?>"></div>
              <div class="col-md-3"><label class="form-label">Province</label><input name="province" class="form-control" value="<?= $v('province') ?>"></div>
            </div></div>
          </div>

          <!-- II. CONTACT DETAILS -->
          <div class="sec">
            <div class="sec-head">II. ESTABLISHMENT CONTACT DETAILS</div>
            <div class="sec-body"><div class="row g-3">
              <div class="col-md-4"><label class="form-label">Owner / President (Full Name)</label><input name="owner_name" class="form-control" value="<?= $v('owner_name') ?>"></div>
              <div class="col-md-4"><label class="form-label">Contact Person (Full Name)</label><input name="contact_person" class="form-control" value="<?= $v('contact_person') ?>"></div>
              <div class="col-md-4"><label class="form-label">Position</label><input name="contact_position" class="form-control" value="<?= $v('contact_position') ?>"></div>
              <div class="col-md-4"><label class="form-label">Telephone Number</label><input name="telephone" class="form-control" value="<?= $v('telephone') ?>"></div>
              <div class="col-md-4"><label class="form-label">Mobile Number</label><input name="phoneNo" class="form-control" value="<?= $v('phoneNo') ?>"></div>
              <div class="col-md-4"><label class="form-label">Fax Number</label><input name="fax" class="form-control" value="<?= $v('fax') ?>"></div>
              <div class="col-md-12"><label class="form-label">E-mail Address</label><input class="form-control bg-light" value="<?= $v('email') ?>" disabled></div>
            </div></div>
          </div>

          <!-- III. VACANCY DETAILS -->
          <div class="sec">
            <div class="sec-head d-flex justify-content-between align-items-center">
              <span>III. VACANCY DETAILS <?= $edit_job ? '— editing #' . (int)$edit_job['id'] : '(optional — leave Position Title blank to skip)' ?></span>
              <?php if ($edit_job): ?><a href="<?= $new_url ?>" class="text-danger" style="font-size:.8rem;font-weight:400">+ new vacancy instead</a><?php endif; ?>
            </div>
            <div class="sec-body"><div class="row g-3">
              <div class="col-md-8"><label class="form-label">Position Title</label><input name="title" class="form-control" value="<?= $j('title') ?>"></div>
              <div class="col-md-4"><label class="form-label">Nature of Work</label>
                <select name="nature_of_work" class="form-select"><option value="">—</option>
                  <?php foreach ($natures as $n): ?><option value="<?= $n ?>" <?= $jsel('nature_of_work',$n) ?>><?= $n ?></option><?php endforeach; ?>
                </select></div>
              <div class="col-md-12"><label class="form-label">Job Description</label><textarea name="description" rows="3" class="form-control"><?= $j('description') ?></textarea></div>
              <div class="col-md-4"><label class="form-label">Place of Work</label><input name="place_of_work" class="form-control" value="<?= $j('place_of_work') ?>"></div>
              <div class="col-md-4"><label class="form-label">Salary</label><input name="salary" class="form-control" value="<?= $j('salary') ?>" placeholder="e.g. 15,000 - 18,000"></div>
              <div class="col-md-4"><label class="form-label">Vacancy Count</label><input type="number" name="vacancy_count" class="form-control" value="<?= $j('vacancy_count') ?>"></div>
            </div></div>
          </div>

          <!-- IV. QUALIFICATION REQUIREMENTS -->
          <div class="sec">
            <div class="sec-head">IV. QUALIFICATION REQUIREMENTS</div>
            <div class="sec-body"><div class="row g-3">
              <div class="col-md-4"><label class="form-label">Work Experience (months)</label><input type="number" name="work_experience_months" class="form-control" value="<?= $j('work_experience_months') ?>"></div>
              <div class="col-md-8"><label class="form-label">Other Qualifications</label><input name="other_qualifications" class="form-control" value="<?= $j('other_qualifications') ?>"></div>
              <div class="col-12">
                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="accepts_pwd" value="1" <?= $jon('accepts_pwd') ?>><label class="form-check-label">Accepts PWD</label></div>
                <?php foreach ($pwdList as $d): ?><div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="pwd_types[]" value="<?= $d ?>" <?= $jpwd($d) ?>><label class="form-check-label"><?= $d ?></label></div><?php endforeach; ?>
              </div>
              <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="accepts_ofw" value="1" <?= $jon('accepts_ofw') ?>><label class="form-check-label">Accepts returning OFWs</label></div></div>
              <div class="col-md-4"><label class="form-label">Educational Level</label><input name="educational_level" class="form-control" value="<?= $j('educational_level') ?>"></div>
              <div class="col-md-4"><label class="form-label">Course / SHS Strand</label><input name="course_strand" class="form-control" value="<?= $j('course_strand') ?>"></div>
              <div class="col-md-4"><label class="form-label">License</label><input name="license" class="form-control" value="<?= $j('license') ?>"></div>
              <div class="col-md-4"><label class="form-label">Eligibility</label><input name="eligibility" class="form-control" value="<?= $j('eligibility') ?>"></div>
              <div class="col-md-4"><label class="form-label">Certification</label><input name="certification" class="form-control" value="<?= $j('certification') ?>"></div>
              <div class="col-md-4"><label class="form-label">Language / Dialect Spoken</label><input name="language" class="form-control" value="<?= $j('language') ?>"></div>
            </div></div>
          </div>

          <!-- V. POSTING DETAILS -->
          <div class="sec">
            <div class="sec-head">V. POSTING DETAILS</div>
            <div class="sec-body"><div class="row g-3">
              <div class="col-md-4"><label class="form-label">Posting Date</label><input type="date" name="posting_date" class="form-control" value="<?= $j('posting_date') ?>"></div>
              <div class="col-md-4"><label class="form-label">Valid Until</label><input type="date" name="valid_until" class="form-control" value="<?= $j('valid_until') ?>"></div>
              <div class="col-md-4"><label class="form-label">Visibility</label>
                <select name="visibility" class="form-select">
                  <option value="public" <?= $jsel('visibility','public') ?>>Public</option>
                  <option value="followers" <?= $jsel('visibility','followers') ?>>Followers</option>
                </select></div>
            </div></div>
          </div>

          <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="<?= site_url('peso') ?>" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-danger"><i class="mdi mdi-content-save"></i> <?= $edit_job ? 'Save & update vacancy' : 'Save & post vacancy' ?></button>
          </div>
          <?= form_close() ?>

          <?php if (!empty($vacancies)): ?>
          <div class="sec">
            <div class="sec-head">Posted Vacancies (<?= count($vacancies) ?>)</div>
            <div class="sec-body p-0">
              <table class="table table-sm mb-0 align-middle">
                <thead><tr><th>Position</th><th>Nature</th><th>Count</th><th>Valid Until</th><th>Status</th><th></th></tr></thead>
                <tbody>
                  <?php foreach ($vacancies as $vac): ?>
                    <tr>
                      <td class="fw-semibold"><?= html_escape($vac['title']) ?></td>
                      <td><?= html_escape($vac['nature_of_work'] ?? '') ?></td>
                      <td><?= html_escape($vac['vacancy_count'] ?? '') ?></td>
                      <td><?= html_escape($vac['valid_until'] ?? '') ?></td>
                      <td><span class="badge bg-light text-dark"><?= html_escape($vac['status']) ?></span></td>
                      <td class="text-end"><a class="text-danger" href="<?= $new_url . '?job=' . (int)$vac['id'] ?>">Edit</a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php endif; ?>

          <!-- FOR USE OF PESO ONLY -->
          <div class="sec">
            <div class="sec-head">FOR USE OF PESO ONLY <?= $edit_job ? '(vacancy #' . (int)$edit_job['id'] . ')' : '' ?></div>
            <div class="sec-body peso-box">
              <?= form_open($assess_action) ?>
              <input type="hidden" name="job_id" value="<?= $edit_job ? (int)$edit_job['id'] : '' ?>">
              <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Assessed by</label><input name="assessed_by" class="form-control" value="<?= $j('assessed_by') ?>"></div>
                <div class="col-md-4"><label class="form-label">Encoded by</label><input name="encoded_by" class="form-control" value="<?= $j('encoded_by') ?>"></div>
                <div class="col-md-4"><label class="form-label">Establishment Status</label>
                  <select name="nsrp_status" class="form-select">
                    <?php foreach (['draft','submitted','assessed'] as $st): ?><option value="<?= $st ?>" <?= $is('nsrp_status',$st) ?>><?= ucfirst($st) ?></option><?php endforeach; ?>
                  </select></div>
              </div>
              <?php if (!$edit_job): ?><div class="text-muted mt-2" style="font-size:.8rem">Open a specific vacancy (Edit) to record its Assessed/Encoded by.</div><?php endif; ?>
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
</body>
</html>
