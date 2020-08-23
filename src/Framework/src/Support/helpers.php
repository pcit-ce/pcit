<?php

declare(strict_types=1);

use PCIT\Framework\Support\Env;

if (!function_exists('app')) {
    function app(string $abstract = null)
    {
        if (null === $abstract) {
            return \PCIT\Framework\Foundation\Application::getInstance();
        }

        return \PCIT\Framework\Foundation\Application::getInstance()->make($abstract);
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

        $config = require base_path('framework/config/'.$file.'.php');

        foreach ($array as $key) {
            $config = $config[$key];
        }

        return $config;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = '')
    {
        return app()->basePath($path);
    }
}

if (!function_exists('view')) {
    function view($path): void
    {
        include base_path('public/'.$path);

        exit;
    }
}

// https://github.com/igorw/retry
if (!function_exists('retry')) {
    function retry(int $retries, callable $fn)
    {
        beginning:
        try {
            return $fn();
        } catch (\Exception $e) {
            if (!$retries) {
                throw new \Exception($e->getMessage(), 0, $e);
            }
            --$retries;
            goto beginning;
        }
    }
}

if (!function_exists('xdebug_is_enabled')) {
    function xdebug_is_enabled(): bool
    {
        return true;
    }
}
