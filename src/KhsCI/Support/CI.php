<?php

namespace KhsCI\Support;

class CI
{
    public static function env()
    {
        return getenv('APP_ENV');
    }
}