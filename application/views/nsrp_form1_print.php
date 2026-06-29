<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * NSRP Form 1 (Rev.3) — print layout faithful to the official DOLE form.
 */
$val = function ($k) use ($p) {
    if (!$p || !isset($p->$k) || $p->$k === null) return '';
    return is_array($p->$k) ? '' : html_escape($p->$k);
};
$arr = function ($k) use ($p) {
    $v = $p->$k ?? '';
    if (is_array($v)) return $v;
    $v = trim((string)$v);
    return $v === '' ? [] : array_map('trim', explode(',', $v));
};
$rows = function ($k) use ($p) { return is_array($p->$k ?? null) ? $p->$k : []; };

// checkbox glyph: filled square if on
$box = function ($on) { return '<span class="cb' . ($on ? ' on' : '') . '"></span>'; };
$eq  = function ($k, $v) use ($p) { return strcasecmp((string)($p->$k ?? ''), $v) === 0; };
$in  = function ($k, $v) use ($arr) { return in_array($v, $arr($k), true); };

// derived
$dob = $val('date_of_birth');
$age = '';
if ($dob && ($t = strtotime($dob))) { $age = (string)(int)((time() - $t) / 31557600); }
$present = trim(implode(', ', array_filter([$val('present_street'), $val('brgy'), $val('city'), $val('province')])), ', ');
$perm = !empty($p->perm_same_as_present)
    ? 'Same as present'
    : trim(implode(', ', array_filter([$val('perm_street'), $val('perm_brgy'), $val('perm_city'), $val('perm_province')])), ', ');
$occ = $rows('pref_occupations');
$loc = $arr('pref_locations_local');
$ovr = $arr('pref_locations_overseas');
$tes = $rows('tesda_certs');
$elig = $rows('eligibilities');
$exp = $rows('exp');
$century = ['Innovation','Team Work','Multitasking','Work Ethics','Self Motivation','Creative Problem Solving','Problem Solving','Critical Thinking','Decision Making','Stress Tolerance','Planning and Organizing','Social Perceptiveness','English Functional Skills','English Comprehension','Math Functional Skill'];
$tech = ['Carpentry','Masonry','Welding','Auto Mechanic','Plumbing','Driving','Gardening','Tailoring','Photography','Hairdressing','Cooking','Baking'];
$selCentury = $arr('century_skills');
$selTech    = $arr('tech_skills_informal');
$selPeso    = $arr('peso_eligibility');
$name = trim($val('first_name') . ' ' . $val('last_name'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>NSRP Form 1 — <?= html_escape($name) ?></title>
  <style>
    *{box-sizing:border-box}
    @page{size:A4 portrait;margin:9mm}
    body{font-family:"Calibri","Segoe UI",Arial,sans-serif;color:#000;font-size:10px;line-height:1.3;margin:0;padding:14px;background:#fff}
    .toolbar{margin-bottom:8px}
    .toolbar button{padding:5px 14px;cursor:pointer;font-size:12px}
    table{width:100%;border-collapse:collapse;table-layout:fixed}
    td,th{border:1px solid #000;padding:3px 6px;vertical-align:top;word-wrap:break-word}
    .noborder,.noborder td{border:none}
    .hd{border:1px solid #000}
    .hd .ttl{font-weight:bold;text-align:center;font-size:11px}
    .hd .gov{text-align:center;line-height:1.25}
    .hd .gov b{font-size:11px}
    .sec{background:#c6d9f0;font-weight:bold;border:1px solid #000;border-top:none;padding:2px 6px;font-size:10px}
    .lbl{font-weight:bold}
    .cap{font-size:8px;font-weight:normal;color:#222}
    .cb{display:inline-block;width:9px;height:9px;border:1px solid #000;vertical-align:middle;margin:0 3px 0 6px;position:relative}
    .cb:first-child{margin-left:0}
    .cb.on:after{content:"";position:absolute;left:1px;top:1px;width:5px;height:5px;background:#000}
    .instr{border:1px solid #000;border-top:none;padding:3px 6px;font-size:8.4px;line-height:1.25}
    .val{min-height:15px}
    .fill{font-weight:600}
    .mt{margin-top:6px}
    @media print{.toolbar{display:none} body{padding:0}}
  </style>
</head>
<body>
  <div class="toolbar"><button onclick="window.print()">Print</button> <button onclick="window.close()">Close</button></div>

  <!-- Header -->
  <table class="hd">
    <tr>
      <td style="width:30%" class="ttl">NSRP Form 1.REV 3</td>
      <td class="gov">
        Republic of the Philippines<br>
        Department of Labor and Employment<br>
        <b>NATIONAL SKILLS REGISTRATION PROGRAM</b><br>
        <b>REGISTRATION FORM</b>
      </td>
    </tr>
  </table>
  <div class="instr"><b>INSTRUCTIONS:</b> Please fill out the form legibly with ballpen. Print in block letters. Check appropriate boxes. Please do not leave any items unanswered. Indicate “NA” if not applicable.</div>

  <!-- I. PERSONAL INFORMATION -->
  <div class="sec">I. PERSONAL INFORMATION</div>
  <table>
    <tr>
      <td style="width:25%"><span class="cap">LAST NAME</span><br><span class="fill"><?= $val('last_name') ?></span></td>
      <td style="width:25%"><span class="cap">FIRST NAME</span><br><span class="fill"><?= $val('first_name') ?></span></td>
      <td style="width:25%"><span class="cap">MIDDLE NAME</span><br>&nbsp;</td>
      <td style="width:25%"><span class="cap">SUFFIX (Sr., Jr.)</span><br>&nbsp;</td>
    </tr>
  </table>
  <table>
    <tr>
      <td style="width:50%"><span class="lbl">DATE OF BIRTH</span> <span class="cap">(mm/dd/yyyy)</span> &nbsp; <span class="fill"><?= $val('date_of_birth') ?></span></td>
      <td style="width:50%"><span class="lbl">AGE</span> &nbsp; <span class="fill"><?= html_escape($age) ?></span></td>
    </tr>
    <tr>
      <td><span class="lbl">SEX</span> <?= $box($eq('sex','male')) ?> Male <?= $box($eq('sex','female')) ?> Female</td>
      <td rowspan="3"><span class="lbl">PRESENT ADDRESS</span><br><span class="fill"><?= html_escape($present) ?></span></td>
    </tr>
    <tr><td><span class="lbl">PLACE OF BIRTH</span> &nbsp; <span class="fill"><?= $val('place_of_birth') ?></span></td></tr>
    <tr>
      <td><span class="lbl">CIVIL STATUS</span>
        <?= $box($eq('civil_status','Single')) ?>Single
        <?= $box($eq('civil_status','Married')) ?>Married
        <?= $box($eq('civil_status','Widowed')) ?>Widowed
        <?= $box($eq('civil_status','Separated')) ?>Separated
        <?= $box($eq('civil_status','Others')) ?>Others
      </td>
    </tr>
    <tr>
      <td><span class="lbl">CITIZENSHIP</span> &nbsp; <span class="fill"><?= $val('citizenship') ?></span></td>
      <td><span class="lbl">PERMANENT ADDRESS</span> <?= $box(!empty($p->perm_same_as_present)) ?><span class="cap">same as present</span><br><span class="fill"><?= html_escape($perm) ?></span></td>
    </tr>
    <tr>
      <td><span class="lbl">HEIGHT</span> <span class="fill"><?= $val('height_cm') ?></span> &nbsp;&nbsp; <span class="lbl">WEIGHT</span> <span class="fill"><?= $val('weight_kg') ?></span></td>
      <td><span class="lbl">RELIGION</span> &nbsp; <span class="fill"><?= $val('religion') ?></span></td>
    </tr>
    <tr>
      <td><span class="lbl">LANDLINE NUMBER</span> &nbsp; <span class="fill"><?= $val('landline') ?></span></td>
      <td><span class="lbl">MOBILE NUMBER</span> &nbsp; <span class="fill"><?= $val('phoneNo') ?><?= $val('mobile_secondary') ? ' / ' . $val('mobile_secondary') : '' ?></span></td>
    </tr>
    <tr>
      <td colspan="2"><span class="lbl">EMAIL ADDRESS</span> &nbsp; <span class="fill"><?= $val('email') ?></span></td>
    </tr>
    <tr>
      <td colspan="2"><span class="lbl">DISABILITY</span>
        <?php foreach (['Visual','Hearing','Speech','Physical','Mental'] as $d): ?><?= $box($in('disability',$d)) ?><?= $d ?><?php endforeach; ?>
      </td>
    </tr>
    <tr>
      <td><span class="lbl">EMPLOYMENT STATUS</span> <?= $box($eq('employment_status','Employed')) ?>Employed <?= $box($eq('employment_status','Unemployed')) ?>Unemployed</td>
      <td><span class="cap">Sub-status:</span> <span class="fill"><?= $val('employment_substatus') ?></span></td>
    </tr>
    <tr>
      <td>Are you actively looking for work? <?= $box(!empty($p->actively_looking)) ?>Yes <?= $box(isset($p->actively_looking) && !$p->actively_looking) ?>No &nbsp; <span class="cap">How long:</span> <span class="fill"><?= $val('looking_duration') ?></span></td>
      <td>Willing to work immediately? <?= $box(!empty($p->willing_immediate)) ?>Yes <?= $box(isset($p->willing_immediate) && !$p->willing_immediate) ?>No &nbsp; <span class="cap">If no, when:</span> <span class="fill"><?= $val('available_when') ?></span></td>
    </tr>
    <tr>
      <td>Are you a 4Ps beneficiary? <?= $box(!empty($p->is_4ps)) ?>Yes <?= $box(isset($p->is_4ps) && !$p->is_4ps) ?>No &nbsp; <span class="cap">Household ID:</span> <span class="fill"><?= $val('fourps_household_id') ?></span></td>
      <td>Are you an OFW? <?= $box(!empty($p->is_ofw)) ?>Yes &nbsp; Considering coming back to PH to work? <?= $box(!empty($p->ofw_returning)) ?>Yes</td>
    </tr>
  </table>

  <!-- II. JOB PREFERENCE -->
  <div class="sec mt">II. JOB PREFERENCE</div>
  <table>
    <tr><td class="cap" style="width:50%;text-align:center">PREFERRED OCCUPATION</td><td class="cap" style="width:50%;text-align:center">INDUSTRY</td></tr>
    <?php for ($i = 0; $i < max(2, count($occ)); $i++): $o = $occ[$i] ?? []; ?>
      <tr><td class="fill val"><?= html_escape($o['occupation'] ?? '') ?></td><td class="fill val"><?= html_escape($o['industry'] ?? '') ?></td></tr>
    <?php endfor; ?>
    <tr>
      <td><span class="lbl">PREFERRED WORK LOCATION (Local):</span><br><span class="fill"><?= html_escape(implode('; ', $loc)) ?></span></td>
      <td><span class="lbl">Overseas:</span><br><span class="fill"><?= html_escape(implode('; ', $ovr)) ?></span></td>
    </tr>
    <tr><td colspan="2"><span class="lbl">Salary Expectation (PHP):</span> <span class="fill"><?= $val('salary_expectation') ?></span></td></tr>
  </table>

  <!-- III. EDUCATIONAL BACKGROUND -->
  <div class="sec mt">III. EDUCATIONAL BACKGROUND</div>
  <table>
    <tr>
      <td colspan="2"><span class="lbl">HIGHEST EDUCATIONAL LEVEL</span><br>
        <?php foreach (['No formal education','Elementary Level','Elementary Graduate','High School Level','High School Graduate','College level','College Graduate','Technical-vocational graduate','Post Graduate'] as $el): ?><?= $box($eq('education_level',$el)) ?><?= $el ?> <?php endforeach; ?>
      </td>
    </tr>
    <tr>
      <td style="width:50%"><span class="lbl">SCHOOL/UNIVERSITY</span><br><span class="fill"><?= $val('school') ?></span></td>
      <td style="width:50%"><span class="lbl">COURSE/PROGRAM</span><br><span class="fill"><?= $val('course') ?></span></td>
    </tr>
    <tr><td colspan="2"><span class="lbl">YEAR GRADUATED/LAST ATTENDED:</span> <span class="fill"><?= $val('year_graduated') ?></span></td></tr>
  </table>

  <!-- IV. TRAINING -->
  <div class="sec mt">IV. TECHNICAL / VOCATIONAL AND OTHER TRAINING</div>
  <table>
    <tr><td class="cap">TRAINING / QUALIFICATION</td><td class="cap">CERTIFICATE / NC NO.</td><td class="cap">EXPIRY</td></tr>
    <?php for ($i = 0; $i < max(2, count($tes)); $i++): $t = $tes[$i] ?? []; ?>
      <tr><td class="fill val"><?= html_escape($t['qualification'] ?? '') ?></td><td class="fill val"><?= html_escape($t['number'] ?? '') ?></td><td class="fill val"><?= html_escape($t['expiry'] ?? '') ?></td></tr>
    <?php endfor; ?>
  </table>

  <!-- V. ELIGIBILITY -->
  <div class="sec mt">V. ELIGIBILITY</div>
  <table>
    <tr><td class="cap">CAREER SERVICE / BOARD / BAR</td><td class="cap">LICENSE NUMBER</td><td class="cap">EXPIRY DATE</td></tr>
    <?php for ($i = 0; $i < max(2, count($elig)); $i++): $e = $elig[$i] ?? []; ?>
      <tr><td class="fill val"><?= html_escape($e['career_service'] ?? '') ?></td><td class="fill val"><?= html_escape($e['license_no'] ?? '') ?></td><td class="fill val"><?= html_escape($e['expiry'] ?? '') ?></td></tr>
    <?php endfor; ?>
    <tr><td colspan="3"><span class="lbl">Dialects / Languages Spoken:</span> <span class="fill"><?= html_escape(implode(', ', $arr('languages'))) ?></span></td></tr>
  </table>

  <!-- VI. WORK EXPERIENCE -->
  <div class="sec mt">VI. WORK EXPERIENCE (last 10 years, most recent first)</div>
  <table>
    <tr><td class="cap">NAME OF OFFICE/COMPANY</td><td class="cap">ADDRESS</td><td class="cap">POSITION HELD</td><td class="cap">INCLUSIVE DATES</td><td class="cap">STATUS</td></tr>
    <?php for ($i = 0; $i < max(3, count($exp)); $i++): $e = $exp[$i] ?? []; ?>
      <tr>
        <td class="fill val"><?= html_escape($e['company'] ?? ($e['employer'] ?? '')) ?></td>
        <td class="fill val"><?= html_escape($e['address'] ?? '') ?></td>
        <td class="fill val"><?= html_escape($e['position'] ?? ($e['role'] ?? '')) ?></td>
        <td class="fill val"><?= html_escape($e['dates'] ?? '') ?></td>
        <td class="fill val"><?= html_escape($e['status'] ?? '') ?></td>
      </tr>
    <?php endfor; ?>
  </table>

  <!-- VII. 21st CENTURY SKILLS -->
  <div class="sec mt">VII. 21st CENTURY SKILLS (self-assessment — check five)</div>
  <table><tr><td>
    <?php foreach ($century as $i => $s): ?><?= $box(in_array($s, $selCentury, true)) ?><?= $s ?><?= ($i % 3 === 2) ? '<br>' : '&nbsp;&nbsp;' ?><?php endforeach; ?>
  </td></tr></table>

  <!-- IX. TECHNICAL SKILLS -->
  <div class="sec mt">IX. TECHNICAL SKILLS ACQUIRED WITHOUT FORMAL TRAINING</div>
  <table><tr><td>
    <?php foreach ($tech as $i => $s): ?><?= $box(in_array($s, $selTech, true)) ?><?= $s ?><?= ($i % 4 === 3) ? '<br>' : '&nbsp;&nbsp;' ?><?php endforeach; ?>
    <?php if ($v = $val('skills')): ?><br><span class="lbl">Others:</span> <span class="fill"><?= $v ?></span><?php endif; ?>
  </td></tr></table>

  <!-- CERTIFICATION -->
  <div class="sec mt">CERTIFICATION / AUTHORIZATION</div>
  <table><tr><td style="font-size:7.6px;line-height:1.2">
    This is to certify that all data/information that I have provided in this form are true to the best of my knowledge. This is also to authorize the DOLE to include my profile in the Skills Registry System maintained in the PhilJobNet. It is understood that my name shall be made available to employers who have access to the Registry. I am also aware that DOLE is not obliged to seek employment on my behalf.
    <br>
    ______________________________ &nbsp;&nbsp;&nbsp; ____________________ &nbsp;
    <span class="cap">Signature of Applicant / Date</span>
  </td></tr></table>

  <!-- FOR USE OF PESO ONLY -->
  <div class="sec mt">FOR USE OF PESO ONLY — PLEASE DO NOT WRITE ABOVE THIS LINE</div>
  <table>
    <tr><td colspan="2">Eligible for public employment services?
      <?php foreach (['SPES','GIP','TUPAD','JobStart'] as $pe): ?><?= $box(in_array($pe, $selPeso, true)) ?><?= $pe ?><?php endforeach; ?>
    </td></tr>
    <tr>
      <td style="width:50%"><span class="lbl">Assessed by:</span> <span class="fill"><?= $val('assessed_by') ?></span></td>
      <td style="width:50%"><span class="lbl">Date:</span> <span class="fill"><?= $val('assessed_at') ?></span></td>
    </tr>
    <tr>
      <td><span class="lbl">Registration Ref. No.:</span> <span class="fill"><?= $val('nsrp_reference') ?></span></td>
      <td><span class="lbl">Status:</span> <span class="fill"><?= html_escape(ucfirst((string)($p->nsrp_status ?? 'draft'))) ?></span></td>
    </tr>
  </table>

  <script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 350); });</script>
</body>
</html>
