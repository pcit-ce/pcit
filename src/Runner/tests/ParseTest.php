<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests;

use PCIT\Runner\Parse;
use Tests\TestCase;

class ParseTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_text(): void
    {
        $image = Parse::text('khs1994/php:${PHP_VERSION}-fpm-alpine-${PCIT_TAG}',
            [
                'PHP_VERSION=7.2.16',
                'PCIT_TAG=1.0.0',
            ]
        );

        $this->assertEquals(sprintf(
            'khs1994/php:%s-fpm-alpine-%s', '7.2.16', '1.0.0'), $image);
    }
}
