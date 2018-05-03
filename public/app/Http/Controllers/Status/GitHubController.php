<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

use Exception;
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
     * @param string $login_username
     * @param string $repo_full_name
     * @param string $commit_sha
     * @param string $state
     * @param string $target_url
     * @param string $description
     * @param string $context
     *
     * @return mixed
     * @throws Exception
     */
    public function create(string $login_username,
                           string $repo_full_name,
                           string $commit_sha,
                           string $state,
                           string $target_url,
                           string $description,
                           string $context)
    {
        $sql = 'SELECT access_token FROM user WHERE username=? AND git_type=?';

        $output = DB::select($sql, [$login_username, 'github']);

        foreach ($output as $k) {
            $accessToken = $k['access_token'];
        }

        list($username, $repo) = explode('/', $repo_full_name);

        return $this->status->create(
            $username,
            $repo,
            $commit_sha,
            $accessToken,
            $state,
            $target_url,
            $description,
            $context
        );
    }

    public function listCombinedStatus(...$arg)
    {
        return $this->status->listCombinedStatus(...$arg);
    }
}
