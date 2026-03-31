<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Infrastructure\RateLimiting;

use GranStudyPlanner\Infrastructure\RateLimiting\InMemoryRateLimiter;
use PHPUnit\Framework\TestCase;

final class InMemoryRateLimiterTest extends TestCase
{
    public function testBlocksAfterLimitIsReached(): void
    {
        $limiter = new InMemoryRateLimiter();

        $first = $limiter->attempt('user:1:/dashboard', 1, 60);
        self::assertTrue($first->allowed);
        self::assertSame(0, $first->retryAfterSeconds);
        self::assertSame(1, $first->limit);
        self::assertSame(0, $first->remaining);

        $second = $limiter->attempt('user:1:/dashboard', 1, 60);
        self::assertFalse($second->allowed);
        self::assertGreaterThanOrEqual(0, $second->retryAfterSeconds);
        self::assertSame(1, $second->limit);
        self::assertSame(0, $second->remaining);
    }
}

