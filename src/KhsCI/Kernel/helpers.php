<?php

declare(strict_types=1);

use KhsCI\KhsCI as PCIT;
use KhsCI\Support\Env;

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
