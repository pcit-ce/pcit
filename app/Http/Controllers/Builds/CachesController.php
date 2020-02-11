<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;

class CachesController
{
    /**
     * Returns all the caches for a repository.
     *
     * /repo/{repository.slug}/caches
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function __invoke(...$args)
    {
        list($username, $repo_name) = $args;

        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        return [];
    }

    /**
     * Deletes all caches for a repository.
     *
     * delete
     *
     * /repo/{repository.slug}/caches
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
