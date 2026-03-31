<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\WeeklyGoals;

interface WeeklyGoalsRepositoryInterface
{
    /** @return array<string,int>|null */
    public function getByUserAndWeek(int $userId, IsoWeek $week): ?array;

    /** @param array<string,int> $goals */
    public function upsert(int $userId, IsoWeek $week, array $goals): void;
}

