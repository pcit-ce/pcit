<?php

declare(strict_types=1);

namespace KhsCI\Service\GitHubApp;

use Curl\Curl;
use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\Env;
use KhsCI\Support\JWT;
use KhsCI\Support\Log;

/**
 * Class Installations.
 *
 * @see https://developer.github.com/v3/apps/installations/
 */
class Installations
{
    private static $curl;

    private static $api_url;

    /**
     * Installations constructor.
     *
     * @param Curl   $curl
     * @param string $api_url
     */
    public function __construct(Curl $curl, string $api_url)
    {
        self::$curl = $curl;

        self::$api_url = $api_url;
    }

    /**
     * List repositories that are accessible to the authenticated installation.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listRepositories()
    {
        $url = self::$api_url.'/installation/repositories';

        return self::$curl->get($url);
    }

    /**
     * List repositories that are accessible to the authenticated user for an installation.
     *
     * @param int $installation_id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listRepositoriesAccessible(int $installation_id)
    {
        $url = self::$api_url.'/user/installations/'.$installation_id.'/repositories';

        return self::$curl->get($url);
    }

    /**
     * Add a single repository to an installation.
     *
     * 204
     *
     * @param int    $installation_id
     * @param int    $repository_id
     * @param string $method
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function add(int $installation_id, int $repository_id, string $method = 'put'): void
    {
        $url = self::$api_url.'/user/installations/'.$installation_id.'/repositories/'.$repository_id;

        self::$curl->$method($url);

        $http_return_code = self::$curl->getCode();

        if (204 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code is not 204 '.$http_return_code);

            throw new Exception('GitHub App Add or remove repo to installation_id error', $http_return_code);
        }
    }

    /**
     * Remove repository from installation.
     *
     * 204
     *
     * @param int $installation_id
     * @param int $repository_id
     *
     * @throws Exception
     */
    public function remove(int $installation_id, int $repository_id): void
    {
        self::add($installation_id, $repository_id, 'delete');
    }

    /**
     * @param string $jwt
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getAppInfo(string $jwt)
    {
        $url = self::$api_url.'/app';

        return self::$curl->get($url, null, [
                'Authorization' => 'Bearer '.$jwt,
                'Accept' => 'application/vnd.github.machine-man-preview+json',
            ]
        );
    }

    /**
     * @param int    $installation_id
     * @param string $private_key_path
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getAccessToken(int $installation_id, string $private_key_path)
    {
        $redis = Cache::connect();

        $access_token = $redis->get("github_app_{$installation_id}_access_token");

        if ($access_token) {
            return $access_token;
        }

        $url = self::$api_url.'/installations/'.$installation_id.'/access_tokens';

        $access_token_json = self::$curl->post($url, null, [
            'Authorization' => 'Bearer '.self::getJWT($private_key_path),
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);

        $access_token_obj = json_decode($access_token_json);

        $http_return_code = self::$curl->getCode();

        if (201 !== $http_return_code) {
            Log::debug(__FILE__, __LINE__, 'Http Return Code is not 201 '.$http_return_code);

            throw new Exception('Get GitHub App AccessToken Error', $http_return_code);
        }

        $access_token = $access_token_obj->token;

        $redis->set("github_app_{$installation_id}_access_token", $access_token, 58 * 60);

        return $access_token;
    }

    /**
     * @param string $private_key_path
     *
     * @return string
     *
     * @throws Exception
     */
    private function getJWT(string $private_key_path)
    {
        $jwt = Cache::connect()->get('github_app_jwt');

        if ($jwt) {
            return $jwt;
        }

        $jwt = JWT::getJWT($private_key_path, (int) Env::get('CI_GITHUB_APP_ID'));

        Cache::connect()->set('github_app_jwt', $jwt, 8 * 60);

        return $jwt;
    }
}
