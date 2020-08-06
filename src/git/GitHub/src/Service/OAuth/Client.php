<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\OAuth;

use Curl\Curl;
use Exception;
use PCIT\GPI\Service\OAuth\OAuthInterface;

class Client implements OAuthInterface
{
    const TYPE = 'github';

    const API_URL = 'https://api.github.com';

    const URL = 'https://github.com/login/oauth/authorize?';

    const POST_URL = 'https://github.com/login/oauth/access_token?';

    private $clientId;

    private $clientSecret;

    private $callbackUrl;

    private $scope;

    private $curl;

    public $state = true;

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
            'security_events',
            // 'admin:repo_hook',
            // 'write:repo_hook',
            'read:repo_hook',
            'admin:org',
            'write:org',
            'read:org',
            'admin:public_key',
            'write:public_key',
            'read:public_key',
            'admin:org_hook',
            'gist',
            'notifications',
            'user',
            'read:user',
            'user:email',
            'user:follow',
            // 'delete_repo',
            'write:discussion',
            'read:discussion',
            'write:packages',
            'read:packages',
            // 'delete:packages',
            'admin:gpg_key',
            'write:gpg_key',
            'read:gpg_key',
            'workflow',
        ];

        $this->scope = $config['scope'] ?? implode(',', $all_scope);
        $this->curl = $curl;
    }

    public function getLoginUrl(?string $state): string
    {
        if (!($this->clientId and $this->clientSecret and $this->callbackUrl)) {
            return '';
        }

        $url = static::URL.http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callbackUrl,
            //'scope' => $this->scope,
            'state' => $state,
            //'allow_signup' => 'true',
        ]);

        return $url;
    }

    /**
     * @throws \Exception
     */
    public function getAccessToken(string $code, ?string $state, bool $json = true): array
    {
        $url = static::POST_URL.http_build_query(
            [
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

        \Log::debug('GitHub AccessToken Raw '.$accessToken);

        // {"access_token":"47bb","token_type":"bearer","scope":"admin:gpg_key,admin:org"}

        true === $json && $accessToken = json_decode($accessToken)->access_token ?? false;

        if ($accessToken) {
            return [$accessToken, ''];
        }

        throw new Exception('access_token not fount');
    }
}
