<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

abstract class Facade
{
    abstract protected static function getFacadeAccessor();

    public static function __callStatic($name, $args)
    {
        $instance = static::getFacadeAccessor();

        if ('string' === \gettype($instance)) {
            return app($instance)->$name(...$args);
        }
    }
}
