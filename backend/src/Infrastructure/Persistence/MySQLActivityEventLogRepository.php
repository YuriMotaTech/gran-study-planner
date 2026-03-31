<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Persistence;

use DateTimeImmutable;
use GranStudyPlanner\Domain\ActivityLog\ActivityEntityType;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventLogInterface;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventType;
use GranStudyPlanner\Domain\StudyPlan\IdGeneratorInterface;
use PDO;

final readonly class MySQLActivityEventLogRepository implements ActivityEventLogInterface
{
    public function __construct(
        private PDO $pdo,
        private IdGeneratorInterface $idGenerator,
    ) {}

    public function record(
        int $userId,
        ActivityEntityType $entityType,
        string $entityId,
        ActivityEventType $eventType,
        array $payload,
        ?DateTimeImmutable $occurredAt = null,
    ): void {
        $when = $occurredAt ?? new DateTimeImmutable();
        $sql = <<<'SQL'
            INSERT INTO activity_events (id, user_id, entity_type, entity_id, event_type, payload, occurred_at)
            VALUES (:id, :user_id, :entity_type, :entity_id, :event_type, :payload, :occurred_at)
        SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $this->idGenerator->next(),
            'user_id' => $userId,
            'entity_type' => $entityType->value,
            'entity_id' => $entityId,
            'event_type' => $eventType->value,
            'payload' => $payload === [] ? null : json_encode($payload, JSON_THROW_ON_ERROR),
            'occurred_at' => $when->format('Y-m-d H:i:s'),
        ]);
    }
}
