<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http;

use DateTimeImmutable;
use DomainException;
use GranStudyPlanner\Application\Auth\LoginUseCase;
use GranStudyPlanner\Application\CreateStudyPlan\CreateStudyPlanUseCase;
use GranStudyPlanner\Application\Dashboard\GetDashboardUseCase;
use GranStudyPlanner\Application\DeleteStudyPlan\DeleteStudyPlanUseCase;
use GranStudyPlanner\Application\ListStudyPlans\ListStudyPlansUseCase;
use GranStudyPlanner\Application\UpdateStudyPlanStatus\UpdateStudyPlanStatusUseCase;
use GranStudyPlanner\Interface\Http\RateLimiting\RateLimiterInterface;
use GranStudyPlanner\Interface\Http\Requests\CreateStudyPlanRequest;
use GranStudyPlanner\Interface\Http\Requests\DeleteStudyPlanRequest;
use GranStudyPlanner\Interface\Http\Requests\ListStudyPlansRequest;
use GranStudyPlanner\Interface\Http\Requests\UpdateStudyPlanStatusRequest;
use GranStudyPlanner\Infrastructure\Logging\FileLogger;
use Throwable;

final readonly class Kernel
{
    public function __construct(
        private LoginUseCase $loginUseCase,
        private CreateStudyPlanUseCase $createStudyPlanUseCase,
        private ListStudyPlansUseCase $listStudyPlansUseCase,
        private UpdateStudyPlanStatusUseCase $updateStudyPlanStatusUseCase,
        private DeleteStudyPlanUseCase $deleteStudyPlanUseCase,
        private GetDashboardUseCase $getDashboardUseCase,
        private AuthMiddleware $auth,
        private RateLimiterInterface $rateLimiter,
        private FileLogger $logger,
    ) {}

    public function handle(Request $request): void
    {
        $requestId = bin2hex(random_bytes(8));

        try {
            if ($request->path === '/health' && $request->method === 'GET') {
                JsonResponse::send(['status' => 'ok', 'time' => (new DateTimeImmutable())->format(DATE_ATOM)]);
                return;
            }

            if ($request->path === '/auth/login' && $request->method === 'POST') {
                $token = $this->loginUseCase->execute(
                    (string) ($request->body['email'] ?? ''),
                    (string) ($request->body['password'] ?? ''),
                );
                JsonResponse::send(['token' => $token]);
                return;
            }

            $userId = $this->auth->userId($request);
            if ($userId === null) {
                JsonResponse::send(['error' => 'Unauthorized', 'requestId' => $requestId], 401);
                return;
            }

            $rateLimit = $this->rateLimitPolicy($request);
            if ($rateLimit !== null) {
                $result = $this->rateLimiter->attempt(
                    key: sprintf('%d:%s:%s', $userId, $request->method, $rateLimit['routeKey']),
                    limit: $rateLimit['limit'],
                    windowSeconds: $rateLimit['windowSeconds'],
                );
                if (!$result->allowed) {
                    JsonResponse::send(
                        [
                            'error' => 'Too Many Requests',
                            'requestId' => $requestId,
                            'retryAfterSeconds' => $result->retryAfterSeconds,
                        ],
                        429,
                        ['Retry-After' => (string) $result->retryAfterSeconds],
                    );
                    return;
                }
            }

            if ($request->path === '/study-plans' && $request->method === 'POST') {
                $input = CreateStudyPlanRequest::from($request, $userId);
                $plan = $this->createStudyPlanUseCase->execute($input);
                JsonResponse::send(['data' => StudyPlanPresenter::one($plan)], 201);
                return;
            }

            if ($request->path === '/study-plans' && $request->method === 'GET') {
                $input = ListStudyPlansRequest::from($request, $userId);
                $result = $this->listStudyPlansUseCase->execute($input);

                JsonResponse::send([
                    'items' => array_map(static fn($plan) => StudyPlanPresenter::one($plan), $result['items']),
                    'total' => $result['total'],
                    'page' => $result['page'],
                    'perPage' => $result['per_page'],
                ]);
                return;
            }

            if (preg_match('#^/study-plans/([a-zA-Z0-9]+)$#', $request->path, $matches) === 1 && $request->method === 'PATCH') {
                $input = UpdateStudyPlanStatusRequest::from($request, $userId, $matches[1]);
                $this->updateStudyPlanStatusUseCase->execute($input);
                JsonResponse::send(['status' => 'ok']);
                return;
            }

            if (preg_match('#^/study-plans/([a-zA-Z0-9]+)$#', $request->path, $matches) === 1 && $request->method === 'DELETE') {
                DeleteStudyPlanRequest::validate($matches[1]);
                $this->deleteStudyPlanUseCase->execute($userId, $matches[1]);
                JsonResponse::send(['status' => 'ok']);
                return;
            }

            if ($request->path === '/dashboard' && $request->method === 'GET') {
                $stats = $this->getDashboardUseCase->execute($userId);
                JsonResponse::send(['stats' => $stats]);
                return;
            }

            JsonResponse::send(['error' => 'Not found', 'requestId' => $requestId], 404);
        } catch (DomainException $e) {
            JsonResponse::send(['error' => $e->getMessage(), 'requestId' => $requestId], 422);
        } catch (Throwable $e) {
            $this->logger->error('Unhandled error', ['requestId' => $requestId, 'message' => $e->getMessage()]);
            JsonResponse::send(['error' => 'Internal server error', 'requestId' => $requestId], 500);
        }
    }

    /** @return array{routeKey:string,limit:int,windowSeconds:int}|null */
    private function rateLimitPolicy(Request $request): ?array
    {
        // Defaults: simple per-minute limits, per user + routeKey.
        if ($request->path === '/study-plans' && $request->method === 'GET') {
            return ['routeKey' => '/study-plans', 'limit' => 60, 'windowSeconds' => 60];
        }
        if ($request->path === '/dashboard' && $request->method === 'GET') {
            return ['routeKey' => '/dashboard', 'limit' => 60, 'windowSeconds' => 60];
        }
        if ($request->path === '/study-plans' && $request->method === 'POST') {
            return ['routeKey' => '/study-plans', 'limit' => 30, 'windowSeconds' => 60];
        }
        if (preg_match('#^/study-plans/[a-zA-Z0-9]+$#', $request->path) === 1 && $request->method === 'PATCH') {
            return ['routeKey' => '/study-plans/{id}:patch', 'limit' => 30, 'windowSeconds' => 60];
        }
        if (preg_match('#^/study-plans/[a-zA-Z0-9]+$#', $request->path) === 1 && $request->method === 'DELETE') {
            return ['routeKey' => '/study-plans/{id}:delete', 'limit' => 30, 'windowSeconds' => 60];
        }

        return null;
    }
}
