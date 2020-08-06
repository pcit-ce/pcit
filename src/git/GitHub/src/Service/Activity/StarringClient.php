<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Activity;

use PCIT\GPI\ServiceClientCommon;

class StarringClient
{
    use ServiceClientCommon;

    /**
     * List Stargazers.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function list(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/stargazers');
    }

    /**
     * List repositories being starred.
     *
     * @param string $username  created or updated
     * @param string $direction asc or desc
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function listRepositoriesBeingStarred(string $username = null, string $sort = 'created', string $direction = 'desc')
    {
        $data = [
            'sort' => $sort,
            'direction' => $direction,
        ];

        if ($username) {
            return $this->curl->get($this->api_url.'/users/'.$username.'/starred?'.http_build_query($data));
        }

        return $this->curl->get($this->api_url.'/user/starred?'.http_build_query($data));
    }

    /**
     * Check if you are starring a repository.
     *
     * 204 404
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function checkStarringRepository(string $repo_full_name)
    {
        $this->curl->get($this->api_url.'/user/starred/'.$repo_full_name);

        $http_return_code = $this->curl->getCode();

        if (204 === $http_return_code) {
            return true;
        }

        if (404 === $http_return_code) {
            return false;
        }

        throw new \Exception('Error', $http_return_code);
    }

    /**
     * Star a repository.
     *
     * 204
     *
     * @throws \Exception
     */
    public function star(string $repo_full_name): void
    {
        $this->curl->put($this->api_url.'/user/starred/'.$repo_full_name);
    }

    /**
     * Unstar a repository.
     *
     * 204
     *
     * @throws \Exception
     */
    public function unstar(string $repo_full_name): void
    {
        $this->curl->delete($this->api_url.'/user/starred/'.$repo_full_name);
    }
}
