<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\StudyPlan;

use DateTimeImmutable;
use DomainException;

final class StudyPlan
{
    public function __construct(
        private string $id,
        private int $userId,
        private string $title,
        private DateTimeImmutable $deadline,
        private StudyPlanStatus $status = StudyPlanStatus::PENDING,
        private DateTimeImmutable $createdAt = new DateTimeImmutable(),
        private DateTimeImmutable $updatedAt = new DateTimeImmutable(),
    ) {
        if (trim($this->title) === '') {
            throw new DomainException('Title is required.');
        }
    }

    public function id(): string { return $this->id; }
    public function userId(): int { return $this->userId; }
    public function title(): string { return $this->title; }
    public function deadline(): DateTimeImmutable { return $this->deadline; }
    public function status(): StudyPlanStatus { return $this->status; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
    public function updatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function updateStatus(StudyPlanStatus $status): void
    {
        if ($this->status === StudyPlanStatus::OVERDUE && $status === StudyPlanStatus::DONE) {
            throw new DomainException('Overdue plan must move to in_progress before done.');
        }

        $this->status = $status;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function markOverdueIfExpired(DateTimeImmutable $now): bool
    {
        if ($this->deadline < $now && in_array($this->status, [StudyPlanStatus::PENDING, StudyPlanStatus::IN_PROGRESS], true)) {
            $this->status = StudyPlanStatus::OVERDUE;
            $this->updatedAt = $now;
            return true;
        }

        return false;
    }
}
