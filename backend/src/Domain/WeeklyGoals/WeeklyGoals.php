<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\WeeklyGoals;

use DomainException;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;

final readonly class WeeklyGoals
{
    /** @param array<string,int> $goals */
    private function __construct(
        public int $userId,
        public IsoWeek $week,
        public array $goals,
    ) {}

    /** @param array<string,int> $goals */
    public static function fromArray(int $userId, IsoWeek $week, array $goals): self
    {
        $normalized = [];
        foreach (StudyPlanStatus::cases() as $status) {
            $key = $status->value;
            $value = $goals[$key] ?? 0;
            if (!is_int($value)) {
                throw new DomainException('Goals must be integers.');
            }
            if ($value < 0) {
                throw new DomainException('Goals must be non-negative.');
            }
            $normalized[$key] = $value;
        }

        return new self($userId, $week, $normalized);
    }

    /** @return array<string,int> */
    public static function empty(): array
    {
        $base = [];
        foreach (StudyPlanStatus::cases() as $status) {
            $base[$status->value] = 0;
        }
        return $base;
    }
}

