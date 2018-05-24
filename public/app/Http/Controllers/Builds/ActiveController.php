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
     * This will activate a repository, allowing its tests to be run on KhsCI.
     *
     * POST
     *
     * /repo/{repository.id}/activate
     */
    public function activate(): void
    {
    }

    /**
     * This will deactivate a repository, preventing any tests from running on Travis CI.
     *
     * POST
     *
     * /repo/{repository.slug}/deactivate
     */
    public function deactivate(): void
    {
    }
}
