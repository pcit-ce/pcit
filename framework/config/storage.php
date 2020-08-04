<?php

declare(strict_types=1);

return [
    's3' => [
        'endpoint' => env('CI_S3_ENDPOINT'),
        'bucket' => '',
        'region' => env('CI_S3_REGION', 'us-east-1'),
    ],
];
