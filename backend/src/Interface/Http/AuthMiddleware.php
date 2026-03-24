<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http;

use GranStudyPlanner\Application\Auth\TokenEncoderInterface;

final readonly class AuthMiddleware
{
    public function __construct(private TokenEncoderInterface $tokenEncoder) {}

    public function userId(Request $request): ?int
    {
        $auth = $request->headers['Authorization'] ?? $request->headers['authorization'] ?? null;
        if (!is_string($auth) || !str_starts_with($auth, 'Bearer ')) {
            return null;
        }

        return $this->tokenEncoder->decode(substr($auth, 7));
    }
}
