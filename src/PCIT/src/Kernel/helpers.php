<?php

declare(strict_types=1);

use PCIT\PCIT;
use PCIT\Support\Env;

if (!function_exists('pcit')) {
    function pcit()
    {
        return new PCIT();
    }
}

if (!function_exists('app')) {
    function app(string $abstract = null)
    {
        if (null === $abstract) {
            return \PCIT\Foundation\Application::getInstance();
        }

        return \PCIT\Foundation\Application::getInstance()->make($abstract);
    }
}

if (!function_exists('resolve')) {
    function resolve($name)
    {
        return app($name);
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        $key = trim($key, '.');

        [$file] = $array = explode('.', $key);

        array_shift($array);

        $config = require base_path().'framework/config/'.$file.'.php';

        foreach ($array as $key) {
            $config = $config[$key];
        }

        return $config;
    }
}

if (!function_exists('base_path')) {
    function base_path()
    {
        return realpath(__DIR__.'/../../../../').'/';
    }
}

if (!function_exists('view')) {
    function view($path): void
    {
        include base_path().'public/'.$path;

        exit;
    }
}
