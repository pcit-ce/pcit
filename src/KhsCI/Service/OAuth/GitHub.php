<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;

class GitHub implements OAuth
{
    const URL = 'https://github.com/login/oauth/authorize?';
    const POST_URL = 'https://github.com/login/oauth/access_token?';

    private $clientId;
    private $clientSecret;
    private $callbackUrl;
    private $scope;
    private $curl;

    public function __construct($config, Curl $curl)
    {
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->callbackUrl = $config['callback_url'];
        $all_scope = [
            'repo',
            'repo:status',
            'repo_deployment',
            'public_repo',
            'repo:invite',
            'admin:org',
            'write:org',
            'read:org',
            'admin:public_key',
            'write:public_key',
            'read:public_key',
            'admin:repo_hook',
            'write:repo_hook',
            'read:repo_hook',
            'admin:org_hook',
            'gist',
            'notifications',
            'user',
            'read:user',
            'user:email',
            'user:follow',
            'delete_repo',
            'write:discussion',
            'admin:gpg_key',
            'write:gpg_key',
            'read:gpg_key',
        ];

        $this->scope = $config['scope'] ?? implode(',', $all_scope);
        $this->curl = $curl;
    }

    public function getLoginUrl(?string $state): void
    {
        $url = self::URL.http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->callbackUrl,
                'scope' => $this->scope,
                'state' => $state,
                'allow_signup' => 'true',
            ]);

        header('Location:'.$url);
    }

    /**
     * @param string      $code
     * @param null|string $state
     * @param bool        $json
     *
     * @return mixed
     */
    public function getAccessToken(string $code, ?string $state, bool $json = true)
    {
        $url = self::POST_URL.http_build_query([
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code,
                    'redirect_uri' => $this->callbackUrl,
                    'state' => $state,
                ]
            );

        true === $json && $this->curl->setHeader('Accept', 'application/json');

        true !== $json && $this->curl->setHeader('Accept', 'application/xml');

        $accessToken = $this->curl->post($url);

        true === $json && $accessToken = json_decode($accessToken)->access_token;
        var_dump($accessToken);
        return $accessToken;
    }

    public static function getProjects(string $accessToken, int $page = 1)
    {
        $curl = new Curl;
        $curl->setHeader('Authorization', 'token '.$accessToken);
        $url = 'https://api.github.com/user/repos?page='.$page;

        return $curl->get($url);
    }
}
