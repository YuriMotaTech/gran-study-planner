<?php

declare(strict_types=1);

$env = fn(string $key, string $default = ''): string => $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $env('DB_HOST', '127.0.0.1'), $env('DB_PORT', '3306'), $env('DB_NAME', 'gran_study')),
    $env('DB_USER', 'gran'),
    $env('DB_PASSWORD', 'gran'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$dir = __DIR__ . '/../database/migrations';
$files = glob($dir . '/*.sql') ?: [];
sort($files);

if ($files === []) {
    throw new RuntimeException('No migration files found.');
}

foreach ($files as $file) {
    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException('Migration file not found: ' . $file);
    }
    $pdo->exec($sql);
}

echo "Migration complete.\n";
