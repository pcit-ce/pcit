<?php

declare(strict_types=1);

namespace PCIT\Service\OAuth;

use Curl\Curl;
use Exception;
use PCIT\Support\Log;

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
     * @return string
     */
    public function getLoginUrl(?string $state): string
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
     * @return array
     *
     * @throws Exception
     */
    public function getAccessToken(string $code, ?string $state, bool $raw = false): array
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

        return $this->parseTokenResult($json);
    }

    /**
     * 解析服务器返回的结果.
     *
     * @return array
     */
    public function parseTokenResult($json): array
    {
        $resultObject = json_decode($json);

        $accessToken = $resultObject->access_token;
        $refreshToken = $resultObject->refresh_token;

        return [$accessToken, $refreshToken];
    }
}
