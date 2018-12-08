<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Env;
use App\Http\Controllers\Users\JWTController;
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
        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        return Env::list((int) $rid, $git_type);
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
     * @return string
     *
     * @throws Exception
     */
    public function create(...$args)
    {
        $json = file_get_contents('php://input');

        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        list(
            'env_var.name' => $name,
            'env_var.value' => $value,
            'env_var.public' => $public
            ) = json_decode($json, true);

        return Env::create((int) $rid, $name, $value, (bool) $public, $git_type);
    }

    /**
     * Returns a single environment variable.
     *
     * /repo/{repository.id}/env_var/{env_var.id}
     *
     * @param array $args
     *
     * @return array|int
     *
     * @throws Exception
     */
    public function find(...$args)
    {
        list($username, $repo_name, $env_var_id) = $args;

        list($rid, $git_type, $uid) = JWTController::checkByRepo($username, $repo_name);

        return env((int) $env_var_id, $rid, $git_type);
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

        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        $json = file_get_contents('php://input');

        list('env_var.value' => $value, 'env_var.public' => $public) = json_decode($json, true);

        Env::update($env_var_id, $rid, $value, (bool) $public, $git_type);
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
     * @return int
     *
     * @throws Exception
     */
    public function delete(...$args)
    {
        list($username, $repo_name, $env_var_id) = $args;

        list($rid, $git_type, $uid) = JWTController::checkByRepo($username, $repo_name);

        return Env::delete((int) $env_var_id, $rid, $git_type);
    }
}
