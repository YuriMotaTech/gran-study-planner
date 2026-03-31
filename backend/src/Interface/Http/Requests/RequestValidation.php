<?php

declare(strict_types=1);

namespace GranStudyPlanner\Interface\Http\Requests;

use DateTimeImmutable;
use DomainException;

final class RequestValidation
{
    /** @param array<string,mixed> $body */
    public static function requiredString(array $body, string $key): string
    {
        $value = $body[$key] ?? null;
        if (!is_string($value)) {
            throw new DomainException(sprintf('%s is required.', $key));
        }
        $value = trim($value);
        if ($value === '') {
            throw new DomainException(sprintf('%s is required.', $key));
        }
        return $value;
    }

    /** @param array<string,mixed> $body */
    public static function optionalString(array $body, string $key): ?string
    {
        if (!array_key_exists($key, $body)) {
            return null;
        }
        $value = $body[$key];
        if ($value === null) {
            return null;
        }
        if (!is_string($value)) {
            throw new DomainException(sprintf('%s must be a string.', $key));
        }
        $value = trim($value);
        return $value === '' ? null : $value;
    }

    public static function parseDateTime(string $raw, string $fieldName = 'deadline'): string
    {
        try {
            new DateTimeImmutable($raw);
            return $raw;
        } catch (\Throwable) {
            throw new DomainException(sprintf('Invalid %s.', $fieldName));
        }
    }

    public static function intQuery(array $query, string $key, int $default): int
    {
        $raw = $query[$key] ?? $default;
        if (is_int($raw)) {
            return $raw;
        }
        if (is_string($raw) && preg_match('/^-?\d+$/', $raw) === 1) {
            return (int) $raw;
        }
        return $default;
    }
}

