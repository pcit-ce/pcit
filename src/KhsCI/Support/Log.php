<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    private static $log;

    /**
     * @param string      $name
     * @param string|null $log_path
     *
     * @return Logger
     *
     * @throws \Exception
     */
    public static function connect(string $name = 'khsci', string $log_path = null)
    {
        date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

        $log_path = $log_path ?? sys_get_temp_dir().DIRECTORY_SEPARATOR.'khsci.log';

        if (!(self::$log instanceof Logger)) {
            $log = new Logger($name);

            $log->pushHandler(new StreamHandler($log_path, Logger::DEBUG));

            self::$log = $log;
        }

        return self::$log;
    }
}
