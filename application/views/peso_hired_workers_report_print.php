<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Print — PESO Hired Workers Report</title>
  <style>
    * { box-sizing: border-box; }

    body {
      font-family: "Segoe UI", Roboto, Arial, sans-serif;
      color: #111827;
      margin: 0;
      padding: 24px;
      background: #fff;
      font-size: 12px;
    }

    .sheet { max-width: 1000px; margin: 0 auto; }

    .title {
      margin: 0;
      font-size: 20px;
      font-weight: 800;
      color: #9b1f24;
    }

    .sub {
      margin: 4px 0 16px;
      color: #6b7280;
      font-size: 11px;
    }

    .box {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 16px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      text-align: left;
      padding: 8px 10px;
      border-bottom: 1px solid #e5e7eb;
      vertical-align: top;
    }

    thead th {
      background: #fbeaea;
      color: #7f1d1d;
      font-size: 10.5px;
      text-transform: uppercase;
      letter-spacing: .03em;
    }

    .summary th { background: #f3f4f6; color: #374151; width: 18%; }

    .muted { color: #6b7280; }

    .filters { display: flex; flex-wrap: wrap; gap: 5px; }

    .pill {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 999px;
      background: #fbeaea;
      color: #9b1f24;
      font-size: 10.5px;
      font-weight: 600;
    }

    @media print {
      body { padding: 0; }
      .box { box-shadow: none; }
    }
  </style>
</head>

<body>
  <?php
  $rows = isset($rows) && is_array($rows) ? $rows : [];
  $stats = isset($stats) && is_array($stats) ? $stats : [];
  $labels = isset($active_filter_labels) && is_array($active_filter_labels) ? $active_filter_labels : [];
  ?>
  <div class="sheet">
    <h1 class="title">PESO Davao Oriental — Hired Workers Report</h1>
    <p class="sub">Generated: <?= date('M d, Y h:i A') ?> • Timezone: Asia/Manila</p>

    <div class="box">
      <table class="summary">
        <tr>
          <th>This Month</th>
          <th>This Year</th>
          <th>Hired Records</th>
          <th>Workers</th>
          <th>Clients</th>
          <th>Filters</th>
        </tr>
        <tr>
          <td><?= (int)($stats['hired_this_month'] ?? 0) ?></td>
          <td><?= (int)($stats['hired_this_year'] ?? 0) ?></td>
          <td><?= (int)($stats['total_hires'] ?? 0) ?></td>
          <td><?= (int)($stats['total_workers'] ?? 0) ?></td>
          <td><?= (int)($stats['total_clients'] ?? 0) ?></td>
          <td>
            <?php if (empty($labels)): ?>
              <span class="muted">No filters applied</span>
            <?php else: ?>
              <div class="filters">
                <?php foreach ($labels as $label): ?>
                  <span class="pill"><?= htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </td>
        </tr>
      </table>
    </div>

    <div class="box">
      <?php if (empty($rows)): ?>
        <div class="muted" style="padding:16px;">No hired worker records found.</div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Worker</th>
              <th>Client</th>
              <th>Project</th>
              <th>Rate</th>
              <th>Status</th>
              <th>Hired Date</th>
              <th>Updated Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <?php
              $rate = isset($r['rate']) && $r['rate'] !== null && $r['rate'] !== ''
                ? number_format((float)$r['rate'], 2)
                : '—';
              $unit = trim((string)($r['rate_unit'] ?? ''));
              if ($rate !== '—' && $unit !== '') {
                $rate .= ' / ' . $unit;
              }

              $createdAtRaw = trim((string)($r['created_at'] ?? ''));
              $updatedAtRaw = trim((string)($r['updated_at'] ?? ''));
              $createdAt = $createdAtRaw !== '' && strtotime($createdAtRaw)
                ? date('M d, Y h:i A', strtotime($createdAtRaw))
                : '—';
              $updatedAt = $updatedAtRaw !== '' && strtotime($updatedAtRaw)
                ? date('M d, Y h:i A', strtotime($updatedAtRaw))
                : '—';
              $project = trim((string)($r['project_title'] ?? ''));
              ?>
              <tr>
                <td>
                  <?= htmlspecialchars((string)($r['worker_name'] ?? ('Worker #' . (int)($r['worker_id'] ?? 0))), ENT_QUOTES, 'UTF-8') ?>
                  <?php if (!empty($r['worker_email'])): ?>
                    <div class="muted" style="font-size:10.5px;"><?= htmlspecialchars((string)$r['worker_email'], ENT_QUOTES, 'UTF-8') ?></div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars((string)($r['client_name'] ?? ('Client #' . (int)($r['client_id'] ?? 0))), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($project !== '' ? $project : '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($rate, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)($r['status'] ?? 'hired'), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($updatedAt, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <script>window.addEventListener('load', function () { window.print(); });</script>
</body>

</html>
