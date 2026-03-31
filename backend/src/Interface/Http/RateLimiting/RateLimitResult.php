<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\RateLimiting;

final readonly class RateLimitResult
{
    public function __construct(
        public bool $allowed,
        public int $retryAfterSeconds,
        public int $limit,
        public int $remaining,
    ) {}
}

