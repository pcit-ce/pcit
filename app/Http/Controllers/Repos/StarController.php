<?php

declare(strict_types=1);

namespace App\Http\Controllers\Repos;

use App\Http\Controllers\Users\JWTController;
use PCIT\Framework\Attributes\Route;

/**
 * 收藏.
 */
class StarController
{
    /**
     * star a repository based on the currently logged in user.
     *
     * @param array $args
     *
     * @throws \Exception
     */
    @@Route('post', 'api/repo/{username}/{repo_name}/star')
    public function __invoke(...$args): void
    {
        list($username, $repo_name) = $args;

        JWTController::checkByRepo(...$args);
    }

    @@Route('post', 'api/repo/{username}/{repo_name}/unstar')
    /**
     * unstar a repository based on the currently logged in user.
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
