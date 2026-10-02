<?php
defined('BASEPATH') or exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

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

/*
 | Credentials via environment in production:
 |   JM_DB_HOST, JM_DB_USER, JM_DB_PASS, JM_DB_NAME
 | Defaults below match the local XAMPP dev setup.
 */
$db['default'] = array(
	'dsn'	=> '',
	'hostname' => jm_env('JM_DB_HOST') ?: '127.0.0.1',
	'username' => jm_env('JM_DB_USER') ?: 'root',
	'password' => jm_env('JM_DB_PASS') !== false ? jm_env('JM_DB_PASS') : '',
	'database' => jm_env('JM_DB_NAME') ?: 'softtech_trabawho',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8mb4',
	'dbcollat' => 'utf8mb4_unicode_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => TRUE,
	'failover' => array(),
	'save_queries' => (ENVIRONMENT !== 'production')
);
