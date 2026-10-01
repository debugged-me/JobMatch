<?php defined('BASEPATH') OR exit('No direct script access allowed');

$hook['post_controller_constructor'][] = [
    'class'    => 'AutoDeactivate',
    'function' => 'run',
    'filename' => 'AutoDeactivate.php',
    'filepath' => 'hooks',
    'params'   => []
];

$hook['post_controller_constructor'][] = [
    'class'    => 'SecurityExtras',
    'function' => 'run',
    'filename' => 'SecurityExtras.php',
    'filepath' => 'hooks',
    'params'   => []
];
