<?php

declare(strict_types=1);

namespace PCIT\Framework\Tests\Support;

use PCIT\Framework\Http\Request;
use Tests\TestCase;

class RequestTest extends TestCase
{
    public function testParseLink(): void
    {
        $link = 'Link: <https://api.github.com/resource?page=2>; rel="next",
                       <https://api.github.com/resource?page=5>; rel="last"';

        $this->app->singleton('request', Request::createfromGlobals());

        $this->assertArrayHasKey('next', \Request::parseLink($link));
    }

    public function test(): void
    {
        $response = $this->request('/testing');

        $this->assertEquals('1', $response->getContent());
    }

    public function test2(): void
    {
        $response = $this->request('/testing/100');

        $this->assertEquals('100', $response->getContent());
    }
}
