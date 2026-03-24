<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Application;

use DateTimeImmutable;
use GranStudyPlanner\Application\MarkOverduePlans\MarkOverduePlansUseCase;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlan;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class MarkOverduePlansUseCaseTest extends TestCase
{
    public function testMarksExpiredPlansAndInvalidatesCache(): void
    {
        $repo = $this->createMock(StudyPlanRepositoryInterface::class);
        $cache = $this->createMock(DashboardCacheInterface::class);

        $plan = new StudyPlan('plan-1', 10, 'Algorithms', new DateTimeImmutable('-2 days'));

        $repo->method('findExpiredForOverdueMark')->willReturn([$plan]);
        $repo->expects(self::once())->method('save')->with($plan);
        $cache->expects(self::once())->method('invalidate')->with(10);

        $useCase = new MarkOverduePlansUseCase($repo, $cache);
        $updated = $useCase->execute(new DateTimeImmutable());

        self::assertSame(1, $updated);
    }
}
