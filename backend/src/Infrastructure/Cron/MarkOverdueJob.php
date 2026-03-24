<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Cron;

use DateTimeImmutable;
use GranStudyPlanner\Application\MarkOverduePlans\MarkOverduePlansUseCase;

final readonly class MarkOverdueJob
{
    public function __construct(private MarkOverduePlansUseCase $useCase) {}

    public function run(): int
    {
        return $this->useCase->execute(new DateTimeImmutable());
    }
}
