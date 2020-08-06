<?php

declare(strict_types=1);

namespace PCIT\Support;

class Config
{
    /**
     * 配置项 生成数组.
     *
     * @throws \Exception
     *
     * @return array
     */
    public static function config(array $config, string $git_type)
    {
        return $config = [
            'git_type' => $git_type,
            'api_url' => Git::getAPIUrl($git_type),
            'coding' => [
                'client_id' => config('git.coding.oauth.client_id'),
                'client_secret' => config('git.coding.oauth.client_secret'),
                'callback_url' => config('git.coding.oauth.callback_url'),
                'access_token' => $config['coding_access_token'] ?? null,
                'team' => config('git.coding.oauth.team'),
            ],
            'gitee' => [
                'client_id' => config('git.gitee.oauth.client_id'),
                'client_secret' => config('git.gitee.oauth.client_secret'),
                'callback_url' => config('git.gitee.oauth.callback_url'),
                'access_token' => $config['gitee_access_token'] ?? null,
            ],
            'github' => [
                'client_id' => config('git.github.oauth.client_id'),
                'client_secret' => config('git.github.oauth.client_secret'),
                'callback_url' => config('git.github.oauth.callback_url'),
                'access_token' => $config['github_access_token'] ?? null,
            ],
            'tencent_ai' => [
                'app_id' => config('ai.tencent.app.id'),
                'app_key' => config('ai.tencent.app.key'),
            ],
            'wechat' => [
                'app_id' => config('wechat.app.id'),
                'app_secret' => config('wechat.app.secret'),
                'token' => config('wechat.app.token'),
                'template_id' => config('wechat.template_id'),
                'open_id' => config('wechat.user_openid'),
            ],
        ];
    }
}
