<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\Requests;

use DomainException;
use GranStudyPlanner\Application\ListStudyPlans\ListStudyPlansInput;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;
use GranStudyPlanner\Interface\Http\Request;

final class ListStudyPlansRequest
{
    public static function from(Request $request, int $userId): ListStudyPlansInput
    {
        $status = null;
        if (isset($request->query['status'])) {
            $raw = $request->query['status'];
            if (!is_string($raw)) {
                throw new DomainException('status must be a string.');
            }
            $raw = trim($raw);
            if ($raw !== '') {
                try {
                    StudyPlanStatus::from($raw);
                } catch (\ValueError) {
                    throw new DomainException('Invalid status.');
                }
                $status = $raw;
            }
        }

        $page = max(1, RequestValidation::intQuery($request->query, 'page', 1));
        $perPage = RequestValidation::intQuery($request->query, 'perPage', 20);
        $perPage = min(100, max(1, $perPage));

        $sortBy = isset($request->query['sortBy']) && is_string($request->query['sortBy'])
            ? trim($request->query['sortBy'])
            : 'deadline';
        $allowedSort = ['deadline', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'deadline';
        }

        $sortDirection = isset($request->query['sortDirection']) && is_string($request->query['sortDirection'])
            ? strtolower(trim($request->query['sortDirection']))
            : 'asc';
        if (!in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'asc';
        }

        return new ListStudyPlansInput(
            userId: $userId,
            status: $status,
            page: $page,
            perPage: $perPage,
            sortBy: $sortBy,
            sortDirection: $sortDirection,
        );
    }
}

