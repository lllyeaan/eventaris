<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'INSERT INTO users (name, email, password, created_at, updated_at)
             VALUES (:name, :email, :password, NOW(), NOW())'
        );

        $statement->bindValue(':name', $data['name']);
        $statement->bindValue(':email', $data['email']);
        $statement->bindValue(':password', $data['password']);
        $statement->execute();

        return (int) $pdo->lastInsertId();
    }

    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->bindValue(':email', $email);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public static function count(): int
    {
        $pdo = Database::connection();
        $statement = $pdo->query('SELECT COUNT(*) AS total FROM users');
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }
}
