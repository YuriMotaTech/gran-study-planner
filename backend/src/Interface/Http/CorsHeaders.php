<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http;

/**
 * Optional CORS for browser clients (e.g. Vite on another port than the API).
 * Set CORS_ALLOW_ORIGIN to a single origin or comma-separated list (first match wins for response).
 */
final class CorsHeaders
{
    public static function applyResponseOrigin(): void
    {
        $raw = self::rawOriginList();
        if ($raw === '') {
            return;
        }
        $origin = self::resolveAllowedOrigin($raw);
        if ($origin === null) {
            return;
        }
        header('Access-Control-Allow-Origin: ' . $origin);
    }

    public static function sendPreflightNoContent(): void
    {
        $raw = self::rawOriginList();
        if ($raw === '') {
            return;
        }
        $origin = self::resolveAllowedOrigin($raw);
        if ($origin === null) {
            return;
        }
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Max-Age: 86400');
        http_response_code(204);
    }

    private static function rawOriginList(): string
    {
        $v = getenv('CORS_ALLOW_ORIGIN');
        if ($v === false || $v === '') {
            return '';
        }

        return trim($v);
    }

    /** @return string|null Origin value to echo, or null if CORS disabled */
    private static function resolveAllowedOrigin(string $raw): ?string
    {
        $allowed = array_map(trim(...), explode(',', $raw));
        $allowed = array_values(array_filter($allowed, static fn(string $o): bool => $o !== ''));
        if ($allowed === []) {
            return null;
        }
        $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (is_string($requestOrigin) && $requestOrigin !== '' && in_array($requestOrigin, $allowed, true)) {
            return $requestOrigin;
        }

        return $allowed[0];
    }
}
