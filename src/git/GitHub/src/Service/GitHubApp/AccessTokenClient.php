<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\GitHubApp;

use Curl\Curl;
use Exception;
use Firebase\JWT\JWT;

class AccessTokenClient
{
    private $curl;
    private $api_url;

    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    public function encodeJWT(string $private_key_path, int $github_app_id): string
    {
        $privateKey = file_get_contents($private_key_path);

        $token = [
            'iss' => $github_app_id,
            'iat' => time(),
            'exp' => time() + 10 * 60,
        ];

        return JWT::encode($token, $privateKey, 'RS256');
    }

    /**
     * GitHub App 由 JWT(expire 10mins) 获取 Token(expire 60mins).
     *
     * https://developer.github.com/apps/building-github-apps/authentication-options-for-github-apps/#authenticating-as-a-github-app
     *
     * @throws \Exception
     */
    public function getJWT(string $private_key_path): string
    {
        $jwt = \Cache::get('pcit/github_app_jwt');

        if ($jwt) {
            return $jwt;
        }

        $jwt = $this->encodeJWT($private_key_path, (int) config('git.github.app.id'));

        \Cache::set('pcit/github_app_jwt', $jwt, 8 * 60);

        return $jwt;
    }

    /**
     * @throws \Exception
     */
    public function getAccessToken(int $installation_id, string $private_key_path): string
    {
        \Log::debug('Get GitHub app Access Token ...');

        $redis = \Cache::store();

        $access_token = $redis->get("github_app_{$installation_id}_access_token");

        if ($access_token) {
            \Log::debug('Get GitHub app Access Token from cache success');

            return $access_token;
        }

        $url = $this->api_url.'/app/installations/'.$installation_id.'/access_tokens';

        $access_token_json = $this->curl->post($url, null, [
            'Authorization' => 'Bearer '.$this->getJWT($private_key_path),
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);

        $access_token_obj = json_decode($access_token_json);

        $http_return_code = $this->curl->getCode();

        if (201 !== $http_return_code) {
            \Log::debug('Http Return Code is not 201 '.$http_return_code);

            \Cache::delete('pcit/github_app_jwt');

            throw new Exception('Get GitHub App AccessToken Error '.$access_token_json, $http_return_code);
        }

        $access_token = $access_token_obj->token;

        $redis->set("github_app_{$installation_id}_access_token", $access_token, 58 * 60);

        \Log::debug('Get GitHub app Access Token from github success');

        return $access_token;
    }
}
