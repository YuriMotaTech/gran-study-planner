<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\StudyPlan;

use DateTimeImmutable;

interface StudyPlanRepositoryInterface
{
    public function save(StudyPlan $studyPlan): void;

    /** @return StudyPlan[] */
    public function listByUser(int $userId, ?StudyPlanStatus $status, int $page, int $perPage, string $sortBy, string $sortDirection): array;

    public function countByUser(int $userId, ?StudyPlanStatus $status): int;

    public function findByIdAndUser(string $id, int $userId): ?StudyPlan;

    public function deleteByIdAndUser(string $id, int $userId): bool;

    /** @return StudyPlan[] */
    public function findExpiredForOverdueMark(DateTimeImmutable $now): array;

    /** @return array<string,int> */
    public function dashboardStatsByUser(int $userId): array;
}
