<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

use KhsCI\Service\Status\GitHub;
use KhsCI\Support\DB;

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
     *
     * @return mixed
     */
    public function list(...$arg)
    {
        return json_decode($this->status->list(...$arg), true);
    }

    /**
     * @param $login_username
     * @param $repo_full_name
     * @param $commit_sha
     * @param $state
     * @param $target_url
     * @param $description
     * @param $context
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function create($login_username, $repo_full_name, $commit_sha, $state, $target_url, $description, $context)
    {
        $sql = 'SELECT access_token FROM user WHERE username=? AND git_type=?';

        $output = DB::select($sql, [$login_username, 'github']);

        foreach ($output as $k) {
            $accessToken = $k['access_token'];
        }

        $array = explode('/', $repo_full_name);

        return $this->status->create($array[0],
            $array[1],
            $commit_sha,
            $accessToken,
            $state,
            $target_url,
            $description,
            $context);
    }

    public function listCombinedStatus(...$arg)
    {
        return $this->status->listCombinedStatus(...$arg);
    }
}
