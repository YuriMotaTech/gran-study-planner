<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\UpdateStudyPlanStatus;

use DomainException;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;

final readonly class UpdateStudyPlanStatusUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private DashboardCacheInterface $dashboardCache,
    ) {}

    public function execute(UpdateStudyPlanStatusInput $input): void
    {
        $plan = $this->repository->findByIdAndUser($input->id, $input->userId);
        if ($plan === null) {
            throw new DomainException('Study plan not found.');
        }

        $plan->updateStatus(StudyPlanStatus::from($input->status));
        $this->repository->save($plan);
        $this->dashboardCache->invalidate($input->userId);
    }
}
