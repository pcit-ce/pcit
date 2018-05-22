<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\APITokenController;

class CachesController
{
    /**
     * Returns all the caches for a repository.
     *
     * /repo/{repository.id}/caches
     *
     * @param array $args
     */
    public function __invoke(...$args): void
    {
        list($git_type, $username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);
    }

    /**
     * Deletes all caches for a repository.
     *
     * delete
     *
     * /repo/{repository.id}/caches
     *
     * @param array $args
     */
    public function delete(...$args): void
    {
        list($git_type, $username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);
    }
}
