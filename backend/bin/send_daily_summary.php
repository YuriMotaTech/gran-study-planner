<?php

declare(strict_types=1);

use GranStudyPlanner\Application\SendDailySummaryEmail\SendDailySummaryEmailUseCase;
use GranStudyPlanner\Infrastructure\Email\SymfonyEmailSender;
use GranStudyPlanner\Infrastructure\Persistence\MySQLStudyPlanRepository;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

require_once __DIR__ . '/../vendor/autoload.php';

$env = fn(string $key, string $default = ''): string => $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;

$enabledRaw = strtolower($env('DAILY_SUMMARY_ENABLED', 'true'));
$enabled = !in_array($enabledRaw, ['0', 'false', 'no', 'off'], true);
if (!$enabled) {
    fwrite(STDOUT, "Daily summary: disabled (DAILY_SUMMARY_ENABLED=false).\n");
    exit(0);
}

$mailTo = trim($env('MAIL_TO', ''));
$mailerDsn = trim($env('MAILER_DSN', ''));
if ($mailTo === '' || $mailerDsn === '') {
    fwrite(STDOUT, "Daily summary: skipped (set MAIL_TO and MAILER_DSN).\n");
    exit(0);
}

$fromAddress = $env('MAIL_FROM', 'noreply@localhost');
$fromName = $env('MAIL_FROM_NAME', 'Gran Study Planner');
$userId = (int) $env('DAILY_SUMMARY_USER_ID', '1');

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $env('DB_HOST', '127.0.0.1'), $env('DB_PORT', '3306'), $env('DB_NAME', 'gran_study')),
    $env('DB_USER', 'gran'),
    $env('DB_PASSWORD', 'gran'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$repository = new MySQLStudyPlanRepository($pdo);
$transport = Transport::fromDsn($mailerDsn);
$mailer = new Mailer($transport);
$sender = new SymfonyEmailSender($mailer, $fromAddress, $fromName);
$useCase = new SendDailySummaryEmailUseCase($repository, $sender, $userId, $mailTo);

$useCase->execute();
fwrite(STDOUT, "Daily summary: sent.\n");
