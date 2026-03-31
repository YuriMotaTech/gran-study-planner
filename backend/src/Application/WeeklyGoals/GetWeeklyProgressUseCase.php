<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\WeeklyGoals;

use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;
use GranStudyPlanner\Domain\WeeklyGoals\IsoWeek;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoals;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoalsRepositoryInterface;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyProgressRepositoryInterface;

final readonly class GetWeeklyProgressUseCase
{
    public function __construct(
        private WeeklyProgressRepositoryInterface $progressRepo,
        private WeeklyGoalsRepositoryInterface $goalsRepo,
    ) {}

    /** @return array{week: string, goals: array<string,int>, counts: array<string,int>, percentages: array<string,int>} */
    public function execute(int $userId, IsoWeek $week): array
    {
        $counts = $this->progressRepo->countsByUserAndWeek($userId, $week);
        $goals = $this->goalsRepo->getByUserAndWeek($userId, $week) ?? WeeklyGoals::empty();

        $percentages = [];
        foreach (StudyPlanStatus::cases() as $status) {
            $key = $status->value;
            $goal = (int) ($goals[$key] ?? 0);
            $count = (int) ($counts[$key] ?? 0);
            $percentages[$key] = $goal <= 0 ? 0 : (int) min(100, floor(($count / $goal) * 100));
        }

        return [
            'week' => $week->value,
            'goals' => $goals,
            'counts' => $counts,
            'percentages' => $percentages,
        ];
    }
}

