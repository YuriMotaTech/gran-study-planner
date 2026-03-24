<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\ListStudyPlans;

final readonly class ListStudyPlansInput
{
    public function __construct(
        public int $userId,
        public ?string $status = null,
        public int $page = 1,
        public int $perPage = 20,
        public string $sortBy = 'deadline',
        public string $sortDirection = 'asc',
    ) {}
}
