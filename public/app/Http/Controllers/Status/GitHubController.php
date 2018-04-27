<?php

namespace App\Http\Controllers\Status;

use KhsCI\Service\Status\GitHub;
use KhsCI\Support\Session;

class GitHubController
{
    public $status;

    /**
     * GitHubController constructor.
     */
    public function __construct()
    {
        $this->status = new GitHub();
    }

    /**
     * @param mixed ...$arg
     * @return mixed
     */
    public function list(...$arg)
    {
        return json_decode($this->status->list(...$arg), true);
    }

    /**
     * @param $username
     * @param $repo
     * @param $commit_sha
     * @param $state
     * @param $target_url
     * @param $description
     * @param $context
     * @return mixed
     */
    public function create($username, $repo, $commit_sha, $state, $target_url, $description, $context)
    {
        $accessToken = Session::get('github.access_token');

        return $this->status->create($username,
            $repo,
            $commit_sha,
            $accessToken,
            $state,
            $target_url,
            $description,
            $context);
    }
}
