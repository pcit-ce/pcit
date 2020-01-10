<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

class App extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'app';
    }
}
