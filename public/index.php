<?php

/***
 * All HTTP requests entry point
 */

require_once __DIR__ . '/../framework/bootstrap.php';

try {

    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $controllersDir = AIR_ROOT . '/app/controllers';
    $controllers = [
        sprintf('%s/%s.php', $controllersDir, $url),
        sprintf('%s/%sindex.php', $controllersDir, $url),
        sprintf('%s/%s/index.php', $controllersDir, $url)
    ];

    $controllerFound = false;
    foreach ($controllers as $controller) {
        if (file_exists($controller)) {
            require_once $controller;
            $class = controller_class($controller);
            call_user_func([new $class, 'action']);
            flush();
            $controllerFound = true;
            break;
        }
    }

    if (!$controllerFound) {
        throw new AirControllerNotFoundException("{$url} not found");
    }

} catch (AirControllerNotFoundException $e) {
    header("HTTP/1.0 404 Not Found");
    echo $e->getMessage();
} catch (AirValidationException $e) {
    $jsonResponse = new AirJsonResponse();
    $jsonResponse->error($e->getMessage());
}