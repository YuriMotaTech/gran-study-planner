<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\Dashboard;

use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;

final readonly class GetDashboardUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private DashboardCacheInterface $cache,
        private int $ttlSeconds,
    ) {}

    /** @return array<string,int> */
    public function execute(int $userId): array
    {
        $cached = $this->cache->get($userId);
        if ($cached !== null) {
            return $cached;
        }

        $stats = $this->repository->dashboardStatsByUser($userId);
        $this->cache->put($userId, $stats, $this->ttlSeconds);

        return $stats;
    }
}
