<?php

declare(strict_types=1);

namespace KhsCI\Support;

class Config
{
    public static function config($config)
    {
        return $config = [
            'coding' => [
                'client_id' => getenv('CODING_CLIENT_ID'),
                'client_secret' => getenv('CODING_CLIENT_SECRET'),
                'callback_url' => getenv('CODING_CALLBACK_URL'),
                'access_token' => $config['coding_access_token'] ?? null,
            ],
            'gitee' => [
                'client_id' => getenv('GITEE_CLIENT_ID'),
                'client_secret' => getenv('GITEE_CLIENT_SECRET'),
                'callback_url' => getenv('GITEE_CALLBACK_URL'),
                'access_token' => $config['gitee_access_token'] ?? null,
            ],
            'github' => [
                'client_id' => getenv('GITHUB_CLIENT_ID'),
                'client_secret' => getenv('GITHUB_CLIENT_SECRET'),
                'callback_url' => getenv('GITHUB_CALLBACK_URL'),
                'access_token' => $config['github_access_token'] ?? null,
            ],
        ];
    }
}
