<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Domain\WeeklyGoals;

use DomainException;
use GranStudyPlanner\Domain\WeeklyGoals\IsoWeek;
use PHPUnit\Framework\TestCase;

final class IsoWeekTest extends TestCase
{
    public function testParsesValidIsoWeek(): void
    {
        $w = IsoWeek::fromString('2026-W13');
        self::assertSame('2026-W13', $w->value);
        $range = $w->range();
        self::assertArrayHasKey('start', $range);
        self::assertArrayHasKey('end', $range);
        self::assertTrue($range['end'] > $range['start']);
    }

    public function testRejectsInvalidFormat(): void
    {
        $this->expectException(DomainException::class);
        IsoWeek::fromString('2026-13');
    }
}

