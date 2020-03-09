<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Gist;

use Exception;
use PCIT\GitHub\Service\CICommon;

class Client
{
    use CICommon;

    /**
     * List a user's gists.
     *
     * @param string $since YYYY-MM-DDTHH:MM:SSZ
     *
     * @return mixed
     *
     * @throws \Exception
     *
     * @see https://developer.github.com/v3/gists/#list-a-users-gists
     */
    public function list(string $username = null, string $since)
    {
        $url = $this->api_url.'/gists';

        if ($username) {
            $url = $this->api_url.'/users/'.$username.'/gists?'.http_build_query(['since' => $since]);
        }

        return $this->curl->get($url);
    }

    /**
     * GitHub 所有用户发布的最新 30*100 条 gists.
     */
    public function all()
    {
        return [];
    }

    /**
     * List starred gists.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function ListStarred(string $since = null)
    {
        return $this->curl->get($this->api_url.'/gists/starred?'.http_build_query(['since' => $since]));
    }

    /**
     * Get a single gist.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function find(string $gist_id)
    {
        return $this->curl->get($this->api_url.'/gists/'.$gist_id);
    }

    /**
     * Get a specific revision of a gist.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getSpecificRevision(string $gist_id, string $sha)
    {
        return $this->curl->get($this->api_url.'/gists/'.$gist_id.'/'.$sha);
    }

    /**
     * Create a gist.
     *
     * 201
     *
     * @param array $files [ $file_name => ['content' => $file_content ] ]
     *
     * @throws \Exception
     */
    public function create(array $files,
                           string $description,
                           bool $public = true): void
    {
        $data = [
            'description' => $description,
            'public' => $public,
            'files' => $files,
        ];

        $this->curl->post($this->api_url.'/gists', json_encode($data));
    }

    /**
     * Edit a gist.
     *
     * @throws \Exception
     */
    public function edit(array $files, string $description, string $gist_id): void
    {
        $data = [
            'description' => $description,
            'files' => $files,
        ];

        $this->curl->patch($this->api_url.'/gists/'.$gist_id, json_encode($data));
    }

    /**
     * List gist commits.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listCommits(string $gist_id)
    {
        return $this->curl->get($this->api_url.'/gists/'.$gist_id.'/commits');
    }

    /**
     * Star a gist.
     *
     * @throws \Exception
     */
    public function star(string $gist_id): void
    {
        $this->curl->put($this->api_url.'/gists/'.$gist_id.'/star');
    }

    /**
     * Unstar a gist.
     *
     * @throws \Exception
     */
    public function unstar(string $gist_id): void
    {
        $this->curl->delete($this->api_url.'/gists/'.$gist_id.'/star');
    }

    /**
     * Check if a gist is starred.
     *
     * 204
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isStarred(string $gist_id)
    {
        $this->curl->get($this->api_url.'/gists/'.$gist_id.'/star');

        $http_return_code = $this->curl->getCode();

        if (204 === $http_return_code) {
            return true;
        }

        if (404 === $http_return_code) {
            throw new Exception('Not Found', 404);
        }

        return false;
    }

    /**
     * Fork a gist.
     *
     * 201
     *
     * @throws \Exception
     */
    public function fork(string $gist_id): void
    {
        $this->curl->post($this->api_url.'/gists/'.$gist_id.'/forks');
    }

    /**
     * List gist forks.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listFork(string $gist_id)
    {
        return $this->curl->get($this->api_url.'/gists/'.$gist_id.'/forks');
    }

    /**
     * Delete a gist.
     *
     * 204
     *
     * @throws \Exception
     */
    public function delete(string $gist_id): void
    {
        $this->curl->delete($this->api_url.'/gists/'.$gist_id);
    }
}
