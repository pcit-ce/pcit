<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;
use Exception;
use KhsCI\Support\Log;

class CodingClient implements OAuthInterface
{
    const API_URL = 'https://coding.net/api';

    const URL = 'https://coding.net/oauth_authorize.html?';

    const POST_URL = 'https://coding.net/api/oauth/access_token?';

    private $curl;

    private $clientId;

    private $clientSecret;

    private $callbackUrl;

    private $scope;

    /**
     * Coding constructor.
     *
     * @param      $config
     * @param Curl $curl
     */
    public function __construct($config, Curl $curl)
    {
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->callbackUrl = $config['callback_url'];
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

    /**
     * @param null|string $state
     *
     * @return mixed|string
     */
    public function getLoginUrl(?string $state)
    {
        $url = $this::URL.http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->callbackUrl,
                'response_type' => 'code',
                'scope' => $this->scope,
            ]);

        return $url;
    }

    /**
     * @param string      $code
     * @param null|string $state
     * @param bool        $raw
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getAccessToken(string $code, ?string $state, bool $raw = false)
    {
        $json = $this->curl->post($this::POST_URL.http_build_query([
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                ]
            )
        );

        Log::connect()->debug('Coding AccessToken Raw '.$json);

        if (true === $raw) {
            return $json;
        }

        // {"access_token":"f2d0","refresh_token":"45924","expires_in":"692804"}

        $accessToken = json_decode($json)->access_token;

        return $accessToken;
    }

    /**
     * @param       $method
     * @param       $url
     * @param array $data
     *
     * @return mixed
     */
    private static function http($method, $url, $data = [])
    {
        $url = static::API_URL.$url;

        $curl = new Curl();

        return $curl->$method($url);
    }

    /**
     * @param string $accessToken
     * @param bool   $raw
     *
     * @throws Exception
     *
     * @return array
     */
    public static function getUserInfo(string $accessToken, bool $raw = false)
    {
        $url = '/account/current_user?access_token='.$accessToken;

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

    /**
     * @param string $accessToken
     * @param int    $page
     * @param bool   $raw
     *
     * @return mixed
     */
    public static function getProjects(string $accessToken, int $page = 1, bool $raw = false)
    {
        $url = '/user/projects?access_token='.$accessToken;

        return $json = self::http('get', $url);
    }

    /**
     * @param string $accessToken
     * @param bool   $raw
     * @param string $username
     * @param string $project
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function getWebhooks(string $accessToken, bool $raw = false, string $username, string $project)
    {
        $url = '/user/'.$username.'/project/'.$project.'/git/hooks?access_token='.$accessToken;

        $json = self::http('get', $url);

        if (true === $raw) {
            return $json;
        }

        $obj = json_decode($json);

        $code = $obj->code;

        if (0 === $code) {
            return json_encode($obj->data);
        }

        throw new Exception('Project Not Found', 404);
    }

    /**
     * @param string $accessToken
     * @param        $data
     * @param string $username
     * @param string $repo
     * @param string $id
     *
     * @return mixed
     */
    public static function setWebhooks(string $accessToken, $data, string $username, string $repo, string $id)
    {
        $url = '/user/'.$username.'/project/'.$repo.'/git/hook/'.$id.'?access_token='.$accessToken;

        var_dump($url);

        return $json = self::http('post', $url);
    }

    public static function unsetWebhooks(string $accessToken, string $username, string $repo, string $id)
    {
        $url = sprintf('/user/%s/project/%s/git/hook/%s', $username, $repo, $id);

        return self::http('delete', $url);
    }
}
