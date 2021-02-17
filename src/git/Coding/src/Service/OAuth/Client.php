<?php

declare(strict_types=1);

namespace PCIT\Coding\Service\OAuth;

use Curl\Curl;
use PCIT\GPI\Service\OAuth\OAuthInterface;

class Client implements OAuthInterface
{
    private $curl;

    private $clientId;

    private $clientSecret;

    private $callbackUrl;

    private $scope;

    private $baseurl;

    private $url;

    private $api_url;

    private $post_url;

    /**
     * Coding constructor.
     *
     * @param $config
     */
    public function __construct($config, Curl $curl)
    {
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->callbackUrl = $config['callback_url'];

        $team = $config['team'];

        if (!$team) {
            throw new \Exception('please set env CI_CODING_TEAM');
        }

        $this->baseurl = 'https://'.$team.'.coding.net/';
        $this->url = $this->baseurl.'oauth_authorize.html?';
        $this->post_url = $this->baseurl.'api/oauth/access_token?';
        $this->api_url = $this->baseurl.'api';

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

    public function getLoginUrl(?string $state): string
    {
        if (!($this->clientId && $this->clientSecret && $this->callbackUrl)) {
            return '';
        }

        return $this->url.http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callbackUrl,
            'response_type' => 'code',
            'scope' => $this->scope,
        ]);
    }

    /**
     * @return string|string[]
     */
    public function getAccessTokenByRefreshToken(string $refresh_token, bool $raw = false)
    {
        $json = $this->curl->post(
            $this->post_url.http_build_query(
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                ]
            )
        );

        \Log::debug('Coding AccessToken Raw '.$json);

        if (true === $raw) {
            return $json;
        }

        // {"access_token":"f2d0","refresh_token":"45924","expires_in":"692804"}

        return $this->parseTokenResult($json);
    }

    public function getAccessToken(string $code, ?string $state, bool $raw = false): array
    {
        $json = $this->curl->post(
            $this->post_url.http_build_query(
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                ]
            )
        );

        \Log::debug('Coding AccessToken Raw '.$json);

        if (true === $raw) {
            return $json;
        }

        // {"access_token":"f2d0","refresh_token":"45924","expires_in":"692804"}

        return $this->parseTokenResult($json);
    }

    /**
     * 解析服务器返回的结果.
     *
     * @param mixed $json
     */
    public function parseTokenResult($json): array
    {
        $resultObject = json_decode($json);

        $accessToken = $resultObject->access_token;
        $refreshToken = $resultObject->refresh_token;

        return [$accessToken, $refreshToken];
    }
}
