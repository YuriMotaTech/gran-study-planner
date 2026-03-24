<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Logging;

final readonly class FileLogger
{
    public function __construct(private string $path) {}

    /** @param array<string,mixed> $context */
    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    /** @param array<string,mixed> $context */
    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    /** @param array<string,mixed> $context */
    private function write(string $level, string $message, array $context): void
    {
        $line = json_encode([
            'timestamp' => gmdate('c'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ]) . PHP_EOL;

        file_put_contents($this->path, $line, FILE_APPEND);
    }
}
