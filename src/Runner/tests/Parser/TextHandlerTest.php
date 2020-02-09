<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Parser;

use PCIT\Runner\Parser\TextHandler;
use Tests\TestCase;

class TextHandlerTest extends TestCase
{
    public function test_text(): void
    {
        $image = (new TextHandler())->handle('khs1994/php:${PHP_VERSION}-fpm-alpine-${PCIT_TAG}',
            [
                'PHP_VERSION=7.4.2',
                'PCIT_TAG=1.0.0',
            ]
        );

        $this->assertEquals(sprintf(
            'khs1994/php:%s-fpm-alpine-%s', '7.4.2', '1.0.0'), $image);
    }
}
