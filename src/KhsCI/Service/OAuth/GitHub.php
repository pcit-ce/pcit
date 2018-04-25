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
     * @param string $code
     * @param null|string $state
     * @param bool $json
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

    /**
     * @param string $method
     * @param string $url
     * @param string $accessToken
     * @param        $data
     * @param array $header
     *
     * @return mixed
     */
    protected static function http(string $method, string $url, string $accessToken, $data = null, array $header = [])
    {
        $url = static::API_URL.$url;

        $curl = new Curl();

        set_time_limit(0);

        $curl->setTimeout(100);

        $curl->setHeader('Authorization', 'token '.$accessToken);

        if ($header) {
            foreach ($header as $k => $v) {
                $curl->setHeader($k, $v);
            }
        }

        return $curl->$method($url, $data);
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

    /**
     * @param string $accessToken
     * @param bool $raw
     * @param string $username
     * @param string $repo
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function getWebhooks(string $accessToken, bool $raw = false, string $username, string $repo)
    {
        $url = '/repos/'.$username.'/'.$repo.'/hooks';

        $json = self::http('get', $url, $accessToken);

        if (true === $raw) {
            return $json;
        }

        $obj = json_decode($json);

        if (null === $obj or $obj->message ?? false) {
            throw new Exception('Project Not Found', 404);
        }

        return $json;
    }

    /**
     * @param string $accessToken
     * @param string $username
     * @param string $repo
     * @param string $url
     * @return int
     * @throws Exception
     */
    public static function getWebhooksStatus(string $accessToken, string $url, string $username, string $repo)
    {
        $json = self::getWebhooks($accessToken, false, $username, $repo);

        $array = json_decode($json);

        if ($array) {
            foreach ($array as $k) {
                if ($url === $k->url) {
                    return 1;
                    break;
                }
            }
        }

        return 0;
    }

    /**
     * @param string $accessToken
     * @param $data
     * @param string $username
     * @param string $repo
     * @param null|string $id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function setWebhooks(string $accessToken, $data, string $username, string $repo, ?string $id)
    {
        $url = '/repos/'.$username.'/'.$repo.'/hooks';

        $json = self::http('post', $url, $accessToken, $data, ['content-type' => 'application/json']);

        json_decode($json);

        if (0 !== json_last_error()) {
            throw new Exception('Project Not Found', 404);
        }

        return $json;
    }

    public static function unsetWebhooks(string $accessToken, string $username, string $repo, string $id)
    {
        $url = sprintf('/repos/%s/%s/hooks/%s', $username, $repo, $id);

        return static::http('delete', $url, $accessToken);
    }
}
