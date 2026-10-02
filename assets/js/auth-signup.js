(function () {
  var form = document.getElementById('signupForm');
  if (!form) return;

  var email = document.getElementById('signupEmail');
  var hint = document.getElementById('emailHint');
  var btn = document.getElementById('btnRegister');
  var tos = document.getElementById('tos');
  var emailCheckUrl = form.dataset.emailCheckUrl || '';
  var hasRecaptcha = !!document.querySelector('.g-recaptcha');
  var captchaInput = document.getElementById('captchaAnswer');
  var greOk = false;

  function humanCheckOk() {
    // math fallback field (legacy) > reCAPTCHA > nothing configured = pass
    if (captchaInput) return captchaInput.value.trim() !== '';
    return hasRecaptcha ? greOk : true;
  }

  function setInputState(state) {
    if (!email) return;
    email.classList.remove('input-invalid', 'input-valid');
    if (state === 'err') email.classList.add('input-invalid');
    if (state === 'ok') email.classList.add('input-valid');
  }

  function setHint(msg, kind) {
    if (!hint) return;
    hint.className =
      'alert alert-inline ' +
      (kind === 'err' ? 'alert-danger' : kind === 'ok' ? 'alert-success' : 'alert-info');
    if (!msg) {
      hint.hidden = true;
      return;
    }
    hint.hidden = false;
    hint.textContent = msg;
  }

  function isLikelyEmail(v) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  }

  function emailIsOk() {
    if (!email) return true;
    var v = (email.value || '').trim();
    return v === '' || email.classList.contains('input-valid');
  }

  function applyDisabled(disabled) {
    if (!btn) return;
    btn.disabled = disabled;
    btn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
    if (disabled) btn.setAttribute('tabindex', '-1');
    else btn.removeAttribute('tabindex');
  }

  function syncBtn() {
    var disabled = !(tos && tos.checked && emailIsOk() && humanCheckOk());
    applyDisabled(disabled);
  }
  window.__signupSyncBtn = syncBtn;

  async function checkEmail() {
    if (!email || !emailCheckUrl) return;
    var v = email.value.trim().toLowerCase();

    if (!v) {
      setHint('', 'info');
      setInputState('neutral');
      syncBtn();
      return;
    }
    if (!isLikelyEmail(v)) {
      setHint('Enter a valid email.', 'err');
      setInputState('err');
      syncBtn();
      return;
    }

    try {
      var res = await fetch(emailCheckUrl + '?email=' + encodeURIComponent(v), {
        credentials: 'same-origin',
      });
      var data = await res.json();
      if (!data.ok) {
        setHint(data.msg || 'Unable to verify email.', 'err');
        setInputState('err');
        syncBtn();
        return;
      }

      if (data.available) {
        setHint('Great, this email is available.', 'ok');
        setInputState('ok');
      } else {
        setHint('This email is already registered. Please log in or use another email.', 'err');
        setInputState('err');
      }
    } catch (e) {
      setHint('Unable to verify email right now.', 'err');
      setInputState('err');
    }
    syncBtn();
  }

  var t = null;
  if (email) {
    email.addEventListener('input', function () {
      clearTimeout(t);
      t = setTimeout(checkEmail, 350);
    });
    email.addEventListener('blur', checkEmail);
    if (email.value) checkEmail();
  }

  if (tos) tos.addEventListener('change', syncBtn);
  if (email) {
    email.addEventListener('input', syncBtn);
    email.addEventListener('blur', syncBtn);
  }
  if (captchaInput) captchaInput.addEventListener('input', syncBtn);

  window.recaptchaOk = function () {
    greOk = true;
    syncBtn();
  };
  window.recaptchaExpired = function () {
    greOk = false;
    syncBtn();
  };
  window.recaptchaError = function () {
    greOk = false;
    syncBtn();
  };

  if (btn) {
    btn.addEventListener('click', function (e) {
      if (btn.disabled) e.preventDefault();
    });
  }
  if (form) {
    form.addEventListener('submit', function (e) {
      if (btn && btn.disabled) e.preventDefault();
    });
  }

  syncBtn();
})();

(function () {
  var modal = document.getElementById('pmModal');
  var openEl = document.getElementById('openPrivacy');
  var agree = document.getElementById('pmAgree');
  var tos = document.getElementById('tos');
  var btn = document.getElementById('btnRegister');
  var email = document.getElementById('signupEmail');

  if (!modal || !openEl) return;

  function openModal(e) {
    if (e) e.preventDefault();
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    modal.classList.remove('show');
    document.body.style.overflow = '';
  }

  function emailIsOk() {
    if (!email) return true;
    var v = (email.value || '').trim();
    return v === '' || email.classList.contains('input-valid');
  }

  var syncBtn = window.__signupSyncBtn || function () {};

  openEl.addEventListener('click', openModal);

  modal.addEventListener('click', function (e) {
    if (e.target.dataset.close === '1' || e.target.id === 'pmBackdrop') closeModal();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
  });

  if (agree) {
    agree.addEventListener('click', function () {
      if (tos && !tos.checked) {
        tos.checked = true;
        tos.dispatchEvent(new Event('change'));
      }
      closeModal();
    });
  }

  if (tos) tos.addEventListener('change', syncBtn);
  if (email) {
    email.addEventListener('input', syncBtn);
    email.addEventListener('blur', syncBtn);
  }
  syncBtn();
})();
