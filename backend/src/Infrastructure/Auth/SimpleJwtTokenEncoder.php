<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Auth;

use GranStudyPlanner\Application\Auth\TokenEncoderInterface;

final readonly class SimpleJwtTokenEncoder implements TokenEncoderInterface
{
    public function __construct(private string $secret) {}

    public function encode(int $userId, int $ttlSeconds): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']) ?: '{}');
        $payload = $this->base64UrlEncode(json_encode([
            'sub' => $userId,
            'exp' => time() + $ttlSeconds,
        ]) ?: '{}');
        $signature = hash_hmac('sha256', $header . '.' . $payload, $this->secret, true);

        return $header . '.' . $payload . '.' . $this->base64UrlEncode($signature);
    }

    public function decode(string $token): ?int
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;
        $expected = $this->base64UrlEncode(hash_hmac('sha256', $header . '.' . $payload, $this->secret, true));
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $decoded = json_decode($this->base64UrlDecode($payload), true);
        if (!is_array($decoded) || !isset($decoded['sub'], $decoded['exp'])) {
            return null;
        }

        if ((int) $decoded['exp'] < time()) {
            return null;
        }

        return (int) $decoded['sub'];
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        return base64_decode(strtr($value, '-_', '+/')) ?: '';
    }
}
