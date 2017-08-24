<?php
date_default_timezone_set('Asia/Shanghai');
ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);
defined('PUBLIC_PATH') or define('PUBLIC_PATH', dirname(__FILE__));

include __DIR__ . '/../L/L.php';

$app = include __DIR__ . '/../protected/conf/app.php';
$app->run();
