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
     */
    public function create($login_username, $repo_full_name, $commit_sha, $state, $target_url, $description, $context)
    {
        $sql = <<<EOF
SELECT access_token FROM user WHERE username='$login_username' AND git_type='github';
EOF;

        $pdo = DB::connect();

        $output = $pdo->query($sql);

        foreach ($output as $k) {
            var_dump($k);
            $accessToken = $k[0];
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
}
