<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\GitHubApp;

use Curl\Curl;

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

    public function get(string $name)
    {
        $url = $this->api_url.'/app/'.$name;

        return $this->curl->get($url, null, [
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function getAppInfo(string $jwt)
    {
        $url = $this->api_url.'/app';

        return $this->curl->get($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    public function listInstallations(string $jwt)
    {
        $url = $this->api_url.'/app/installations';

        return $this->curl->get($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    public function getInstallations(string $jwt, $installation_id)
    {
        $url = $this->api_url.'/app/installations/'.$installation_id;

        return $this->curl->get($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    public function suspendInstallation(string $jwt, $installation_id)
    {
        $url = $this->api_url.'/app/installations/'.$installation_id.'/suspended';

        return $this->curl->get($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    public function unsuspendInstallation(string $jwt, $installation_id)
    {
        $url = $this->api_url.'/app/installations/'.$installation_id.'/suspended';

        return $this->curl->delete($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    public function deleteInstallation(string $jwt, $installation_id)
    {
        $url = $this->api_url.'/app/installations/'.$installation_id;

        return $this->curl->delete($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    /**
     * Find organization installation.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function findOrganizationInstallation(string $jwt, string $org_name)
    {
        $url = $this->api_url.'/orgs/'.$org_name.'/installation';

        return $this->curl->get($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    /**
     * Find repository installation.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function findRepositoryInstallation(string $jwt, string $username, string $repo)
    {
        $url = $this->api_url.'/repos/'.$username.'/'.$repo.'/installation';

        return $this->curl->get($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }

    /**
     * Find user installation.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function findUserInstallation(string $jwt, string $username)
    {
        $url = $this->api_url.'/users/'.$username.'/installation';

        return $this->curl->get($url, null, [
            'Authorization' => 'Bearer '.$jwt,
            'Accept' => 'application/vnd.github.machine-man-preview+json',
        ]);
    }
}
