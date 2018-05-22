<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class CronController
{
    /**
     * Returns a list of crons for an individual repository.
     *
     * /repo/{repository.id}/crons
     *
     * @param array $args
     */
    public function __invoke(...$args): void
    {
        list($git_type, $username, $repo_name) = $args;
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
     * /repo/{repository.id}/branch/{branch.name}/cron
     *
     * @param array $args
     */
    public function findByBranch(...$args): void
    {
        list($git_type, $username, $repo_name, $branch) = $args;
    }

    /**
     * This creates a cron on the specified branch for the specified repository.
     *
     * post
     *
     * /repo/{repository.id}/branch/{branch.name}/cron
     *
     * @param array $args
     */
    public function createByBranch(...$args): void
    {
        list($git_type, $username, $repo_name, $branch) = $args;
    }
}
