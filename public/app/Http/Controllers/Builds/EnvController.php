<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class EnvController
{
    /**
     * Returns a list of environment variables for an individual repository.
     *
     * /repo/{repository.id}/env_vars
     *
     * @param array $args
     */
    public function __invoke(...$args)
    {
        list($git_type, $username, $repo_name) = $args;
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
     */
    public function create(): void
    {
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
    public function find(...$args)
    {
        list($git_type, $username, $repo_name, $env_var_id) = $args;
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
    public function update(...$args)
    {
        list($git_type, $username, $repo_name, $env_var_id) = $args;
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
        list($git_type, $username, $repo_name, $env_var_id) = $args;
    }
}
