<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Env;
use App\Http\Controllers\APITokenController;
use App\Repo;
use Exception;

class EnvController
{
    /**
     * Returns a list of environment variables for an individual repository.
     *
     * /repo/{repository.id}/env_vars
     *
     * @param array $args
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function __invoke(...$args)
    {
        list($rid, $git_type, $uid) = APITokenController::checkByRepo(...$args);

        return Env::list($git_type, (int) $rid);
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
     *
     * @throws Exception
     */
    public function create(...$args)
    {
        $json = file_get_contents('php://input');

        list($rid, $git_type, $uid) = APITokenController::checkByRepo(...$args);

        list(
            'env_var.name' => $name,
            'env_var.value' => $value,
            'env_var.public' => $public
            ) = json_decode($json, true);

        return Env::create($git_type, (int) $rid, $name, $value, $public);
    }

    /**
     * Returns a single environment variable.
     *
     * /repo/{repository.id}/env_var/{env_var.id}
     *
     * @param array $args
     *
     * @throws Exception
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
     *
     * @throws Exception
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
     *
     * @throws Exception
     */
    public function delete(...$args): void
    {
        list($username, $repo_name, $env_var_id) = $args;

        APITokenController::checkByRepo(...$args);
    }
}
