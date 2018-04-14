<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;
use Exception;
use KhsCI\Support\Response;

class Coding implements OAuth
{
    const URL = 'https://coding.net/oauth_authorize.html?';
    const POST_URL = 'https://coding.net/api/oauth/access_token?';

    private $curl;
    private $clientId;
    private $clientSecret;
    private $uri;
    private $scope;

    public function __construct($config, Curl $curl)
    {
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->uri = $config['callback_url'];
        $all_scope = [
            'user',
            'user:email',
            'notification',
            'social',
            'social:message',
            'project',
            'project:members',
            'project:task',
            'project:file',
            'project:depot',
            'project:key',
        ];
        $this->scope = $scope ?? implode(',', $all_scope);

        $this->curl = $curl;
    }

    public function getLoginUrl(?string $state): void
    {
        $url = $this::URL.http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->uri,
                'response_type' => 'code',
                'scope' => $this->scope,
            ]);

        Response::redirect($url);
    }

    public function getAccessToken(string $code, ?string $state)
    {
        $json = $this->curl->post($this::POST_URL.http_build_query([
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                ]
            )
        );

        return $json;
    }

    private static function http($method, $url, $data = [])
    {
        $url = 'https://coding.net'.$url;
        $curl = new Curl();

        return $curl->$method($url);
    }

    /**
     * @param string $accessToken
     * @param bool   $raw
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getUserInfo(string $accessToken, bool $raw = false)
    {
        $url = '/api/account/current_user?access_token='.$accessToken;

        $json = self::http('get', $url);

        if ($raw) {
            return $json;
        }

        $obj = json_decode($json)->data ?? false;

        if ($obj) {
            return [
                'uid' => $obj->id,
                'name' => $obj->global_key,
                'pic' => $obj->avatar,
            ];
        }

        throw new Exception('access_token not found');
    }

    public static function getProjects(string $accessToken, int $page = 1, bool $raw = false)
    {
        $url = '/api/user/projects?access_token='.$accessToken;

        return $json = self::http('get', $url);
    }

    public static function getWebhooks(string $accessToken, string $username, string $project, bool $raw)
    {
        $url = '/api/user/'.$username.'/project/'.$project.'/git/hooks?access_token='.$accessToken;

        return $json = self::http('get', $url);
    }

    public static function setWebhooks($accessToken, $username, $project)
    {
        $url = '/api/user/'.$username.'/project/'.$project.'/git/hook/{hook_id}?access_token='.$accessToken;

        return $json = self::http('get', $url);
    }
}
