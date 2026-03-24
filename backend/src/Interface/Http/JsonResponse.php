<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http;

final class JsonResponse
{
    /** @param array<string,mixed> $payload */
    public static function send(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
