<?php

declare(strict_types=1);

namespace KhsCI\Support;

class CI
{
    public static function env()
    {
        return getenv('APP_ENV');
    }
}
