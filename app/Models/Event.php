<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DateTimeImmutable;
use PDO;
use PDOStatement;

class Event
{
    public static function all(): array
    {
        $pdo = Database::connection();
        $statement = $pdo->query(
            'SELECT * FROM events ORDER BY registration_start DESC, created_at DESC'
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ownedBy(int $ownerId): array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'SELECT *
             FROM events
             WHERE owner_id = :owner_id
             ORDER BY registration_start DESC, created_at DESC'
        );
        $statement->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function paginate(int $limit = 20): array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'SELECT * FROM events ORDER BY created_at DESC LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'SELECT * FROM events WHERE id = :id LIMIT 1'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $event = $statement->fetch(PDO::FETCH_ASSOC);

        return $event ?: null;
    }

    public static function findForOwner(int $id, int $ownerId): ?array
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'SELECT * FROM events WHERE id = :id AND owner_id = :owner_id LIMIT 1'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
        $statement->execute();

        $event = $statement->fetch(PDO::FETCH_ASSOC);

        return $event ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'INSERT INTO events (
                owner_id,
                name,
                description,
                location,
                event_date,
                participant_quota,
                committee_quota,
                registration_start,
                registration_end,
                status,
                committee_divisions,
                created_at,
                updated_at
            ) VALUES (
                :owner_id,
                :name,
                :description,
                :location,
                :event_date,
                :participant_quota,
                :committee_quota,
                :registration_start,
                :registration_end,
                :status,
                :committee_divisions,
                NOW(),
                NOW()
            )'
        );

        $statement->bindValue(':owner_id', $data['owner_id'], PDO::PARAM_INT);
        $statement->bindValue(':name', $data['name']);
        $statement->bindValue(':description', $data['description']);
        $statement->bindValue(':location', $data['location']);
        self::bindNullable($statement, ':event_date', $data['event_date']);
        $statement->bindValue(':participant_quota', $data['participant_quota'], PDO::PARAM_INT);
        $statement->bindValue(':committee_quota', $data['committee_quota'], PDO::PARAM_INT);
        self::bindNullable($statement, ':registration_start', $data['registration_start']);
        self::bindNullable($statement, ':registration_end', $data['registration_end']);
        $statement->bindValue(':status', $data['status']);
        self::bindNullable($statement, ':committee_divisions', $data['committee_divisions'] ?? null);
        $statement->execute();

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare(
            'UPDATE events SET
                name = :name,
                description = :description,
                location = :location,
                event_date = :event_date,
                participant_quota = :participant_quota,
                committee_quota = :committee_quota,
                registration_start = :registration_start,
                registration_end = :registration_end,
                status = :status,
                committee_divisions = :committee_divisions,
                updated_at = NOW()
             WHERE id = :id'
        );

        $statement->bindValue(':name', $data['name']);
        $statement->bindValue(':description', $data['description']);
        $statement->bindValue(':location', $data['location']);
        self::bindNullable($statement, ':event_date', $data['event_date']);
        $statement->bindValue(':participant_quota', $data['participant_quota'], PDO::PARAM_INT);
        $statement->bindValue(':committee_quota', $data['committee_quota'], PDO::PARAM_INT);
        self::bindNullable($statement, ':registration_start', $data['registration_start']);
        self::bindNullable($statement, ':registration_end', $data['registration_end']);
        $statement->bindValue(':status', $data['status']);
        self::bindNullable($statement, ':committee_divisions', $data['committee_divisions'] ?? null);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::connection();
        $statement = $pdo->prepare('DELETE FROM events WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public static function count(?int $ownerId = null): int
    {
        $pdo = Database::connection();
        if ($ownerId === null) {
            $statement = $pdo->query('SELECT COUNT(*) AS total FROM events');
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } else {
            $statement = $pdo->prepare('SELECT COUNT(*) AS total FROM events WHERE owner_id = :owner_id');
            $statement->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        }

        return (int) ($result['total'] ?? 0);
    }

    public static function quotaSummary(?int $ownerId = null): array
    {
        $pdo = Database::connection();
        if ($ownerId === null) {
            $sql = 'SELECT
                e.id,
                e.name,
                e.participant_quota,
                e.committee_quota,
                e.status,
                e.registration_start,
                e.registration_end,
                e.event_date,
                (SELECT COUNT(*) FROM participant_regs pr WHERE pr.event_id = e.id AND pr.status_code = \'approved\') AS participants_approved,
                (SELECT COUNT(*) FROM participant_regs pr WHERE pr.event_id = e.id AND pr.status_code = \'pending\') AS participants_pending,
                (SELECT COUNT(*) FROM committee_apps ca WHERE ca.event_id = e.id AND ca.status_code = \'approved\') AS committees_approved,
                (SELECT COUNT(*) FROM committee_apps ca WHERE ca.event_id = e.id AND ca.status_code = \'pending\') AS committees_pending
            FROM events e
            ORDER BY e.created_at DESC';
            $statement = $pdo->query($sql);
        } else {
            $sql = 'SELECT
                e.id,
                e.name,
                e.participant_quota,
                e.committee_quota,
                e.status,
                e.registration_start,
                e.registration_end,
                e.event_date,
                (SELECT COUNT(*) FROM participant_regs pr WHERE pr.event_id = e.id AND pr.status_code = \'approved\') AS participants_approved,
                (SELECT COUNT(*) FROM participant_regs pr WHERE pr.event_id = e.id AND pr.status_code = \'pending\') AS participants_pending,
                (SELECT COUNT(*) FROM committee_apps ca WHERE ca.event_id = e.id AND ca.status_code = \'approved\') AS committees_approved,
                (SELECT COUNT(*) FROM committee_apps ca WHERE ca.event_id = e.id AND ca.status_code = \'pending\') AS committees_pending
            FROM events e
            WHERE e.owner_id = :owner_id
            ORDER BY e.created_at DESC';
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
            $statement->execute();
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function isRecruitmentOpen(array $event): bool
    {
        if (($event['status'] ?? '') !== 'open') {
            return false;
        }

        $now = new DateTimeImmutable('now');

        $start = isset($event['registration_start']) && $event['registration_start'] !== null
            ? new DateTimeImmutable($event['registration_start'])
            : null;

        $end = isset($event['registration_end']) && $event['registration_end'] !== null
            ? new DateTimeImmutable($event['registration_end'])
            : null;

        if ($start !== null && $now < $start) {
            return false;
        }

        if ($end !== null && $now > $end) {
            return false;
        }

        return true;
    }

    private static function bindNullable(PDOStatement $statement, string $parameter, mixed $value): void
    {
        if ($value === null || $value === '') {
            $statement->bindValue($parameter, null, PDO::PARAM_NULL);
            return;
        }

        $statement->bindValue($parameter, $value);
    }
}
