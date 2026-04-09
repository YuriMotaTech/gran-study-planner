<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\SendDailySummaryEmail;

use GranStudyPlanner\Domain\Notification\EmailSenderInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;

final readonly class SendDailySummaryEmailUseCase
{
    public function __construct(
        private StudyPlanRepositoryInterface $repository,
        private EmailSenderInterface $email,
        private int $userId,
        private string $mailTo
    ) {}

    public function execute(): void
    {
        $stats = $this->repository->dashboardStatsByUser($this->userId);
        $subject = 'Gran Study Planner — Daily summary';
        $text = $this->formatPlainText($stats);
        $html = $this->formatHtml($stats);
        $this->email->send($this->mailTo, $subject, $text, $html);
    }

    /** @param array<string,int> $stats */
    private function formatPlainText(array $stats): string
    {
        $lines = [
            'Study plan totals by status',
            '---------------------------',
            'Pending: ' . ($stats['pending'] ?? 0),
            'In progress: ' . ($stats['in_progress'] ?? 0),
            'Done: ' . ($stats['done'] ?? 0),
            'Overdue: ' . ($stats['overdue'] ?? 0),
            '',
            'User ID: ' . $this->userId,
        ];

        return implode("\n", $lines);
    }

    /** @param array<string,int> $stats */
    private function formatHtml(array $stats): string
    {
        $rows = '';
        foreach (['pending' => 'Pending', 'in_progress' => 'In progress', 'done' => 'Done', 'overdue' => 'Overdue'] as $key => $label) {
            $n = (int) ($stats[$key] ?? 0);
            $rows .= sprintf('<tr><td>%s</td><td>%d</td></tr>', htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $n);
        }

        return '<!DOCTYPE html><html><body><h1>Daily summary</h1><table border="1" cellpadding="6"><thead><tr><th>Status</th><th>Count</th></tr></thead><tbody>'
            . $rows
            . '</tbody></table><p>User ID: ' . $this->userId . '</p></body></html>';
    }
}
