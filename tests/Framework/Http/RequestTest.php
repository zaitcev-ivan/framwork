<?php

namespace Tests\Framework\Http;

use Framework\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $_GET = [];
        $_POST = [];
    }

    public function testEmpty(): void
    {
        $request = new Request();
        $this->assertEquals([], $request->getQueryParams());
        $this->assertNull($request->getParsedBody());
    }

    public function testQueryParams(): void
    {
        $_GET = $data = [
            'name' => 'John',
            'age' => '22',
        ];

        $request = new Request();
        $this->assertEquals($data, $request->getQueryParams());
    }

    public function testParsedBody(): void
    {
        $_POST = $data = ['title' => 'Title'];

        $request = new Request();
        $this->assertEquals([], $request->getQueryParams());
        $this->assertEquals($data, $request->getParsedBody());
    }
}