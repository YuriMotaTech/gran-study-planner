<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\ActivityLog;

use DateTimeImmutable;

interface ActivityEventLogInterface
{
    /**
     * @param array<string,mixed> $payload
     */
    public function record(
        int $userId,
        ActivityEntityType $entityType,
        string $entityId,
        ActivityEventType $eventType,
        array $payload,
        ?DateTimeImmutable $occurredAt = null,
    ): void;
}
