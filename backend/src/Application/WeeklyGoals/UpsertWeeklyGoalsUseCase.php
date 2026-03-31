<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\WeeklyGoals;

use GranStudyPlanner\Domain\WeeklyGoals\IsoWeek;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoals;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoalsRepositoryInterface;

final readonly class UpsertWeeklyGoalsUseCase
{
    public function __construct(private WeeklyGoalsRepositoryInterface $repo) {}

    /** @param array<string,int> $goals */
    public function execute(int $userId, IsoWeek $week, array $goals): void
    {
        $weeklyGoals = WeeklyGoals::fromArray($userId, $week, $goals);
        $this->repo->upsert($userId, $week, $weeklyGoals->goals);
    }
}

