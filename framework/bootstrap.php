<?php
$AIR_ROOT = dirname(__DIR__);

if (DIRECTORY_SEPARATOR == '\\') {
    $AIR_ROOT = str_replace('\\', '/', $AIR_ROOT);
}

define('AIR_ROOT', $AIR_ROOT);

$composerAutoload = AIR_ROOT . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

require_once __DIR__ . "/functions/helpers.php";
require_once __DIR__ . "/functions/autoload.php";

date_default_timezone_set(cfg('common', 'timezone', 'UTC'));
spl_autoload_register('autoload');
ini_set('log_errors', 0);