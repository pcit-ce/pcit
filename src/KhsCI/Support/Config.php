<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Exception;

class Config
{
    /**
     * 配置项 生成数组.
     *
     * @param array  $config
     * @param string $git_type
     *
     * @return array
     *
     * @throws Exception
     */
    public static function config(array $config, string $git_type)
    {
        return $config = [
            'git_type' => $git_type,
            'api_url' => Git::getAPIUrl($git_type),
            'coding' => [
                'client_id' => getenv('CI_CODING_CLIENT_ID'),
                'client_secret' => getenv('CI_CODING_CLIENT_SECRET'),
                'callback_url' => getenv('CI_CODING_CALLBACK_URL'),
                'access_token' => $config['coding_access_token'] ?? null,
            ],
            'gitee' => [
                'client_id' => getenv('CI_GITEE_CLIENT_ID'),
                'client_secret' => getenv('CI_GITEE_CLIENT_SECRET'),
                'callback_url' => getenv('CI_GITEE_CALLBACK_URL'),
                'access_token' => $config['gitee_access_token'] ?? null,
            ],
            'github' => [
                'client_id' => getenv('CI_GITHUB_CLIENT_ID'),
                'client_secret' => getenv('CI_GITHUB_CLIENT_SECRET'),
                'callback_url' => getenv('CI_GITHUB_CALLBACK_URL'),
                'access_token' => $config['github_access_token'] ?? null,
            ],
            'github_app' => [
                'client_id' => getenv('CI_GITHUB_APP_CLIENT_ID'),
                'client_secret' => getenv('CI_GITHUB_APP_CLIENT_SECRET'),
                'callback_url' => getenv('CI_GITHUB_APP_CALLBACK_URL'),
                'access_token' => $config['github_app_access_token'] ?? null,
            ],
            'tencent_ai' => [
                'app_id' => getenv('CI_TENCENT_AI_APPID'),
                'app_key' => getenv('CI_TENCENT_AI_APPKEY'),
            ],
        ];
    }
}
