<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Interface\Http\Requests;

use DomainException;
use GranStudyPlanner\Interface\Http\Request;
use GranStudyPlanner\Interface\Http\Requests\UpdateStudyPlanStatusRequest;
use PHPUnit\Framework\TestCase;

final class UpdateStudyPlanStatusRequestTest extends TestCase
{
    public function testParsesValidPayload(): void
    {
        $request = new Request(
            method: 'PATCH',
            path: '/study-plans/abc123',
            query: [],
            body: ['status' => 'in_progress'],
            headers: [],
        );

        $input = UpdateStudyPlanStatusRequest::from($request, 5, 'abc123');
        self::assertSame(5, $input->userId);
        self::assertSame('abc123', $input->id);
        self::assertSame('in_progress', $input->status);
    }

    public function testRejectsInvalidStatus(): void
    {
        $request = new Request(
            method: 'PATCH',
            path: '/study-plans/abc123',
            query: [],
            body: ['status' => 'nope'],
            headers: [],
        );

        $this->expectException(DomainException::class);
        UpdateStudyPlanStatusRequest::from($request, 5, 'abc123');
    }

    public function testRejectsInvalidId(): void
    {
        $request = new Request(
            method: 'PATCH',
            path: '/study-plans/../x',
            query: [],
            body: ['status' => 'pending'],
            headers: [],
        );

        $this->expectException(DomainException::class);
        UpdateStudyPlanStatusRequest::from($request, 5, '../x');
    }
}

