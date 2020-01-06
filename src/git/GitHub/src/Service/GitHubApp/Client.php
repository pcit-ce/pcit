<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\GitHubApp;

use Curl\Curl;
use Exception;
use PCIT\Framework\Support\Env;
use PCIT\Framework\Support\JWT;

/**
 * Class Installations.
 *
 * @see https://developer.github.com/v3/apps/installations/
 */
class Client
{
    private $curl;

    private $api_url;

    /**
     * Installations constructor.
     */
    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
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
        $url = $this->api_url.'/installation/repositories';

        return $this->curl->get($url);
    }

    /**
     * List repositories that are accessible to the authenticated user for an installation.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function listRepositoriesAccessible(int $installation_id)
    {
        $url = $this->api_url.'/user/installations/'.$installation_id.'/repositories';

        return $this->curl->get($url);
    }

    /**
     * Add a single repository to an installation.
     *
     * 204
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function add(int $installation_id, int $repository_id, string $method = 'put'): void
    {
        $url = $this->api_url.'/user/installations/'.$installation_id.'/repositories/'.$repository_id;

        $this->curl->$method($url);

        $http_return_code = $this->curl->getCode();

        if (204 !== $http_return_code) {
            \Log::debug('Http Return Code is not 204 '.$http_return_code);

            throw new Exception('GitHub App Add or remove repo to installation_id error', $http_return_code);
        }
    }

    /**
     * Remove repository from installation.
     *
     * 204
     *
     * @throws Exception
     */
    public function remove(int $installation_id, int $repository_id): void
    {
        $this->add($installation_id, $repository_id, 'delete');
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function getAppInfo(string $jwt)
    {
        $url = $this->api_url.'/app';

        return $this->curl->get($url, null, [
                'Authorization' => 'Bearer '.$jwt,
                'Accept' => 'application/vnd.github.machine-man-preview+json',
            ]
        );
    }

    /**
     * @param string $private_key_path
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getAccessToken(int $installation_id, string $private_key_path = null)
    {
        $private_key_path = $private_key_path ??
            base_path().'framework/storage/private_key/private.key';

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

            \Cache::store()->delete('pcit/github_app_jwt');

            throw new Exception('Get GitHub App AccessToken Error '.$access_token_json, $http_return_code);
        }

        $access_token = $access_token_obj->token;

        $redis->set("github_app_{$installation_id}_access_token", $access_token, 58 * 60);

        \Log::debug('Get GitHub app Access Token from github success');

        return $access_token;
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    private function getJWT(string $private_key_path)
    {
        $jwt = \Cache::store()->get('pcit/github_app_jwt');

        if ($jwt) {
            return $jwt;
        }

        $jwt = JWT::getJWT($private_key_path, (int) env('CI_GITHUB_APP_ID'));

        \Cache::store()->set('pcit/github_app_jwt', $jwt, 8 * 60);

        return $jwt;
    }

    /**
     * Find organization installation.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function findOrganizationInstallation(string $org_name)
    {
        $url = $this->api_url.'/orgs/'.$org_name.'/installation';

        return $this->curl->get($url);
    }

    /**
     * Find repository installation.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function findRepositoryInstallation(string $username, string $repo)
    {
        $url = $this->api_url.'/repos/'.$username.'/'.$repo.'/installation';

        return $this->curl->get($url);
    }

    /**
     * Find user installation.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function findUserInstallation(string $username)
    {
        $url = $this->api_url.'/users/'.$username.'/installation';

        return $this->curl->get($url);
    }

    /**
     * 某用户或组织的 GitHub App 安装请求地址，即用户在此 URL 安装 GitHub App.
     *
     * @return string
     */
    public function getInstallUrl(int $rid)
    {
        return $url = 'https://github.com/apps/'.env('CI_GITHUB_APP_NAME').
            '/installations/new/permissions?suggested_target_id='.$rid;
    }

    /**
     * @return string
     */
    public function getSettingsUrlByUser(int $installation_id)
    {
        return $url = 'https://github.com/settings/installations/'.$installation_id;
    }

    /**
     * @return string
     */
    public function getSettingsUrlByOrg(string $org_name, int $installation_id)
    {
        return $url = 'https://github.com/organizations/'.$org_name.'/settings/installations/'.$installation_id;
    }

    /**
     * @see https://developer.github.com/v3/apps/#create-a-content-attachment
     */
    public function createContentAttachment($content_reference_id, string $title, string $body)
    {
        $url = $this->api_url.'/content_references/'.$content_reference_id.'/attachments';

        return $this->curl->post($url, json_encode(compact('title', 'body')), [
            'Accept' => 'application/vnd.github.corsair-preview+json',
        ]);
    }
}
