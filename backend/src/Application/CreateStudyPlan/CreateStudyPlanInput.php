<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\CreateStudyPlan;

final readonly class CreateStudyPlanInput
{
    public function __construct(
        public int $userId,
        public string $title,
        public string $deadline,
    ) {}
}
