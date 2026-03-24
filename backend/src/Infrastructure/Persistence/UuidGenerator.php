<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Persistence;

use GranStudyPlanner\Domain\StudyPlan\IdGeneratorInterface;

final class UuidGenerator implements IdGeneratorInterface
{
    public function next(): string
    {
        return bin2hex(random_bytes(16));
    }
}
