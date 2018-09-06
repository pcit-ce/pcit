<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    private static $log;

    /**
     * Detailed debug information.
     */
    private static $debug = 100;

    const DEBUG = 'debug';

    /**
     * Interesting events.
     *
     * Examples: User logs in, SQL logs.
     */
    private static $info = 200;

    const INFO = 'info';

    /**
     * Uncommon events.
     */
    private static $notice = 250;

    const NOTICE = 'notice';

    /**
     * Exceptional occurrences that are not errors.
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    private static $warning = 300;

    const WARNING = 'warning';

    /**
     * Runtime errors.
     */
    private static $error = 400;

    const ERROR = 'error';

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     */
    private static $critical = 500;

    const CRITICAL = 'critical';

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    private static $alter = 550;

    const ALTER = 'alter';

    /**
     * Urgent alert.
     */
    private static $emergency = 600;

    const EMERGENCY = 'emergency';

    /**
     * @param string      $name
     * @param string|null $log_path
     *
     * @return Logger
     *
     * @throws Exception
     */
    public static function connect(string $name = 'khsci', string $log_path = null)
    {
        date_default_timezone_set(Env::get('CI_TZ', 'PRC'));

        $log_path = $log_path ?? sys_get_temp_dir().\DIRECTORY_SEPARATOR.'khsci.'.date('Y-m-d').'.log';

        if (!(self::$log instanceof Logger)) {
            $log = new Logger($name);

            $log->pushHandler(new StreamHandler($log_path, Logger::DEBUG));

            self::$log = $log;
        }

        return self::$log;
    }

    /**
     * @param string $file
     * @param int    $line
     * @param string $debug_info
     * @param array  $context
     * @param string $level
     *
     * @throws Exception
     */
    public static function debug(string $file = null,
                                 int $line = null,
                                 string $debug_info = null,
                                 array $context = [],
                                 $level = 'debug'): void
    {
        $log_level = Env::get('CI_LOG_LEVEL', 'info');

        if (self::$$log_level > self::$$level) {
            return;
        }

        $debug_info = json_encode(array_filter([
            'file' => $file,
            'line' => $line,
            'info' => $debug_info,
        ]));

        self::connect()->$level($debug_info, $context);
    }

    public static function close(): void
    {
        self::$log = null;
    }

    public function __get($name)
    {
        return 'debug';
    }

    /**
     * @return Logger
     *
     * @throws Exception
     */
    public static function getMonolog()
    {
        return self::connect();
    }
}
