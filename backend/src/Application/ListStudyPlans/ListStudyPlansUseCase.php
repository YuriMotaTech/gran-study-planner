<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\ListStudyPlans;

use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;

final readonly class ListStudyPlansUseCase
{
    public function __construct(private StudyPlanRepositoryInterface $repository) {}

    /** @return array{items: array, total: int, page: int, per_page: int} */
    public function execute(ListStudyPlansInput $input): array
    {
        $status = $input->status !== null ? StudyPlanStatus::from($input->status) : null;
        $items = $this->repository->listByUser($input->userId, $status, $input->page, $input->perPage, $input->sortBy, $input->sortDirection);
        $total = $this->repository->countByUser($input->userId, $status);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $input->page,
            'per_page' => $input->perPage,
        ];
    }
}
