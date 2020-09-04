<?php

declare(strict_types=1);

namespace App\Http\Controllers\Status;

use PCIT\Framework\Attributes\Route;

class GitHubController
{
    private static $status;

    /**
     * GitHubController constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        self::$status = \PCIT::check_run;
    }

    /**
     * @param mixed ...$arg
     * @param mixed $username
     * @param mixed $repo_name
     * @param mixed $ref
     *
     * @throws \Exception
     */
    #[Route('get', 'api/status/github/{username}/{repo_name}/{ref}')]
    public function list($username, $repo_name, $ref)
    {
        $repo_full_name = $username.'/'.$repo_name;

        return \Response::json(self::$status->listSpecificRef($repo_full_name, $ref), true);
    }
}
