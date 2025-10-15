<?php
declare(strict_types=1);

use App\Core\Session;

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__);

        if ($path === '') {
            return $base;
        }

        return $base . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        return base_path('app' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('load_env')) {
    function load_env(string $directory): void
    {
        $envFile = $directory . DIRECTORY_SEPARATOR . '.env';

        if (!file_exists($envFile)) {
            $example = $directory . DIRECTORY_SEPARATOR . '.env.example';
            if (!file_exists($example)) {
                return;
            }
            $envFile = $example;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2) + ['', '']);
            $value = trim($value, "\"'");

            $_ENV[$name] = $value;
            putenv(sprintf('%s=%s', $name, $value));
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $config = null;

        if ($config === null && isset($GLOBALS['app_config'])) {
            $config = $GLOBALS['app_config'];
        }

        if ($key === '*') {
            return $config ?? [];
        }

        $segments = explode('.', $key);
        $value = $config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('set_config')) {
    function set_config(array $config): void
    {
        $GLOBALS['app_config'] = $config;
    }
}

if (!function_exists('view')) {
    function view(string $template, array $data = []): void
    {
        $viewPath = app_path('Views' . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $template) . '.php');

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($template, ENT_QUOTES, 'UTF-8');
            return;
        }

        extract($data, EXTR_SKIP);

        $appName = config('app.name', 'Eventory');
        $content = (function () use ($viewPath, $data) {
            extract($data, EXTR_SKIP);
            ob_start();
            include $viewPath;
            return ob_get_clean();
        })();

        $layoutPath = app_path('Views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'main.php');
        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('back')) {
    function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = null): mixed
    {
        return Session::getOldInput($key, $default);
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $message = null): mixed
    {
        if (func_num_args() === 1) {
            return Session::getFlash($key);
        }

        Session::flash($key, $message);
        return null;
    }
}

if (!function_exists('route_is')) {
    function route_is(string $path): bool
    {
        $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        return trim($current, '/') === trim($path, '/');
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('logger')) {
    function logger(string $message): void
    {
        $logDir = storage_path('logs');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $file = $logDir . DIRECTORY_SEPARATOR . 'app.log';
        $timestamp = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        file_put_contents($file, sprintf("[%s] %s%s", $timestamp, $message, PHP_EOL), FILE_APPEND);
    }
}
