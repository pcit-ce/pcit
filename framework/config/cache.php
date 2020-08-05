<?php

declare(strict_types=1);

return [
    'default' => env('CI_CACHE_DRIVE', 'redis'),

    'stores' => [
        'redis' => [
        'host' => env('CI_REDIS_HOST', 'redis'),
        'port' => env('CI_REDIS_PORT', 6379),
        'database' => env('CI_REDIS_DATABASE', 8),
        ],
    ],
];
