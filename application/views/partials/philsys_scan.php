<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * partials/philsys_scan.php — National ID (PhilSys) scan + status widget.
 *
 * Drop into any page:
 *   <?php $this->load->view('partials/philsys_scan', [
 *       'ps'        => $philsys,          // User_model::philsys_brief() or null
 *       'ps_mode'   => 'attach',          // 'attach' (logged-in) | 'preview' (signup)
 *       'ps_fields' => ['first' => '#first_name', 'last' => '#last_name',
 *                      'middle' => '#mName', 'dob' => '#dob', 'sex' => '#sex'],
 *   ]); ?>
 *
 * In 'preview' mode the raw QR is stored in hidden input[name=philsys_qr]
 * so the enclosing <form> posts it back for server-side re-verification —
 * the client is never trusted to claim a successful verification.
 */

$ps       = is_array($ps ?? null) ? $ps : [];
$psMode   = ($ps_mode ?? 'attach') === 'preview' ? 'preview' : 'attach';
$psFields = is_array($ps_fields ?? null) ? $ps_fields : [];
$status   = (string)($ps['philsys_status'] ?? '');
$cardType = (string)($ps['philsys_id_type'] ?? '');

$statusLabel = [
    'verified' => 'National ID Verified',
    'pending'  => 'ID Scanned — Pending Review',
    'failed'   => 'ID Check Failed',
][$status] ?? '';
$statusIcon = [
    'verified' => 'mdi-shield-check',
    'pending'  => 'mdi-timer-sand',
    'failed'   => 'mdi-shield-alert-outline',
][$status] ?? 'mdi-card-account-details-outline';
$statusCls = [
    'verified' => 'pscan-badge--ok',
    'pending'  => 'pscan-badge--warn',
    'failed'   => 'pscan-badge--bad',
][$status] ?? '';
?>

<div class="pscan" data-pscan>
  <div class="pscan-row">
    <?php if ($status !== ''): ?>
      <span class="pscan-badge <?= $statusCls ?>">
        <i class="mdi <?= $statusIcon ?>"></i> <?= $statusLabel ?>
      </span>
      <?php if (!empty($ps['philsys_name'])): ?>
        <span class="pscan-meta">
          <?= htmlspecialchars($ps['philsys_name'], ENT_QUOTES, 'UTF-8') ?>
          <?= $cardType !== '' ? '· ' . htmlspecialchars($cardType, ENT_QUOTES, 'UTF-8') : '' ?>
        </span>
      <?php endif; ?>
    <?php endif; ?>

    <button type="button" class="pscan-btn" data-pscan-open>
      <i class="mdi mdi-qrcode-scan"></i>
      <?= $status === '' ? 'Scan National ID' : 'Rescan ID' ?>
    </button>
  </div>

  <div class="pscan-status" data-pscan-status hidden></div>
  <div class="pscan-result" data-pscan-result hidden></div>

  <?php if ($psMode === 'preview'): ?>
    <input type="hidden" name="philsys_qr" id="philsysQr" value="">
  <?php endif; ?>

  <div id="pscanModal" class="pscan-modal" data-pscan-modal role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="pscanTitle">
    <div class="pscan-modal__panel">
      <div class="pscan-modal__head">
        <h3 id="pscanTitle"><i class="mdi mdi-card-account-details-outline"></i> Scan your National ID</h3>
        <button type="button" class="pscan-x" data-pscan-close aria-label="Close">&times;</button>
      </div>
      <p class="pscan-hint">
        PhilID card or ePhilID printout — point the camera at the QR code on the back/front.
      </p>
      <div id="pscanReader" class="pscan-reader" data-pscan-reader></div>
      <div class="pscan-status" data-pscan-modal-status hidden></div>
      <div class="pscan-modal__foot">
        <label class="pscan-file">
          <i class="mdi mdi-image-outline"></i> Upload a photo of the QR instead
          <input type="file" accept="image/*" data-pscan-file hidden>
        </label>
        <button type="button" class="pscan-btn pscan-btn--ghost" data-pscan-close>Cancel</button>
      </div>
    </div>
  </div>
</div>

<?php static $__pscanAssets = false; if (!$__pscanAssets): $__pscanAssets = true; ?>
<style>
.pscan-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px}
.pscan-badge{display:inline-flex;align-items:center;gap:6px;font-weight:700;font-size:.78rem;border-radius:9999px;padding:.3rem .7rem;border:1px solid transparent}
.pscan-badge--ok{background:rgba(22,163,74,.10);color:#166534;border-color:rgba(22,163,74,.25)}
.pscan-badge--warn{background:rgba(245,158,11,.12);color:#92400e;border-color:rgba(245,158,11,.3)}
.pscan-badge--bad{background:rgba(239,68,68,.10);color:#991b1b;border-color:rgba(239,68,68,.25)}
.pscan-meta{font-size:.82rem;color:var(--jm-gray-600,#4b5563)}
.pscan-btn{display:inline-flex;align-items:center;gap:.45rem;border:1px solid var(--jm-gray-300,#d1d5db);background:#fff;color:var(--jm-gray-800,#1f2937);border-radius:10px;padding:.5rem .9rem;font-weight:700;font-size:.85rem;cursor:pointer}
.pscan-btn:hover{border-color:var(--jm-primary,#b91c1c);color:var(--jm-primary,#b91c1c)}
.pscan-btn--ghost{border-color:transparent}
.pscan-status{font-size:.82rem;margin:.35rem 0}
.pscan-status--err{color:#b91c1c}.pscan-status--info{color:#2563eb}.pscan-status--ok{color:#166534;font-weight:600}
.pscan-result{margin:.5rem 0}
.pscan-result-card{border:1px solid var(--jm-gray-200,#e5e7eb);border-radius:12px;padding:12px 14px;background:#fff;max-width:520px}
.pscan-result--verified{border-color:rgba(22,163,74,.4)}
.pscan-result--failed{border-color:rgba(239,68,68,.4)}
.pscan-result-head{font-weight:800;margin-bottom:6px}
.pscan-result dl{display:grid;grid-template-columns:1fr;gap:2px 16px;margin:0}
.pscan-result dt{font-size:.72rem;color:var(--jm-gray-500,#6b7280);text-transform:uppercase;letter-spacing:.03em}
.pscan-result dd{margin:0 0 4px;font-weight:600}
.pscan-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:1070;background:rgba(2,6,23,.55);backdrop-filter:saturate(140%) blur(3px);padding:16px}
.pscan-modal.show{display:flex}
.pscan-modal__panel{width:100%;max-width:480px;max-height:92vh;overflow-y:auto;background:#fff;border-radius:16px;border:1px solid var(--jm-gray-200,#e5e7eb);box-shadow:0 22px 60px rgba(2,6,23,.25);padding:18px}
.pscan-modal__head{display:flex;align-items:center;justify-content:space-between;gap:8px}
.pscan-modal__head h3{margin:0;font-size:1.05rem;display:flex;align-items:center;gap:8px}
.pscan-x{border:0;background:none;font-size:1.5rem;line-height:1;cursor:pointer;color:var(--jm-gray-500,#6b7280)}
.pscan-hint{font-size:.82rem;color:var(--jm-gray-500,#6b7280);margin:6px 0 12px}
.pscan-reader{width:100%;min-height:220px;border-radius:12px;overflow:hidden;background:#0f172a;display:block}
.pscan-reader video{width:100%!important;border-radius:12px}
.pscan-modal__foot{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:12px;flex-wrap:wrap}
.pscan-file{display:inline-flex;align-items:center;gap:6px;font-size:.82rem;font-weight:600;color:var(--jm-blue-600,#2563eb);cursor:pointer}
.pscan-file:hover{text-decoration:underline}
</style>
<script src="<?= base_url('assets/js/html5-qrcode.min.js') ?>"></script>
<script src="<?= base_url('assets/js/philsys-scan.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var box = document.querySelector('[data-pscan]');
  if (!box || !window.PhilsysScan) return;
  window.PhilsysScan.init(box, {
    endpoint: '<?= site_url($psMode === 'preview' ? 'philsys/preview' : 'philsys/attach') ?>',
    <?php if ($psMode === 'preview'): ?>hiddenField: '#philsysQr',<?php endif; ?>
    fields: <?= json_encode($psFields, JSON_UNESCAPED_SLASHES) ?>,
    <?php if ($psMode !== 'preview'): ?>
    onDone: function (r) {
      if (r && (r.status === 'verified' || r.status === 'pending')) {
        setTimeout(function () { window.location.reload(); }, 1600);
      }
    }
    <?php else: ?>
    onDone: null
    <?php endif; ?>
  });
});
</script>
<?php endif; ?>
