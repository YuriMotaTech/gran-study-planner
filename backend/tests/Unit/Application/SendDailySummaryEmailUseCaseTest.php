<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Application;

use GranStudyPlanner\Application\SendDailySummaryEmail\SendDailySummaryEmailUseCase;
use GranStudyPlanner\Domain\Notification\EmailSenderInterface;
use GranStudyPlanner\Domain\StudyPlan\StudyPlanRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class SendDailySummaryEmailUseCaseTest extends TestCase
{
    public function testSendsEmailWithDashboardStats(): void
    {
        $repo = $this->createMock(StudyPlanRepositoryInterface::class);
        $repo->method('dashboardStatsByUser')->with(1)->willReturn([
            'pending' => 2,
            'in_progress' => 1,
            'done' => 5,
            'overdue' => 0,
        ]);

        $email = $this->createMock(EmailSenderInterface::class);
        $email->expects(self::once())->method('send')->with(
            'candidate@example.com',
            'Gran Study Planner — Daily summary',
            self::callback(static function (string $body): bool {
                return str_contains($body, 'Pending: 2')
                    && str_contains($body, 'In progress: 1')
                    && str_contains($body, 'Done: 5')
                    && str_contains($body, 'Overdue: 0')
                    && str_contains($body, 'User ID: 1');
            }),
            self::callback(static function (?string $html): bool {
                return $html !== null && str_contains($html, 'Daily summary') && str_contains($html, '<td>2</td>');
            })
        );

        $useCase = new SendDailySummaryEmailUseCase($repo, $email, 1, 'candidate@example.com');
        $useCase->execute();
    }
}
