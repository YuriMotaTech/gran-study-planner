<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\RateLimiting;

interface RateLimiterInterface
{
    public function attempt(string $key, int $limit, int $windowSeconds): RateLimitResult;
}

