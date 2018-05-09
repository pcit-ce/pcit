<?php

declare(strict_types=1);

namespace KhsCI\Service\GitHubApps;

use Curl\Curl;
use Exception;
use KhsCI\Support\Env;
use KhsCI\Support\JWT;

class Installations
{
    const API_URL = 'https://api.github.com';

    private static $curl;

    public function __construct(Curl $curl)
    {
        self::$curl = $curl;
    }

    /**
     * List repositories that are accessible to the authenticated installation.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listRepositories($access_token)
    {
        $url = self::API_URL.'/installation/repositories';

        return self::$curl->get($url, null, [
                'Authorization' => 'token '.$access_token,
                'Accept' => 'application/vnd.github.machine-man-preview+json',
            ]
        );
    }

    /**
     * List repositories that are accessible to the authenticated user for an installation.
     *
     * @param int $installation_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listRepositoriesAccessible(int $installation_id)
    {
        $url = self::API_URL.'/user/installations/'.$installation_id.'/repositories';

        return self::$curl->get($url);
    }

    public function add(): void
    {
    }

    public function remove(): void
    {
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
        $url = self::API_URL.'/app';

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
        $url = self::API_URL.'/installations/'.$installation_id.'/access_tokens';

        $jwt = self::getJWT($private_key_path);

        return self::$curl->post($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    /**
     * @param string $private_key_path
     *
     * @return string
     */
    private function getJWT(string $private_key_path)
    {
        return JWT::getJWT($private_key_path, (int) Env::get('GITHUB_APP_ID'));
    }
}
