<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http;

final readonly class Request
{
    /** @param array<string,mixed> $body */
    public function __construct(
        public string $method,
        public string $path,
        public array $query,
        public array $body,
        public array $headers,
    ) {}

    public static function fromGlobals(): self
    {
        $raw = file_get_contents('php://input') ?: '';
        $decoded = json_decode($raw, true);
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        return new self(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            path: parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/',
            query: $_GET,
            body: is_array($decoded) ? $decoded : [],
            headers: is_array($headers) ? $headers : [],
        );
    }
}
