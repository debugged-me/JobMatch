<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 |--------------------------------------------------------------------------
 | Email configuration — secrets come from environment variables.
 |
 | Set these on the server (Apache SetEnv, .env loader, or system env):
 |   JM_SMTP_HOST, JM_SMTP_USER, JM_SMTP_PASS, JM_SMTP_PORT,
 |   JM_FROM_EMAIL, JM_REPLY_TO_EMAIL
 |
 | NEVER commit real credentials here.
 |--------------------------------------------------------------------------
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
$config['protocol']     = 'smtp';
$config['smtp_host']    = jm_env('JM_SMTP_HOST') ?: '';
$config['smtp_user']    = jm_env('JM_SMTP_USER') ?: '';
$config['smtp_pass']    = jm_env('JM_SMTP_PASS') ?: '';
$config['smtp_port']    = (int) (jm_env('JM_SMTP_PORT') ?: 465);
$config['smtp_crypto']  = jm_env('JM_SMTP_CRYPTO') ?: 'ssl';
$config['from_email']   = jm_env('JM_FROM_EMAIL') ?: 'no-reply@jobmatch.local';
$config['from_name']    = 'JobMatch DavOr';
$config['support_name'] = 'JobMatch DavOr Support';
$config['reply_to_email'] = jm_env('JM_REPLY_TO_EMAIL') ?: $config['from_email'];
$config['reply_to_name']  = 'JobMatch DavOr';
$config['charset']      = 'utf-8';
$config['mailtype']     = 'html';
$config['newline']      = "\r\n";
$config['crlf']         = "\r\n";
$config['useragent']    = 'CodeIgniter';
$config['smtp_timeout'] = 5;
$config['wordwrap']     = TRUE;
$config['validate']     = TRUE;
