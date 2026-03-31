<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\RateLimiting;

use GranStudyPlanner\Interface\Http\RateLimiting\RateLimitResult;
use GranStudyPlanner\Interface\Http\RateLimiting\RateLimiterInterface;
use Redis;

final readonly class RedisRateLimiter implements RateLimiterInterface
{
    public function __construct(private Redis $redis) {}

    public function attempt(string $key, int $limit, int $windowSeconds): RateLimitResult
    {
        $windowSeconds = max(1, $windowSeconds);
        $now = time();
        $bucketKey = "rl:{$key}";

        $this->redis->multi();
        $this->redis->incr($bucketKey);
        $this->redis->ttl($bucketKey);
        /** @var array{0:int|string,1:int|string|false} $res */
        $res = $this->redis->exec() ?: [0, -1];

        $count = (int) $res[0];
        $ttl = $res[1];

        // If this is the first hit, or TTL is missing, enforce an expiration.
        if ($count === 1 || $ttl === -1 || $ttl === false) {
            $this->redis->expire($bucketKey, $windowSeconds);
            $ttl = $windowSeconds;
        }

        $allowed = $count <= $limit;
        $retryAfter = $allowed ? 0 : max(0, (int) $ttl);
        $remaining = $allowed ? max(0, $limit - $count) : 0;

        return new RateLimitResult(
            allowed: $allowed,
            retryAfterSeconds: $retryAfter,
            limit: $limit,
            remaining: $remaining,
        );
    }
}

