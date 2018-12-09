<?php

declare(strict_types=1);

return [
    'name' => env('CI_NAME', 'PCIT'),
    'debug' => env('CI_DEBUG', false),
    'providers' => [
        'App\Providers\AppServiceProvider',
    ],
];
