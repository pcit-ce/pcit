<?php

declare(strict_types=1);

namespace PCIT\Framework\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log extends Logger
{
    /**
     * @return Logger
     *
     * @throws \Exception
     */
    public function __construct(string $name = 'pcit', string $log_path = null)
    {
        date_default_timezone_set(env('CI_TZ', 'PRC'));

        $log_path = $log_path ?? sys_get_temp_dir().\DIRECTORY_SEPARATOR.'pcit.'.date('Y-m-d').'.log';

        parent::__construct($name);

        $log_level = env('CI_LOG_LEVEL', 'info');

        $this->pushHandler(new StreamHandler($log_path, $log_level));
    }

    public function log($level, $message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::log($level, $message, $context);
    }

    public function emergency($message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::emergency($message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::alert($message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::critical($message, $context);
    }

    public function error($message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::error($message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::warning($message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::notice($message, $context);
    }

    public function info($message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::info($message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $debug = debug_backtrace();

        $context = $this->getContext($debug, $context);

        parent::debug($message, $context);
    }

    public function getContext($debug, array $context = [])
    {
        $file = $debug[1]['file'];
        $line = $debug[1]['line'];

        if ($file && 0 === strpos($file, base_path())) {
            $file = substr($file, \strlen(base_path()));
        }

        $context = array_merge(compact('file', 'line'), $context);

        return $context;
    }
}
