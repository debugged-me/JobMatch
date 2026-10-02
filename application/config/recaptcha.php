<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 | reCAPTCHA keys — set via environment, never commit real keys:
 |   JM_RECAPTCHA_SITE_KEY, JM_RECAPTCHA_SECRET
 */
if (!function_exists('jm_env')) {
	function jm_env($key) {
		static $secrets_loaded = false;
		if (!$secrets_loaded) {
			$secrets_loaded = true;
			$__f = dirname(__FILE__) . '/secrets.php';
			if (is_file($__f)) require $__f;
		}
		$v = getenv($key);
		if ($v === false) $v = isset($_SERVER[$key]) ? $_SERVER[$key] : false;
		return $v;
	}
}
$config['recaptcha_site_key'] = jm_env('JM_RECAPTCHA_SITE_KEY') ?: '';
$config['recaptcha_secret']   = jm_env('JM_RECAPTCHA_SECRET') ?: '';
$config['recaptcha_check_remoteip'] = TRUE;
