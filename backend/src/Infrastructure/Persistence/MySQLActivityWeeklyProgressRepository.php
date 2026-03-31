<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Persistence;

use GranStudyPlanner\Domain\WeeklyGoals\IsoWeek;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyProgressRepositoryInterface;
use PDO;

/**
 * Weekly counts from activity_events: created (initial status), status_changed (to), marked_overdue (overdue).
 * Deleted events are not counted toward progress.
 */
final readonly class MySQLActivityWeeklyProgressRepository implements WeeklyProgressRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function countsByUserAndWeek(int $userId, IsoWeek $week): array
    {
        $range = $week->range();
        $sql = <<<'SQL'
            SELECT bucket, COUNT(*) AS total FROM (
                SELECT
                    CASE
                        WHEN event_type = 'created' THEN JSON_UNQUOTE(JSON_EXTRACT(payload, '$.status'))
                        WHEN event_type = 'status_changed' THEN JSON_UNQUOTE(JSON_EXTRACT(payload, '$.to'))
                        WHEN event_type = 'marked_overdue' THEN 'overdue'
                        ELSE NULL
                    END AS bucket
                FROM activity_events
                WHERE user_id = :user_id
                  AND entity_type = 'study_plan'
                  AND occurred_at >= :start AND occurred_at < :end
                  AND event_type IN ('created', 'status_changed', 'marked_overdue')
            ) AS derived
            WHERE bucket IS NOT NULL AND bucket IN ('pending', 'in_progress', 'done', 'overdue')
            GROUP BY bucket
            SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'start' => $range['start']->format('Y-m-d H:i:s'),
            'end' => $range['end']->format('Y-m-d H:i:s'),
        ]);

        $base = ['pending' => 0, 'in_progress' => 0, 'done' => 0, 'overdue' => 0];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
            $base[(string) $row['bucket']] = (int) $row['total'];
        }

        return $base;
    }
}
