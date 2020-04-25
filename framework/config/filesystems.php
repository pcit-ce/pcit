<?php

declare(strict_types=1);

return [
    'default' => env('CI_FILESYSTEM_DRIVER', 's3'),

    'disks' => [
        's3' => [
            'version' => 'latest',
            'credentials' => [
                'key' => env('CI_S3_ACCESS_KEY_ID'),
                'secret' => env('CI_S3_SECRET_ACCESS_KEY'),
            ],
            'region' => env('CI_S3_REGION', 'us-east-1'),
            'bucket' => env('CI_S3_BUCKET', 'pcit'),
            // 'url' => env('AWS_URL'),
            'use_path_style_endpoint' => env('CI_S3_USE_PATH_STYLE_ENDPOINT', true),
            'endpoint' => env('CI_S3_ENDPOINT'),
            'http' => [
                'connect_timeout' => 0,
            ],
        ],
    ],

    'bucket' => env('CI_S3_BUCKET', 'pcit'),

    'cache_bucket' => env('CI_S3_CACHE_BUCKET', 'pcit-caches'),

    'artifact_bucket' => env('CI_S3_ARTIFACT_BUCKET', 'pcit-artifact'),
];
