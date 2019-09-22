<?php

declare(strict_types=1);

namespace PCIT\Tests\Support;

use PCIT\Support\Request;
use Tests\PCITTestCase;

class RequestTest extends PCITTestCase
{
    public function testParseLink(): void
    {
        $link = 'Link: <https://api.github.com/resource?page=2>; rel="next",
                       <https://api.github.com/resource?page=5>; rel="last"';

        $this->assertArrayHasKey('next', Request::parseLink($link));
    }

    /**
     * @group dont-test
     */
    public function test(): void
    {
        $this->getTest()
            ->request = \Symfony\Component\HttpFoundation\Request::create('/test');

        $response = '';

        $this->assertEquals(1, $response);
    }
}
