<?php

declare(strict_types=1);

namespace KhsCI\Tests\Support;

use KhsCI\Support\Request;
use KhsCI\Tests\KhsCITestCase;

class RequestTest extends KhsCITestCase
{
    public function testParseLink(): void
    {
        $link = 'Link: <https://api.github.com/resource?page=2>; rel="next",
                       <https://api.github.com/resource?page=5>; rel="last"';

        $this->assertArrayHasKey('next', Request::parseLink($link));
    }
}
