<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\DeleteStudyPlan;

use DomainException;
use GranStudyPlanner\Domain\ActivityLog\ActivityEntityType;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventLogInterface;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventType;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;

final readonly class DeleteStudyPlanUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private DashboardCacheInterface $dashboardCache,
        private ActivityEventLogInterface $activityLog,
    ) {}

    public function execute(int $userId, string $id): void
    {
        $plan = $this->repository->findByIdAndUser($id, $userId);
        if ($plan === null) {
            throw new DomainException('Study plan not found.');
        }

        $status = $plan->status()->value;
        if (!$this->repository->deleteByIdAndUser($id, $userId)) {
            throw new DomainException('Study plan not found.');
        }

        $this->dashboardCache->invalidate($userId);
        $this->activityLog->record(
            $userId,
            ActivityEntityType::STUDY_PLAN,
            $id,
            ActivityEventType::DELETED,
            ['status' => $status],
        );
    }
}
