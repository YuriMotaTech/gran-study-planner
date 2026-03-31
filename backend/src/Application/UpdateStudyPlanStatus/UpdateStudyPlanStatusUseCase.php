<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\UpdateStudyPlanStatus;

use DomainException;
use GranStudyPlanner\Domain\ActivityLog\ActivityEntityType;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventLogInterface;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventType;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;

final readonly class UpdateStudyPlanStatusUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private DashboardCacheInterface $dashboardCache,
        private ActivityEventLogInterface $activityLog,
    ) {}

    public function execute(UpdateStudyPlanStatusInput $input): void
    {
        $plan = $this->repository->findByIdAndUser($input->id, $input->userId);
        if ($plan === null) {
            throw new DomainException('Study plan not found.');
        }

        $from = $plan->status()->value;
        $plan->updateStatus(StudyPlanStatus::from($input->status));
        $this->repository->save($plan);
        $this->dashboardCache->invalidate($input->userId);
        $this->activityLog->record(
            $input->userId,
            ActivityEntityType::STUDY_PLAN,
            $input->id,
            ActivityEventType::STATUS_CHANGED,
            ['from' => $from, 'to' => $plan->status()->value],
        );
    }
}
