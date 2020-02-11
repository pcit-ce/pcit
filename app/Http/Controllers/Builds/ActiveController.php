<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class ActiveController
{
    /**
     * Returns a list of "active" builds for the owner.
     *
     * /owner/{git_type}/{username}/active
     *
     * @param array $args
     */
    public function __invoke(...$args): void
    {
        list($git_type, $username) = $args;
    }

    /**
     * This will activate a repository, allowing its tests to be run on PCIT.
     *
     * POST
     *
     * /repo/{repository.slug}/activate
     */
    public function activate(...$args): void
    {
        list($username, $repo) = $args;
    }

    /**
     * This will deactivate a repository, preventing any tests.
     *
     * POST
     *
     * /repo/{repository.slug}/deactivate
     */
    public function deactivate(...$args): void
    {
        list($username, $repo) = $args;
    }
}
