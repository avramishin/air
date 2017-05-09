<?php

/**
 * Autoload class
 * @param string $className
 */
function autoload($className)
{
    $classPath = [
        AIR_ROOT . "/framework/classes/{$className}.php",
        AIR_ROOT . "/framework/classes/exceptions/{$className}.php",
        AIR_ROOT . "/app/models/{$className}.php",
        AIR_ROOT . "/app/events/{$className}.php",
        AIR_ROOT . "/app/commands/{$className}.php"
    ];

    if (preg_match('/^([A-Z][a-z]*)\w+/', $className, $m)) {
        $prefix = strtolower($m[1]);
        $classPath [] = AIR_ROOT . "/app/models/{$prefix}/{$className}.php";
    }

    foreach ($classPath as $class) {
        if (file_exists($class)) {
            require_once $class;
            return;
        }
    }
}