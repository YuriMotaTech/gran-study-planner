<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Domain;

use DateTimeImmutable;
use DomainException;
use GranStudyPlanner\Domain\StudyPlan\StudyPlan;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;
use PHPUnit\Framework\TestCase;

final class StudyPlanTest extends TestCase
{
    public function testCreatesWithPendingStatus(): void
    {
        $plan = new StudyPlan('id-1', 1, 'PHP study', new DateTimeImmutable('+7 days'));

        self::assertSame(StudyPlanStatus::PENDING, $plan->status());
    }

    public function testMarksAsOverdueWhenDeadlinePassed(): void
    {
        $plan = new StudyPlan('id-2', 1, 'React study', new DateTimeImmutable('-1 day'));

        $updated = $plan->markOverdueIfExpired(new DateTimeImmutable());

        self::assertTrue($updated);
        self::assertSame(StudyPlanStatus::OVERDUE, $plan->status());
    }

    public function testOverdueNeedsInProgressBeforeDone(): void
    {
        $plan = new StudyPlan('id-3', 1, 'Node study', new DateTimeImmutable('-1 day'), StudyPlanStatus::OVERDUE);

        $this->expectException(DomainException::class);
        $plan->updateStatus(StudyPlanStatus::DONE);
    }
}
