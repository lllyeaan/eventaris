<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Committee
{
    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'INSERT INTO committee_apps (
                event_id,
                full_name,
                email,
                phone,
                institution,
                primary_division,
                secondary_division,
                motivation,
                status_code,
                created_at,
                updated_at
            ) VALUES (
                :event_id,
                :full_name,
                :email,
                :phone,
                :institution,
                :primary_division,
                :secondary_division,
                :motivation,
                :status_code,
                NOW(),
                NOW()
            )'
        );

        $statement->bindValue(':event_id', $data['event_id'], PDO::PARAM_INT);
        $statement->bindValue(':full_name', $data['full_name']);
        $statement->bindValue(':email', $data['email']);
        $statement->bindValue(':phone', $data['phone']);
        $statement->bindValue(':institution', $data['institution']);
        $statement->bindValue(':primary_division', $data['primary_division']);
        $statement->bindValue(':secondary_division', $data['secondary_division']);
        $statement->bindValue(':motivation', $data['motivation']);
        $statement->bindValue(':status_code', $data['status_code']);
        $statement->execute();

        return (int) $pdo->lastInsertId();
    }

    public static function list(?int $eventId = null, ?string $status = null, int $limit = 20): array
    {
        $pdo = Database::connection();

        $sql = 'SELECT ca.*, e.name AS event_name, cs.label AS status_label
                FROM committee_apps ca
                INNER JOIN events e ON e.id = ca.event_id
                INNER JOIN committee_statuses cs ON cs.code = ca.status_code
                WHERE 1=1';

        if ($eventId !== null) {
            $sql .= ' AND ca.event_id = :event_id';
        }

        if ($status !== null && $status !== '') {
            $sql .= ' AND ca.status_code = :status_code';
        }

        $sql .= ' ORDER BY ca.created_at DESC LIMIT :limit';

        $statement = $pdo->prepare($sql);

        if ($eventId !== null) {
            $statement->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        }

        if ($status !== null && $status !== '') {
            $statement->bindValue(':status_code', $status);
        }

        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'SELECT ca.*, e.name AS event_name, cs.label AS status_label
             FROM committee_apps ca
             INNER JOIN events e ON e.id = ca.event_id
             INNER JOIN committee_statuses cs ON cs.code = ca.status_code
             WHERE ca.id = :id
             LIMIT 1'
        );

        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $committee = $statement->fetch(PDO::FETCH_ASSOC);

        return $committee ?: null;
    }

    public static function updateStatus(int $id, string $statusCode): bool
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'UPDATE committee_apps
             SET status_code = :status_code, updated_at = NOW()
             WHERE id = :id'
        );

        $statement->bindValue(':status_code', $statusCode);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare('DELETE FROM committee_apps WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function countPending(): int
    {
        $pdo = Database::connection();
        $statement = $pdo->query(
            'SELECT COUNT(*) AS total FROM committee_apps WHERE status_code = \'pending\''
        );
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }

    public static function summaryByEvent(): array
    {
        $pdo = Database::connection();
        $statement = $pdo->query(
            'SELECT
                event_id,
                COUNT(*) AS total,
                SUM(CASE WHEN status_code = \'approved\' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN status_code = \'pending\' THEN 1 ELSE 0 END) AS pending
             FROM committee_apps
             GROUP BY event_id'
        );

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $summary = [];

        foreach ($rows as $row) {
            $summary[(int) $row['event_id']] = [
                'total' => (int) $row['total'],
                'approved' => (int) $row['approved'],
                'pending' => (int) $row['pending'],
            ];
        }

        return $summary;
    }

    public static function summaryForEvent(int $eventId): array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status_code = \'approved\' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN status_code = \'pending\' THEN 1 ELSE 0 END) AS pending
             FROM committee_apps
             WHERE event_id = :event_id'
        );

        $statement->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'approved' => 0, 'pending' => 0];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'approved' => (int) ($row['approved'] ?? 0),
            'pending' => (int) ($row['pending'] ?? 0),
        ];
    }

    public static function statuses(): array
    {
        $pdo = Database::connection();
        $statement = $pdo->query('SELECT code, label FROM committee_statuses ORDER BY sort_order ASC');

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
