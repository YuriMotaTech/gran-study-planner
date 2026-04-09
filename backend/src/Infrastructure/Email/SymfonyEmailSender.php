<?php

declare(strict_types=1);

namespace GranStudyPlanner\Infrastructure\Email;

use GranStudyPlanner\Domain\Notification\EmailSenderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final readonly class SymfonyEmailSender implements EmailSenderInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromAddress,
        private string $fromName
    ) {}

    public function send(string $to, string $subject, string $textBody, ?string $htmlBody = null): void
    {
        $from = $this->fromName !== ''
            ? new Address($this->fromAddress, $this->fromName)
            : new Address($this->fromAddress);
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text($textBody);
        if ($htmlBody !== null && $htmlBody !== '') {
            $email->html($htmlBody);
        }
        $this->mailer->send($email);
    }
}
