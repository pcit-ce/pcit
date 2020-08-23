<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static void put(string $name, $value)
 * @method static void forget(string $name)
 * @method static string|null get(string $name)
 * @method static bool has(string $name)
 * @method static array all()
 * @method static bool flush()
 * @method static bool pull(string $name)
 */
class Session extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'session';
    }
}
