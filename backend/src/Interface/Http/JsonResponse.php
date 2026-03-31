<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http;

final class JsonResponse
{
    /** @param array<string,mixed> $payload */
    /** @param array<string,string> $headers */
    public static function send(array $payload, int $status = 200, array $headers = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
