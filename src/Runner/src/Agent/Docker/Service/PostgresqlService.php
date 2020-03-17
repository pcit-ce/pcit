<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker\Service;

use PCIT\Runner\Agent\Interfaces\ServiceInterface;

class PostgresqlService implements ServiceInterface
{
    public static $image = 'postgres:11.1-alpine';
    public static $env = [];
    public static $entrypoint = [
        'POSTGRES_PASSWORD=test',
        'POSTGRES_USER=test',
        'POSTGRES_DB=test',
    ];

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
