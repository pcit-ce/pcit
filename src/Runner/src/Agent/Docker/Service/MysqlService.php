<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker\Service;

use PCIT\Runner\Agent\Interfaces\ServiceInterface;

class MysqlService implements ServiceInterface
{
    public static $image = 'mysql:5.7.29';
    public static $env = [
        'MYSQL_DATABASE=test',
        'MYSQL_ROOT_PASSWORD=test',
    ];
    public static $entrypoint = null;
    public static $commands = [
        '--character-set-server=utf8mb4',
        '--default-authentication-plugin=mysql_native_password',
    ];

    public static function handle(): array
    {
        $image = self::$image;
        $env = self::$env;
        $entrypoint = self::$entrypoint;
        $commands = self::$commands;

        return compact('image', 'env', 'entrypoint', 'commands');
    }
}
