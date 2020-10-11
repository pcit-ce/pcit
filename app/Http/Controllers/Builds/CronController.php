<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;
use PCIT\Framework\Attributes\Route;

class CronController
{
    /**
     * Returns a list of crons for an individual repository.
     *
     * @param array $args
     */
    #[Route('get', 'api/repo/{username}/{repo_name}/crons')]
    public function __invoke(...$args): void
    {
        list($username, $repo_name) = $args;

        JWTController::checkByRepo(...$args);
    }

    /**
     * Returns a single cron.
     *
     * @param $cron_id
     */
    #[Route('get', 'api/cron/{cron.id}')]
    public function find($cron_id): void
    {
    }

    /**
     * Deletes a single cron.
     *
     * @param $cron_id
     */
    #[Route('delete', 'api/cron/{cron.id}')]
    public function delete($cron_id): void
    {
    }

    /**
     * Returns the cron set for the specified branch for the specified repository.
     *
     * @param array $args
     */
    #[Route('get', 'api/repo/{username}/{repo_name}/branch/{branch.name}/cron')]
    public function findByBranch(...$args): void
    {
        list($username, $repo_name, $branch) = $args;

        JWTController::checkByRepo(...$args);
    }

    /**
     * This creates a cron on the specified branch for the specified repository.
     *
     * @param array $args
     */
    #[Route('post', 'api/repo/{username}/{repo_name}/branch/{branch.name}/cron')]
    public function createByBranch(...$args): void
    {
        list($username, $repo_name, $branch) = $args;

        JWTController::checkByRepo(...$args);
    }
}
