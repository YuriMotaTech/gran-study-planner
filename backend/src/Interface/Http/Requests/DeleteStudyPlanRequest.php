<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\Requests;

use DomainException;

final class DeleteStudyPlanRequest
{
    public static function validate(string $id): void
    {
        if (preg_match('/^[a-zA-Z0-9]+$/', $id) !== 1) {
            throw new DomainException('Invalid id.');
        }
    }
}

