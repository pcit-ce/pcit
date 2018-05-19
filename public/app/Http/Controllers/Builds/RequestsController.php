<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class RequestsController
{
    /**
     * Return a list of requests belonging to a repository.
     *
     * /repo/{repository.id}/requests
     */
    public function __invoke(): void
    {
        $limit = $_GET['limit'];
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
     */
    public function create(): void
    {
    }

    /**
     * Get single request details.
     *
     * /repo/{repository.id}/request/{request.id}
     *
     * @param array $args
     */
    public function find(...$args)
    {
        list($git_type, $username, $repo_name, $request_id) = $args;
    }
}
