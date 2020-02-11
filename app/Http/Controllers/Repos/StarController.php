<?php

declare(strict_types=1);

namespace App\Http\Controllers\Repos;

use App\Http\Controllers\Users\JWTController;

/**
 * 收藏.
 */
class StarController
{
    /**
     * star a repository based on the currently logged in user.
     *
     * post
     *
     * /repo/{repository.slug}/star
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
     * unstar a repository based on the currently logged in user.
     *
     * post
     *
     * /repo/{repository.slug}/unstar
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function unStar(...$args): void
    {
        list($username, $repo_name) = $args;

        JWTController::checkByRepo(...$args);
    }
}
