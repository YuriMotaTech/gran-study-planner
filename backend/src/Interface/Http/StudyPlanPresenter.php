<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http;

use GranStudyPlanner\Domain\StudyPlan\StudyPlan;

final class StudyPlanPresenter
{
    /** @return array<string,mixed> */
    public static function one(StudyPlan $plan): array
    {
        return [
            'id' => $plan->id(),
            'userId' => $plan->userId(),
            'title' => $plan->title(),
            'deadline' => $plan->deadline()->format(DATE_ATOM),
            'status' => $plan->status()->value,
            'createdAt' => $plan->createdAt()->format(DATE_ATOM),
            'updatedAt' => $plan->updatedAt()->format(DATE_ATOM),
        ];
    }
}
