<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;

class CachesController
{
    /**
     * Returns all the caches for a repository.
     *
     * /repo/{repository.id}/caches
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function __invoke(...$args): void
    {
        list($username, $repo_name) = $args;

        JWTController::checkByRepo(...$args);
    }

    /**
     * Deletes all caches for a repository.
     *
     * delete
     *
     * /repo/{repository.id}/caches
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function delete(...$args): void
    {
        list($username, $repo_name) = $args;

        JWTController::checkByRepo(...$args);
    }
}
