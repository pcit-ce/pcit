<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\APITokenController;
use App\Repo;
use App\User;
use Exception;

class RepositoriesController
{
    public function index(): void
    {
        require __DIR__.'/../../../../public/repo/index.html';

        exit;
    }

    /**
     * This returns a list of repositories the current user has access to.
     *
     * /repos
     *
     * @throws Exception
     */
    public function __invoke()
    {
        list($git_type, $uid) = APITokenController::getUser();

        return Repo::allByRepoPrefix($git_type, User::getUsername($git_type, $uid));
    }

    /**
     * This returns a list of repositories an owner has access to.
     *
     * /{git_type}/{username}/repos
     *
     * @param string $git_type
     * @param string $username
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function list(string $git_type, string $username)
    {
        return Repo::allByRepoPrefix($git_type, $username);
    }

    /**
     * This returns an individual repository.
     *
     * /repo/{git_type}/{username}/{repo.name}
     *
     * @param string $git_type
     * @param string $username
     * @param string $repo_name
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function find(string $git_type, string $username, string $repo_name)
    {
        return Repo::findByRepoFullName($git_type, $username, $repo_name);
    }
}
