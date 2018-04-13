<?php

declare(strict_types=1);

namespace KhsCI\Service\OAuth;

use Curl\Curl;

class Coding implements OAuth
{
    const URL = 'https://coding.net/oauth_authorize.html?';
    const POST_URL = 'https://coding.net/api/oauth/access_token?';

    private $curl;
    private $clientId;
    private $clientSecret;
    private $uri;
    private $scope;

    public function __construct($config, Curl $curl, $scope = null)
    {
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->uri = $config['callback_url'];
        $all_scope = 'user,user:email,notification,social,social:message,project,project:members,project:task,project:file,project:depot,project:key';
        $this->scope = $scope ?? $all_scope;
        $this->curl = $curl;
    }

    public function getLoginUrl(): void
    {
        $url = $this::URL.http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->uri,
                'response_type' => 'code',
                'scope' => $this->scope,
            ]);

        header('location:'.$url);
    }

    public function getAccessToken(string $code)
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

    public static function getUserInfo()
    {

    }

    public static function getProjects()
    {

    }

    public static function getWebhooks()
    {

    }

    public static function setWebhooks()
    {

    }


}
