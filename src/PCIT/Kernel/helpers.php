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

if (!function_exists('base_path')) {
    function base_path()
    {
        return __DIR__.'/../../../';
    }
}

if (!function_exists('view')) {
    function view($path): void
    {
        include base_path().'public/public/'.$path;

        exit;
    }
}
