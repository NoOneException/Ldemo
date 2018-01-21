<?php
ob_start();
defined('L_PATH') or define('L_PATH', __DIR__);
require_once 'base/Autoload.php';
Autoload::init();
spl_autoload_register(['Autoload', 'load']);
require_once 'base/L.php';
$app = include BASE_PATH . '/conf/app.php';
$app->run();