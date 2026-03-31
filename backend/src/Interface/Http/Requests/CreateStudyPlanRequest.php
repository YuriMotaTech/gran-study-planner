<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\Requests;

use GranStudyPlanner\Application\CreateStudyPlan\CreateStudyPlanInput;
use GranStudyPlanner\Interface\Http\Request;

final class CreateStudyPlanRequest
{
    public static function from(Request $request, int $userId): CreateStudyPlanInput
    {
        $title = RequestValidation::requiredString($request->body, 'title');
        $deadline = RequestValidation::requiredString($request->body, 'deadline');
        $deadline = RequestValidation::parseDateTime($deadline, 'deadline');

        return new CreateStudyPlanInput(
            userId: $userId,
            title: $title,
            deadline: $deadline,
        );
    }
}

