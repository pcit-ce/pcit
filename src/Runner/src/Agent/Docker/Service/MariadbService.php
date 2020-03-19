<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker\Service;

use PCIT\Runner\Agent\Interfaces\ServiceInterface;

class MariadbService implements ServiceInterface
{
    public static $image = 'mariadb:10.4.1-bionic';
    public static $env = [];
    public static $entrypoint = [
        'MYSQL_DATABASE=test',
        'MYSQL_ROOT_PASSWORD=test',
    ];
    public static $command = null;

    public static function handle(): array
    {
        $image = self::$image;
        $env = self::$env;
        $entrypoint = self::$entrypoint;
        $command = self::$command;

        return compact('image', 'env', 'entrypoint', 'command');
    }
}
