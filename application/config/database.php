<?php
defined('BASEPATH') or exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

/*
 | Credentials via environment in production:
 |   JM_DB_HOST, JM_DB_USER, JM_DB_PASS, JM_DB_NAME
 | Defaults below match the local XAMPP dev setup.
 */
$db['default'] = array(
	'dsn'	=> '',
	'hostname' => getenv('JM_DB_HOST') ?: '127.0.0.1',
	'username' => getenv('JM_DB_USER') ?: 'root',
	'password' => getenv('JM_DB_PASS') !== false ? getenv('JM_DB_PASS') : '',
	'database' => getenv('JM_DB_NAME') ?: 'softtech_trabawho',
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
