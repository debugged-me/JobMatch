/**
 * philsys-scan.js — shared PhilSys (national ID) QR scanner UI.
 *
 * Framework-free; works with the vendored html5-qrcode build
 * (assets/js/html5-qrcode.min.js) and CSRF auto-injection from csrf.js.
 *
 * Markup is rendered by application/views/partials/philsys_scan.php.
 * Each instance lives inside a container with [data-pscan]; wire it with:
 *
 *   PhilsysScan.init(container, {
 *     endpoint:    'philsys/preview' | 'philsys/attach'  (site-relative or abs)
 *     hiddenField: '#philsysQr'        // receives raw QR text (signup flow)
 *     fields: {                        // autofill targets (optional)
 *       first: '#first_name', last: '#last_name', middle: '#mName',
 *       dob: '#date_of_birth', sex: '#sex'
 *     },
 *     onDone: function (result) { ... }
 *   });
 */
(function () {
  'use strict';

  if (window.PhilsysScan) return;

  function $(sel, root) { return (root || document).querySelector(sel); }

  function toast(msg, kind) {
    if (window.JM && typeof window.JM.toast === 'function') {
      window.JM.toast(msg, kind);
    }
  }

  function statusPillText(status) {
    switch (status) {
      case 'verified': return 'National ID verified';
      case 'pending':  return 'ID scanned — pending staff confirmation';
      case 'failed':   return 'ID check failed';
      default:         return 'National ID not linked';
    }
  }

  function maskPcn(pcn) {
    var d = String(pcn || '').replace(/\D/g, '');
    return d.length >= 4 ? '••••-••••-••••-' + d.slice(-4) : '••••';
  }

  function setField(root, sel, val) {
    if (!sel || val === undefined || val === null || val === '') return;
    var el = $(sel);
    if (!el) return;
    if (el.tagName === 'SELECT') {
      var v = String(val).toLowerCase();
      for (var i = 0; i < el.options.length; i++) {
        if (el.options[i].value.toLowerCase() === v) { el.selectedIndex = i; break; }
      }
    } else {
      el.value = val;
    }
    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function init(container, opts) {
    var root = typeof container === 'string' ? $(container) : container;
    if (!root || root.__pscanInit) return;
    root.__pscanInit = true;
    opts = opts || {};

    var modal      = $('[data-pscan-modal]', root) || $('[data-pscan] .pscan-modal', root) || $('#pscanModal');
    var readerEl   = $('[data-pscan-reader]', root);
    var statusEl   = $('[data-pscan-status]', root);
    var resultEl   = $('[data-pscan-result]', root);
    var fileInput  = $('[data-pscan-file]', root);
    var openBtns   = root.querySelectorAll('[data-pscan-open]');
    // Status line rendered inside the modal so feedback is visible while
    // the overlay is open (the page-level one sits behind it).
    var modalStatusEl = modal ? $('[data-pscan-modal-status]', modal) : null;
    var scanner    = null;
    var busy       = false;

    // Reparent to <body>: a transformed/animated ancestor would otherwise
    // become the containing block for position:fixed and trap the overlay
    // inside the form column (seen on the auth card pages).
    if (modal && modal.parentNode !== document.body) {
      document.body.appendChild(modal);
    }

    function setStatus(msg, kind) {
      [statusEl, modalStatusEl].forEach(function (el) {
        if (!el) return;
        el.textContent = msg || '';
        el.className = 'pscan-status' + (kind ? ' pscan-status--' + kind : '');
        el.hidden = !msg;
      });
    }

    function showResult(html) {
      if (!resultEl) return;
      resultEl.innerHTML = html || '';
      resultEl.hidden = !html;
    }

    function openModal(e) {
      if (e) e.preventDefault();
      if (!modal) return;
      modal.classList.add('show');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      startCamera();
    }

    function closeModal() {
      stopCamera();
      if (!modal) return;
      modal.classList.remove('show');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    function startCamera() {
      if (!readerEl || typeof Html5Qrcode === 'undefined') {
        setStatus('QR library not loaded.', 'err');
        return;
      }
      setStatus('Point the camera at the QR code on your PhilID / ePhilID.', 'info');
      try {
        scanner = new Html5Qrcode(readerEl.id, { verbose: false });
        scanner.start(
          { facingMode: 'environment' },
          { fps: 10, qrbox: { width: 240, height: 240 } },
          onDecoded,
          function () { /* per-frame decode miss — ignore */ }
        ).catch(function (err) {
          setStatus('Camera unavailable (' + err + '). You can upload a photo of the QR instead.', 'err');
        });
      } catch (e) {
        setStatus('Could not start camera. Try the upload option below.', 'err');
      }
    }

    // Returns a promise — callers that reuse the reader element (photo
    // upload path) must wait for teardown or scanFile() throws while the
    // camera is still stopping.
    function stopCamera() {
      var s = scanner;
      scanner = null;
      if (!s) return Promise.resolve();
      try {
        return s.stop().then(function () {
          try { s.clear(); } catch (e) {}
        }).catch(function () {});
      } catch (e) {
        return Promise.resolve();
      }
    }

    function onDecoded(text) {
      if (busy) return;
      stopCamera();
      postQr(text);
    }

    function postQr(text) {
      busy = true;
      setStatus('Verifying ID…', 'info');
      var fd = new FormData();
      fd.append('qr', text);

      fetch(opts.endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      }).then(function (res) {
        return res.json().catch(function () { return {}; }).then(function (json) {
          return { status: res.status, json: json };
        });
      }).then(function (r) {
        busy = false;
        var d = r.json || {};
        if (!d.ok) {
          setStatus(d.msg || 'This QR code could not be verified.', 'err');
          toast(d.msg || 'ID verification failed.', 'error');
          // Keep the modal usable — restart the camera so they can retry.
          setTimeout(function () {
            if (modal && modal.classList.contains('show')) startCamera();
          }, 1800);
          return;
        }

        // Persist raw payload for the server to re-verify on submit (signup)
        if (opts.hiddenField) {
          var h = $(opts.hiddenField);
          if (h) h.value = text;
        }

        var s = d.subject || {};
        if (opts.fields) {
          setField(null, opts.fields.first,  s.fName);
          setField(null, opts.fields.last,   s.lName);
          setField(null, opts.fields.middle, s.mName);
          setField(null, opts.fields.dob,    s.dob);
          setField(null, opts.fields.sex,    s.sex && String(s.sex).toLowerCase());
        }

        var icon = d.status === 'verified' ? '✔' : (d.status === 'failed' ? '✖' : '•');
        var sv   = d.sig !== undefined ? d.sig : d.sig_valid;
        var sig  = sv === true ? 'signature valid' : (sv === false ? 'signature INVALID' : 'signature not checked');
        var html =
          '<div class="pscan-result-card pscan-result--' + (d.status || 'pending') + '">' +
          '  <div class="pscan-result-head">' + icon + ' ' + statusPillText(d.status) + '</div>' +
          '  <dl>' +
          (d.name || s.fName ? '    <div><dt>Name on ID</dt><dd>' + escHtml(d.name || ((s.fName || '') + ' ' + (s.lName || ''))) + '</dd></div>' : '') +
          (s.dob || d.dob ? '    <div><dt>Date of birth</dt><dd>' + escHtml(s.dob || d.dob) + '</dd></div>' : '') +
          (s.sex ? '    <div><dt>Sex</dt><dd>' + escHtml(s.sex) + '</dd></div>' : '') +
          (s.pcn_masked ? '    <div><dt>PCN</dt><dd>' + escHtml(s.pcn_masked) + '</dd></div>' : '') +
          '    <div><dt>Card</dt><dd>' + escHtml(d.card_type || d.card || '') + ' · ' + escHtml(sig) + '</dd></div>' +
          '  </dl>' +
          '</div>';
        showResult(html);
        setStatus(d.msg || statusPillText(d.status), d.status === 'verified' ? 'ok' : 'info');

        if (typeof opts.onDone === 'function') opts.onDone(d);
        toast(d.msg || statusPillText(d.status), d.status === 'failed' ? 'error' : (d.status === 'verified' ? 'success' : 'info'));
        // Brief beat so the user sees the outcome inside the modal.
        setTimeout(closeModal, 1200);
      }).catch(function () {
        busy = false;
        setStatus('Network error while verifying. Please try again.', 'err');
      });
    }

    function escHtml(s) {
      return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
      });
    }

    for (var i = 0; i < openBtns.length; i++) {
      openBtns[i].addEventListener('click', openModal);
    }
    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal || e.target.hasAttribute('data-pscan-close')) closeModal();
      });
    }
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal && modal.classList.contains('show')) closeModal();
    });
    if (fileInput) {
      fileInput.addEventListener('change', function () {
        var f = fileInput.files && fileInput.files[0];
        if (!f) return;
        setStatus('Reading QR from photo…', 'info');
        // Wait for the camera to fully release the reader element, then
        // decode on a fresh instance (a stopped instance can't scanFile).
        stopCamera().then(function () {
          try {
            new Html5Qrcode(readerEl.id, { verbose: false })
              .scanFile(f, false)
              .then(function (text) { postQr(text); })
              .catch(function () {
                setStatus('Could not find a QR code in that image. Try a clearer photo or scan with the camera.', 'err');
              });
          } catch (e) {
            setStatus('Could not read that image.', 'err');
          }
        });
        fileInput.value = '';
      });
    }
  }

  window.PhilsysScan = { init: init, statusText: statusPillText, maskPcn: maskPcn };
})();
