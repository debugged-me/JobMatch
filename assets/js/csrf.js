/**
 * JobMatch CSRF auto-injector.
 *
 * Reads the current CSRF hash from the `jm_csrf` cookie (set server-side on
 * every response by the SecurityExtras hook) and transparently attaches it to:
 *   - every <form method="post"> on submit (incl. dynamically added forms)
 *   - fetch() POST/PUT/PATCH/DELETE bodies (FormData, URLSearchParams,
 *     urlencoded strings, or empty bodies on same-origin requests)
 *   - XMLHttpRequest.send() bodies of the same kinds
 *   - jQuery ajax requests (ajaxPrefilter)
 *
 * Requires csrf_regenerate = FALSE (token is stable for the session) or the
 * hook to re-issue the cookie on each response — both are configured.
 */
(function () {
    'use strict';
    if (window.__jmCsrfArmed) return;
    window.__jmCsrfArmed = true;

    var FIELD = 'csrf_test_name';

    function hash() {
        var m = document.cookie.match(/(?:^|;\s*)jm_csrf=([a-z0-9]+)/i);
        return m ? m[1] : '';
    }

    function sameOrigin(url) {
        try {
            var u = new URL(url, window.location.href);
            return u.origin === window.location.origin;
        } catch (e) {
            return true; // relative URL — treat as same-origin
        }
    }

    function injectForm(form) {
        if (!form || !form.method || String(form.method).toLowerCase() !== 'post') return;
        if (form.querySelector('input[type="hidden"][name="' + FIELD + '"]')) return;
        var h = hash();
        if (!h) return;
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = FIELD;
        input.value = h;
        form.appendChild(input);
    }

    // ---- forms (capture so dynamically-added forms are covered) ----
    document.addEventListener('submit', function (e) { injectForm(e.target); }, true);
    document.addEventListener('DOMContentLoaded', function () {
        var forms = document.querySelectorAll('form');
        for (var i = 0; i < forms.length; i++) injectForm(forms[i]);
    });

    // ---- fetch ----
    if (window.fetch) {
        var _fetch = window.fetch.bind(window);
        window.fetch = function (url, opts) {
            try {
                opts = opts || {};
                var method = (opts.method || 'GET').toUpperCase();
                if (method !== 'GET' && method !== 'HEAD' && sameOrigin(url)) {
                    var h = hash();
                    if (h) {
                        if (opts.body instanceof FormData) {
                            if (!opts.body.has(FIELD)) opts.body.append(FIELD, h);
                        } else if (opts.body instanceof URLSearchParams) {
                            if (!opts.body.has(FIELD)) opts.body.append(FIELD, h);
                        } else if (typeof opts.body === 'string') {
                            if (opts.body.indexOf(FIELD + '=') === -1) {
                                opts.body = opts.body + (opts.body ? '&' : '') + FIELD + '=' + encodeURIComponent(h);
                            }
                        } else if (opts.body === undefined || opts.body === null) {
                            opts.body = FIELD + '=' + encodeURIComponent(h);
                            opts.headers = opts.headers || {};
                            if (!opts.headers['Content-Type'] && !(opts.headers instanceof Headers)) {
                                opts.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
                            }
                        }
                    }
                }
            } catch (e) { /* never break the caller */ }
            return _fetch(url, opts);
        };
    }

    // ---- XMLHttpRequest ----
    var _open = XMLHttpRequest.prototype.open;
    var _send = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.open = function (method, url) {
        this.__jmMethod = (method || 'GET').toUpperCase();
        this.__jmUrl = url;
        return _open.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function (body) {
        try {
            if (this.__jmMethod && this.__jmMethod !== 'GET' && this.__jmMethod !== 'HEAD'
                && sameOrigin(this.__jmUrl || '')) {
                var h = hash();
                if (h) {
                    if (body instanceof FormData) {
                        if (!body.has(FIELD)) body.append(FIELD, h);
                    } else if (typeof body === 'string' && body.indexOf(FIELD + '=') === -1) {
                        body = body + (body ? '&' : '') + FIELD + '=' + encodeURIComponent(h);
                    } else if (body === undefined || body === null) {
                        body = FIELD + '=' + encodeURIComponent(h);
                    }
                }
            }
        } catch (e) { /* never break the caller */ }
        return _send.call(this, body);
    };

    // ---- navigator.sendBeacon (presence ping etc.) ----
    if (navigator.sendBeacon) {
        var _beacon = navigator.sendBeacon.bind(navigator);
        navigator.sendBeacon = function (url, data) {
            try {
                var h = hash();
                if (h && sameOrigin(url)) {
                    if (data instanceof FormData) {
                        if (!data.has(FIELD)) data.append(FIELD, h);
                    } else if (typeof data === 'string' && data.indexOf(FIELD + '=') === -1) {
                        data = data + (data ? '&' : '') + FIELD + '=' + encodeURIComponent(h);
                    } else if (data === undefined || data === null) {
                        data = FIELD + '=' + encodeURIComponent(h);
                    }
                }
            } catch (e) { /* noop */ }
            return _beacon(url, data);
        };
    }

    // ---- jQuery (when present) ----
    function armJQuery() {
        if (!window.jQuery || window.jQuery.__jmCsrf) return;
        window.jQuery.__jmCsrf = true;
        window.jQuery.ajaxPrefilter(function (opts) {
            try {
                var method = (opts.type || 'GET').toUpperCase();
                if (method === 'GET' || method === 'HEAD') return;
                var h = hash();
                if (!h) return;
                if (opts.data instanceof FormData) {
                    if (!opts.data.has(FIELD)) opts.data.append(FIELD, h);
                } else if (typeof opts.data === 'string') {
                    if (opts.data.indexOf(FIELD + '=') === -1) {
                        opts.data = opts.data + (opts.data ? '&' : '') + FIELD + '=' + encodeURIComponent(h);
                    }
                } else {
                    opts.data = opts.data || {};
                    opts.data[FIELD] = h;
                }
            } catch (e) { /* noop */ }
        });
    }
    if (window.jQuery) armJQuery();
    else {
        var _jq = window.jQuery;
        Object.defineProperty(window, 'jQuery', {
            configurable: true,
            get: function () { return _jq; },
            set: function (v) { _jq = v; armJQuery(); }
        });
    }
})();
