<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\Auth;

interface TokenEncoderInterface
{
    public function encode(int $userId, int $ttlSeconds): string;

    public function decode(string $token): ?int;
}
