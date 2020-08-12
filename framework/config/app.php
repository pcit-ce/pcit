<?php

declare(strict_types=1);

return [
    'name' => env('CI_NAME', 'PCIT'),
    'debug' => env('CI_DEBUG', false),
    'env' => env('APP_ENV', 'production'),
    'host' => env('CI_HOST', 'http://127.0.0.1:8080'),
    'timezone' => env('CI_TZ', 'PRC'),
    'tz' => env('CI_TZ', 'PRC'),
    'alias' => [
        'Route' => PCIT\Framework\Support\Facades\Route::class,
        'Request' => PCIT\Framework\Support\Facades\Request::class,
        'Response' => PCIT\Framework\Support\Facades\Response::class,
        'Cache' => PCIT\Framework\Support\Facades\Cache::class,
        'Session' => PCIT\Framework\Support\Facades\Session::class,
        'Log' => PCIT\Framework\Support\Facades\Log::class,
        'App' => PCIT\Framework\Support\Facades\App::class,
        'Storage' => PCIT\Framework\Support\Facades\Storage::class,
        'PCIT' => PCIT\Support\Facades\PCIT::class,
    ],
    'providers' => [
        PCIT\Framework\Routing\RoutingServiceProvider::class,
        PCIT\Framework\Http\HttpServiceProvider::class,
        PCIT\Framework\Cache\CacheServiceProvider::class,
        PCIT\Framework\Session\SessionServiceProvider::class,
        PCIT\Framework\Log\LogServiceProvider::class,
        PCIT\Framework\Storage\StorageServiceProvider::class,

        App\Providers\AppServiceProvider::class,

        PCIT\PCITServiceProvider::class,
    ],
    'cache' => [
        'driver' => 'redis',
        // 'driver' => 'file',
    ],
];
