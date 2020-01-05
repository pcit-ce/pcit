<?php

declare(strict_types=1);

return [
    'name' => env('CI_NAME', 'PCIT'),
    'debug' => env('CI_DEBUG', false),
    'alias' => [
        'Route' => PCIT\Framework\Support\Facades\Route::class,
        'Request' => PCIT\Framework\Support\Facades\Request::class,
        'Response' => PCIT\Framework\Support\Facades\Response::class,
    ],
    'providers' => [
        PCIT\Framework\Routing\RoutingServiceProvider::class,
        PCIT\Framework\Http\HttpServiceProvider::class,

        App\Providers\AppServiceProvider::class,
    ],
];
