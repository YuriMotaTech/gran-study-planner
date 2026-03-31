<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Persistence;

use DateTimeImmutable;
use GranStudyPlanner\Domain\WeeklyGoals\IsoWeek;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoals;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoalsRepositoryInterface;
use PDO;

final readonly class MySQLWeeklyGoalsRepository implements WeeklyGoalsRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function getByUserAndWeek(int $userId, IsoWeek $week): ?array
    {
        $stmt = $this->pdo->prepare('SELECT goal_pending, goal_in_progress, goal_done, goal_overdue FROM weekly_goals WHERE user_id = :user_id AND iso_year_week = :week');
        $stmt->execute(['user_id' => $userId, 'week' => $week->value]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            return null;
        }

        return [
            'pending' => (int) $row['goal_pending'],
            'in_progress' => (int) $row['goal_in_progress'],
            'done' => (int) $row['goal_done'],
            'overdue' => (int) $row['goal_overdue'],
        ];
    }

    public function upsert(int $userId, IsoWeek $week, array $goals): void
    {
        $base = WeeklyGoals::empty();
        $goals = array_merge($base, $goals);

        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $sql = <<<'SQL'
            INSERT INTO weekly_goals (
                user_id, iso_year_week, goal_pending, goal_in_progress, goal_done, goal_overdue, created_at, updated_at
            ) VALUES (
                :user_id, :week, :goal_pending, :goal_in_progress, :goal_done, :goal_overdue, :created_at, :updated_at
            )
            ON DUPLICATE KEY UPDATE
                goal_pending = VALUES(goal_pending),
                goal_in_progress = VALUES(goal_in_progress),
                goal_done = VALUES(goal_done),
                goal_overdue = VALUES(goal_overdue),
                updated_at = VALUES(updated_at)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'week' => $week->value,
            'goal_pending' => (int) $goals['pending'],
            'goal_in_progress' => (int) $goals['in_progress'],
            'goal_done' => (int) $goals['done'],
            'goal_overdue' => (int) $goals['overdue'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

