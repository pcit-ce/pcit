<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;

class CronController
{
    /**
     * Returns a list of crons for an individual repository.
     *
     * /repo/{repository.slug}/crons
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
     * Returns a single cron.
     *
     * /cron/{cron.id}
     *
     * @param $cron_id
     */
    public function find($cron_id): void
    {
    }

    /**
     * Deletes a single cron.
     *
     * delete
     *
     * /cron/{cron.id}
     *
     * @param $cron_id
     */
    public function delete($cron_id): void
    {
    }

    /**
     * Returns the cron set for the specified branch for the specified repository.
     *
     * /repo/{repository.slug}/branch/{branch.name}/cron
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function findByBranch(...$args): void
    {
        list($username, $repo_name, $branch) = $args;

        JWTController::checkByRepo(...$args);
    }

    /**
     * This creates a cron on the specified branch for the specified repository.
     *
     * post
     *
     * /repo/{repository.slug}/branch/{branch.name}/cron
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function createByBranch(...$args): void
    {
        list($username, $repo_name, $branch) = $args;

        JWTController::checkByRepo(...$args);
    }
}
