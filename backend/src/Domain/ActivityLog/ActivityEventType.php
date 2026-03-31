<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\ActivityLog;

enum ActivityEventType: string
{
    case CREATED = 'created';
    case STATUS_CHANGED = 'status_changed';
    case DELETED = 'deleted';
    case MARKED_OVERDUE = 'marked_overdue';
}
