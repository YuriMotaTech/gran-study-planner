<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\ActivityLog;

enum ActivityEntityType: string
{
    case STUDY_PLAN = 'study_plan';
}
