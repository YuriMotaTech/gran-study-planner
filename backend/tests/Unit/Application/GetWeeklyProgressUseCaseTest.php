<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Application;

use GranStudyPlanner\Application\WeeklyGoals\GetWeeklyProgressUseCase;
use GranStudyPlanner\Domain\WeeklyGoals\IsoWeek;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyGoalsRepositoryInterface;
use GranStudyPlanner\Domain\WeeklyGoals\WeeklyProgressRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class GetWeeklyProgressUseCaseTest extends TestCase
{
    public function testBuildsPercentagesFromCountsAndGoals(): void
    {
        $progress = $this->createMock(WeeklyProgressRepositoryInterface::class);
        $goals = $this->createMock(WeeklyGoalsRepositoryInterface::class);

        $week = IsoWeek::fromString('2026-W13');
        $progress->method('countsByUserAndWeek')->with(1, $week)->willReturn([
            'pending' => 2,
            'in_progress' => 0,
            'done' => 0,
            'overdue' => 0,
        ]);
        $goals->method('getByUserAndWeek')->willReturn(['pending' => 4, 'in_progress' => 0, 'done' => 0, 'overdue' => 0]);

        $useCase = new GetWeeklyProgressUseCase($progress, $goals);
        $result = $useCase->execute(1, $week);

        self::assertSame('2026-W13', $result['week']);
        self::assertSame(50, $result['percentages']['pending']);
        self::assertSame(0, $result['percentages']['done']);
    }
}
