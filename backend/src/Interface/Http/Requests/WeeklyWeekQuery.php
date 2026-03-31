<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\Requests;

use DomainException;
use GranStudyPlanner\Domain\WeeklyGoals\IsoWeek;
use GranStudyPlanner\Interface\Http\Request;

final class WeeklyWeekQuery
{
    public static function from(Request $request): IsoWeek
    {
        if (!isset($request->query['week'])) {
            return IsoWeek::current();
        }

        $raw = $request->query['week'];
        if (!is_string($raw)) {
            throw new DomainException('week must be a string.');
        }

        return IsoWeek::fromString($raw);
    }
}

