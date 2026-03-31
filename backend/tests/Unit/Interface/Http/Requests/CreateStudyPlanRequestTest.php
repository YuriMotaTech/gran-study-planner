<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Interface\Http\Requests;

use DomainException;
use GranStudyPlanner\Interface\Http\Request;
use GranStudyPlanner\Interface\Http\Requests\CreateStudyPlanRequest;
use PHPUnit\Framework\TestCase;

final class CreateStudyPlanRequestTest extends TestCase
{
    public function testParsesValidPayload(): void
    {
        $request = new Request(
            method: 'POST',
            path: '/study-plans',
            query: [],
            body: ['title' => '  Study PHP  ', 'deadline' => '2030-04-01 10:00:00'],
            headers: [],
        );

        $input = CreateStudyPlanRequest::from($request, 10);

        self::assertSame(10, $input->userId);
        self::assertSame('Study PHP', $input->title);
        self::assertSame('2030-04-01 10:00:00', $input->deadline);
    }

    public function testRejectsMissingTitle(): void
    {
        $request = new Request(
            method: 'POST',
            path: '/study-plans',
            query: [],
            body: ['deadline' => '2030-04-01 10:00:00'],
            headers: [],
        );

        $this->expectException(DomainException::class);
        CreateStudyPlanRequest::from($request, 1);
    }

    public function testRejectsInvalidDeadline(): void
    {
        $request = new Request(
            method: 'POST',
            path: '/study-plans',
            query: [],
            body: ['title' => 'Study', 'deadline' => 'not-a-date'],
            headers: [],
        );

        $this->expectException(DomainException::class);
        CreateStudyPlanRequest::from($request, 1);
    }
}

