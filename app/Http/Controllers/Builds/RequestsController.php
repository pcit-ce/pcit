<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Http\Controllers\Users\JWTController;
use App\Repo;
use Exception;

class RequestsController
{
    /**
     * Return a list of requests belonging to a repository.
     *
     * /repo/{repository.id}/requests
     *
     * @param array $args
     *
     * @return array|int
     *
     * @throws Exception
     */
    public function __invoke(...$args)
    {
        // $limit = $_GET['limit'];

        list($username, $repo_name) = $args;

        list($uid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        $rid = Repo::getRid($username, $repo_name, $git_type);

        $output = Build::allByRid((int) $rid, null, null, true, true, $git_type);

        if ($output) {
            return $output;
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * Create a request for an individual repository, triggering a build to run on CI.
     *
     * post
     *
     * /repo/{repository.id}/requests
     *
     * <pre>
     *
     * {
     *     "request": {
     *         "message": "Override the commit message: this is an api request",
     *         "branch": "master"
     *     }
     * }
     *
     * <pre>
     *
     * @param array $args
     *
     * @throws Exception
     */
    public function create(...$args): void
    {
        list($username, $repo_name) = $args;

        JWTController::checkByRepo(...$args);
    }

    /**
     * Get single request details.
     *
     * /repo/{repository.id}/request/{request.id}
     *
     * @param array $args
     *
     * @return array|int
     *
     * @throws Exception
     */
    public function find(...$args)
    {
        list($username, $repo_name, $request_id) = $args;

        JWTController::checkByRepo(...$args);

        $output = Build::find((int) $request_id);

        if ($output) {
            return $output;
        }

        throw new Exception('Not Found', 404);
    }
}
