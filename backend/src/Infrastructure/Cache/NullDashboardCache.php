<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Cache;

use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;

final class NullDashboardCache implements DashboardCacheInterface
{
    public function get(int $userId): ?array
    {
        return null;
    }

    public function put(int $userId, array $stats, int $ttlSeconds): void
    {
    }

    public function invalidate(int $userId): void
    {
    }
}
