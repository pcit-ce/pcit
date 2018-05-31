<?php

declare(strict_types=1);

namespace KhsCI\Service\Gist;

use Exception;
use KhsCI\Service\CICommon;

class GitHubClient
{
    use CICommon;

    /**
     * List a user's gists.
     *
     * @param string|null $username
     * @param string      $since    YYYY-MM-DDTHH:MM:SSZ
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(string $username = null, string $since)
    {
        $url = $this->api_url.'/gists';

        if ($username) {
            $url = $this->api_url.'/users/'.$username.'/gists';
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

    public function ListStarred(): void
    {
    }

    public function find(): void
    {
    }

    public function getSpecificRevision(): void
    {
    }

    public function create(): void
    {
    }

    public function edit(): void
    {
    }

    public function listCommits(): void
    {
    }

    public function star(): void
    {
    }

    public function unstar(): void
    {
    }

    public function isStarred(): void
    {
    }

    public function fork(): void
    {
    }

    public function listFork(): void
    {
    }

    public function delete(): void
    {
    }
}
