<?php
require_once __DIR__ . "/framework/bootstrap.php";
$class = !empty($argv[1]) ? "{$argv[1]}Command" : "CheckEnvCommand";
$args = [];

foreach ($argv as $key => $value) {
    if ($key > 1) {
        $args[] = $value;
    }
}

new $class($args);