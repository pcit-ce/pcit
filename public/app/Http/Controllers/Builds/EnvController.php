<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\APITokenController;

class EnvController
{
    /**
     * Returns a list of environment variables for an individual repository.
     *
     * /repo/{repository.id}/env_vars
     *
     * @param array $args
     */
    public function __invoke(...$args): void
    {
        list($username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);
    }

    /**
     * Creates an environment variable for an individual repository.
     *
     * post
     *
     * <pre>
     * { "env_var.name": "FOO", "env_var.value": "bar", "env_var.public": false }
     * </pre>
     *
     * /repo/{repository.slug}/env_vars
     *
     * @param array $args
     */
    public function create(...$args): void
    {
        list($username, $repo_name) = $args;

        APITokenController::checkByRepo(...$args);

        $json = file_get_contents('php://input');

        list($name, $value, $public) = json_decode($json, true);
    }

    /**
     * Returns a single environment variable.
     *
     * /repo/{repository.id}/env_var/{env_var.id}
     *
     * @param array $args
     */
    public function find(...$args): void
    {
        list($username, $repo_name, $env_var_id) = $args;

        APITokenController::checkByRepo(...$args);
    }

    /**
     * Updates a single environment variable.
     *
     * patch
     *
     * <pre>
     * { "env_var.value": "bar", "env_var.public": false }
     * </pre>
     *
     * /repo/{repository.id}/env_var/{env_var.id}
     *
     * @param array $args
     */
    public function update(...$args): void
    {
        list($username, $repo_name, $env_var_id) = $args;

        APITokenController::checkByRepo(...$args);
    }

    /**
     * Deletes a single environment variable.
     *
     * delete
     *
     * /repo/{repository.id}/env_var/{env_var.id}
     *
     * @param array $args
     */
    public function delete(...$args): void
    {
        list($username, $repo_name, $env_var_id) = $args;

        APITokenController::checkByRepo(...$args);
    }
}
