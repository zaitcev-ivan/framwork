<?php

namespace Tests\Framework\Http;

use Framework\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testEmpty(): void
    {
        $request = new Request();
        $this->assertEquals([], $request->getQueryParams());
        $this->assertNull($request->getParsedBody());
    }

    public function testQueryParams(): void
    {
        $request = new Request($data = [
            'name' => 'John',
            'age' => '22',
        ]);
        $this->assertEquals($data, $request->getQueryParams());
    }

    public function testParsedBody(): void
    {
        $request = new Request([], $data = ['title' => 'Title']);
        $this->assertEquals([], $request->getQueryParams());
        $this->assertEquals($data, $request->getParsedBody());
    }
}