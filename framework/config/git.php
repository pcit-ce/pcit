<?php

declare(strict_types=1);

return [
    'default' => 'github',

    'github' => [
        'app' => [
            'name' => env('CI_GITHUB_APP_NAME'),
            'id' => env('CI_GITHUB_APP_ID'),
            'private_key_path' => base_path('framework/storage/private_key/private.key'),
        ],
        'check_run' => [
            'prefix' => env('CI_GITHUB_CHECK_RUN_PREFIX', 'PCIT'),
        ],
        'oauth' => [
            'client_id' => env('CI_GITHUB_CLIENT_ID'),
            'client_secret' => env('CI_GITHUB_CLIENT_SECRET'),
            'callback_url' => env('CI_GITHUB_CALLBACK_URL'),
        ],
        'api_url' => 'https://api.github.com',
    ],

    'gitee' => [
        'oauth' => [
            'client_id' => env('CI_GITEE_CLIENT_ID'),
            'client_secret' => env('CI_GITEE_CLIENT_SECRET'),
            'callback_url' => env('CI_GITEE_CALLBACK_URL'),
        ],
        'api_url' => 'https://gitee.com/api/v5',
    ],

    'coding' => [
        'oauth' => [
            'client_id' => env('CI_CODING_CLIENT_ID'),
            'client_secret' => env('CI_CODING_CLIENT_SECRET'),
            'callback_url' => env('CI_CODING_CALLBACK_URL'),
            'team' => env('CI_CODING_TEAM'),
        ],
        'host' => env('CI_CODING_HOST', null),
        'class_name' => 'Coding',
        'api_url' => 'https://'.env('CI_CODING_TEAM').'.coding.net',
    ],

    'webhooks' => [
        'token' => env('CI_WEBHOOKS_TOKEN'),
        'debug' => env('CI_WEBHOOKS_DEBUG', false),
    ],
];
