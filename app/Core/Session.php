<?php
declare(strict_types=1);

namespace App\Core;

class Session
{
    private static array $oldInput = [];

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $oldInput = $_SESSION['_old_input'] ?? [];
        self::$oldInput = is_array($oldInput) ? $oldInput : [];
        unset($_SESSION['_old_input']);
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flush(): void
    {
        $_SESSION = [];
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function flash(string $key, mixed $value): void
    {
        if (!isset($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }

        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key): mixed
    {
        if (!isset($_SESSION['_flash'][$key])) {
            return null;
        }

        $message = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);

        return $message;
    }

    public static function flashInput(array $input): void
    {
        $_SESSION['_old_input'] = $input;
    }

    public static function getOldInput(string $key, mixed $default = null): mixed
    {
        return self::$oldInput[$key] ?? $default;
    }

    public static function user(): ?array
    {
        if (!self::has('user_id')) {
            return null;
        }

        return [
            'id' => self::get('user_id'),
            'name' => self::get('user_name'),
            'email' => self::get('user_email'),
        ];
    }
}
