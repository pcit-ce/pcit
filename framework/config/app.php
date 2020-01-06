<?php

declare(strict_types=1);

return [
    'name' => env('CI_NAME', 'PCIT'),
    'debug' => env('CI_DEBUG', false),
    'alias' => [
        'Route' => PCIT\Framework\Support\Facades\Route::class,
        'Request' => PCIT\Framework\Support\Facades\Request::class,
        'Response' => PCIT\Framework\Support\Facades\Response::class,
        'Cache' => PCIT\Framework\Support\Facades\Cache::class,
        'Session' => PCIT\Framework\Support\Facades\Session::class,
        'Log' => PCIT\Framework\Support\Facades\Log::class,
    ],
    'providers' => [
        PCIT\Framework\Routing\RoutingServiceProvider::class,
        PCIT\Framework\Http\HttpServiceProvider::class,
        PCIT\Framework\Cache\CacheServiceProvider::class,
        PCIT\Framework\Session\SessionServiceProvider::class,
        PCIT\Framework\Log\LogServiceProvider::class,

        App\Providers\AppServiceProvider::class,
    ],
];
