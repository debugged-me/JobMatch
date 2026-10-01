<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Emits a JS-readable copy of the current CSRF hash as the `jm_csrf`
 * cookie so assets/js/csrf.js can attach it to POST forms and AJAX calls.
 * (CI3's own csrf cookie inherits cookie_httponly and is not JS-readable.)
 */
class SecurityExtras
{
    public function run()
    {
        $CI =& get_instance();
        if (!$CI->config->item('csrf_protection')) {
            return;
        }

        $hash = (string) $CI->security->get_csrf_hash();
        if ($hash === '') {
            return;
        }

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (bool) $CI->config->item('cookie_secure');
        $expire = time() + (int) ($CI->config->item('csrf_expire') ?: 7200);

        setcookie('jm_csrf', $hash, [
            'expires'  => $expire,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
    }
}
