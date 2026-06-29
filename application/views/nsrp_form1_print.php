<?php defined('BASEPATH') or exit('No direct script access allowed');
$g = function ($k, $d = '—') use ($p) {
    if (!$p || !isset($p->$k) || $p->$k === null || $p->$k === '') return $d;
    return is_array($p->$k) ? html_escape(json_encode($p->$k)) : html_escape($p->$k);
};
$list = function ($k) use ($p) {
    $val = $p->$k ?? '';
    if (is_array($val)) return $val;
    $val = trim((string)$val);
    return $val === '' ? [] : array_map('trim', explode(',', $val));
};
$rows = function ($k) use ($p) { return is_array($p->$k ?? null) ? $p->$k : []; };
$name = trim((string)($p->first_name ?? '') . ' ' . (string)($p->last_name ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>NSRP Form 1 — <?= html_escape($name) ?></title>
  <style>
    *{box-sizing:border-box} body{font-family:"Segoe UI",Arial,sans-serif;color:#111;font-size:12px;margin:24px}
    .hdr{text-align:center;margin-bottom:14px} .hdr h2{margin:2px 0;font-size:15px}
    .hdr .sub{font-size:11px;color:#333}
    .sec-title{background:#dbe7f3;border:1px solid #99b;padding:4px 8px;font-weight:bold;margin-top:12px;font-size:12px}
    table{width:100%;border-collapse:collapse;margin-top:0}
    td{border:1px solid #bbb;padding:4px 6px;vertical-align:top}
    td.lbl{background:#f4f6f8;font-weight:600;width:22%;white-space:nowrap}
    .toolbar{margin-bottom:14px} .toolbar button{padding:6px 14px;cursor:pointer}
    @media print{.toolbar{display:none}}
  </style>
</head>
<body>
  <div class="toolbar"><button onclick="window.print()">Print</button> <button onclick="window.close()">Close</button></div>
  <div class="hdr">
    <div class="sub">Republic of the Philippines · Department of Labor and Employment</div>
    <div class="sub">NATIONAL SKILLS REGISTRATION PROGRAM · PESO Davao Oriental</div>
    <h2>NSRP Form 1 — Registration Form</h2>
  </div>

  <div class="sec-title">I. PERSONAL INFORMATION</div>
  <table>
    <tr><td class="lbl">Name</td><td><?= html_escape($name ?: '—') ?></td><td class="lbl">Sex</td><td><?= $g('sex') ?></td></tr>
    <tr><td class="lbl">Date of Birth</td><td><?= $g('date_of_birth') ?></td><td class="lbl">Place of Birth</td><td><?= $g('place_of_birth') ?></td></tr>
    <tr><td class="lbl">Civil Status</td><td><?= $g('civil_status') ?></td><td class="lbl">Citizenship</td><td><?= $g('citizenship') ?></td></tr>
    <tr><td class="lbl">Religion</td><td><?= $g('religion') ?></td><td class="lbl">Height / Weight</td><td><?= $g('height_cm') ?> cm / <?= $g('weight_kg') ?> kg</td></tr>
    <tr><td class="lbl">Mobile</td><td><?= $g('phoneNo') ?> / <?= $g('mobile_secondary') ?></td><td class="lbl">Landline / Email</td><td><?= $g('landline') ?> / <?= $g('email') ?></td></tr>
    <tr><td class="lbl">Present Address</td><td colspan="3"><?= html_escape(trim(implode(', ', array_filter([$p->present_street ?? '', $p->brgy ?? '', $p->city ?? '', $p->province ?? ''])))) ?: '—' ?></td></tr>
    <tr><td class="lbl">Permanent Address</td><td colspan="3"><?= !empty($p->perm_same_as_present) ? 'Same as present' : (html_escape(trim(implode(', ', array_filter([$p->perm_street ?? '', $p->perm_brgy ?? '', $p->perm_city ?? '', $p->perm_province ?? ''])))) ?: '—') ?></td></tr>
    <tr><td class="lbl">Disability</td><td colspan="3"><?= html_escape(implode(', ', $list('disability'))) ?: '—' ?></td></tr>
  </table>

  <div class="sec-title">EMPLOYMENT STATUS</div>
  <table>
    <tr><td class="lbl">Status</td><td><?= $g('employment_status') ?> <?= $g('employment_substatus','') ?></td><td class="lbl">Actively looking</td><td><?= !empty($p->actively_looking) ? 'Yes' : 'No' ?> <?= $g('looking_duration','') ?></td></tr>
    <tr><td class="lbl">4Ps beneficiary</td><td><?= !empty($p->is_4ps) ? 'Yes — '.$g('fourps_household_id','') : 'No' ?></td><td class="lbl">OFW</td><td><?= !empty($p->is_ofw) ? 'Yes' : 'No' ?></td></tr>
  </table>

  <div class="sec-title">II. JOB PREFERENCE</div>
  <table>
    <?php $occ = $rows('pref_occupations'); ?>
    <tr><td class="lbl">Occupation / Industry</td><td colspan="3"><?php if ($occ) { foreach ($occ as $o) { echo html_escape(($o['occupation'] ?? '').' / '.($o['industry'] ?? '')).'<br>'; } } else { echo '—'; } ?></td></tr>
    <tr><td class="lbl">Local</td><td><?= html_escape(implode(', ', $list('pref_locations_local'))) ?: '—' ?></td><td class="lbl">Overseas</td><td><?= html_escape(implode(', ', $list('pref_locations_overseas'))) ?: '—' ?></td></tr>
    <tr><td class="lbl">Salary Expectation</td><td colspan="3"><?= $g('salary_expectation') ?></td></tr>
  </table>

  <div class="sec-title">III. EDUCATIONAL BACKGROUND</div>
  <table>
    <tr><td class="lbl">Highest Level</td><td><?= $g('education_level') ?></td><td class="lbl">Course/Strand</td><td><?= $g('course') ?></td></tr>
    <tr><td class="lbl">School</td><td><?= $g('school') ?></td><td class="lbl">Year Graduated</td><td><?= $g('year_graduated') ?></td></tr>
  </table>

  <?php $tes = $rows('tesda_certs'); if ($tes): ?>
  <div class="sec-title">IV. TECHNICAL / VOCATIONAL TRAINING</div>
  <table><tr><td class="lbl">Qualification</td><td class="lbl">Cert. No.</td><td class="lbl">Expiry</td></tr>
    <?php foreach ($tes as $t): ?><tr><td><?= html_escape($t['qualification'] ?? '') ?></td><td><?= html_escape($t['number'] ?? '') ?></td><td><?= html_escape($t['expiry'] ?? '') ?></td></tr><?php endforeach; ?>
  </table>
  <?php endif; ?>

  <?php $exp = $rows('exp'); if ($exp): ?>
  <div class="sec-title">VI. WORK EXPERIENCE</div>
  <table><tr><td class="lbl">Company</td><td class="lbl">Position</td><td class="lbl">Dates</td><td class="lbl">Status</td></tr>
    <?php foreach ($exp as $e): ?><tr><td><?= html_escape($e['company'] ?? ($e['employer'] ?? '')) ?></td><td><?= html_escape($e['position'] ?? ($e['role'] ?? '')) ?></td><td><?= html_escape($e['dates'] ?? '') ?></td><td><?= html_escape($e['status'] ?? '') ?></td></tr><?php endforeach; ?>
  </table>
  <?php endif; ?>

  <div class="sec-title">SKILLS</div>
  <table>
    <tr><td class="lbl">21st Century</td><td colspan="3"><?= html_escape(implode(', ', $list('century_skills'))) ?: '—' ?></td></tr>
    <tr><td class="lbl">Technical</td><td colspan="3"><?= html_escape(implode(', ', $list('tech_skills_informal'))) ?: '—' ?></td></tr>
    <tr><td class="lbl">Other</td><td colspan="3"><?= $g('skills') ?></td></tr>
  </table>

  <div class="sec-title">FOR USE OF PESO ONLY</div>
  <table>
    <tr><td class="lbl">Eligible for</td><td colspan="3"><?= html_escape(implode(', ', $list('peso_eligibility'))) ?: '—' ?></td></tr>
    <tr><td class="lbl">Assessed by</td><td><?= $g('assessed_by') ?></td><td class="lbl">Date</td><td><?= $g('assessed_at') ?></td></tr>
    <tr><td class="lbl">Registration Ref.</td><td><?= $g('nsrp_reference') ?></td><td class="lbl">Status</td><td><?= html_escape(ucfirst((string)($p->nsrp_status ?? 'draft'))) ?></td></tr>
  </table>

  <script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 300); });</script>
</body>
</html>
