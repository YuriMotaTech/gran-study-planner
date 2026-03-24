<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\MarkOverduePlans;

use DateTimeImmutable;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;

final readonly class MarkOverduePlansUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private DashboardCacheInterface $dashboardCache,
    ) {}

    public function execute(DateTimeImmutable $now): int
    {
        $plans = $this->repository->findExpiredForOverdueMark($now);
        $updated = 0;

        foreach ($plans as $plan) {
            if ($plan->markOverdueIfExpired($now)) {
                $this->repository->save($plan);
                $this->dashboardCache->invalidate($plan->userId());
                $updated++;
            }
        }

        return $updated;
    }
}
