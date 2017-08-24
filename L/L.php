<?php
ob_start();
defined('L_PATH') or define('L_PATH', dirname(__FILE__));
require_once 'base/Autoload.php';
Autoload::init();
spl_autoload_register(['Autoload', 'load']);
require_once 'base/L.php';