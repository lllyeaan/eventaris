<?php
declare(strict_types=1);

/** @var App\Core\Router $router */
$router = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';

require base_path('routes' . DIRECTORY_SEPARATOR . 'web.php');

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $router->dispatch($uri, $method);
} catch (Throwable $exception) {
    $isDebug = filter_var(config('app.debug', false), FILTER_VALIDATE_BOOL);

    if ($isDebug) {
        http_response_code(500);
        echo '<h1>Application Error</h1>';
        echo '<pre>' . e($exception->getMessage()) . '</pre>';
    } else {
        logger('Unhandled exception: ' . $exception->getMessage());
        http_response_code(500);
        echo 'An unexpected error occurred.';
    }
}
