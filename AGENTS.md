# AGENTS.md — JobMatch DavOr

CodeIgniter 3 app (PHP 8.2, MySQLi, XAMPP layout). Webroot = repo root
(`index.php` at top level; `application/` holds the app code and is
denied by `.htaccess`).

## Verify / lint

```sh
# Lint every PHP file (fast syntax gate — run before committing)
find application -name '*.php' -print0 | xargs -0 -n1 php -l > /dev/null

# Rebuild Tailwind CSS bundles when view markup changes (see build/tw/)
cd build/tw && npx tailwindcss -c tailwind.full.config.js  -i input-full.css  -o ../../assets/css/tw-build.css --minify
cd build/tw && npx tailwindcss -c tailwind.utils.config.js -i input-utils.css -o ../../assets/css/tw-utils.css --minify

# DB migrations (idempotent — safe to re-run)
mysql -u <user> -p <db> < database/migrations/001_schema_consolidation.sql
```

## Deployment checklist

1. **Environment** — set env vars (none of these may be committed):
   - `JM_BASE_URL` — canonical https URL (fixes host-header poisoning; without
     it `base_url` falls back to `HTTP_HOST`)
   - `JM_ENCRYPTION_KEY` — 32+ random chars (`openssl rand -hex 32`)
   - `JM_DB_HOST` `JM_DB_USER` `JM_DB_PASS` `JM_DB_NAME`
   - `JM_SMTP_HOST` `JM_SMTP_USER` `JM_SMTP_PASS` `JM_SMTP_PORT` `JM_SMTP_CRYPTO`
   - `JM_RECAPTCHA_SITE_KEY` `JM_RECAPTCHA_SECRET`
   - `ENVIRONMENT=production` — turns off `db_debug`/display_errors;
     `cookie_secure` is auto-enabled on HTTPS
2. **Webroot hygiene** — `.htaccess` denies `application/ system/ vendor/
   writable/ database/ build/` + dotfiles + `.git` + sql/zip/log/etc.
   Confirm `mod_rewrite` is enabled, or the `<IfModule !mod_rewrite.c>`
   fallback `<FilesMatch>` rules apply. `robots.txt` disallows admin areas;
   known-bad bot UAs and scanner paths are 403'd.
3. **Backups** — keep `database_backups/` OUTSIDE the webroot (it is
   gitignored). Never place `.sql` dumps in the repo root.
4. **Uploads** — `uploads/.htaccess` disables PHP execution + indexes.
   On Apache ensure `AllowOverride All` so it applies.
5. **Migrations** — run `database/migrations/001_schema_consolidation.sql`
   (idempotent) once per environment. Do not add runtime `ALTER TABLE`
   calls — add a numbered migration instead.
6. **Sessions** — `sess_save_path = writable/sessions` (denied via .htaccess;
   on a hardened host point it outside the webroot).

## Auth model

- `application/core/MY_Controller.php` is the base controller —
  `require_login()`, `require_role([...])`, `require_post()`, plus
  `*_json` variants for AJAX. All new controllers must extend it.
- Roles are normalized (`_`/`-` → space, lowercase) before comparison:
  `school_admin` in the DB is `'school admin'` in guards.
- Login hardening: `failed_attempts`/`locked_until` (5 tries → 15 min),
  session regeneration on login, uniform "invalid credentials" errors,
  no staff self-approval.
- CSRF is on globally (`csrf_regenerate=FALSE` for parallel AJAX);
  `assets/js/csrf.js` auto-injects the token into forms/fetch/XHR/jQuery —
  any view without `includes_footer` must include it manually.

## Design system

- `assets/css/tokens.css` is the canonical token source (`--jm-*`).
  `--brand-blue`/`--brand-gold` in custom.css are deprecated aliases that
  do NOT contain blue/gold (red and steel-blue) — kept only so existing
  pages don't break; migrate usages to `--jm-*`.
- `application/views/partials/head.php` is the shared head — new pages must
  use it instead of copying the vendor/link block.
- Tailwind is a local compiled build (`tw-build.css` full / `tw-utils.css`
  utilities-only for pages that also load Bootstrap). The runtime CDN
  script must not return.
- `dist/` (Midone) is kept only for the auth pages' app.css + 6 JS files.
- Shared UI kit (`assets/js/jm-ui.js` + `assets/css/jm-components.css`,
  framework-free, `.jm-*` namespace) is loaded via `includes_footer` on
  themed pages and explicitly on standalone pages. Use it instead of
  `alert()`/`confirm()`/ad-hoc toasts or CDN libs:
  `JM.toast(msg,'success|error|warning|info')`, `JM.confirm({title,text,
  danger,confirmText})` → Promise, `JM.alert(msg,type)`, `JM.modal(el,show)`,
  `JM.busy(btn,true)`. Legacy `toast(msg,ok)`/`showToast(msg,kind)` calls
  shim onto it automatically. CSS self-injects — adding the script tag is
  enough on any new standalone page.

- Design system conventions (all documented in `assets/css/tokens.css`):
  semantic `--jm-*` tokens for new code; FIXED ramps (`--jm-slate-*`,
  `--jm-gray-*`, …) for literal parity; z-index tiers 10/100/500/1000/1050/
  1060/1070(JM modal)/1080(JM toast); media tiers 576/768/992/1200;
  `.u-note`/`.u-flexrow` micro-utilities in custom.css; every themed page
  uses `partials/head.php`; single font family (Karla); all JS/CSS/fonts
  vendored locally — the only external request is conditional reCAPTCHA.
  Forms auto-busy their submit button (opt out: `data-no-autobusy`).

## Still outstanding / known debt

- Public GitHub history contains old SMTP + reCAPTCHA credentials — they
  were rotated; repo should be private regardless.
- `users` has 4 avatar columns (`avatar photo image profile_pic`) and dual
  `is_active`/`status` lifecycle — reconciled at login but schema
  consolidation is TODO.
- `profile_worker.php` (~1.7k lines) and several views still mix business
  logic into markup; queries in nav partials remain.
- `Media::preview` serves files to any logged-in user; truly sensitive
  uploads should move outside the webroot behind ownership checks.
- CI3 is EOL — plan migration to CI4/Laravel or pin a maintained CI3 fork.
