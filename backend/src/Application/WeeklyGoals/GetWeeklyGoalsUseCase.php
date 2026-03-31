<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\WeeklyGoals;

use GranStudyPlanner\Domain\WeeklyGoals\IsoWeek;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoals;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoalsRepositoryInterface;

final readonly class GetWeeklyGoalsUseCase
{
    public function __construct(private WeeklyGoalsRepositoryInterface $repo) {}

    /** @return array<string,int> */
    public function execute(int $userId, IsoWeek $week): array
    {
        $goals = $this->repo->getByUserAndWeek($userId, $week);
        return $goals ?? WeeklyGoals::empty();
    }
}

