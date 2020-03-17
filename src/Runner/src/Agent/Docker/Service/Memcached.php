<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker\Service;

use PCIT\Runner\Agent\Interfaces\ServiceInterface;

class Memcached implements ServiceInterface
{
    public static $image = 'memcached:1.5.12-alpine';
    public static $env = [];
    public static $entrypoint = null;
    public static $commands = null;

    public static function handle(): array
    {
        $image = self::$image;
        $env = self::$env;
        $entrypoint = self::$entrypoint;
        $commands = self::$commands;

        return compact('image', 'env', 'entrypoint', 'commands');
    }
}
