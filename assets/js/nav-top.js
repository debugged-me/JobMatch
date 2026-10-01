(function () {
  var nav = document.getElementById('jmNavTop');
  if (!nav) return;

  var msgCountUrl = nav.dataset.msgCountUrl || '';
  var msgFeedUrl = nav.dataset.msgFeedUrl || '';
  var notifFeedUrl = nav.dataset.notifFeedUrl || '';
  var notifCountUrl = nav.dataset.notifCountUrl || '';
  var notifMarkUrl = nav.dataset.notifMarkUrl || '';
  var defaultAvatar = nav.dataset.defaultAvatar || '/uploads/avatars/avatar.png';
  var csrfName = nav.dataset.csrfName || '';
  var csrfHash = nav.dataset.csrfHash || '';
  var userId = parseInt(nav.dataset.userId || '0', 10) || 0;

  function csrfPair() {
    if (!csrfName || !csrfHash) return null;
    return { name: csrfName, hash: csrfHash };
  }

  function escapeHtml(s) {
    return String(s || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  window.showToast = function (msg, kind) {
    if (!msg) return;
    var wrap = document.getElementById('twToast');
    if (!wrap) {
      wrap = document.createElement('div');
      wrap.id = 'twToast';
      wrap.style.position = 'fixed';
      wrap.style.top = '16px';
      wrap.style.right = '16px';
      wrap.style.zIndex = '99999';
      document.body.appendChild(wrap);
    }
    var card = document.createElement('div');
    card.className = 'alert ' + (kind === 'error' ? 'alert-danger' : 'alert-success') + ' shadow';
    card.style.marginTop = '8px';
    card.style.transition = 'opacity .35s ease';
    card.textContent = msg;
    wrap.appendChild(card);
    setTimeout(function () {
      card.style.opacity = '0';
      setTimeout(function () {
        card.remove();
      }, 360);
    }, 2200);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  function boot() {
    try {
      var s = sessionStorage.getItem('twToastMsg');
      if (s) {
        window.showToast(s);
        sessionStorage.removeItem('twToastMsg');
      }
    } catch (e) {}

    try {
      var p = new URLSearchParams(window.location.search);
      if (p.get('sent') === '1') {
        window.showToast('Hire request sent');
      }
    } catch (e) {}

    initMessages();
    initNotifications();
    bindNotificationClick();
  }

  function initMessages() {
    if (!msgCountUrl || !msgFeedUrl) return;
    var msgBadge = document.getElementById('msgBadge');
    var msgList = document.getElementById('msgList');
    var msgToggle = document.getElementById('messageDropdown');

    var lastMsgCount = (function () {
      if (!msgBadge || msgBadge.classList.contains('d-none')) return 0;
      var n = parseInt(msgBadge.textContent, 10);
      return isNaN(n) ? 0 : n;
    })();

    var msgInFlight = false;

    function setMsgBadge(n) {
      if (!msgBadge) return;
      if (n > 0) {
        msgBadge.textContent = n;
        msgBadge.classList.remove('d-none');
      } else {
        msgBadge.textContent = '0';
        msgBadge.classList.add('d-none');
      }
    }

    function buildMsgList(items) {
      if (!msgList) return;
      if (!items || !items.length) {
        msgList.innerHTML = '<div class="px-3 pb-3 text-muted small">No conversations yet.</div>';
        return;
      }
      var html = '';
      items.forEach(function (t) {
        var av = t.avatar && t.avatar.trim() ? t.avatar : defaultAvatar;
        html +=
          '<a href="' +
          t.link +
          '" class="dropdown-item preview-item d-flex align-items-start msg-item" data-thread="' +
          t.thread_id +
          '">' +
          '<img src="' +
          escapeHtml(av) +
          '" class="me-2 rounded-circle nav-avatar" onerror="this.onerror=null;this.src=\'' +
          defaultAvatar +
          '\';">' +
          '<div class="preview-item-content flex-grow">' +
          '<p class="preview-subject mb-0">' +
          escapeHtml(t.name) +
          '</p>' +
          '<p class="small text-muted mb-0">' +
          escapeHtml(t.snippet) +
          '</p>' +
          '</div>' +
          (t.unread ? '<span class="badge bg-primary ms-2">' + t.unread + '</span>' : '') +
          '</a>' +
          '<div class="dropdown-divider"></div>';
      });
      msgList.innerHTML = html;
    }

    function refreshMessages() {
      if (msgInFlight) return;
      msgInFlight = true;
      fetch(msgCountUrl, { credentials: 'same-origin' })
        .then(function (r) {
          return r.json();
        })
        .then(function (res) {
          if (!res || !res.ok) return;
          var n = parseInt(res.unread, 10) || 0;
          var delta = n - lastMsgCount;
          if (delta !== 0) {
            setMsgBadge(n);
            return fetch(msgFeedUrl + '?limit=8', { credentials: 'same-origin' })
              .then(function (r) {
                return r.json();
              })
              .then(function (f) {
                if (f && f.ok) buildMsgList(f.items || []);
                if (delta > 0 && !(msgToggle && msgToggle.parentElement && msgToggle.parentElement.classList.contains('show'))) {
                  var m = delta === 1 ? 'New message' : delta + ' new messages';
                  window.showToast && window.showToast(m);
                }
              });
          }
        })
        .catch(function () {})
        .finally(function () {
          msgInFlight = false;
        });
    }

    if (msgToggle) {
      msgToggle.addEventListener('show.bs.dropdown', function () {
        fetch(msgFeedUrl + '?limit=8', { credentials: 'same-origin' })
          .then(function (r) {
            return r.json();
          })
          .then(function (f) {
            if (f && f.ok) buildMsgList(f.items || []);
          });
      });
    }

    setInterval(refreshMessages, 20000);
    window.addEventListener('focus', refreshMessages);
  }

  function initNotifications() {
    if (!notifFeedUrl || !notifCountUrl) return;
    var badge = document.getElementById('notifBadge');
    var list = document.getElementById('notifList');
    var dropdown = document.getElementById('notificationDropdown');

    var lastUnread = (function () {
      if (!badge || badge.classList.contains('d-none')) return 0;
      var n = parseInt(badge.textContent, 10);
      return isNaN(n) ? 0 : n;
    })();

    var lastSeenId = 0;
    if (userId) {
      lastSeenId = parseInt(localStorage.getItem('twNotifLastId:' + userId) || '0', 10);
    }

    if (!lastSeenId && list) {
      var first = list.querySelector('.notif-item');
      if (first) {
        var id = parseInt(first.getAttribute('data-id') || '0', 10);
        if (id && userId) {
          lastSeenId = id;
          localStorage.setItem('twNotifLastId:' + userId, String(id));
        }
      }
    }

    function setBadge(n) {
      if (!badge) return;
      if (n > 0) {
        badge.textContent = n;
        badge.classList.remove('d-none');
      } else {
        badge.textContent = '0';
        badge.classList.add('d-none');
      }
    }

    function rebuildList(items) {
      if (!list) return;
      if (!items || !items.length) {
        list.innerHTML = '<div class="px-3 pb-3 text-muted small">No notifications yet.</div>';
        return;
      }
      var html = '';
      items.forEach(function (n) {
        var av = n.avatar && n.avatar.trim() ? n.avatar : defaultAvatar;
        html +=
          '<a href="' +
          (n.link ? n.link : 'javascript:void(0)') +
          '" class="dropdown-item preview-item d-flex align-items-start notif-item" data-id="' +
          n.id +
          '">' +
          '<img src="' +
          escapeHtml(av) +
          '" class="me-2 rounded-circle nav-avatar" onerror="this.onerror=null;this.src=\'' +
          defaultAvatar +
          '\';">' +
          '<div class="preview-item-content flex-grow">' +
          '<p class="preview-subject mb-0 ' +
          (n.is_read ? '' : 'fw-bold') +
          '">' +
          escapeHtml(n.title) +
          '</p>' +
          (n.body ? '<p class="small text-muted mb-0">' + escapeHtml(n.body) + '</p>' : '') +
          '<p class="small text-muted mb-0">' +
          escapeHtml(n.created) +
          '</p>' +
          '</div>' +
          '</a>' +
          '<div class="dropdown-divider"></div>';
      });
      list.innerHTML = html;
    }

    function fetchFeedAndMaybeToast(unreadDelta) {
      return fetch(notifFeedUrl + '?limit=10', { credentials: 'same-origin' })
        .then(function (r) {
          return r.json();
        })
        .then(function (res) {
          if (!res || !res.ok) return;
          var items = res.items || [];
          rebuildList(items);

          var maxId = items.reduce(function (m, x) {
            return Math.max(m, parseInt(x.id || 0, 10) || 0);
          }, 0);
          if (maxId && maxId > lastSeenId && userId) {
            localStorage.setItem('twNotifLastId:' + userId, String(maxId));
            lastSeenId = maxId;

            var ddOpen = dropdown && dropdown.parentElement && dropdown.parentElement.classList.contains('show');
            if (!ddOpen && unreadDelta > 0) {
              var msg = unreadDelta === 1 ? 'New notification' : unreadDelta + ' new notifications';
              window.showToast && window.showToast(msg);
            }
          }
        })
        .catch(function () {});
    }

    var inFlight = false;
    function tick() {
      if (inFlight) return;
      inFlight = true;
      fetch(notifCountUrl, { credentials: 'same-origin' })
        .then(function (r) {
          return r.json();
        })
        .then(function (res) {
          if (!res || !res.ok) return;
          var n = parseInt(res.unread, 10) || 0;
          var delta = n - lastUnread;
          if (delta !== 0) {
            setBadge(n);
            fetchFeedAndMaybeToast(delta);
            lastUnread = n;
          }
        })
        .catch(function () {})
        .finally(function () {
          inFlight = false;
        });
    }

    if (dropdown) {
      dropdown.addEventListener('show.bs.dropdown', function () {
        fetch(notifFeedUrl, { credentials: 'same-origin' })
          .then(function (r) {
            return r.json();
          })
          .then(function (res) {
            if (!res || !res.ok) return;
            rebuildList(res.items || []);
            return fetch(notifCountUrl, { credentials: 'same-origin' });
          })
          .then(function (r) {
            return r ? r.json() : null;
          })
          .then(function (cnt) {
            if (!cnt || !cnt.ok || !badge) return;
            if (cnt.unread > 0) {
              badge.textContent = cnt.unread;
              badge.classList.remove('d-none');
            } else {
              badge.classList.add('d-none');
            }
          })
          .catch(function () {});
      });
    }

    setInterval(tick, 15000);
    window.addEventListener('focus', tick);
    tick();
  }

  function bindNotificationClick() {
    if (!notifMarkUrl) return;
    document.addEventListener('click', function (e) {
      var item = e.target.closest('.notif-item');
      if (!item) return;

      e.preventDefault();
      var id = item.getAttribute('data-id');
      var href = item.getAttribute('href') || '#';

      var subj = item.querySelector('.preview-subject');
      var wasUnread = subj && subj.classList.contains('fw-bold');
      if (subj) subj.classList.remove('fw-bold');

      var badge = document.getElementById('notifBadge');
      if (wasUnread && badge && !badge.classList.contains('d-none')) {
        var n = parseInt(badge.textContent, 10);
        n = isNaN(n) ? 0 : n;
        if (n > 0) {
          n -= 1;
          badge.textContent = n > 0 ? String(n) : '0';
          if (n <= 0) badge.classList.add('d-none');
        }
      }

      var fd = new FormData();
      var csrf = csrfPair();
      if (csrf) {
        fd.append(csrf.name, csrf.hash);
      }

      fetch(notifMarkUrl + '/' + id, {
        method: 'POST',
        body: fd,
        credentials: 'same-origin',
        keepalive: true,
      })
        .catch(function () {})
        .finally(function () {
          setTimeout(function () {
            window.location.href = href;
          }, 350);
        });
    });
  }
})();
