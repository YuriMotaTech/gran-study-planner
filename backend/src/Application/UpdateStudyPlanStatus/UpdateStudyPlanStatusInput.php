<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\UpdateStudyPlanStatus;

final readonly class UpdateStudyPlanStatusInput
{
    public function __construct(
        public int $userId,
        public string $id,
        public string $status,
    ) {}
}
