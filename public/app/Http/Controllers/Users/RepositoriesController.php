<?php

namespace App\Http\Controllers\Users;


class RepositoriesController
{
    public function index()
    {
        require __DIR__.'/../../../../public/repo/index.html';

        exit;
    }

    /**
     * This returns a list of repositories the current user has access to.
     *
     * /repos/
     */
    public function __invoke()
    {

    }

    /**
     * This returns a list of repositories an owner has access to.
     *
     * /owner/{git_type}/{username}/repos
     *
     * @param string $git_type
     * @param string $username
     */
    public function list(string $git_type, string $username)
    {

    }

    /**
     * This returns an individual repository.
     *
     * /repo/{git_type}/{username}/{repo.name}
     *
     * @param string $git_type
     * @param string $username
     * @param string $repo_name
     */
    public function find(string $git_type, string $username, string $repo_name)
    {

    }
}
