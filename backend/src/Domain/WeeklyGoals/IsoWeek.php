<?php

declare(strict_types=1);

namespace GranStudyPlanner\Domain\WeeklyGoals;

use DateTimeImmutable;
use DomainException;

final readonly class IsoWeek
{
    private function __construct(public string $value) {}

    public static function fromString(string $raw): self
    {
        $raw = trim($raw);
        if (preg_match('/^\d{4}-W\d{2}$/', $raw) !== 1) {
            throw new DomainException('Invalid week. Expected YYYY-Www.');
        }

        [$yearPart, $weekPart] = explode('-W', $raw);
        $year = (int) $yearPart;
        $week = (int) $weekPart;
        if ($year < 2000 || $year > 2100) {
            throw new DomainException('Invalid week.');
        }
        if ($week < 1 || $week > 53) {
            throw new DomainException('Invalid week.');
        }

        // Validate against ISO calendar (handles non-existent week 53 for some years).
        $dt = (new DateTimeImmutable())->setISODate($year, $week, 1);
        if ((int) $dt->format('o') !== $year || (int) $dt->format('W') !== $week) {
            throw new DomainException('Invalid week.');
        }

        return new self(sprintf('%04d-W%02d', $year, $week));
    }

    public static function current(): self
    {
        $now = new DateTimeImmutable();
        return new self($now->format('o') . '-W' . $now->format('W'));
    }

    /** @return array{start: DateTimeImmutable, end: DateTimeImmutable} */
    public function range(): array
    {
        [$yearPart, $weekPart] = explode('-W', $this->value);
        $year = (int) $yearPart;
        $week = (int) $weekPart;

        $start = (new DateTimeImmutable())->setISODate($year, $week, 1)->setTime(0, 0, 0);
        $end = $start->modify('+7 days')->setTime(0, 0, 0);

        return ['start' => $start, 'end' => $end];
    }
}

