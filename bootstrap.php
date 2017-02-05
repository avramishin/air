<?php
/**
 * Include framework functions and classes
 * Include composer files if any, directory /vendor
 * Register autoload function
 */

define('ROOT', __DIR__);
error_reporting(E_ALL);

$includes = [
    ROOT . '/core/classes/*.php',
    ROOT . '/core/functions/*.php',
];

foreach ($includes as $pattern) {
    foreach (glob($pattern) as $phpFile) {
        require $phpFile;
    }
}



if (file_exists(ROOT . '/vendor/autoload.php')) {
    require ROOT . '/vendor/autoload.php';
}

/**
 * Autoload app models
 */
spl_autoload_register(function ($classname) {

    $includes = [
        ROOT . "/app/models/{$classname}.php"
    ];

    if (preg_match('/^([A-Z][a-z]*)\w+/', $classname, $m)) {
        $prefix = strtolower($m[1]);
        $includes [] = ROOT . "/app/models/{$prefix}/{$classname}.php";
        $includes [] = ROOT . "/app/models/{$prefix}/tables/{$classname}.php";
    }

    foreach ($includes as $phpFile) {
        if (file_exists($phpFile)) {
            require_once $phpFile;
            return;
        }
    }
});

/**
 * Send all errors to RSS feed
 */
require ROOT . "/core/libraries/Debug/ErrorHook/Listener.php";

$errorHook = new Debug_ErrorHook_Listener();
$errorHook->addNotifier(
    new Debug_ErrorHook_RemoveDupsWrapper(
        new Debug_ErrorHook_RssNotifier(
            Debug_ErrorHook_TextNotifier::LOG_ALL,
            cfg()->baseurl . "/" . cfg()->errorHook->rss->submit
        ),
        ROOT . "/data/tmp/errorHook", # lock directory
        cfg()->errorHook->resendTimeout # do not resend the same error within resendTimeout seconds
    )
);