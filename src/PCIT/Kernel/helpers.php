<?php

declare(strict_types=1);

use PCIT\PCIT;
use PCIT\Support\Env;

if (!function_exists('app')) {
    function pcit()
    {
        return new PCIT();
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        return Env::get($key, $default);
    }
}
