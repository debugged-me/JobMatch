<?php defined('BASEPATH') or exit('No direct script access allowed');
$g = function ($k, $d = '—') use ($p) {
    if (!$p || !isset($p->$k) || $p->$k === null || $p->$k === '') return $d;
    return html_escape($p->$k);
};
$business = $g('business_name', '') ?: $g('companyName');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>NSRP Form 2 — <?= html_escape($business) ?></title>
  <style>
    *{box-sizing:border-box} body{font-family:"Segoe UI",Arial,sans-serif;color:#111;font-size:12px;margin:24px}
    .hdr{text-align:center;margin-bottom:14px} .hdr h2{margin:2px 0;font-size:15px} .hdr .sub{font-size:11px;color:#333}
    .sec-title{background:#dbe7f3;border:1px solid #99b;padding:4px 8px;font-weight:bold;margin-top:12px;font-size:12px}
    table{width:100%;border-collapse:collapse} td,th{border:1px solid #bbb;padding:4px 6px;vertical-align:top;text-align:left}
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
    <h2>NSRP Form 2 — Establishment Registration Form</h2>
  </div>

  <div class="sec-title">I. ESTABLISHMENT DETAILS</div>
  <table>
    <tr><td class="lbl">Business Name</td><td><?= html_escape($business) ?: '—' ?></td><td class="lbl">Trade Name</td><td><?= $g('trade_name') ?></td></tr>
    <tr><td class="lbl">Acronym</td><td><?= $g('acronym') ?></td><td class="lbl">Office</td><td><?= html_escape(ucfirst((string)($p->office_type ?? ''))) ?: '—' ?></td></tr>
    <tr><td class="lbl">TIN</td><td><?= $g('tin') ?></td><td class="lbl">Employer Type</td><td><?= html_escape(ucfirst((string)($p->employer_type ?? ''))) ?> <?= $g('employer_subtype','') ?></td></tr>
    <tr><td class="lbl">Work Force</td><td><?= html_escape(ucfirst((string)($p->workforce_size ?? ''))) ?: '—' ?></td><td class="lbl">Line of Business</td><td><?= $g('line_of_business') ?></td></tr>
    <tr><td class="lbl">Address</td><td colspan="3"><?= html_escape(trim(implode(', ', array_filter([$p->street_village ?? '', $p->brgy ?? '', $p->city ?? '', $p->province ?? ''])))) ?: '—' ?></td></tr>
  </table>

  <div class="sec-title">II. CONTACT DETAILS</div>
  <table>
    <tr><td class="lbl">Owner / President</td><td><?= $g('owner_name') ?></td><td class="lbl">Contact Person</td><td><?= $g('contact_person') ?></td></tr>
    <tr><td class="lbl">Position</td><td><?= $g('contact_position') ?></td><td class="lbl">Telephone</td><td><?= $g('telephone') ?></td></tr>
    <tr><td class="lbl">Mobile</td><td><?= $g('phoneNo') ?></td><td class="lbl">Fax / Email</td><td><?= $g('fax') ?> / <?= $g('email') ?></td></tr>
  </table>

  <div class="sec-title">III–V. VACANCIES (<?= count($vacancies) ?>)</div>
  <?php if (empty($vacancies)): ?>
    <table><tr><td>No vacancies on file.</td></tr></table>
  <?php else: foreach ($vacancies as $vac): ?>
    <table style="margin-top:8px">
      <tr><td class="lbl">Position Title</td><td><?= html_escape($vac['title'] ?? '') ?></td><td class="lbl">Nature of Work</td><td><?= html_escape($vac['nature_of_work'] ?? '—') ?></td></tr>
      <tr><td class="lbl">Job Description</td><td colspan="3"><?= nl2br(html_escape($vac['description'] ?? '—')) ?></td></tr>
      <tr><td class="lbl">Place of Work</td><td><?= html_escape($vac['place_of_work'] ?? '—') ?></td><td class="lbl">Salary</td><td><?= html_escape($vac['salary'] ?? '—') ?></td></tr>
      <tr><td class="lbl">Vacancy Count</td><td><?= html_escape($vac['vacancy_count'] ?? '—') ?></td><td class="lbl">Work Exp. (months)</td><td><?= html_escape($vac['work_experience_months'] ?? '—') ?></td></tr>
      <tr><td class="lbl">Educational Level</td><td><?= html_escape($vac['educational_level'] ?? '—') ?></td><td class="lbl">Course/Strand</td><td><?= html_escape($vac['course_strand'] ?? '—') ?></td></tr>
      <tr><td class="lbl">License / Eligibility</td><td><?= html_escape(($vac['license'] ?? '').' '.($vac['eligibility'] ?? '')) ?: '—' ?></td><td class="lbl">Certification / Language</td><td><?= html_escape(($vac['certification'] ?? '').' '.($vac['language'] ?? '')) ?: '—' ?></td></tr>
      <tr><td class="lbl">Accepts PWD / OFW</td><td><?= (!empty($vac['accepts_pwd']) ? 'PWD ('.html_escape($vac['pwd_types'] ?? '').') ' : '') . (!empty($vac['accepts_ofw']) ? 'OFW' : '') ?: '—' ?></td><td class="lbl">Posting / Valid Until</td><td><?= html_escape(($vac['posting_date'] ?? '—').' → '.($vac['valid_until'] ?? '—')) ?></td></tr>
      <tr><td class="lbl">Assessed by</td><td><?= html_escape($vac['assessed_by'] ?? '—') ?></td><td class="lbl">Encoded by</td><td><?= html_escape($vac['encoded_by'] ?? '—') ?></td></tr>
    </table>
  <?php endforeach; endif; ?>

  <div class="sec-title" style="margin-top:12px">REGISTRATION STATUS</div>
  <table><tr><td class="lbl">Status</td><td><?= html_escape(ucfirst((string)($p->nsrp_status ?? 'draft'))) ?></td></tr></table>

  <script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 300); });</script>
</body>
</html>
