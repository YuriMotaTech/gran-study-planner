<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\RateLimiting;

use GranStudyPlanner\Interface\Http\RateLimiting\RateLimitResult;
use GranStudyPlanner\Interface\Http\RateLimiting\RateLimiterInterface;

/**
 * Best-effort fallback limiter (per PHP process).
 * Intended for local/dev and for safe operation when Redis is unavailable.
 */
final class InMemoryRateLimiter implements RateLimiterInterface
{
    /** @var array<string, array{resetAt:int, count:int}> */
    private static array $buckets = [];

    public function attempt(string $key, int $limit, int $windowSeconds): RateLimitResult
    {
        $now = time();
        $bucket = self::$buckets[$key] ?? null;

        if ($bucket === null || $bucket['resetAt'] <= $now) {
            $bucket = ['resetAt' => $now + max(1, $windowSeconds), 'count' => 0];
        }

        $nextCount = $bucket['count'] + 1;
        $allowed = $nextCount <= $limit;
        $bucket['count'] = $allowed ? $nextCount : $bucket['count'];
        self::$buckets[$key] = $bucket;

        $retryAfter = max(0, $bucket['resetAt'] - $now);
        $remaining = $allowed ? max(0, $limit - $bucket['count']) : 0;

        return new RateLimitResult(
            allowed: $allowed,
            retryAfterSeconds: $allowed ? 0 : $retryAfter,
            limit: $limit,
            remaining: $remaining,
        );
    }
}

