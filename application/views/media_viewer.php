<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="Content-Security-Policy"
    content="default-src 'self';
                 img-src 'self' data: blob: <?= base_url('uploads') ?>;
                 script-src 'self' 'unsafe-inline';
                 style-src 'self' 'unsafe-inline';
                 frame-src 'self' blob: data:;
                 object-src 'none';
                 base-uri 'self';">
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> • JobMatch DavOr</title>
  <link rel="icon" href="<?= base_url('assets/images/logo.png') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/css/tokens.css') ?>" />
  <?php

  $OPEN_URL = ($type === 'image' && !empty($img_url)) ? $img_url : $url;
  $DL_URL   = $OPEN_URL . (strpos($OPEN_URL, '?') === false ? '?dl=1' : '&dl=1');
  $WM_TEXT = isset($wm_text) && $wm_text !== '' ? $wm_text
    : ('');
  $WM_SVG = rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" width="1600" height="800" viewBox="0 0 1600 800">
       <defs>
         <filter id="wmblur" x="-50%" y="-50%" width="200%" height="200%">
           <feGaussianBlur stdDeviation="0.6"/>
         </filter>
       </defs>
       <g transform="rotate(-30 800 400) skewX(-12)">
         <text x="60" y="520"
               font-family="Inter, Arial, sans-serif"
               font-size="140"
               font-weight="900"
               letter-spacing="6"
               fill="#d00"
               fill-opacity=".5"
               stroke="#000"
               stroke-opacity=".18"
               stroke-width="3"
               filter="url(#wmblur)">' .
      htmlspecialchars($WM_TEXT, ENT_QUOTES, "UTF-8") .
      '</text>
       </g>
     </svg>'
  );
  ?>
  <style>
    :root {
      --bg: var(--jm-ink);
      --panel: var(--jm-slate-900);
      --ink: var(--jm-gray-200);
      --muted: var(--jm-slate-400);
      --line: var(--jm-gray-800);
      --btn: var(--jm-gray-800);
      --btn-hover: var(--jm-gray-900);
    }

    * {
      box-sizing: border-box
    }

    html,
    body {
      height: 100%
    }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--ink);
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial
    }

    .wrap {
      height: 100%;
      display: grid;
      grid-template-rows: auto 1fr auto
    }

    header {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 14px;
      background: var(--panel);
      border-bottom: 1px solid var(--line)
    }

    header img.logo {
      width: 22px;
      height: 22px;
      border-radius: 4px
    }

    header .ttl {
      font-weight: 700;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis
    }

    header .spacer {
      flex: 1
    }

    .toolbar {
      display: flex;
      gap: 8px;
      flex-wrap: wrap
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .45rem .7rem;
      background: transparent;
      border: 1px solid var(--btn);
      border-radius: 8px;
      color: var(--ink);
      text-decoration: none;
      cursor: pointer;
      user-select: none
    }

    .btn:hover {
      background: var(--btn-hover)
    }

    .btn[disabled] {
      opacity: .5;
      cursor: not-allowed
    }

    main {
      position: relative
    }

    .stage {
      position: absolute;
      inset: 0;
      overflow: hidden;
      background: radial-gradient(ellipse at center, #0d1326 0%, var(--jm-ink) 60%)
    }

    .canvas {
      position: absolute;
      top: 50%;
      left: 50%;
      will-change: transform
    }

    .canvas img {
      display: block;
      max-width: none;
      max-height: none;
      image-rendering: auto
    }

    .pdf-wrap {
      position: absolute;
      inset: 0;
      background: var(--jm-gray-900)
    }

    .pdf-wrap iframe {
      width: 100%;
      height: 100%;
      border: 0;
      background: var(--jm-gray-900)
    }

    .pdf-fallback {
      position: absolute;
      inset: 0;
      display: none;
      align-items: center;
      justify-content: center;
      gap: 12px;
      flex-direction: column;
      text-align: center;
      padding: 20px;
      background: var(--jm-gray-900);
    }

    footer {
      padding: 10px 14px;
      background: var(--panel);
      border-top: 1px solid var(--line);
      display: flex;
      gap: 10px;
      align-items: center
    }

    .info {
      color: var(--muted);
      font-size: .9rem
    }

    .wm-overlay {
      position: absolute;
      inset: 0;
      z-index: 50;
      pointer-events: none;
      background-image: url('data:image/svg+xml;utf8,<?= $WM_SVG ?>');
      background-repeat: repeat;
      background-position: center;
      background-size: 1600px 800px;
      opacity: 1;
    }
  </style>
</head>

<body>
  <div class="wrap">
    <header>
      <img class="logo" src="<?= base_url('assets/images/logo.png') ?>" alt="logo">
      <div class="ttl"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></div>
      <div class="spacer"></div>

      <?php if ($type === 'image'): ?>
        <div class="toolbar">
          <button class="btn" id="fitBtn" title="Fit to screen">Fit</button>
          <button class="btn" id="hundredBtn" title="Actual size (100%)">100%</button>
          <button class="btn" id="zoomOutBtn" title="Zoom out">−</button>
          <button class="btn" id="zoomInBtn" title="Zoom in">+</button>
          <button class="btn" id="rotateBtn" title="Rotate 90°">↻</button>
          <button class="btn" id="resetBtn" title="Reset view">Reset</button>
        </div>
      <?php endif; ?>

      <div class="toolbar">
        <?php if ($type === 'image'): ?>
          <a class="btn" href="<?= htmlspecialchars($OPEN_URL, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Open</a>
          <a class="btn" href="<?= htmlspecialchars($DL_URL,   ENT_QUOTES, 'UTF-8') ?>" rel="noopener">Download</a>

        <?php elseif ($type === 'pdf'): ?>
          <a class="btn" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Open</a>
          <a class="btn" href="<?= htmlspecialchars($url . (strpos($url, '?') === false ? '?' : '&') . 'dl=1', ENT_QUOTES, 'UTF-8') ?>" rel="noopener">Download</a>

        <?php else: ?>
          <a class="btn" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Open</a>
          <a class="btn" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" download rel="noopener">Download</a>
        <?php endif; ?>

        <button class="btn" id="fsBtn" title="Fullscreen">Fullscreen</button>
        <button class="btn" id="closeBtn" title="Close">Close</button>
      </div>
    </header>

    <main>
      <div class="stage" id="stage">
        <?php if ($type === 'image'): ?>
          <div class="canvas" id="canvas" style="transform: translate(-50%, -50%) scale(1) rotate(0deg);">
            <img id="img" src="<?= htmlspecialchars(($type === 'image' && !empty($img_url)) ? $img_url : $url, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
          </div>

        <?php elseif ($type === 'pdf'): ?>
          <div class="pdf-wrap">
            <iframe
              id="pdfFrame"
              src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>#toolbar=1&navpanes=1&scrollbar=1"
              title="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
            </iframe>

            <div id="pdfFallback" class="pdf-fallback">
              <p>This PDF can’t be previewed here due to browser restrictions.</p>
              <div>
                <a class="btn" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Open PDF</a>
                <a class="btn" href="<?= htmlspecialchars($url . (strpos($url, '?') === false ? '?' : '&') . 'dl=1', ENT_QUOTES, 'UTF-8') ?>" rel="noopener">Download</a>
              </div>
            </div>
          </div>

        <?php else: ?>
          <div style="display:flex;align-items:center;justify-content:center;height:100%;text-align:center;padding:20px;">
            <div>
              <p>This file type can’t be previewed here.</p>
              <p><a class="btn" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" download>Download file</a></p>
            </div>
          </div>
        <?php endif; ?>
        <div class="wm-overlay"></div>
      </div>
    </main>

    <footer>
      <div class="info" id="status">
        <?php if ($type === 'image'): ?>
          Loading…
        <?php else: ?>
          PDF viewer
        <?php endif; ?>
      </div>
    </footer>
  </div>

  <script>
    (function() {
      const TYPE = <?= json_encode($type) ?>;
      const FILE_URL = <?= json_encode($url) ?>;

      function wireCommon() {
        const fsBtn = document.getElementById('fsBtn');
        const closeBtn = document.getElementById('closeBtn');
        fsBtn?.addEventListener('click', () => {
          const el = document.documentElement;
          if (!document.fullscreenElement) el.requestFullscreen?.();
          else document.exitFullscreen?.();
        });
        closeBtn?.addEventListener('click', () => {
          if (!window.close()) history.length > 1 ? history.back() : location.href = FILE_URL;
        });
      }

      if (TYPE === 'pdf') {
        setupPdf();
        wireCommon();
        return;
      }

      const stage = document.getElementById('stage');
      const canvas = document.getElementById('canvas');
      const img = document.getElementById('img');
      const status = document.getElementById('status');

      const fitBtn = document.getElementById('fitBtn');
      const hundredBtn = document.getElementById('hundredBtn');
      const zoomInBtn = document.getElementById('zoomInBtn');
      const zoomOutBtn = document.getElementById('zoomOutBtn');
      const rotateBtn = document.getElementById('rotateBtn');
      const resetBtn = document.getElementById('resetBtn');

      let scale = 1,
        deg = 0,
        panX = 0,
        panY = 0;
      let naturalW = 0,
        naturalH = 0;
      let isPanning = false,
        startX = 0,
        startY = 0;
      const MIN = 0.1,
        MAX = 8.0,
        STEP = 0.1;

      img?.addEventListener('load', () => {
        naturalW = img.naturalWidth;
        naturalH = img.naturalHeight;
        fitToScreen();
        updateStatus();
      });

      function getFitScale() {
        const rect = stage.getBoundingClientRect();
        const rotated = (deg % 180) !== 0;
        const iw = rotated ? naturalH : naturalW;
        const ih = rotated ? naturalW : naturalH;
        if (!iw || !ih) return 1;
        return Math.max(MIN, Math.min(1, Math.min(rect.width / iw, rect.height / ih)));
      }

      function applyTransform() {
        canvas.style.transform = `translate(-50%,-50%) translate(${panX}px,${panY}px) scale(${scale}) rotate(${deg}deg)`;
      }

      function fitToScreen() {
        scale = getFitScale();
        panX = 0;
        panY = 0;
        applyTransform();
      }

      function set100() {
        scale = 1;
        panX = 0;
        panY = 0;
        applyTransform();
      }

      function resetView() {
        deg = 0;
        fitToScreen();
      }

      function clamp(v, a, b) {
        return Math.max(a, Math.min(b, v));
      }

      function zoomAt(factor, x, y) {
        const prev = scale;
        scale = clamp(scale * factor, MIN, MAX);
        const rect = stage.getBoundingClientRect();
        const cx = x - rect.left - rect.width / 2,
          cy = y - rect.top - rect.height / 2;
        panX -= cx * (scale / prev - 1);
        panY -= cy * (scale / prev - 1);
        applyTransform();
      }

      stage?.addEventListener('wheel', (e) => {
        e.preventDefault();
        zoomAt(e.deltaY > 0 ? 1 - STEP : 1 + STEP, e.clientX, e.clientY);
        updateStatus();
      }, {
        passive: false
      });
      stage?.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return;
        isPanning = true;
        startX = e.clientX;
        startY = e.clientY;
        stage.style.cursor = 'grabbing';
      });
      window.addEventListener('mousemove', (e) => {
        if (!isPanning) return;
        panX += e.clientX - startX;
        panY += e.clientY - startY;
        startX = e.clientX;
        startY = e.clientY;
        applyTransform();
      });
      window.addEventListener('mouseup', () => {
        isPanning = false;
        stage.style.cursor = 'default';
      });
      stage?.addEventListener('dblclick', () => {
        Math.abs(scale - 1) < .01 ? fitToScreen() : set100();
        updateStatus();
      });

      fitBtn?.addEventListener('click', () => {
        fitToScreen();
        updateStatus();
      });
      hundredBtn?.addEventListener('click', () => {
        set100();
        updateStatus();
      });
      zoomInBtn?.addEventListener('click', () => {
        zoomAt(1 + STEP, stage.clientWidth / 2, stage.clientHeight / 2);
        updateStatus();
      });
      zoomOutBtn?.addEventListener('click', () => {
        zoomAt(1 - STEP, stage.clientWidth / 2, stage.clientHeight / 2);
        updateStatus();
      });
      rotateBtn?.addEventListener('click', () => {
        deg = (deg + 90) % 360;
        fitToScreen();
        updateStatus();
      });
      resetBtn?.addEventListener('click', () => {
        resetView();
        updateStatus();
      });
      window.addEventListener('resize', () => {
        const fit = getFitScale();
        if (Math.abs(scale - fit) < .02) fitToScreen();
      });

      wireCommon();

      function updateStatus() {
        const pct = Math.round(scale * 100);
        status.textContent = `Zoom: ${pct}% • Rotate: ${deg}°`;
      }

      function setupPdf() {
        const frame = document.getElementById('pdfFrame');
        const fb = document.getElementById('pdfFallback');
        let loaded = false;
        frame?.addEventListener('load', function() {
          loaded = true;
          if (fb) fb.style.display = 'none';
        });
        setTimeout(function() {
          if (!loaded) {
            if (fb) fb.style.display = 'flex';
          }
        }, 800);
      }
    })();
  </script>
</body>

</html>