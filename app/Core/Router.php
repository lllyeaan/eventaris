<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Router
{
    /**
     * @var array<string, array<string, array{handler: callable|array, middleware: array<int, callable|string>}>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function dispatch(string $uri, string $method): mixed
    {
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?? '/');

        $method = strtoupper($method);
        if ($method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper((string) $_POST['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                $method = $override;
            }
        }

        $route = $this->routes[$method][$path] ?? null;

        if ($route === null) {
            http_response_code(404);
            echo '404 Not Found';
            return null;
        }

        $request = [
            'uri' => $path,
            'method' => $method,
            'query' => $_GET,
            'body' => $_POST,
        ];

        return $this->runMiddleware(
            $route['middleware'],
            $request,
            fn() => $this->invokeHandler($route['handler'])
        );
    }

    private function addRoute(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $normalized = $this->normalizePath($path);
        $this->routes[$method][$normalized] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);
        if ($path === '' || $path === '/') {
            return '/';
        }

        $path = '/' . trim($path, '/');

        return $path === '' ? '/' : $path;
    }

    private function invokeHandler(callable|array $handler): mixed
    {
        if (is_array($handler)) {
            [$class, $method] = $handler + [null, null];
            if (!is_string($class) || !is_string($method)) {
                throw new RuntimeException('Invalid controller definition.');
            }

            if (!class_exists($class)) {
                throw new RuntimeException("Controller {$class} not found.");
            }

            $instance = new $class();

            if (!method_exists($instance, $method)) {
                throw new RuntimeException("Controller method {$class}::{$method} not found.");
            }

            return $instance->$method();
        }

        if (is_callable($handler)) {
            return $handler();
        }

        throw new RuntimeException('Invalid route handler.');
    }

    private function runMiddleware(array $middlewares, array $request, callable $handler): mixed
    {
        if (empty($middlewares)) {
            return $handler();
        }

        $middleware = array_shift($middlewares);

        if (is_string($middleware) && class_exists($middleware)) {
            $instance = new $middleware();

            if (!method_exists($instance, 'handle')) {
                throw new RuntimeException("Middleware {$middleware} must define handle method.");
            }

            return $instance->handle(
                $request,
                fn() => $this->runMiddleware($middlewares, $request, $handler)
            );
        }

        if (is_callable($middleware)) {
            return $middleware(
                $request,
                fn() => $this->runMiddleware($middlewares, $request, $handler)
            );
        }

        throw new RuntimeException('Invalid middleware definition.');
    }
}
