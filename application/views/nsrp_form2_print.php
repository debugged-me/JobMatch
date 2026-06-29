<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * NSRP Form 2 — print layout faithful to the official DOLE establishment form.
 * Vars: $p (object|null), $vacancies (array)
 */
$val = function ($k) use ($p) {
    if (!$p || !isset($p->$k) || $p->$k === null) return '';
    return is_array($p->$k) ? '' : html_escape($p->$k);
};
$eq  = function ($k, $v) use ($p) { return strcasecmp((string)($p->$k ?? ''), $v) === 0; };
$box = function ($on) { return '<span class="cb' . ($on ? ' on' : '') . '"></span>'; };

$business = $val('business_name') ?: $val('companyName');
$addr = trim(implode(', ', array_filter([$val('street_village'), $val('brgy'), $val('city'), $val('province')])), ', ');
$natures = ['Permanent','Contractual','Project-based','Internship / OJT','Part-time','Work from home / online job'];
$pwdList = ['Visual','Hearing','Speech','Physical','Mental'];
$vacs = !empty($vacancies) ? $vacancies : [[]];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>NSRP Form 2 — <?= html_escape($business) ?></title>
  <style>
    *{box-sizing:border-box}
    body{font-family:"Calibri","Segoe UI",Arial,sans-serif;color:#000;font-size:10.5px;margin:0;padding:18px 22px;background:#fff}
    .toolbar{margin-bottom:10px} .toolbar button{padding:6px 16px;cursor:pointer;font-size:12px}
    table{width:100%;border-collapse:collapse;table-layout:fixed}
    td,th{border:1px solid #000;padding:2px 5px;vertical-align:top;word-wrap:break-word}
    .hd{border:1px solid #000} .hd .ttl{font-weight:bold;text-align:center;font-size:11px} .hd .gov{text-align:center;line-height:1.25} .hd .gov b{font-size:11px}
    .sec{background:#c6d9f0;font-weight:bold;border:1px solid #000;border-top:none;padding:2px 6px;font-size:10.5px}
    .lbl{font-weight:bold} .cap{font-size:8.5px;color:#222}
    .cb{display:inline-block;width:9px;height:9px;border:1px solid #000;vertical-align:middle;margin:0 3px 0 6px;position:relative}
    .cb:first-child{margin-left:0} .cb.on:after{content:"";position:absolute;left:1px;top:1px;width:5px;height:5px;background:#000}
    .instr{border:1px solid #000;border-top:none;padding:3px 6px;font-size:8.6px;line-height:1.3}
    .fill{font-weight:600} .val{min-height:13px} .mt{margin-top:9px}
    .vac{border:1px solid #000;border-top:none;padding:0}
    @media print{.toolbar{display:none} body{padding:0} .vacancy{page-break-inside:avoid}}
  </style>
</head>
<body>
  <div class="toolbar"><button onclick="window.print()">Print</button> <button onclick="window.close()">Close</button></div>

  <table class="hd">
    <tr>
      <td style="width:30%" class="ttl">NSRP Form 2<br><span class="cap">September 2020</span></td>
      <td class="gov">
        Republic of the Philippines<br>
        Department of Labor and Employment<br>
        <b>NATIONAL SKILLS REGISTRATION PROGRAM</b><br>
        <b>ESTABLISHMENT REGISTRATION FORM</b>
      </td>
    </tr>
  </table>
  <div class="instr"><b>INSTRUCTIONS:</b> Please fill out the form legibly in block letters with a ballpoint pen. Check appropriate boxes. Please do not leave any items unanswered. Indicate “NA” if not applicable.</div>

  <!-- I. ESTABLISHMENT DETAILS -->
  <div class="sec">I. ESTABLISHMENT DETAILS</div>
  <table>
    <tr><td colspan="2"><span class="lbl">Business Name:</span> <span class="fill"><?= html_escape($business) ?></span></td></tr>
    <tr>
      <td style="width:50%"><span class="lbl">Trade Name:</span> <span class="fill"><?= $val('trade_name') ?></span></td>
      <td style="width:50%"><span class="lbl">Acronym/Abbreviation:</span> <span class="fill"><?= $val('acronym') ?></span></td>
    </tr>
    <tr>
      <td><?= $box($eq('office_type','main')) ?>Main office <?= $box($eq('office_type','branch')) ?>Branch</td>
      <td><span class="lbl">Tax Identification Number:</span> <span class="fill"><?= $val('tin') ?></span></td>
    </tr>
    <tr>
      <td colspan="2"><span class="lbl">Employer type:</span>
        <?= $box($eq('employer_type','public')) ?>Public
        ( <?= $box($eq('employer_subtype','National Government Agency')) ?>National Government Agency
        <?= $box($eq('employer_subtype','Local Government Unit')) ?>LGU
        <?= $box($eq('employer_subtype','Government-owned and Controlled Corporation')) ?>GOCC
        <?= $box($eq('employer_subtype','State/Local University or College')) ?>State/Local University or College )
        &nbsp; <?= $box($eq('employer_type','private')) ?>Private
        ( <?= $box($eq('employer_subtype','Direct Hire')) ?>Direct Hire
        <?= $box($eq('employer_subtype','Local Recruitment Agency')) ?>Local Recruitment Agency
        <?= $box($eq('employer_subtype','Overseas Recruitment Agency')) ?>Overseas Recruitment Agency
        <?= $box($eq('employer_subtype','D.O. 174')) ?>D.O. 174 )
      </td>
    </tr>
    <tr>
      <td colspan="2"><span class="lbl">Total Work Force:</span>
        <?= $box($eq('workforce_size','micro')) ?>Micro (1-9)
        <?= $box($eq('workforce_size','small')) ?>Small (10-99)
        <?= $box($eq('workforce_size','medium')) ?>Medium (100-199)
        <?= $box($eq('workforce_size','large')) ?>Large (200 and up)
      </td>
    </tr>
    <tr><td colspan="2"><span class="lbl">Line of Business/Industry</span> <span class="cap">(check BIR 2303)</span>: <span class="fill"><?= $val('line_of_business') ?></span></td></tr>
    <tr><td colspan="2"><span class="lbl">Address:</span> <span class="fill"><?= html_escape($addr) ?></span></td></tr>
  </table>

  <!-- II. CONTACT DETAILS -->
  <div class="sec mt">II. ESTABLISHMENT CONTACT DETAILS</div>
  <table>
    <tr>
      <td style="width:50%"><span class="lbl">Name of Owner/President:</span> <span class="fill"><?= $val('owner_name') ?></span></td>
      <td style="width:50%"><span class="lbl">Contact Person:</span> <span class="fill"><?= $val('contact_person') ?></span></td>
    </tr>
    <tr>
      <td><span class="lbl">Position:</span> <span class="fill"><?= $val('contact_position') ?></span></td>
      <td><span class="lbl">Telephone Number:</span> <span class="fill"><?= $val('telephone') ?></span></td>
    </tr>
    <tr>
      <td><span class="lbl">Mobile Number:</span> <span class="fill"><?= $val('phoneNo') ?></span></td>
      <td><span class="lbl">Fax Number:</span> <span class="fill"><?= $val('fax') ?></span></td>
    </tr>
    <tr><td colspan="2"><span class="lbl">E-mail Address:</span> <span class="fill"><?= $val('email') ?></span></td></tr>
  </table>

  <!-- Per-vacancy: III, IV, V -->
  <?php foreach ($vacs as $idx => $vac): ?>
  <div class="vacancy">
    <div class="sec mt">III. VACANCY DETAILS<?= count($vacs) > 1 ? ' (' . ($idx + 1) . ' of ' . count($vacs) . ')' : '' ?></div>
    <table>
      <tr><td colspan="2"><span class="lbl">Position Title:</span> <span class="fill"><?= html_escape($vac['title'] ?? '') ?></span></td></tr>
      <tr>
        <td style="width:60%"><span class="lbl">Job Description:</span><br><span class="fill"><?= nl2br(html_escape($vac['description'] ?? '')) ?></span></td>
        <td style="width:40%"><span class="lbl">Nature of Work:</span><br>
          <?php foreach ($natures as $i => $n): ?><?= $box(strcasecmp((string)($vac['nature_of_work'] ?? ''), $n) === 0) ?><?= $n ?><?= ($i % 2 === 1) ? '<br>' : '' ?><?php endforeach; ?>
        </td>
      </tr>
      <tr>
        <td><span class="lbl">Place of Work:</span> <span class="fill"><?= html_escape($vac['place_of_work'] ?? '') ?></span></td>
        <td><span class="lbl">Salary:</span> <span class="fill"><?= html_escape($vac['salary'] ?? '') ?></span> &nbsp; <span class="lbl">Vacancy Count:</span> <span class="fill"><?= html_escape($vac['vacancy_count'] ?? '') ?></span></td>
      </tr>
    </table>

    <div class="sec mt">IV. QUALIFICATION REQUIREMENTS</div>
    <table>
      <tr>
        <td style="width:50%"><span class="lbl">Work Experience (months):</span> <span class="fill"><?= html_escape($vac['work_experience_months'] ?? '') ?></span></td>
        <td style="width:50%"><span class="lbl">Other qualifications:</span> <span class="fill"><?= html_escape($vac['other_qualifications'] ?? '') ?></span></td>
      </tr>
      <tr>
        <td>Accepts PWD? <?= $box(!empty($vac['accepts_pwd'])) ?>Yes <?= $box(isset($vac['accepts_pwd']) && !$vac['accepts_pwd']) ?>No<br>
          <?php $pt = array_map('trim', explode(',', (string)($vac['pwd_types'] ?? ''))); foreach ($pwdList as $d): ?><?= $box(in_array($d, $pt, true)) ?><?= $d ?><?php endforeach; ?>
        </td>
        <td>Accepts returning OFWs? <?= $box(!empty($vac['accepts_ofw'])) ?>Yes <?= $box(isset($vac['accepts_ofw']) && !$vac['accepts_ofw']) ?>No</td>
      </tr>
      <tr>
        <td><span class="lbl">Educational Level:</span> <span class="fill"><?= html_escape($vac['educational_level'] ?? '') ?></span></td>
        <td><span class="lbl">Course/SHS Strand:</span> <span class="fill"><?= html_escape($vac['course_strand'] ?? '') ?></span></td>
      </tr>
      <tr>
        <td><span class="lbl">License:</span> <span class="fill"><?= html_escape($vac['license'] ?? '') ?></span></td>
        <td><span class="lbl">Eligibility:</span> <span class="fill"><?= html_escape($vac['eligibility'] ?? '') ?></span></td>
      </tr>
      <tr>
        <td><span class="lbl">Certification:</span> <span class="fill"><?= html_escape($vac['certification'] ?? '') ?></span></td>
        <td><span class="lbl">Language/Dialect Spoken:</span> <span class="fill"><?= html_escape($vac['language'] ?? '') ?></span></td>
      </tr>
    </table>

    <div class="sec mt">V. POSTING DETAILS</div>
    <table>
      <tr>
        <td style="width:50%"><span class="lbl">Posting Date:</span> <span class="fill"><?= html_escape($vac['posting_date'] ?? '') ?></span></td>
        <td style="width:50%"><span class="lbl">Valid Until:</span> <span class="fill"><?= html_escape($vac['valid_until'] ?? '') ?></span></td>
      </tr>
    </table>

    <div class="sec">FOR USE OF PESO ONLY</div>
    <table>
      <tr>
        <td style="width:50%"><span class="lbl">Assessed by:</span> <span class="fill"><?= html_escape($vac['assessed_by'] ?? '') ?></span></td>
        <td style="width:50%"><span class="lbl">Encoded by:</span> <span class="fill"><?= html_escape($vac['encoded_by'] ?? '') ?></span></td>
      </tr>
    </table>
  </div>
  <?php endforeach; ?>

  <!-- CERTIFICATION -->
  <div class="sec mt">CERTIFICATION / AUTHORIZATION</div>
  <table><tr><td style="font-size:8.6px;line-height:1.3">
    This is to certify that all data/information provided in this form are true to the best of my knowledge. This is also to authorize the DOLE to include our profile in the PESO Employment Information System (PEIS). It is understood that relevant information provided shall be made available to those who have access to PEIS. I am also aware that DOLE is not obliged to seek applicants on our behalf.
    <br><br>
    ______________________________ &nbsp;&nbsp;&nbsp; ____________________<br>
    <span class="cap">Signature over printed name of Authorized Representative</span> &nbsp;&nbsp; <span class="cap">Date</span>
  </td></tr></table>

  <script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 350); });</script>
</body>
</html>
