<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests;

use PCIT\Builder\Parse;
use Tests\PCITTestCase;

class ParseTest extends PCITTestCase
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

    /**
     * @throws \Exception
     */
    public function test_command(): void
    {
        $command = Parse::command('mock', 'khs1994/mock', [
            'pwd',
            'composer install',
            'vendor/bin/phpunit',
        ]);

        $this->assertEquals(180, \strlen($command));
    }
}
