<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\DeleteStudyPlan;

use DomainException;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;

final readonly class DeleteStudyPlanUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private DashboardCacheInterface $dashboardCache,
    ) {}

    public function execute(int $userId, string $id): void
    {
        if (!$this->repository->deleteByIdAndUser($id, $userId)) {
            throw new DomainException('Study plan not found.');
        }

        $this->dashboardCache->invalidate($userId);
    }
}
