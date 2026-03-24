<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http;

use DateTimeImmutable;
use DomainException;
use GranStudyPlanner\Application\Auth\LoginUseCase;
use GranStudyPlanner\Application\CreateStudyPlan\CreateStudyPlanInput;
use GranStudyPlanner\Application\CreateStudyPlan\CreateStudyPlanUseCase;
use GranStudyPlanner\Application\Dashboard\GetDashboardUseCase;
use GranStudyPlanner\Application\DeleteStudyPlan\DeleteStudyPlanUseCase;
use GranStudyPlanner\Application\ListStudyPlans\ListStudyPlansInput;
use GranStudyPlanner\Application\ListStudyPlans\ListStudyPlansUseCase;
use GranStudyPlanner\Application\UpdateStudyPlanStatus\UpdateStudyPlanStatusInput;
use GranStudyPlanner\Application\UpdateStudyPlanStatus\UpdateStudyPlanStatusUseCase;
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

            if ($request->path === '/study-plans' && $request->method === 'POST') {
                $plan = $this->createStudyPlanUseCase->execute(new CreateStudyPlanInput(
                    userId: $userId,
                    title: (string) ($request->body['title'] ?? ''),
                    deadline: (string) ($request->body['deadline'] ?? ''),
                ));
                JsonResponse::send(['data' => StudyPlanPresenter::one($plan)], 201);
                return;
            }

            if ($request->path === '/study-plans' && $request->method === 'GET') {
                $result = $this->listStudyPlansUseCase->execute(new ListStudyPlansInput(
                    userId: $userId,
                    status: isset($request->query['status']) ? (string) $request->query['status'] : null,
                    page: max(1, (int) ($request->query['page'] ?? 1)),
                    perPage: min(100, max(1, (int) ($request->query['perPage'] ?? 20))),
                    sortBy: (string) ($request->query['sortBy'] ?? 'deadline'),
                    sortDirection: (string) ($request->query['sortDirection'] ?? 'asc'),
                ));

                JsonResponse::send([
                    'items' => array_map(static fn($plan) => StudyPlanPresenter::one($plan), $result['items']),
                    'total' => $result['total'],
                    'page' => $result['page'],
                    'perPage' => $result['per_page'],
                ]);
                return;
            }

            if (preg_match('#^/study-plans/([a-zA-Z0-9]+)$#', $request->path, $matches) === 1 && $request->method === 'PATCH') {
                $this->updateStudyPlanStatusUseCase->execute(new UpdateStudyPlanStatusInput(
                    userId: $userId,
                    id: $matches[1],
                    status: (string) ($request->body['status'] ?? ''),
                ));
                JsonResponse::send(['status' => 'ok']);
                return;
            }

            if (preg_match('#^/study-plans/([a-zA-Z0-9]+)$#', $request->path, $matches) === 1 && $request->method === 'DELETE') {
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
}
