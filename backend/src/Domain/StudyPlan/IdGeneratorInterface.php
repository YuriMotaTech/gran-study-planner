<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\StudyPlan;

interface IdGeneratorInterface
{
    public function next(): string;
}
