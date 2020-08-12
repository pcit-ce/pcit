<?php

declare(strict_types=1);

namespace PCIT\GitHub;

use Curl\Curl;
use PCIT\GPI\GPI;
use PCIT\GPI\Support\Git;
use TencentAI\TencentAI;

class GitHub extends GPI
{
    public $class_name = 'GitHub';

    public function __construct(TencentAI $tencent_ai, ?string $access_token = null)
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }

        $this['config'] = [
            'git_type' => $git_type = strtolower($this->class_name),
            'api_url' => Git::getAPIUrl($git_type),
            'github' => [
                'client_id' => config('git.github.oauth.client_id'),
                'client_secret' => config('git.github.oauth.client_secret'),
                'callback_url' => config('git.github.oauth.callback_url'),
                'access_token' => $access_token,
            ],
        ];

        $this['curl_config'] = $access_token ? [null, false,
            [
                'Authorization' => 'token '.$this['config']['github']['access_token'],
                'Accept' => 'application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json',
                'Content-Type' => 'application/json',
            ],
        ] : [];

        $this['curl_timeout'] = 1 * 60;
        $curl = new Curl(...$this['curl_config']);
        $curl->setTimeout($this['curl_timeout']);

        $this['curl'] = $curl;
        $this['tencent_ai'] = $tencent_ai;
    }
}
