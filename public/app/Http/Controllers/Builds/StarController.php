<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\APITokenController;

class StarController
{
    /**
     * star a repository based on the currently logged in user.
     *
     * post
     *
     * /repo/{repository.id}/star
     *
     * @param array $args
     */
    public function __invoke(...$args): void
    {
        list($username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);
    }

    /**
     * unstar a repository based on the currently logged in user.
     *
     * post
     *
     * /repo/{repository.slug}/unstar
     *
     * @param array $args
     */
    public function unStar(...$args): void
    {
        list($username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);
    }
}
