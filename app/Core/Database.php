<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    public static function boot(array $config): void
    {
        if (self::$connection !== null) {
            return;
        }

        $driver = $config['driver'] ?? 'mysql';
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';

        if ($driver !== 'mysql') {
            throw new RuntimeException('Only MySQL is supported.');
        }

        $dsn = sprintf('%s:host=%s;port=%d;dbname=%s;charset=%s', $driver, $host, (int) $port, $database, $charset);

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException('Unable to connect to database: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
        }

        self::$connection = $pdo;
    }

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            throw new RuntimeException('Database has not been booted.');
        }

        return self::$connection;
    }
}
