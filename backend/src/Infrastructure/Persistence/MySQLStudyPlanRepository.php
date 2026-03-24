<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Persistence;

use DateTimeImmutable;
use GranStudyPlanner\Domain\StudyPlan\StudyPlan;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanStatus;
use PDO;

final readonly class MySQLStudyPlanRepository implements StudyPlanRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function save(StudyPlan $studyPlan): void
    {
        $sql = <<<'SQL'
            INSERT INTO study_plans (id, user_id, title, deadline, status, created_at, updated_at)
            VALUES (:id, :user_id, :title, :deadline, :status, :created_at, :updated_at)
            ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                deadline = VALUES(deadline),
                status = VALUES(status),
                updated_at = VALUES(updated_at)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $studyPlan->id(),
            'user_id' => $studyPlan->userId(),
            'title' => $studyPlan->title(),
            'deadline' => $studyPlan->deadline()->format('Y-m-d H:i:s'),
            'status' => $studyPlan->status()->value,
            'created_at' => $studyPlan->createdAt()->format('Y-m-d H:i:s'),
            'updated_at' => $studyPlan->updatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function listByUser(int $userId, ?StudyPlanStatus $status, int $page, int $perPage, string $sortBy, string $sortDirection): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $allowedSort = ['deadline', 'status', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSort, true) ? $sortBy : 'deadline';
        $sortDirection = strtolower($sortDirection) === 'desc' ? 'DESC' : 'ASC';

        $sql = 'SELECT * FROM study_plans WHERE user_id = :user_id';
        $params = ['user_id' => $userId];

        if ($status !== null) {
            $sql .= ' AND status = :status';
            $params['status'] = $status->value;
        }

        $sql .= sprintf(' ORDER BY %s %s LIMIT :limit OFFSET :offset', $sortBy, $sortDirection);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
        if ($status !== null) {
            $stmt->bindValue('status', $status->value, PDO::PARAM_STR);
        }
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn(array $row): StudyPlan => $this->hydrate($row), $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
    }

    public function countByUser(int $userId, ?StudyPlanStatus $status): int
    {
        $sql = 'SELECT COUNT(*) FROM study_plans WHERE user_id = :user_id';
        $params = ['user_id' => $userId];

        if ($status !== null) {
            $sql .= ' AND status = :status';
            $params['status'] = $status->value;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findByIdAndUser(string $id, int $userId): ?StudyPlan
    {
        $stmt = $this->pdo->prepare('SELECT * FROM study_plans WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $this->hydrate($row) : null;
    }

    public function deleteByIdAndUser(string $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM study_plans WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    public function findExpiredForOverdueMark(DateTimeImmutable $now): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM study_plans WHERE deadline < :now AND status IN ('pending', 'in_progress')"
        );
        $stmt->execute(['now' => $now->format('Y-m-d H:i:s')]);

        return array_map(fn(array $row): StudyPlan => $this->hydrate($row), $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
    }

    public function dashboardStatsByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT status, COUNT(*) as total FROM study_plans WHERE user_id = :user_id GROUP BY status');
        $stmt->execute(['user_id' => $userId]);

        $base = ['pending' => 0, 'in_progress' => 0, 'done' => 0, 'overdue' => 0];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
            $base[(string) $row['status']] = (int) $row['total'];
        }

        return $base;
    }

    private function hydrate(array $row): StudyPlan
    {
        return new StudyPlan(
            id: (string) $row['id'],
            userId: (int) $row['user_id'],
            title: (string) $row['title'],
            deadline: new DateTimeImmutable((string) $row['deadline']),
            status: StudyPlanStatus::from((string) $row['status']),
            createdAt: new DateTimeImmutable((string) $row['created_at']),
            updatedAt: new DateTimeImmutable((string) $row['updated_at']),
        );
    }
}
