<?php

declare(strict_types=1);

return [
    'connections' => [
        'mysql' => [
            'host' => env('CI_MYSQL_HOST', 'mysql'),
            'port' => env('CI_MYSQL_PORT', 3306),
            'username' => env('CI_MYSQL_USERNAME', 'root'),
            'password' => env('CI_MYSQL_PASSWORD', 'root'),
            'database' => env('CI_MYSQL_DATABASE', 'pcit'),
        ],
    ],
];
