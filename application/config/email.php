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
$config['protocol']     = 'smtp';
$config['smtp_host']    = getenv('JM_SMTP_HOST') ?: '';
$config['smtp_user']    = getenv('JM_SMTP_USER') ?: '';
$config['smtp_pass']    = getenv('JM_SMTP_PASS') ?: '';
$config['smtp_port']    = (int) (getenv('JM_SMTP_PORT') ?: 465);
$config['smtp_crypto']  = 'ssl';
$config['from_email']   = getenv('JM_FROM_EMAIL') ?: 'no-reply@jobmatch.local';
$config['from_name']    = 'JobMatch DavOr';
$config['support_name'] = 'JobMatch DavOr Support';
$config['reply_to_email'] = getenv('JM_REPLY_TO_EMAIL') ?: $config['from_email'];
$config['reply_to_name']  = 'JobMatch DavOr';
$config['charset']      = 'utf-8';
$config['mailtype']     = 'html';
$config['newline']      = "\r\n";
$config['crlf']         = "\r\n";
$config['useragent']    = 'CodeIgniter';
$config['smtp_timeout'] = 5;
$config['wordwrap']     = TRUE;
$config['validate']     = TRUE;
