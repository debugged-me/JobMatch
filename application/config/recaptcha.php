<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 | reCAPTCHA keys — set via environment, never commit real keys:
 |   JM_RECAPTCHA_SITE_KEY, JM_RECAPTCHA_SECRET
 */
$config['recaptcha_site_key'] = getenv('JM_RECAPTCHA_SITE_KEY') ?: '';
$config['recaptcha_secret']   = getenv('JM_RECAPTCHA_SECRET') ?: '';
$config['recaptcha_check_remoteip'] = TRUE;
