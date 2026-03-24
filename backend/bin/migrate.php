<?php

declare(strict_types=1);

$env = fn(string $key, string $default = ''): string => $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $env('DB_HOST', '127.0.0.1'), $env('DB_PORT', '3306'), $env('DB_NAME', 'gran_study')),
    $env('DB_USER', 'gran'),
    $env('DB_PASSWORD', 'gran'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = file_get_contents(__DIR__ . '/../database/migrations/001_create_study_plans.sql');
if ($sql === false) {
    throw new RuntimeException('Migration file not found.');
}

$pdo->exec($sql);
echo "Migration complete.\n";
