<?php
/**
 * Default entry point for http requests
 */

try {

    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $controllersDir = __DIR__ . '/app/controllers';
    $controllers = [
        sprintf('%s/%s.php', $controllersDir, $url),
        sprintf('%s/%sindex.php', $controllersDir, $url),
        sprintf('%s/%s/index.php', $controllersDir, $url)
    ];

    /**
     * Include custom application router to override default file based routing
     * you can manage request yourself and build your list of controllers in
     * $controllers array to include
     */
    require __DIR__ . '/app/router.php';

    $controllerFound = false;
    foreach ($controllers as $controller) {
        if (file_exists($controller)) {
            require __DIR__ . '/app/bootstrap.php';
            require $controller;
            $controllerFound = true;
            break;
        }
    }

    if (!$controllerFound) {
        throw new Exception(sprintf('%s not found', $url));
    }

} catch (Exception $e) {
    header("HTTP/1.0 404 Not Found");
    echo $e->getMessage();
}