<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\GitHubApp;

use Curl\Curl;
use Exception;

/**
 * Class Installations.
 *
 * @see https://developer.github.com/v3/apps/installations/
 */
class InstallationsClient
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
     * List repositories accessible to the app installation.
     *
     * @return mixed
     */
    public function listRepositories()
    {
        $url = $this->api_url.'/installation/repositories';

        return $this->curl->get($url, null, [
            'Accept' => 'application/vnd.github.mercy-preview+json,application/vnd.github.machine-man-preview+json',
        ]);
    }

    /**
     * List app installations accessible to the user access token.
     */
    public function listUser(string $oauthToken)
    {
        return $this->curl->get($this->api_url.'/user/installations', null, [
            'Authorization' => 'token '.$oauthToken,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    /**
     * List repositories accessible to the user access token.
     *
     * @return mixed
     */
    public function listRepositoriesByInstallationId(string $oauthToken, int $installation_id)
    {
        $url = $this->api_url.'/user/installations/'.$installation_id.'/repositories';

        return $this->curl->get($url, null, [
            'Authorization' => 'token '.$oauthToken,
            'Accept' => 'application/vnd.github.mercy-preview+json,application/vnd.github.machine-man-preview+json',
        ]);
    }

    /**
     * Add a single repository to an installation.
     * Token is personal access token.
     *
     * 204
     *
     * @return mixed
     */
    public function add(
        string $personal_access_token,
        int $installation_id,
        int $repository_id,
        string $method = 'put'
    ): void {
        $url = $this->api_url.'/user/installations/'.$installation_id.'/repositories/'.$repository_id;

        $this->curl->$method($url, null, [
            'Authorization' => 'token '.$personal_access_token,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);

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
     */
    public function remove(string $personal_access_token, int $installation_id, int $repository_id): void
    {
        $this->add($personal_access_token, $installation_id, $repository_id, 'delete');
    }

    public function RevokeAccessToken(): void
    {
        $this->curl->delete($this->api_url.'/installation/token');

        \Cache::set('pcit/github_app_jwt', '');
    }

    /**
     * 某用户或组织的 GitHub App 安装请求地址，即用户在此 URL 安装 GitHub App.
     *
     * @return string
     */
    public function getInstallUrl(int $rid)
    {
        return $url = 'https://github.com/apps/'.config('git.github.app.name').
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
     *
     * @param mixed $content_reference_id
     */
    public function createContentAttachment($content_reference_id, string $title, string $body)
    {
        $url = $this->api_url.'/content_references/'.$content_reference_id.'/attachments';

        return $this->curl->post($url, json_encode(compact('title', 'body')), [
            'Accept' => 'application/vnd.github.corsair-preview+json',
        ]);
    }
}
