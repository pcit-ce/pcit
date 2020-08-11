<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Env;
use App\Http\Controllers\Users\JWTController;

class EnvController
{
    /**
     * Returns a list of environment variables for an individual repository.
     *
     * @param array $args
     *
     * @throws \Exception
     *
     * @return array|string
     */
    @@\Route('get', 'api/repo/{username}/{repo_name}/env_vars')
    public function __invoke(...$args)
    {
        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        $array = Env::list((int) $rid, $git_type);

        $i = -1;

        foreach ($array as $item) {
            ++$i;
            if ('1' !== $item['public']) {
                $array[$i]['value'] = '***';
            }
        }

        return $array;
    }

    /**
     * Creates an environment variable for an individual repository.
     *
     * <pre>
     * { "env_var.name": "FOO", "env_var.value": "bar", "env_var.public": false }
     * </pre>
     *
     * @param array $args
     *
     * @throws \Exception
     *
     * @return string
     */
    @@\Route('post', 'api/repo/{username}/{repo_name}/env_vars')
    public function create(...$args)
    {
        $request = app('request');

        // $json = file_get_contents('php://input');
        $json = $request->getContent();

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
     * @param array $args
     *
     * @throws \Exception
     *
     * @return array|int
     */
    @@\Route('get', 'api/repo/{username}/{repo_name}/env_var/{env_var.id}')
    public function find(...$args)
    {
        list($username, $repo_name, $env_var_id) = $args;

        list($rid, $git_type, $uid) = JWTController::checkByRepo($username, $repo_name);

        return Env::find((int) $env_var_id, $rid, $git_type);
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
     * @param array $args
     *
     * @throws \Exception
     */
    @@\Route('patch', 'api/repo/{username}/{repo_name}/env_var/{env_var.id}')
    public function update(...$args): void
    {
        $request = app('request');

        list($username, $repo_name, $env_var_id) = $args;

        list($rid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        // $json = file_get_contents('php://input');
        $json = $request->getContent();

        list('env_var.value' => $value, 'env_var.public' => $public) = json_decode($json, true);

        Env::update($env_var_id, $rid, $value, (bool) $public, $git_type);
    }

    /**
     * Deletes a single environment variable.
     *
     * delete
     *
     * @param array $args
     *
     * @throws \Exception
     *
     * @return int
     */
    @@\Route('delete', 'api/repo/{username}/{repo_name}/env_var/{env_var.id}')
    public function delete(...$args)
    {
        list($username, $repo_name, $env_var_id) = $args;

        list($rid, $git_type, $uid) = JWTController::checkByRepo($username, $repo_name);

        return Env::delete((int) $env_var_id, $rid, $git_type);
    }
}
