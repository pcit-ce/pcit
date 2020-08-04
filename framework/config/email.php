<?php

declare(strict_types=1);

return [
    'host' => env('CI_EMAIL_HOST'),
    'username' => env('CI_EMAIL_USERNAME'),
    'password' => env('CI_EMAIL_PASSWORD'),
    'smtp_secure' => env('CI_EMAIL_SMTP_SECURE', 'ssl'),
    'port' => env('CI_EMAIL_SMTP_PORT', 465),

    'from_address' => env('CI_EMAIL_FROM'),
    'from_name' => env('CI_EMAIL_FROM_NAME'),
];
