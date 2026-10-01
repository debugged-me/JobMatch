(function () {
  var modal = document.getElementById('twx-about-modal');
  if (!modal) return;
  var triggers = Array.prototype.slice.call(document.querySelectorAll('[data-twx-open="about"]'));
  if (!triggers.length) return;

  var dialog = modal.querySelector('.twx-modal__dialog');
  var closers = modal.querySelectorAll('[data-twx-close]');
  var lastFocus = null;
  var scrollBarComp = 0;

  function lockScroll() {
    var scrollBar = window.innerWidth - document.documentElement.clientWidth;
    scrollBarComp = scrollBar;
    document.body.style.overflow = 'hidden';
    if (scrollBar > 0) {
      document.body.style.paddingRight = scrollBar + 'px';
    }
  }

  function unlockScroll() {
    document.body.style.overflow = '';
    if (scrollBarComp > 0) {
      document.body.style.paddingRight = '';
    }
  }

  function openModal(e) {
    if (e) e.preventDefault();
    lastFocus = document.activeElement;
    modal.classList.add('twx-open');
    modal.setAttribute('aria-hidden', 'false');
    lockScroll();
    if (dialog) dialog.focus();
  }

  function closeModal() {
    modal.classList.remove('twx-open');
    modal.setAttribute('aria-hidden', 'true');
    unlockScroll();
    if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
  }

  triggers.forEach(function (el) {
    el.addEventListener('click', openModal);
  });
  closers.forEach(function (el) {
    el.addEventListener('click', closeModal);
  });
  modal.addEventListener('click', function (e) {
    if (e.target.matches('[data-twx-close], .twx-modal__overlay')) closeModal();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('twx-open')) closeModal();
  });
})();

(function () {
  var cfg = document.getElementById('jmFooterConfig');
  if (!cfg) return;
  if (cfg.dataset.loggedIn !== '1') return;

  var csrfName = cfg.dataset.csrfName || '';
  var csrfHash = cfg.dataset.csrfHash || '';
  var pingUrl = cfg.dataset.presencePingUrl || '';
  var beaconUrl = cfg.dataset.presenceBeaconUrl || '';

  function ping(status) {
    if (!pingUrl || !csrfName || !csrfHash) return;
    try {
      var fd = new FormData();
      fd.append(csrfName, csrfHash);
      fd.append('status', status || (document.hidden ? 'away' : 'online'));
      fetch(pingUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: fd,
        cache: 'no-store',
      }).catch(function () {});
    } catch (e) {}
  }

  function beaconOffline() {
    if (!beaconUrl) return;
    try {
      var url = beaconUrl + '?ts=' + Date.now();
      if (!navigator.sendBeacon || !navigator.sendBeacon(url)) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, false);
        try {
          xhr.send(null);
        } catch (e) {}
      }
    } catch (e) {}
  }

  ping();
  setInterval(ping, 30000);
  document.addEventListener('visibilitychange', function () {
    ping(document.hidden ? 'away' : 'online');
  });
  window.addEventListener('pagehide', beaconOffline);
  window.addEventListener('beforeunload', beaconOffline);
  document.addEventListener(
    'click',
    function (e) {
      var a = e.target.closest('a[href*="/logout"]');
      if (a) beaconOffline();
    },
    true
  );
})();
