<?php

declare(strict_types=1);

use GranStudyPlanner\Application\Auth\LoginUseCase;
use GranStudyPlanner\Application\CreateStudyPlan\CreateStudyPlanUseCase;
use GranStudyPlanner\Application\Dashboard\GetDashboardUseCase;
use GranStudyPlanner\Application\DeleteStudyPlan\DeleteStudyPlanUseCase;
use GranStudyPlanner\Application\ListStudyPlans\ListStudyPlansUseCase;
use GranStudyPlanner\Application\MarkOverduePlans\MarkOverduePlansUseCase;
use GranStudyPlanner\Application\UpdateStudyPlanStatus\UpdateStudyPlanStatusUseCase;
use GranStudyPlanner\Interface\Http\AuthMiddleware;
use GranStudyPlanner\Interface\Http\Kernel;
use GranStudyPlanner\Interface\Http\Request;
use GranStudyPlanner\Infrastructure\Auth\SimpleJwtTokenEncoder;
use GranStudyPlanner\Infrastructure\Cache\NullDashboardCache;
use GranStudyPlanner\Infrastructure\Cache\RedisDashboardCache;
use GranStudyPlanner\Infrastructure\Logging\FileLogger;
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

$cache = new NullDashboardCache();
if (class_exists('Redis')) {
    try {
        $redis = new Redis();
        $redis->connect($env('REDIS_HOST', '127.0.0.1'), (int) $env('REDIS_PORT', '6379'));
        $cache = new RedisDashboardCache($redis);
    } catch (Throwable) {
    }
}

$tokenEncoder = new SimpleJwtTokenEncoder($env('AUTH_JWT_SECRET', 'dev-secret'));
$logger = new FileLogger(__DIR__ . '/../storage/logs/app.log');

$kernel = new Kernel(
    loginUseCase: new LoginUseCase(
        tokenEncoder: $tokenEncoder,
        defaultEmail: 'candidate@gran.com',
        defaultPassword: 'gran123',
        defaultUserId: 1,
        ttlSeconds: (int) $env('AUTH_TOKEN_TTL', '3600'),
    ),
    createStudyPlanUseCase: new CreateStudyPlanUseCase($repository, new UuidGenerator(), $cache),
    listStudyPlansUseCase: new ListStudyPlansUseCase($repository),
    updateStudyPlanStatusUseCase: new UpdateStudyPlanStatusUseCase($repository, $cache),
    deleteStudyPlanUseCase: new DeleteStudyPlanUseCase($repository, $cache),
    getDashboardUseCase: new GetDashboardUseCase($repository, $cache, (int) $env('DASHBOARD_CACHE_TTL', '120')),
    auth: new AuthMiddleware($tokenEncoder),
    logger: $logger,
);

$kernel->handle(Request::fromGlobals());
