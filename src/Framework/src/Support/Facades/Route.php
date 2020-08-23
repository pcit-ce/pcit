<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static void get(string $url, \Closure|string $action)
 * @method static void post(string $url, \Closure|string $action)
 * @method static void put(string $url, \Closure|string $action)
 * @method static void patch(string $url, \Closure|string $action)
 * @method static void delete(string $url, \Closure|string $action)
 * @method static void options(string $url, \Closure|string $action)
 */
class Route extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'router';
    }
}
