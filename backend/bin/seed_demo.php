<?php

declare(strict_types=1);

/**
 * Idempotent demo seed for the default demo user (user_id = 1, same as LoginUseCase).
 * Removes previous rows whose titles start with "[demo]" for that user, then inserts sample data.
 */

$env = fn(string $key, string $default = ''): string => $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $env('DB_HOST', '127.0.0.1'), $env('DB_PORT', '3306'), $env('DB_NAME', 'gran_study')),
    $env('DB_USER', 'gran'),
    $env('DB_PASSWORD', 'gran'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

const DEMO_USER_ID = 1;
const DEMO_TITLE_PREFIX = '[demo]';

$now = new DateTimeImmutable('now');
$stamp = $now->format('Y-m-d H:i:s');

$pdo->beginTransaction();

try {
    $deletePlans = $pdo->prepare('DELETE FROM study_plans WHERE user_id = :uid AND title LIKE :prefix');
    $deletePlans->execute([
        'uid' => DEMO_USER_ID,
        'prefix' => DEMO_TITLE_PREFIX . '%',
    ]);

    $insertPlan = $pdo->prepare(
        'INSERT INTO study_plans (id, user_id, title, deadline, status, created_at, updated_at)
         VALUES (:id, :user_id, :title, :deadline, :status, :created_at, :updated_at)'
    );

    $planRows = [
        [
            'title' => DEMO_TITLE_PREFIX . ' Direito Constitucional — art. 5º',
            'deadline' => $now->modify('+1 day'),
            'status' => 'pending',
        ],
        [
            'title' => DEMO_TITLE_PREFIX . ' Raciocínio lógico — conjuntos',
            'deadline' => $now->modify('+3 days'),
            'status' => 'in_progress',
        ],
        [
            'title' => DEMO_TITLE_PREFIX . ' Português — concordância',
            'deadline' => $now->modify('+7 days'),
            'status' => 'pending',
        ],
        [
            'title' => DEMO_TITLE_PREFIX . ' Informática — hardware (revisão)',
            'deadline' => $now->modify('-1 day'),
            'status' => 'overdue',
        ],
        [
            'title' => DEMO_TITLE_PREFIX . ' Atualidades — mês anterior',
            'deadline' => $now->modify('-2 days'),
            'status' => 'done',
        ],
    ];

    foreach ($planRows as $row) {
        $id = bin2hex(random_bytes(16));
        $deadline = $row['deadline']->format('Y-m-d H:i:s');
        $insertPlan->execute([
            'id' => $id,
            'user_id' => DEMO_USER_ID,
            'title' => $row['title'],
            'deadline' => $deadline,
            'status' => $row['status'],
            'created_at' => $stamp,
            'updated_at' => $stamp,
        ]);
    }

    $isoWeek = $now->format('o-\WW');

    $pdo->prepare('DELETE FROM weekly_goals WHERE user_id = :uid AND iso_year_week = :week')->execute([
        'uid' => DEMO_USER_ID,
        'week' => $isoWeek,
    ]);

    $insertGoals = $pdo->prepare(
        'INSERT INTO weekly_goals (user_id, iso_year_week, goal_pending, goal_in_progress, goal_done, goal_overdue, created_at, updated_at)
         VALUES (:user_id, :iso_year_week, :gp, :gip, :gd, :go, :created_at, :updated_at)'
    );
    $insertGoals->execute([
        'user_id' => DEMO_USER_ID,
        'iso_year_week' => $isoWeek,
        'gp' => 4,
        'gip' => 2,
        'gd' => 3,
        'go' => 1,
        'created_at' => $stamp,
        'updated_at' => $stamp,
    ]);

    $pdo->commit();
    echo 'Demo seed complete (study_plans + weekly_goals for current week).' . PHP_EOL;
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
