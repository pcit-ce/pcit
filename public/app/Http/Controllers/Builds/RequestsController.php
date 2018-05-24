<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Http\Controllers\APITokenController;
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

        list($git_type, $username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);

        $rid = Repo::getRid($git_type, $username, $repo_name);

        $output = Build::listByRid((int) $rid);

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
     */
    public function create(...$args): void
    {
        list($git_type, $username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);
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
        list($git_type, $username, $repo_name, $request_id) = $args;

        APITokenController::checkByRepo(...$args);

        $output = Build::find((int) $request_id);

        if ($output) {
            return $output;
        }

        throw new Exception('Not Found', 404);
    }
}
