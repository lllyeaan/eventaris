<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR);
        exit;
    }
}
