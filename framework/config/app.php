<?php

declare(strict_types=1);

return [
    'name' => env('CI_NAME', 'PCIT'),
    'debug' => env('CI_DEBUG', false),
    'providers' => [
        'App\Providers\AppServiceProvider',
    ],
    'alias' => [
        'Route' => PCIT\Framework\Support\Facades\Route::class,
        'Request' => PCIT\Framework\Support\Facades\Request::class,
    ],
    'providers' => [
        PCIT\Framework\Routing\RoutingServiceProvider::class,

        App\Providers\AppServiceProvider::class,
    ],
];
