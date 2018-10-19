<?php

declare(strict_types=1);

namespace PCIT\Service\Repositories;

use Curl\Curl;
use Exception;

class CollaboratorsGitHubClient
{
    private $curl;

    private $api_url;

    /**
     * Collaborators constructor.
     *
     * @param Curl   $curl
     * @param string $api_url
     */
    public function __construct(Curl $curl, string $api_url)
    {
        $this->curl = $curl;

        $this->api_url = $api_url;
    }

    /**
     * List collaborators.
     *
     * @param string $repo_full_name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $repo_full_name)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/collaborators';

        return $this->curl->get($url);
    }

    /**
     * Check if a user is a collaborator.
     *
     * @param string $repo_full_name
     * @param string $user
     *
     * @return bool
     *
     * @throws Exception
     */
    public function exists(string $repo_full_name, string $user)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/collaborators/'.$user;

        $this->curl->get($url);

        $http_return_code = $this->curl->getCode();

        if (404 === $http_return_code) {
            return false;
        }

        if (204 === $http_return_code) {
            return true;
        }

        throw new Exception('', $http_return_code);
    }

    /**
     * Review a user's permission level.
     *
     * @param string $repo_full_name Repository name
     * @param string $user
     * @param string $level          admin write read none
     *
     * @return bool
     *
     * @throws Exception
     */
    public function reviewPermissionLevel(string $repo_full_name, string $user, string $level)
    {
        $url = $this->api_url.'/repos/'.$repo_full_name.'/collaborators/'.$user.'/'.$level;

        return $this->curl->get($url);
    }

    /**
     * Add user as a collaborator.
     */
    public function add(): void
    {
    }

    /**
     * Remove user as a collaborator.
     */
    public function remove(): void
    {
    }
}
