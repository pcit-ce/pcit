<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

use App\GetAccessToken;
use Exception;
use PCIT\PCIT;

class GitHubController
{
    private static $status;

    /**
     * GitHubController constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $pcit = new PCIT();

        self::$status = $pcit->repo_status;
    }

    /**
     * @param mixed ...$arg
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function list(...$arg)
    {
        return json_decode(self::$status->list(...$arg), true);
    }

    /**
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
    public function create(string $repo_full_name,
                           string $commit_sha,
                           string $state,
                           string $target_url,
                           string $description,
                           string $context,
                           string $accessToken = null
    ) {
        if (!$accessToken) {
            $accessToken = GetAccessToken::byRepoFullName($repo_full_name);
        }

        list($username, $repo) = explode('/', $repo_full_name);

        $pcit = new PCIT(['github_access_token' => $accessToken]);

        return $pcit->repo_status->create(
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
     *
     * @throws Exception
     */
    public function listCombinedStatus(...$arg)
    {
        return self::$status->listCombinedStatus(...$arg);
    }
}
