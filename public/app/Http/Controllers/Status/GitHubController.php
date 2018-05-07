<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\DB;

class GitHubController
{
    private static $status;

    public function __construct()
    {
        $khsci = new KhsCI();

        self::$status = $khsci->repo_status;
    }

    /**
     * @param mixed ...$arg
     *
     * @return mixed
     * @throws Exception
     */
    public function list(...$arg)
    {
        return json_decode(self::$status->list(...$arg), true);
    }

    /**
     * @param string $login_username
     * @param string $repo_full_name
     * @param string $commit_sha
     * @param string $state
     * @param string $target_url
     * @param string $description
     * @param string $context
     * @param string $accessToken
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function create(string $login_username,
                           string $repo_full_name,
                           string $commit_sha,
                           string $state,
                           string $target_url,
                           string $description,
                           string $context,
                           string $accessToken = null
    )
    {
        $sql = 'SELECT repo_admin FROM repo WHERE repo_full_name=? AND git_type=?';

        $admin = DB::select($sql, [$repo_full_name, 'github'], true);

        if (!$accessToken) {
            foreach (json_decode($admin, true) as $k) {
                $sql = 'SELECT access_token FROM user WHERE uid=? AND git_type=?';
                $output = DB::select($sql, [$k, 'github'], true);

                if ($output) {
                    $accessToken = $output;
                    break;
                }
            }
        }

        list($username, $repo) = explode('/', $repo_full_name);

        $khsci = new KhsCI(['github_access_token' => $accessToken]);

        return $khsci->repo_status->create(
            $username,
            $repo,
            $commit_sha,
            $state,
            $target_url,
            $description,
            $context
        );
    }

    /**
     * @param mixed ...$arg
     *
     * @return mixed
     * @throws Exception
     */
    public function listCombinedStatus(...$arg)
    {
        return self::$status->listCombinedStatus(...$arg);
    }
}
