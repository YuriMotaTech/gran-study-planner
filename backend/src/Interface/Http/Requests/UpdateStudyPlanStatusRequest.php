<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\Requests;

use DomainException;
use GranStudyPlanner\Application\UpdateStudyPlanStatus\UpdateStudyPlanStatusInput;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;
use GranStudyPlanner\Interface\Http\Request;

final class UpdateStudyPlanStatusRequest
{
    public static function from(Request $request, int $userId, string $id): UpdateStudyPlanStatusInput
    {
        if (preg_match('/^[a-zA-Z0-9]+$/', $id) !== 1) {
            throw new DomainException('Invalid id.');
        }

        $status = RequestValidation::requiredString($request->body, 'status');
        try {
            StudyPlanStatus::from($status);
        } catch (\ValueError) {
            throw new DomainException('Invalid status.');
        }

        return new UpdateStudyPlanStatusInput(
            userId: $userId,
            id: $id,
            status: $status,
        );
    }
}

