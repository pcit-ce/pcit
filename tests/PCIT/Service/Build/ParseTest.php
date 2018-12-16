<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build;

use PCIT\Service\Build\Parse;
use PCIT\Tests\PCITTestCase;

class ParseTest extends PCITTestCase
{
    /**
     * @throws \Exception
     */
    public function test_image(): void
    {
        $php_version = '7.2.13';

        $image = Parse::image('khs1994/php:${PHP_VERSION}-fpm-alpine',
            [
                'PHP_VERSION' => $php_version,
            ]
        );

        $this->assertEquals(sprintf('khs1994/php:%s-fpm-alpine', $php_version), $image);
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
