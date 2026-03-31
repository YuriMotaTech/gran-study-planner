<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\CreateStudyPlan;

use DateTimeImmutable;
use GranStudyPlanner\Domain\ActivityLog\ActivityEntityType;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventLogInterface;
use GranStudyPlanner\Domain\ActivityLog\ActivityEventType;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\IdGeneratorInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlan;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;

final readonly class CreateStudyPlanUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private IdGeneratorInterface $idGenerator,
        private DashboardCacheInterface $dashboardCache,
        private ActivityEventLogInterface $activityLog,
    ) {}

    public function execute(CreateStudyPlanInput $input): StudyPlan
    {
        $plan = new StudyPlan(
            id: $this->idGenerator->next(),
            userId: $input->userId,
            title: $input->title,
            deadline: new DateTimeImmutable($input->deadline),
        );

        $this->repository->save($plan);
        $this->dashboardCache->invalidate($input->userId);
        $this->activityLog->record(
            $input->userId,
            ActivityEntityType::STUDY_PLAN,
            $plan->id(),
            ActivityEventType::CREATED,
            ['status' => $plan->status()->value],
        );

        return $plan;
    }
}
