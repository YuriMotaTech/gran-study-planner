<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\StudyPlan;

interface DashboardCacheInterface
{
    /** @return array<string,int>|null */
    public function get(int $userId): ?array;

    /** @param array<string,int> $stats */
    public function put(int $userId, array $stats, int $ttlSeconds): void;

    public function invalidate(int $userId): void;
}
