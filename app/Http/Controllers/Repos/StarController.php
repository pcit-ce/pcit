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
    #[Route('post', 'api/repo/{username}/{repo_name}/star')]
    public function __invoke(...$args): void
    {
        list($username, $repo_name) = $args;

        JWTController::checkByRepo(...$args);
    }

    /**
     * unstar a repository based on the currently logged in user.
     *
     * @param array $args
     *
     * @throws \Exception
     */
    #[Route('post', 'api/repo/{username}/{repo_name}/unstar')]
    public function unStar(...$args): void
    {
        list($username, $repo_name) = $args;

        JWTController::checkByRepo(...$args);
    }
}
