<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker\Service;

use PCIT\Runner\Agent\Interfaces\ServiceInterface;

class RedisService implements ServiceInterface
{
    public static $image = 'redis:6.0.2-alpine';
    public static $env = [];
    public static $entrypoint = null;
    public static $command = [
        '--bind',
        '0.0.0.0',
    ];

    public static function handle(): array
    {
        $image = self::$image;
        $env = self::$env;
        $entrypoint = self::$entrypoint;
        $command = self::$command;

        return compact('image', 'env', 'entrypoint', 'command');
    }
}
