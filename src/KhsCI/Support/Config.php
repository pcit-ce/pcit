<?php

declare(strict_types=1);

namespace KhsCI\Support;

class Config
{
    public static function config()
    {
        return $config = [
            'coding' => [
                'client_id' => getenv('CODING_CLIENT_ID'),
                'client_secret' => getenv('CODING_CLIENT_SECRET'),
                'callback_url' => getenv('CODING_CALLBACK_URL'),
            ],
            'gitee' => [
                'client_id' => getenv('GITEE_CLIENT_ID'),
                'client_secret' => getenv('GITEE_CLIENT_SECRET'),
                'callback_url' => getenv('GITEE_CALLBACK_URL'),
            ],
            'github' => [
                'client_id' => getenv('GITHUB_CLIENT_ID'),
                'client_secret' => getenv('GITHUB_CLIENT_SECRET'),
                'callback_url' => getenv('GITHUB_CALLBACK_URL'),
            ],
        ];
    }
}
