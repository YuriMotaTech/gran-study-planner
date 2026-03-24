<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\StudyPlan;

enum StudyPlanStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case OVERDUE = 'overdue';
}
