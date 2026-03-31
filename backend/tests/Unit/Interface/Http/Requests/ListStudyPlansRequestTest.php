<?php

declare(strict_types=1);

namespace GranStudyPlanner\Tests\Unit\Interface\Http\Requests;

use DomainException;
use GranStudyPlanner\Interface\Http\Request;
use GranStudyPlanner\Interface\Http\Requests\ListStudyPlansRequest;
use PHPUnit\Framework\TestCase;

final class ListStudyPlansRequestTest extends TestCase
{
    public function testAppliesDefaultsAndNormalization(): void
    {
        $request = new Request(
            method: 'GET',
            path: '/study-plans',
            query: ['page' => '0', 'perPage' => '999', 'sortBy' => 'nope', 'sortDirection' => 'NOPE'],
            body: [],
            headers: [],
        );

        $input = ListStudyPlansRequest::from($request, 2);

        self::assertSame(2, $input->userId);
        self::assertNull($input->status);
        self::assertSame(1, $input->page);
        self::assertSame(100, $input->perPage);
        self::assertSame('deadline', $input->sortBy);
        self::assertSame('asc', $input->sortDirection);
    }

    public function testRejectsInvalidStatus(): void
    {
        $request = new Request(
            method: 'GET',
            path: '/study-plans',
            query: ['status' => 'bad_status'],
            body: [],
            headers: [],
        );

        $this->expectException(DomainException::class);
        ListStudyPlansRequest::from($request, 2);
    }

    public function testAcceptsValidStatus(): void
    {
        $request = new Request(
            method: 'GET',
            path: '/study-plans',
            query: ['status' => 'pending'],
            body: [],
            headers: [],
        );

        $input = ListStudyPlansRequest::from($request, 2);
        self::assertSame('pending', $input->status);
    }
}

