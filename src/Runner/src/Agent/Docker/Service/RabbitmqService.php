<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker\Service;

use PCIT\Runner\Agent\Interfaces\ServiceInterface;

class RabbitmqService implements ServiceInterface
{
    public static $image = 'rabbitmq:3.7.8-management-alpine';
    public static $env = [];
    public static $entrypoint = null;
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
