<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

class Cache extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'cache';
    }
}
