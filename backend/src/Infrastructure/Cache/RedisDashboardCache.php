<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Cache;

use GranStudyPlanner\Domain\StudyPlan\DashboardCacheInterface;
use Redis;

final readonly class RedisDashboardCache implements DashboardCacheInterface
{
    public function __construct(private Redis $redis) {}

    public function get(int $userId): ?array
    {
        $raw = $this->redis->get($this->key($userId));
        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    public function put(int $userId, array $stats, int $ttlSeconds): void
    {
        $this->redis->setex($this->key($userId), $ttlSeconds, json_encode($stats) ?: '{}');
    }

    public function invalidate(int $userId): void
    {
        $this->redis->del($this->key($userId));
    }

    private function key(int $userId): string
    {
        return 'dashboard:stats:' . $userId;
    }
}
