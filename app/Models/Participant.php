<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Participant
{
    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'INSERT INTO participant_regs (
                event_id,
                full_name,
                email,
                phone,
                institution,
                notes,
                status_code,
                created_at,
                updated_at
            ) VALUES (
                :event_id,
                :full_name,
                :email,
                :phone,
                :institution,
                :notes,
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
        $statement->bindValue(':notes', $data['notes']);
        $statement->bindValue(':status_code', $data['status_code']);
        $statement->execute();

        return (int) $pdo->lastInsertId();
    }

    public static function list(?int $eventId = null, ?string $status = null, int $limit = 20, ?int $ownerId = null): array
    {
        $pdo = Database::connection();

        $sql = 'SELECT pr.*, e.name AS event_name, ps.label AS status_label
                FROM participant_regs pr
                INNER JOIN events e ON e.id = pr.event_id
                INNER JOIN participant_statuses ps ON ps.code = pr.status_code
                WHERE 1=1';

        if ($eventId !== null) {
            $sql .= ' AND pr.event_id = :event_id';
        }

        if ($ownerId !== null) {
            $sql .= ' AND e.owner_id = :owner_id';
        }

        if ($status !== null && $status !== '') {
            $sql .= ' AND pr.status_code = :status_code';
        }

        $sql .= ' ORDER BY pr.created_at DESC LIMIT :limit';

        $statement = $pdo->prepare($sql);

        if ($eventId !== null) {
            $statement->bindValue(':event_id', $eventId, PDO::PARAM_INT);
        }

        if ($ownerId !== null) {
            $statement->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
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
            'SELECT pr.*, e.name AS event_name, ps.label AS status_label
             FROM participant_regs pr
             INNER JOIN events e ON e.id = pr.event_id
             INNER JOIN participant_statuses ps ON ps.code = pr.status_code
             WHERE pr.id = :id
             LIMIT 1'
        );

        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $participant = $statement->fetch(PDO::FETCH_ASSOC);

        return $participant ?: null;
    }

    public static function findForOwner(int $id, int $ownerId): ?array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'SELECT pr.*, e.name AS event_name, ps.label AS status_label, e.owner_id
             FROM participant_regs pr
             INNER JOIN events e ON e.id = pr.event_id
             INNER JOIN participant_statuses ps ON ps.code = pr.status_code
             WHERE pr.id = :id AND e.owner_id = :owner_id
             LIMIT 1'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
        $statement->execute();

        $participant = $statement->fetch(PDO::FETCH_ASSOC);

        return $participant ?: null;
    }

    public static function updateStatus(int $id, string $statusCode): bool
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'UPDATE participant_regs
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
        $statement = $pdo->prepare('DELETE FROM participant_regs WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function countPending(?int $ownerId = null): int
    {
        $pdo = Database::connection();

        if ($ownerId === null) {
            $statement = $pdo->query(
                'SELECT COUNT(*) AS total FROM participant_regs WHERE status_code = \'pending\''
            );
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } else {
            $statement = $pdo->prepare(
                'SELECT COUNT(*) AS total
                 FROM participant_regs pr
                 INNER JOIN events e ON e.id = pr.event_id
                 WHERE pr.status_code = \'pending\' AND e.owner_id = :owner_id'
            );
            $statement->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        }

        return (int) ($result['total'] ?? 0);
    }

    public static function summaryByEvent(?int $ownerId = null): array
    {
        $pdo = Database::connection();
        if ($ownerId === null) {
            $sql = 'SELECT
                event_id,
                COUNT(*) AS total,
                SUM(CASE WHEN status_code = \'approved\' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN status_code = \'pending\' THEN 1 ELSE 0 END) AS pending
             FROM participant_regs
             GROUP BY event_id';
            $statement = $pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $sql = 'SELECT
                pr.event_id,
                COUNT(*) AS total,
                SUM(CASE WHEN pr.status_code = \'approved\' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN pr.status_code = \'pending\' THEN 1 ELSE 0 END) AS pending
             FROM participant_regs pr
             INNER JOIN events e ON e.id = pr.event_id
             WHERE e.owner_id = :owner_id
             GROUP BY pr.event_id';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        }

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
             FROM participant_regs
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
        $statement = $pdo->query('SELECT code, label FROM participant_statuses ORDER BY sort_order ASC');

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
