<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= html_escape($page_title ?? 'Messages') ?> • JobMatch DavOr</title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=1.0.7') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root {
      --bg: #f7f9fb;
      --card: #ffffff;
      --ink: #0f172a;
      --muted: #64748b;
      --line: #e6eaf0;
      --brand: #6366f1;
      --accent: #22c55e;
      --danger: #ef4444;
    }

    body {
      font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: var(--bg);
      color: var(--ink)
    }

    .content-wrapper {
      padding-top: 1rem
    }

    .app {
      max-width: 980px;
      margin: 0 auto;
      padding: 0 16px
    }

    .badge.online {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #86efac;
    }

    .badge.away {
      background: #fef9c3;
      color: #854d0e;
      border: 1px solid #fde68a;
    }

    .badge.offline {
      background: #e2e8f0;
      color: #1f2937;
      border: 1px solid #cbd5e1;
    }

    .chat-wrap {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: 14px;
      box-shadow: 0 8px 24px rgba(2, 6, 23, .08);
      display: flex;
      flex-direction: column;
      height: 82vh;
      min-height: 600px;
    }

    @media (max-width:992px) {
      .chat-wrap {
        height: calc(100vh - 140px);
        min-height: 560px;
      }
    }

    @media (max-width:576px) {
      .chat-wrap {
        height: calc(100vh - 110px);
        min-height: 520px;
      }
    }

    .chat-head {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border-bottom: 1px solid var(--line)
    }

    .chat-head img {
      width: 42px;
      height: 42px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid #e5e7eb
    }

    .chat-head .name {
      font-weight: 800
    }

    .chat-body {
      flex: 1;
      overflow: auto;
      padding: 14px;
      background: #f8fafc
    }

    .chat-send {
      display: flex;
      gap: 8px;
      padding: 10px;
      border-top: 1px solid var(--line);
      background: #fff
    }

    .chat-send textarea {
      flex: 1;
      resize: none;
      height: 44px;
      max-height: 160px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: .55rem .65rem
    }

    .chat-send .btn {
      white-space: nowrap
    }

    .project-banner {
      padding: 12px 14px;
      background: #f8fafc;
      border-bottom: 1px solid var(--line)
    }

    .pb-title {
      font-weight: 800
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: .28rem .6rem;
      border-radius: 9999px;
      border: 1px solid #e5e7eb;
      background: #fff;
      font-size: .82rem
    }

    .chip-muted {
      background: #f1f5f9
    }

    .chip-soft {
      background: #eef2ff;
      border-color: #c7d2fe
    }

    .chip i {
      font-size: 1rem
    }

    .proj-files {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 8px
    }

    .proj-thumb {
      width: 84px;
      height: 84px;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
      object-fit: cover
    }

    .desc small {
      color: #64748b
    }

    .desc .toggle {
      font-weight: 600;
      text-decoration: none
    }

    .action-row {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 10px
    }

    .action-row .btn {
      border-radius: 10px
    }

    .day-sep {
      position: relative;
      text-align: center;
      margin: 10px 0 14px
    }

    .day-sep span {
      display: inline-block;
      background: #e5e7eb;
      color: #475569;
      font-weight: 600;
      font-size: .75rem;
      padding: .15rem .5rem;
      border-radius: 9999px
    }

    .msg-row {
      display: flex;
      gap: 8px;
      margin: 6px 0;
      align-items: flex-end
    }

    .msg-row.me {
      justify-content: flex-end
    }

    .msg-avatar {
      width: 28px;
      height: 28px;
      border-radius: 9999px;
      object-fit: cover;
      border: 1px solid #e5e7eb
    }

    .bubble {
      max-width: 72%;
      padding: .55rem .75rem;
      border-radius: 14px;
      line-height: 1.38;
      background: #fff;
      border: 1px solid #e5e7eb
    }

    .msg-row.me .bubble {
      background: #e0e7ff;
      border-color: #c7d2fe
    }

    .bubble .time {
      display: block;
      margin-top: 4px;
      font-size: .72rem;
      color: #94a3b8
    }

    .bubble.error {
      background: #fee2e2;
      border-color: #fecaca;
      opacity: .9
    }

    @media (max-width:640px) {
      .bubble {
        max-width: 86%
      }
    }

    .meta-row {
      display: flex;
      gap: 8px;
      align-items: center;
      overflow-x: auto;
      white-space: nowrap;
      padding: 4px 0;
    }

    .meta-row .chip {
      flex: 0 0 auto
    }

    .meta-row::-webkit-scrollbar {
      height: 6px
    }

    .meta-row::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 10px
    }

    .bubble .time {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .msg-del {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 22px;
      height: 22px;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      text-decoration: none;
    }

    .msg-del:hover {
      background: #f1f5f9;
    }

    .msg-del i {
      font-size: 14px;
      line-height: 1;
      color: #ef4444;
    }

    .ext-note {
      margin-top: 8px;
      border: 1px dashed #c7d2fe;
      background: #f8faff;
      color: #384152;
      border-radius: 12px;
      padding: 8px 10px;
      font-size: .80rem;
      display: flex;
      gap: 8px;
      align-items: flex-start;
    }

    .ext-note i {
      color: #6366f1;
      font-size: 16px;
      line-height: 1.2
    }

    .ext-note a {
      font-weight: 600;
    }

    .back-btn {
      display: none;
      border: 1px solid #e5e7eb;
      background: #fff;
      width: 36px;
      height: 36px;
      border-radius: 10px;
      align-items: center;
      justify-content: center;
      line-height: 0;
      padding: 0;
      cursor: pointer;
    }

    .back-btn i {
      font-size: 18px;
      color: #334155
    }

    @media (max-width: 768px) {
      .chat-head {
        padding: 10px 12px;
      }

      .back-btn {
        display: inline-flex;
      }

      .chat-head img {
        width: 38px;
        height: 38px;
      }

      .chat-head .name {
        font-size: 15px;
      }
    }
  </style>
</head>

<body>
  <div class="container-scroller">
    <?php $this->load->view('includes_nav'); ?>
    <div class="container-fluid page-body-wrapper">
      <?php $this->load->view('includes_nav_top'); ?>
      <div class="main-panel">
        <div class="content-wrapper pb-0">
          <div class="app">

            <div class="mb-3">
              <div class="eyebrow"><strong>JobMatch-Thread</strong> - Start Conversation</div>
            </div>

            <div class="chat-wrap"
              data-thread="<?= (int)$thread->id ?>"
              data-me="<?= (int)$me ?>"
              data-other="<?= (int)$other_id ?>"
              data-role="<?= htmlspecialchars($this->session->userdata('role') ?? '', ENT_QUOTES, 'UTF-8') ?>">

              <div id="projBanner" class="project-banner d-none">
                <div id="projBannerInner"></div>
              </div>

              <div class="chat-head">
                <img src="<?= html_escape($other_avatar) ?>"
                  alt="Avatar"
                  onerror="this.src='<?= base_url('uploads/avatars/avatar.png') ?>'">
                <div>
                  <div class="name d-flex align-items-center gap-2">
                    <?= html_escape($other_name) ?>
                    <span id="presencePill" class="badge rounded-pill offline" style="font-size:.72rem">● Offline</span>
                  </div>
                  <div id="presenceNote" class="text-muted small">Last seen recently</div>
                </div>
              </div>

              <div id="chatBody" class="chat-body"></div>

              <form id="sendForm" class="chat-send" autocomplete="off">
                <textarea id="msgInput" name="body" placeholder="Type your message..."></textarea>
                <button class="btn btn-sm btn-primary" type="submit">
                  <i class="mdi mdi-send"></i> Send
                </button>
              </form>
            </div>

            <script>
              window.__TW_INVITE = <?= json_encode($invite ?? null) ?>;
              window.__TW_INVITE_STATUS = <?= json_encode($invite_status ?? null) ?>;
              window.__OTHER_AVATAR = <?= json_encode($other_avatar ?? '') ?>;
            </script>

          </div>
        </div>
        <?php $this->load->view('includes_footer'); ?>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/js/misc.js') ?>"></script>

  <script>
    (function() {
      const wrap = document.querySelector('.chat-wrap');
      const threadId = parseInt(wrap.dataset.thread, 10);
      const meId = parseInt(wrap.dataset.me, 10);
      const otherAv = window.__OTHER_AVATAR || '';
      const csrf = {
        name: '<?= $this->security->get_csrf_token_name(); ?>',
        hash: '<?= $this->security->get_csrf_hash(); ?>'
      };
      const bodyEl = document.getElementById('chatBody');
      const form = document.getElementById('sendForm');
      const input = document.getElementById('msgInput');

      /* ===== Helpers ===== */
      const ALLOW_HOSTS = [
        location.hostname,
        '<?= preg_replace('~^https?://~', '', rtrim(base_url(), '/')) ?>'.replace(/\/.*$/, '') // base host only
      ].filter(Boolean);

      const esc = (s) => String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');

      function dayKey(label) {
        if (!label) return '';
        const parts = String(label).split(' ');
        return parts.slice(0, 3).join(' ')
      }

      function addDayDivider(label) {
        const row = document.createElement('div');
        row.className = 'day-sep';
        row.innerHTML = '<span>' + esc(label) + '</span>';
        bodyEl.appendChild(row)
      }

      function scrollBottom() {
        bodyEl.scrollTop = bodyEl.scrollHeight + 1000
      }

      // Find URLs (http/https) in plain text
      function findUrls(text) {
        const re = /\bhttps?:\/\/[^\s<>"']+/gi;
        const urls = [];
        let m;
        while ((m = re.exec(text))) {
          urls.push(m[0]);
        }
        return urls;
      }

      function isExternal(urlStr) {
        try {
          const u = new URL(urlStr);
          const host = u.hostname.toLowerCase();
          return !ALLOW_HOSTS.some(h => h && host.endsWith(String(h).toLowerCase()));
        } catch (e) {
          return false;
        }
      }

      function linkify(text) {
        return esc(text).replace(/\bhttps?:\/\/[^\s<>"']+/gi, function(m) {
          const safe = esc(m);
          const ext = isExternal(m);
          return `<a href="${safe}" target="_blank" rel="nofollow noopener noreferrer"${ext?' data-ext="1"':''}>${safe}</a>`;
        }).replace(/\n/g, '<br>');
      }

      /* ===== Render one message (with external link disclaimer) ===== */
      let lastId = 0;
      let lastDay = '';

      function renderOne(m, opts) {
        opts = opts || {};
        const dkey = dayKey(m.created_at);
        if (dkey && dkey !== lastDay) {
          addDayDivider(dkey);
          lastDay = dkey;
        }

        const row = document.createElement('div');
        row.className = 'msg-row ' + (m.is_me ? 'me' : 'other');
        if (m.id && m.id > 0) row.dataset.id = m.id;
        if (opts.tempKey) row.dataset.temp = opts.tempKey;

        const avatar = !m.is_me ? `<img class="msg-avatar" src="${esc(otherAv)}" alt="">` : '';
        const delBtn = (m.is_me && m.id) ?
          `<a href="#" class="msg-del" title="Delete this message" aria-label="Delete" data-id="${m.id}">
           <i class="mdi mdi-trash-can-outline"></i>
         </a>` : '';

        const hasUrls = findUrls(m.body || '');
        const anyExt = hasUrls.some(isExternal);
        const txtHTML = linkify(m.body || '');

        let disclaimer = '';
        if (anyExt) {
          disclaimer = `
        <div class="ext-note" role="note" aria-live="polite">
          <div>
            This link opens <strong>outside JobMatch</strong>. Keep chats and payments on the platform for your safety. 
           
          </div>
        </div>`;
        }

        row.innerHTML = `
      ${avatar}
      <div class="bubble${opts.error ? ' error' : ''}">
        <div class="txt">${txtHTML}</div>
        ${disclaimer}
        <div class="time">
          ${esc(m.created_at)}
          ${delBtn}
        </div>
      </div>
    `;
        bodyEl.appendChild(row);
        return row;
      }

      function appendIfNew(m) {
        if (m.id && document.querySelector('.msg-row[data-id="' + m.id + '"]')) {
          lastId = Math.max(lastId, m.id);
          return;
        }
        renderOne(m);
        if (m.id) lastId = Math.max(lastId, m.id);
      }

      /* ===== Polling & send ===== */
      function fetchNew() {
        fetch('<?= site_url('messages/api/thread/') ?>' + threadId + '?after_id=' + lastId, {
            credentials: 'same-origin'
          })
          .then(r => r.json())
          .then(res => {
            if (!res.ok) return;
            const items = res.messages || [];
            if (items.length) {
              items.forEach(appendIfNew);
              scrollBottom();
            }
          }).catch(() => {});
      }
      let poller = null;

      function startPoll() {
        if (poller) clearInterval(poller);
        poller = setInterval(fetchNew, 5000);
      }
      fetchNew();
      startPoll();

      window.addEventListener('focus', function() {
        const fd = new FormData();
        fd.append(csrf.name, csrf.hash);
        fetch('<?= site_url('messages/api/read/') ?>' + threadId, {
          method: 'POST',
          credentials: 'same-origin',
          body: fd
        });
      });

      let isComposing = false;
      input.addEventListener('compositionstart', () => {
        isComposing = true
      });
      input.addEventListener('compositionend', () => {
        isComposing = false
      });
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey && !isComposing) {
          e.preventDefault();
          if (input.value.trim() === '') return;
          if (form.requestSubmit) form.requestSubmit();
          else form.dispatchEvent(new Event('submit', {
            bubbles: true,
            cancelable: true
          }));
        }
      });

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        const tempKey = 't' + Date.now();
        const tmp = {
          id: 0,
          is_me: true,
          body: text,
          created_at: (new Date()).toLocaleString()
        };
        const tmpEl = renderOne(tmp, {
          tempKey
        });
        scrollBottom();
        input.value = '';

        const fd = new FormData();
        fd.append('thread_id', threadId);
        fd.append('body', text);
        fd.append(csrf.name, csrf.hash);

        fetch('<?= site_url('messages/api/send') ?>', {
            method: 'POST',
            credentials: 'same-origin',
            body: fd
          })
          .then(r => r.json())
          .then(res => {
            if (!res.ok) {
              tmpEl.querySelector('.bubble').classList.add('error');
              if (window.showToast) window.showToast(res.message || 'Send failed', 'error');
              return;
            }
            if (tmpEl && tmpEl.parentNode) tmpEl.parentNode.removeChild(tmpEl);
            appendIfNew({
              id: res.id,
              is_me: true,
              body: text,
              created_at: res.created_at
            });
            scrollBottom();
          })
          .catch(() => {
            tmpEl.querySelector('.bubble').classList.add('error');
            if (window.showToast) window.showToast('Send failed', 'error');
          });
      });

      bodyEl.addEventListener('click', function(e) {
        const a = e.target.closest('.msg-del');
        if (!a) return;
        e.preventDefault();
        const id = parseInt(a.dataset.id, 10);
        if (!id) return;
        if (!confirm('Delete this message for you?')) return;

        const fd = new FormData();
        fd.append('id', id);
        fd.append(csrf.name, csrf.hash);
        fetch('<?= site_url('messages/api_delete') ?>', {
            method: 'POST',
            credentials: 'same-origin',
            body: fd
          })
          .then(r => r.json())
          .then(res => {
            if (!res.ok) {
              window.showToast && window.showToast(res.message || 'Delete failed', 'error');
              return;
            }
            const row = bodyEl.querySelector(`.msg-row[data-id="${id}"]`);
            if (row) row.remove();
          })
          .catch(() => window.showToast && window.showToast('Delete failed', 'error'));
      });
    })();
  </script>
  <script>
    /* ===== Project invite banner (with working Accept/Decline) ===== */
    (function() {
      const wrap = document.querySelector('.chat-wrap');
      const role = (wrap?.dataset.role || '').toLowerCase();
      const tid = parseInt(wrap?.dataset.thread || '0', 10);

      const INV = window.__TW_INVITE || null;
      const STAT = window.__TW_INVITE_STATUS || null;
      if (!INV && !STAT) return;

      const banner = document.getElementById('projBanner');
      const bannerIn = document.getElementById('projBannerInner');

      const esc = (s) => String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
      const money = (n) => (n === null || n === undefined || n === '') ? '' : '₱' + Number(n).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      const has = (v) => v !== null && v !== undefined && String(v) !== '';

      function moneyChips(inv) {
        const out = [];
        if (has(inv.rate)) {
          out.push(`<span class="chip chip-soft"><i class="mdi mdi-cash-multiple"></i> ${money(inv.rate)}</span>`);
          if (inv.rate_unit) out.push(`<span class="chip chip-muted"><i class="mdi mdi-timer-sand-complete"></i> / ${esc(inv.rate_unit)}</span>`);
        } else if (has(inv.budget_min) || has(inv.budget_max)) {
          const min = has(inv.budget_min) ? money(inv.budget_min) : '—';
          const max = has(inv.budget_max) ? money(inv.budget_max) : '—';
          out.push(`<span class="chip chip-soft"><i class="mdi mdi-cash-multiple"></i> ${min} – ${max}</span>`);
          if (inv.rate_unit) out.push(`<span class="chip chip-muted"><i class="mdi mdi-timer-sand-complete"></i> / ${esc(inv.rate_unit)}</span>`);
        } else if (inv.rate_unit) {
          out.push(`<span class="chip chip-muted"><i class="mdi mdi-timer-sand-complete"></i> / ${esc(inv.rate_unit)}</span>`);
        }
        return out;
      }

      function metaChips(inv) {
        const c = [];
        if (inv.category) c.push(`<span class="chip chip-muted"><i class="mdi mdi-tag-outline"></i> ${esc(inv.category)}</span>`);
        if (inv.loc) c.push(`<span class="chip chip-muted"><i class="mdi mdi-map-marker-outline"></i> ${esc(inv.loc)}</span>`);
        if (inv.posted_ago || inv.posted_at)
          c.push(`<span class="chip chip-muted"><i class="mdi mdi-calendar-blank"></i> ${esc(inv.posted_ago||inv.posted_at)}</span>`);
        if (inv.visibility) c.push(`<span class="chip chip-muted"><i class="mdi mdi-eye-outline"></i> ${esc(inv.visibility)}</span>`);
        return c;
      }

      function actionButtons(inv, isFinal) {
        const filesBtn = (Array.isArray(inv.files) && inv.files.length) ?
          `<a href="${esc(inv.files[0].url)}" target="_blank" rel="noopener" class="btn btn-sm btn-light">
           <i class="mdi mdi-paperclip"></i> View Files
         </a>` : '';
        if (isFinal) return `<div class="action-row">${filesBtn}</div>`;

        const haveUnit = (inv.rate_unit && String(inv.rate_unit).length > 0);
        const unitSel = haveUnit ?
          `<div class="input-group-text bg-light border">/ ${esc((inv.rate_unit||'project').toLowerCase())}</div>` :
          `<select id="invUnit" class="form-select form-select-sm" style="max-width:140px">
           <option value="project" ${inv.rate_unit==='project'?'selected':''}>/ project</option>
           <option value="day" ${inv.rate_unit==='day'?'selected':''}>/ day</option>
           <option value="hour" ${inv.rate_unit==='hour'?'selected':''}>/ hour</option>
         </select>`;

        return `
      <div class="mt-2">
        <div class="small text-muted mb-1">Accept as-is or propose a rate:</div>
        <div class="input-group input-group-sm" style="max-width:320px">
          <span class="input-group-text">₱</span>
          <input id="invRate" type="number" step="0.01" min="0" class="form-control"
                 placeholder="${has(inv.rate)?esc(inv.rate):'Enter rate'}" value="${has(inv.rate)?esc(inv.rate):''}">
          ${unitSel}
        </div>
        <div class="action-row">
          <button id="btnAccept" type="button" class="btn btn-sm btn-primary">
            <i class="mdi mdi-check"></i> Accept
          </button>
          <button id="btnDecline" type="button" class="btn btn-sm btn-outline-danger">
            <i class="mdi mdi-close"></i> Decline
          </button>
          ${filesBtn}
        </div>
      </div>`;
      }

      function render() {
        let html = '';
        if (INV) {
          const chips = [...moneyChips(INV), ...metaChips(INV)];
          html += `<div class="pb-title">Project: ${esc(INV.title||'—')}</div>
               <div class="meta-row">${chips.join('')}</div>`;
        }

        if (STAT && STAT.state) {
          const s = ('' + STAT.state).toLowerCase();
          const ok = s === 'accepted';
          html += `<div class="mt-2">
                 <span class="chip ${ok?'chip-soft':''}"
                       style="${ok?'border-color:#bbf7d0;background:#ecfdf5;color:#065f46'
                                  :'border-color:#fecaca;background:#fee2e2;color:#b91c1c'}">
                   <i class="mdi ${ok?'mdi-check-circle-outline':'mdi-close-octagon-outline'}"></i>
                   ${ok?'Accepted':'Declined'}
                 </span>
                 ${STAT.rate_agreed!==null?`<span class="chip chip-muted"><i class="mdi mdi-cash"></i> ${money(STAT.rate_agreed)}</span>`:''}
               </div>
               <div class="mt-2">${actionButtons(INV||{}, true)}</div>`;
        } else if (INV && INV.can_act && role === 'worker') {
          html += actionButtons(INV, false);
        } else if (INV) {
          html += actionButtons(INV, true);
        }

        bannerIn.innerHTML = html;
        banner.classList.remove('d-none');

        // === Wire up buttons (this was missing) ===
        if (!STAT && INV && INV.can_act && role === 'worker') {
          const acceptBtn = document.getElementById('btnAccept');
          const declineBtn = document.getElementById('btnDecline');

          function setLoading(on) {
            [acceptBtn, declineBtn].forEach(b => {
              if (!b) return;
              b.disabled = on;
              b.classList.toggle('disabled', on);
            });
          }

          function postInviteAction(action) {
            const fd = new FormData();
            fd.append('thread_id', tid);
            fd.append('project_id', INV.pid);
            fd.append('action', action);
            if (action === 'accept') fd.append('confirm', '1');
            const rateEl = document.getElementById('invRate');
            const unitEl = document.getElementById('invUnit');
            if (rateEl && rateEl.value !== '') fd.append('rate', rateEl.value);
            if (unitEl && unitEl.value) fd.append('rate_unit', unitEl.value);
            fd.append('<?= $this->security->get_csrf_token_name(); ?>', '<?= $this->security->get_csrf_hash(); ?>');
            setLoading(true);
            return fetch('<?= site_url('messages/api_invite_action') ?>', {
              method: 'POST',
              credentials: 'same-origin',
              body: fd
            }).then(r => r.json()).finally(() => setLoading(false));
          }

          acceptBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            if (!confirm('Accept this request?')) return;
            postInviteAction('accept').then(res => {
              if (!res || !res.ok) {
                window.showToast && window.showToast(res?.message || 'Failed', 'error');
                return;
              }
              window.showToast && window.showToast('Invitation accepted', 'success');
              location.reload();
            });
          });

          declineBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            if (!confirm('Decline this request?')) return;
            postInviteAction('decline').then(res => {
              if (!res || !res.ok) {
                window.showToast && window.showToast(res?.message || 'Failed', 'error');
                return;
              }
              window.showToast && window.showToast('Invitation declined', 'success');
              location.reload();
            });
          });
        }
      }

      render();
    })();
  </script>
  <script>
    (function() {
      const wrap = document.querySelector('.chat-wrap');
      if (!wrap) return;

      const pill = document.getElementById('presencePill');
      const note = document.getElementById('presenceNote');

      const csrf = {
        name: '<?= $this->security->get_csrf_token_name(); ?>',
        hash: '<?= $this->security->get_csrf_hash(); ?>'
      };

      function setUI(state, lastSeenHuman) {
        if (!pill || !note) return;
        pill.classList.remove('online', 'away', 'offline');
        pill.classList.add(state || 'offline');
        pill.textContent = (state === 'online' ? '● Online' : state === 'away' ? '● Away' : '● Offline');
        if (state === 'online') {
          note.textContent = 'Active now';
        } else if (state === 'away') {
          note.textContent = 'Last seen ' + (lastSeenHuman || 'a moment ago');
        } else {
          note.textContent = lastSeenHuman ? ('Last seen ' + lastSeenHuman) : 'Last seen recently';
        }
      }

      function ping(status) {
        const fd = new FormData();
        fd.append(csrf.name, csrf.hash);
        if (status) fd.append('status', status);
        else fd.append('status', document.hidden ? 'away' : 'online');
        return fetch('<?= site_url('messages/api_presence_ping') ?>', {
          method: 'POST',
          credentials: 'same-origin',
          body: fd,
          cache: 'no-store'
        }).catch(() => {});
      }

      function fetchOther() {
        const url = '<?= site_url('messages/api_presence_get') ?>/' + wrap.dataset.other + '?t=' + Date.now();
        fetch(url, {
            credentials: 'same-origin',
            cache: 'no-store'
          })
          .then(r => r.json())
          .then(res => {
            if (res && res.ok) setUI(res.status || 'offline', res.last_seen_human || null);
          })
          .catch(() => {});
      }

      ping();
      fetchOther();
      const PING_MS = 10000;
      const POLL_MS = 8000;
      const pingTimer = setInterval(() => ping(), PING_MS);
      const pollTimer = setInterval(fetchOther, POLL_MS);
      document.addEventListener('visibilitychange', () => ping(document.hidden ? 'away' : 'online'));

      function beaconOffline() {
        try {
          const url = '<?= site_url('messages/presence_beacon') ?>?ts=' + Date.now();
          if (navigator.sendBeacon) {
            navigator.sendBeacon(url);
          } else {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', url, false);
            try {
              xhr.send(null);
            } catch (_) {}
          }
        } catch (_) {}
      }
      window.addEventListener('pagehide', beaconOffline);
      window.addEventListener('beforeunload', beaconOffline);
      document.addEventListener('click', (e) => {
        const a = e.target.closest('a[href*="/logout"]');
        if (a) beaconOffline();
      }, true);

    })();
  </script>

</body>

</html>