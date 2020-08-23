<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Repo;
use App\User;
use PCIT\Framework\Attributes\Route;

class RepositoriesController
{
    /**
     * This returns a list of repositories the current user has access to.
     *
     * @throws \Exception
     */
    @@Route('get', 'api/repos')
    public function __invoke()
    {
        list($uid, $git_type) = JWTController::getUser(false);

        return Repo::allByUsername(User::getUsername($uid, $git_type), $git_type);
    }

    /**
     * This returns a list of repositories an owner has access to.
     *
     * @throws \Exception
     *
     * @return array|string
     */
    @@Route('get', 'api/repos/{git_type}/{username}')
    public function list(string $git_type, string $username)
    {
        return Repo::allByUsername($username, $git_type);
    }

    /**
     * This returns an individual repository.
     *
     * @throws \Exception
     *
     * @return array|string
     */
    @@Route('get', 'api/repo/{git_type}/{username}/{repo_name}')
    public function find(string $git_type, string $username, string $repo_name)
    {
        return Repo::findByRepoFullName($username, $repo_name, $git_type)[0] ?? [];
    }
}
