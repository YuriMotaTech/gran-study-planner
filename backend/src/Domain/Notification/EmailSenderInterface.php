<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\Notification;

interface EmailSenderInterface
{
    public function send(string $to, string $subject, string $textBody, ?string $htmlBody = null): void;
}
