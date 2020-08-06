<?php

declare(strict_types=1);

use PCIT\Runner\Events\Handler\TextHandler;
use Tests\TestCase;

class TextHandlerTest extends TestCase
{
    public function test_array_env(): void
    {
        $image = (new TextHandler())->handle(
            'khs1994/php:${PHP_VERSION}-fpm-alpine-${PCIT_TAG}',
            [
                'PHP_VERSION=7.4.2',
                'PCIT_TAG=1.0.0',
            ]
        );

        $this->assertEquals(sprintf(
            'khs1994/php:%s-fpm-alpine-%s',
            '7.4.2',
            '1.0.0'
        ), $image);
    }

    public function test_obj_env(): void
    {
        $image = (new TextHandler())->handle(
            'khs1994/php:${PHP_VERSION}-fpm-alpine-${PCIT_TAG}',
            [
                'PHP_VERSION' => '7.4.2',
                'PCIT_TAG' => '1.0.0',
            ]
        );

        $this->assertEquals(sprintf(
            'khs1994/php:%s-fpm-alpine-%s',
            '7.4.2',
            '1.0.0'
        ), $image);
    }

    public function test_handle_array(): void
    {
        $result = (new TextHandler())->handleArray([
            'k=${VAR}',
            'k2=${VAR}',
        ], ['VAR=value']);

        $this->assertEquals(['k=value', 'k2=value'], $result);
    }
}
