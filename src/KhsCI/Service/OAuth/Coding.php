<?php

namespace KhsCI\Service\OAuth;

use Curl\Curl;
use KhsCI\Support\Config;

class Coding implements OAuth
{
    const URL = 'https://coding.net/oauth_authorize.html?';
    const POST_URL = 'https://coding.net/api/oauth/access_token';

    private $config;
    private $curl;
    private $clientId;
    private $clientSecret;
    private $uri;

    public function __construct($config, Curl $curl)
    {
        $this->config = $config;
        $this->curl = $curl;
    }

    public function getLoginUrl()
    {
        $url = $this::URL.http_build_query([
                'client_id' => $this->clientId,
                'redirect_uri' => $this->uri,
                'response_type' => 'code',
            ]);

        header('location:'.$url);
    }

    public function getAccessToken()
    {
        $code = $_GET['code'];

        $json = $this->curl->post($this::POST_URL.http_build_query([
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code
                ]
            )
        );

        return $json;
    }
}