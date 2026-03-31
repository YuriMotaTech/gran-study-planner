<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\WeeklyGoals;

use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;

interface WeeklyProgressRepositoryInterface
{
    /** @return array<string,int> */
    public function countsByUserAndWeek(int $userId, IsoWeek $week): array;
}

