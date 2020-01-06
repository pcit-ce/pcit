<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static emergency(string $message, array $context = [])
 * @method static alert(string $message, array $context = [])
 * @method static critical(string $message, array $context = [])
 * @method static error(string $message, array $context = [])
 * @method static warning(string $message, array $context = [])
 * @method static notice(string $message, array $context = [])
 * @method static info(string $message, array $context = [])
 * @method static debug(string $message, array $context = [])
 * @method static log($level, string $message, array $context = [])
 */
class Log extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'log';
    }
}
