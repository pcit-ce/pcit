<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\APITokenController;

class SettingsController
{
    /**
     * Returns a list of the settings for that repository.
     *
     * /repo/{repository.id}/settings
     *
     * @param array $args
     */
    public function __invoke(...$args): void
    {
        list($git_type, $username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);
    }

    /**
     * Returns a single setting.
     *
     * /repo/{repository.id}/setting/{setting.name}
     *
     * @param array $args
     */
    public function get(...$args): void
    {
        list($git_type, $username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);
    }

    /**
     * Updates a single setting.
     *
     * patch
     *
     * /repo/{repository.id}/setting/{setting.name}
     *
     * <pre>
     * ['setting.value'=>true]
     *
     * { "setting.value": true }
     * </pre>
     *
     * @param array $args
     */
    public function update(...$args): void
    {
        list($git_type, $username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);

        $json = file_get_contents('php://input');
    }
}
