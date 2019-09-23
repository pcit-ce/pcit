<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static get(string $url, Closure|string $action)
 * @method static post(string $url, Closure|string $action)
 * @method static put(string $url, Closure|string $action)
 * @method static patch(string $url, Closure|string $action)
 * @method static delete(string $url, Closure|string $action)
 * @method static options(string $url, Closure|string $action)
 */
class Route extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'router';
    }
}
