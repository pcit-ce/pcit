<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;
use Exception;

class GitHub implements OAuth
{
    const API_URL = 'https://api.github.com';

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

    public function getLoginUrl(?string $state)
    {
        $url = static::URL.http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->callbackUrl,
                'scope' => $this->scope,
                'state' => $state,
                'allow_signup' => 'true',
            ]);

        return $url;
    }

    /**
     * @param string      $code
     * @param null|string $state
     * @param bool        $json
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function getAccessToken(string $code, ?string $state, bool $json = true)
    {
        $url = static::POST_URL.http_build_query([
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

        // {"access_token":"47bb","token_type":"bearer","scope":"admin:gpg_key,admin:org"}

        true === $json && $accessToken = json_decode($accessToken)->access_token ?? false;

        if ($accessToken) {
            return $accessToken;
        }

        throw new Exception('access_token not fount');
    }

    protected static function http(string $method, string $url, string $accessToken, ...$data)
    {
        $url = static::API_URL.$url;

        $curl = new Curl();

        $curl->setHeader('Authorization', 'token '.$accessToken);

        return $curl->$method($url);
    }

    public static function getUserInfo(string $accessToken, bool $raw = false)
    {
        $url = '/user';

        $json = static::http('get', $url, $accessToken);

        if ($raw) {
            return $json;
        }

        $obj = json_decode($json);

        return [
            'uid' => $obj->id,
            'name' => $obj->login,
            'pic' => $obj->avatar_url,
        ];
    }

    public static function getProjects(string $accessToken, int $page = 1, bool $raw = false)
    {
        $url = '/user/repos?page='.$page;

        return static::http('get', $url, $accessToken);
    }

    public static function getWebhooks(string $accessToken, string $username, string $repo, bool $raw = false)
    {
        $url = '/repos/'.$username.'/'.$repo.'/hooks';

        return self::http('get', $url, $accessToken);
    }

    public static function setWebhooks(string $accessToken, string $username, string $repo, array $data): void
    {
        $url = '/repos/'.$username.'/'.$repo.'/hooks';
    }

    public static function unsetWebhooks(string $accessToken, string $username, string $repo, string $id)
    {
        $url = sprintf('/repos/%s/%s/hooks/%s', $username, $repo, $id);

        return static::http('delete', $url, $accessToken);
    }
}
