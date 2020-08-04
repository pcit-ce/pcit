<?php

declare(strict_types=1);

return [
    'app' => [
        'id' => env('CI_WECHAT_APP_ID'),
        'secret' => env('CI_WECHAT_APP_SECRET'),
        'token' => env('CI_WECHAT_TOKEN'),
    ],
    'template_id' => env('CI_WECHAT_TEMPLATE_ID'),
    'user_openid' => env('CI_WECHAT_USER_OPENID'),
];
