<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static put(string $name, $value)
 * @method static forget(string $name)
 * @method static get(string $name)
 * @method static has(string $name)
 * @method static all()
 * @method static flush()
 * @method static pull(string $name)
 */
class Session extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'session';
    }
}
