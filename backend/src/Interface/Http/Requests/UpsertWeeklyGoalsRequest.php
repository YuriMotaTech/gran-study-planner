<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\Requests;

use DomainException;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;
use GranStudyPlanner\Interface\Http\Request;

final class UpsertWeeklyGoalsRequest
{
    /** @return array<string,int> */
    public static function from(Request $request): array
    {
        $goals = [];
        foreach (StudyPlanStatus::cases() as $status) {
            $key = $status->value;
            $raw = $request->body[$key] ?? 0;

            if (is_int($raw)) {
                $value = $raw;
            } elseif (is_string($raw) && preg_match('/^-?\d+$/', $raw) === 1) {
                $value = (int) $raw;
            } elseif ($raw === null) {
                $value = 0;
            } else {
                throw new DomainException(sprintf('%s must be an integer.', $key));
            }

            if ($value < 0) {
                throw new DomainException(sprintf('%s must be non-negative.', $key));
            }

            $goals[$key] = $value;
        }

        return $goals;
    }
}

