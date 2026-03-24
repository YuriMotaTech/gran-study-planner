<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Application;

use GranStudyPlanner\Application\CreateStudyPlan\CreateStudyPlanInput;
use GranStudyPlanner\Application\CreateStudyPlan\CreateStudyPlanUseCase;
use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use GranStudyPlanner\Domain\StudyPlan\IdGeneratorInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class CreateStudyPlanUseCaseTest extends TestCase
{
    public function testCreatesAndInvalidatesDashboardCache(): void
    {
        $repo = $this->createMock(StudyPlanRepositoryInterface::class);
        $idGenerator = $this->createMock(IdGeneratorInterface::class);
        $cache = $this->createMock(DashboardCacheInterface::class);

        $idGenerator->method('next')->willReturn('generated-id');
        $repo->expects(self::once())->method('save');
        $cache->expects(self::once())->method('invalidate')->with(1);

        $useCase = new CreateStudyPlanUseCase($repo, $idGenerator, $cache);
        $plan = $useCase->execute(new CreateStudyPlanInput(1, 'Study PHP', '2030-04-01 10:00:00'));

        self::assertSame('generated-id', $plan->id());
        self::assertSame('Study PHP', $plan->title());
    }
}
