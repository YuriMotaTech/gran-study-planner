<?php

declare(strict_types=1);

use GranStudyPlanner\Application\MarkOverduePlans\MarkOverduePlansUseCase;
use GranStudyPlanner\Infrastructure\Cache\NullDashboardCache;
use GranStudyPlanner\Infrastructure\Cron\MarkOverdueJob;
use GranStudyPlanner\Infrastructure\Persistence\MySQLActivityEventLogRepository;
use GranStudyPlanner\Infrastructure\Persistence\MySQLStudyPlanRepository;
use GranStudyPlanner\Infrastructure\Persistence\UuidGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$env = fn(string $key, string $default = ''): string => $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $env('DB_HOST', '127.0.0.1'), $env('DB_PORT', '3306'), $env('DB_NAME', 'gran_study')),
    $env('DB_USER', 'gran'),
    $env('DB_PASSWORD', 'gran'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$repository = new MySQLStudyPlanRepository($pdo);
$activityLog = new MySQLActivityEventLogRepository($pdo, new UuidGenerator());
$job = new MarkOverdueJob(new MarkOverduePlansUseCase($repository, new NullDashboardCache(), $activityLog));
$count = $job->run();
echo sprintf("Marked %d overdue plans.\n", $count);
