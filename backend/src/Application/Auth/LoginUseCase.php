<?php

declare(strict_types=1);

namespace GranStudyPlanner\Application\Auth;

use DomainException;

final readonly class LoginUseCase
{
    public function __construct(
        private TokenEncoderInterface $tokenEncoder,
        private string $defaultEmail,
        private string $defaultPassword,
        private int $defaultUserId,
        private int $ttlSeconds,
    ) {}

    public function execute(string $email, string $password): string
    {
        if ($email !== $this->defaultEmail || $password !== $this->defaultPassword) {
            throw new DomainException('Invalid credentials.');
        }

        return $this->tokenEncoder->encode($this->defaultUserId, $this->ttlSeconds);
    }
}
