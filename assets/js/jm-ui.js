/*!
 * jm-ui.js — shared UI kit for JobMatch DavOr (framework-free).
 *
 *   JM.toast('Saved!', 'success')                 // success|error|warning|info
 *   JM.confirm({title:'Delete?', danger:true})    // -> Promise<boolean>
 *   JM.alert('All done', 'success')               // -> Promise<void>
 *   JM.modal(el) / JM.modal('#myModal')           // show/hide custom modals
 *   JM.busy(btn, true)                            // spinner on a button
 *
 * Compat: window.toast(msg, ok) / window.showToast(msg, kind) map onto
 * JM.toast so legacy per-page calls keep working.
 *
 * The stylesheet (jm-components.css) is self-injected so the kit works on
 * pages that don't load partials/head.
 */
(function (window, document) {
  'use strict';

  var CSS_HREF = (function () {
    // Resolve base URL from any existing asset link (e.g. csrf.js/favicon)
    var link = document.querySelector('link[href*="assets/"], script[src*="assets/"]');
    if (link) {
      var src = link.getAttribute('href') || link.getAttribute('src') || '';
      var idx = src.indexOf('assets/');
      if (idx !== -1) return src.slice(0, idx) + 'assets/css/jm-components.css?v=1';
    }
    return 'assets/css/jm-components.css?v=1';
  })();

  if (!document.querySelector('link[href*="jm-components.css"]')) {
    var l = document.createElement('link');
    l.rel = 'stylesheet';
    l.href = CSS_HREF;
    document.head.appendChild(l);
  }

  var TYPES = { success: 1, error: 1, warning: 1, info: 1 };
  var ICONS = {
    success: 'mdi-check-circle-outline',
    error:   'mdi-alert-circle-outline',
    warning: 'mdi-alert-outline',
    info:    'mdi-information-outline',
    danger:  'mdi-delete-outline'
  };

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function normType(t) { return TYPES[t] ? t : 'info'; }

  /* ---------------------------------------------------------------- toast */
  var toastStack = null;
  function stack() {
    if (!toastStack || !toastStack.isConnected) {
      toastStack = document.createElement('div');
      toastStack.className = 'jm-toast-stack';
      toastStack.setAttribute('role', 'status');
      toastStack.setAttribute('aria-live', 'polite');
      document.body.appendChild(toastStack);
    }
    return toastStack;
  }

  function toast(msg, type, opts) {
    opts = opts || {};
    type = normType(type);
    var el = document.createElement('div');
    el.className = 'jm-toast jm-toast--' + type;
    el.innerHTML =
      '<i class="jm-toast__icon mdi ' + esc(ICONS[type]) + '" aria-hidden="true"></i>' +
      '<div class="jm-toast__body">' + esc(msg) + '</div>' +
      '<button type="button" class="jm-toast__close" aria-label="Dismiss">&times;</button>';

    var timeout = typeof opts.timeout === 'number' ? opts.timeout : (type === 'error' ? 6000 : 4000);
    var timer = null;
    function dismiss() {
      if (timer) clearTimeout(timer);
      el.classList.add('is-leaving');
      setTimeout(function () { el.remove(); }, 200);
    }
    el.querySelector('.jm-toast__close').addEventListener('click', dismiss);
    if (timeout > 0) {
      timer = setTimeout(dismiss, timeout);
      el.addEventListener('mouseenter', function () { clearTimeout(timer); });
      el.addEventListener('mouseleave', function () { timer = setTimeout(dismiss, 1500); });
    }
    stack().appendChild(el);
    return { dismiss: dismiss, el: el };
  }

  /* ---------------------------------------------------------------- modal */
  var openModals = [];

  function closeBackdrop(backdrop, result, resolve) {
    if (backdrop.classList.contains('is-leaving')) return;
    backdrop.classList.add('is-leaving');
    setTimeout(function () {
      backdrop.remove();
      openModals = openModals.filter(function (m) { return m.backdrop !== backdrop; });
      if (!openModals.length) document.body.style.overflow = '';
    }, 160);
    resolve(result);
  }

  /**
   * JM.confirm({title, text, confirmText, cancelText, danger, icon})
   * Resolves true on confirm, false on cancel/dismiss.
   */
  function confirmDialog(opts) {
    opts = opts || {};
    return new Promise(function (resolve) {
      var danger  = !!opts.danger;
      var iconKey = opts.icon || (danger ? 'danger' : 'warning');
      var title   = opts.title || 'Are you sure?';
      var text    = opts.text  || '';

      var backdrop = document.createElement('div');
      backdrop.className = 'jm-modal-backdrop';
      backdrop.innerHTML =
        '<div class="jm-modal" role="dialog" aria-modal="true" aria-labelledby="jmModalTitle">' +
          '<div class="jm-modal__head">' +
            '<i class="jm-modal__icon jm-modal__icon--' + esc(iconKey) + ' mdi ' + esc(ICONS[iconKey] || ICONS.info) + '" aria-hidden="true"></i>' +
            '<h3 class="jm-modal__title" id="jmModalTitle">' + esc(title) + '</h3>' +
            '<button type="button" class="jm-modal__close" data-jm-cancel aria-label="Close">&times;</button>' +
          '</div>' +
          (text ? '<div class="jm-modal__body">' + esc(text) + '</div>' : '') +
          '<div class="jm-modal__foot">' +
            '<button type="button" class="jm-btn jm-btn--ghost" data-jm-cancel>' + esc(opts.cancelText || 'Cancel') + '</button>' +
            '<button type="button" class="jm-btn ' + (danger ? 'jm-btn--danger' : 'jm-btn--primary') + '" data-jm-ok>' + esc(opts.confirmText || 'Confirm') + '</button>' +
          '</div>' +
        '</div>';

      var m = { backdrop: backdrop };
      openModals.push(m);
      document.body.style.overflow = 'hidden';
      document.body.appendChild(backdrop);

      function done(v) { closeBackdrop(backdrop, v, resolve); }
      backdrop.querySelector('[data-jm-ok]').addEventListener('click', function () { done(true); });
      backdrop.querySelectorAll('[data-jm-cancel]').forEach(function (b) {
        b.addEventListener('click', function () { done(false); });
      });
      backdrop.addEventListener('mousedown', function (e) {
        if (e.target === backdrop) done(false);
      });
      // focus the primary action; Esc handled globally below
      var okBtn = backdrop.querySelector('[data-jm-ok]');
      if (okBtn) okBtn.focus();
    });
  }

  function alertDialog(msg, type, title) {
    type = normType(type);
    return new Promise(function (resolve) {
      var backdrop = document.createElement('div');
      backdrop.className = 'jm-modal-backdrop';
      backdrop.innerHTML =
        '<div class="jm-modal" role="alertdialog" aria-modal="true">' +
          '<div class="jm-modal__head">' +
            '<i class="jm-modal__icon jm-modal__icon--' + esc(type) + ' mdi ' + esc(ICONS[type]) + '" aria-hidden="true"></i>' +
            '<h3 class="jm-modal__title">' + esc(title || 'JobMatch') + '</h3>' +
          '</div>' +
          '<div class="jm-modal__body">' + esc(msg) + '</div>' +
          '<div class="jm-modal__foot">' +
            '<button type="button" class="jm-btn jm-btn--primary" data-jm-ok>OK</button>' +
          '</div>' +
        '</div>';
      openModals.push({ backdrop: backdrop });
      document.body.style.overflow = 'hidden';
      document.body.appendChild(backdrop);
      var ok = backdrop.querySelector('[data-jm-ok]');
      ok.addEventListener('click', function () { closeBackdrop(backdrop, true, resolve); });
      backdrop.addEventListener('mousedown', function (e) {
        if (e.target === backdrop) closeBackdrop(backdrop, true, resolve);
      });
      ok.focus();
    });
  }

  /**
   * JM.modal('#id' | element, 'show'|'hide'|'toggle')
   * Wraps any element in .jm-modal-backdrop behaviour:
   * Esc + backdrop click close; [data-jm-close] inside closes.
   */
  function modal(target, action) {
    var el = typeof target === 'string' ? document.querySelector(target) : target;
    if (!el) return;
    var backdrop = el.__jmBackdrop;
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.className = 'jm-modal-backdrop';
      el.parentNode && el.parentNode.removeChild(el);
      el.classList.add('jm-modal');
      el.style.display = '';
      backdrop.appendChild(el);
      el.__jmBackdrop = backdrop;
      backdrop.addEventListener('mousedown', function (e) {
        if (e.target === backdrop) modal(el, 'hide');
      });
      el.querySelectorAll('[data-jm-close]').forEach(function (b) {
        b.addEventListener('click', function () { modal(el, 'hide'); });
      });
    }
    var show = action === 'toggle'
      ? !backdrop.isConnected
      : action !== 'hide';
    if (show) {
      openModals.push({ backdrop: backdrop });
      document.body.style.overflow = 'hidden';
      document.body.appendChild(backdrop);
    } else if (backdrop.isConnected) {
      backdrop.classList.add('is-leaving');
      setTimeout(function () {
        backdrop.remove();
        openModals = openModals.filter(function (x) { return x.backdrop !== backdrop; });
        if (!openModals.length) document.body.style.overflow = '';
      }, 160);
    }
    return el;
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && openModals.length) {
      var top = openModals[openModals.length - 1].backdrop;
      top.classList.add('is-leaving');
      setTimeout(function () { top.remove(); }, 160);
      openModals.pop();
      if (!openModals.length) document.body.style.overflow = '';
    }
  });

  /** JM.busy(btn, true) — spinner + disable; false restores. */
  function busy(btn, on) {
    if (!btn) return;
    btn.classList.toggle('is-busy', !!on);
    btn.disabled = !!on;
  }

  /* ------------------------------------------------------------ exports */
  window.JM = {
    toast: toast,
    confirm: confirmDialog,
    alert: alertDialog,
    modal: modal,
    busy: busy,
    esc: esc
  };

  /* Legacy compat — local page code already calls these: */
  window.toast = window.toast || function (msg, ok) {
    // toast(msg, true/false) or toast(msg, 'success'|'error'|...)
    var t = typeof ok === 'string' ? ok : (ok === false ? 'error' : 'success');
    return toast(msg, t);
  };
  window.showToast = window.showToast || function (msg, kind) {
    return toast(msg, typeof kind === 'string' ? kind : 'info');
  };
})(window, document);
