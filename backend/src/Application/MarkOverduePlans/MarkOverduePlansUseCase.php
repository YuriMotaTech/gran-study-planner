<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\MarkOverduePlans;

use DateTimeImmutable;
use GranStudyPlanner\Domain\ActivityLog\ActivityEntityType;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventLogInterface;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventType;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;

final readonly class MarkOverduePlansUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private DashboardCacheInterface $dashboardCache,
        private ActivityEventLogInterface $activityLog,
    ) {}

    public function execute(DateTimeImmutable $now): int
    {
        $plans = $this->repository->findExpiredForOverdueMark($now);
        $updated = 0;

        foreach ($plans as $plan) {
            $from = $plan->status()->value;
            if ($plan->markOverdueIfExpired($now)) {
                $this->repository->save($plan);
                $this->dashboardCache->invalidate($plan->userId());
                $this->activityLog->record(
                    $plan->userId(),
                    ActivityEntityType::STUDY_PLAN,
                    $plan->id(),
                    ActivityEventType::MARKED_OVERDUE,
                    ['from' => $from],
                    $now,
                );
                $updated++;
            }
        }

        return $updated;
    }
}
