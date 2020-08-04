<?php

declare(strict_types=1);

return [
    'github' => [
        'app' => [
            'name' => env('CI_GITHUB_APP_NAME'),
            'id' => env('CI_GITHUB_APP_ID'),
        ],
        'check_run' => [
            'prefix' => env('CI_GITHUB_CHECK_RUN_PREFIX', 'PCIT'),
        ],
        'oauth' => [
            'client_id' => env('CI_GITHUB_CLIENT_ID'),
            'client_secret' => env('CI_GITHUB_CLIENT_SECRET'),
            'callback_url' => env('CI_GITHUB_CALLBACK_URL'),
        ],
    ],

    'gitee' => [
        'oauth' => [
            'client_id' => env('CI_GITEE_CLIENT_ID'),
            'client_secret' => env('CI_GITEE_CLIENT_SECRET'),
            'callback_url' => env('CI_GITEE_CALLBACK_URL'),
        ],
    ],

    'coding' => [
        'oauth' => [
            'client_id' => env('CI_CODING_CLIENT_ID'),
            'client_secret' => env('CI_CODING_CLIENT_SECRET'),
            'callback_url' => env('CI_CODING_CALLBACK_URL'),
            'team' => env('CI_CODING_TEAM'),
        ],
        'host' => env('CI_CODING_HOST', null),
    ],

    'webhooks' => [
        'token' => env('CI_WEBHOOKS_TOKEN'),
        'debug' => env('CI_WEBHOOKS_DEBUG', false),
    ],
];
