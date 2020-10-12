<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\GetAccessToken;
use App\Http\Controllers\Users\JWTController;
use App\Repo;
use Exception;
use JsonSchema\Constraints\BaseConstraint;
use PCIT\Config\Validator;
use PCIT\Framework\Attributes\Route;
use PCIT\Framework\Http\Request;
use PCIT\PCIT;
use Symfony\Component\Yaml\Yaml;

class RequestsController
{
    /**
     * Return a list of requests belonging to a repository.
     *
     * @param array $args
     *
     * @return array|int
     */
    #[Route('get', 'api/repo/{git_type}/{username}/{repo_name}/requests')]
    public function __invoke(...$args)
    {
        $request = app('request');

        list($git_type, $username, $repo_name) = $args;

        // $before = (int) $_GET['before'] ?? null;
        $before = $request->query->get('before');
        // $limit = (int) $_GET['limit'] ?? null;
        $limit = $request->query->get('limit');

        // list($uid, $git_type, $uid) = JWTController::checkByRepo(...$args);

        $rid = Repo::getRid($username, $repo_name, $git_type);

        $result = Build::allByRid(
            (int) $rid,
            (int) $before,
            (int) $limit,
            true,
            true,
            $git_type
        );

        if ($result) {
            return $result;
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * Create a request for an individual repository, triggering a build to run on CI.
     *
     * <pre>
     *
     * {
     *     "request": {
     *         "message": "Override the commit message: this is an api request",
     *         "branch": "master",
     *         "config": ""
     *     }
     * }
     *
     * <pre>
     *
     * @param array $args
     */
    #[Route('post', 'api/repo/{username}/{repo_name}/requests')]
    public function create(Request $request, Validator $validator, ...$args)
    {
        list($username, $repo_name) = $args;

        list($rid, $git_type) = JWTController::checkByRepo($username, $repo_name);

        $token = GetAccessToken::byRid($rid, $git_type);

        /** @var \PCIT\GPI\GPI */
        $pcit = app(PCIT::class)->git($git_type, $token);

        // $body = file_get_contents('php://input');

        $body = $request->getContent();

        $body_obj = json_decode($body);

        $config = $body_obj->request->config ?? '';
        $branch = $body_obj->request->branch ?? 'master';

        if ($config) {
            $config_array = Yaml::parse($config);

            $config = json_encode($config_array);

            $validator = new Validator();

            $result = $validator->validate(
                BaseConstraint::arrayToObjectRecursive($config_array)
            );

            if ([] !== $result) {
                $response = \Response::json($result);
                $response->setStatusCode(400);

                return $response;
            }
        }

        $pcit->repo->createDispatchEvent($username, $repo_name, 'pcit', compact(
            'branch',
            'config',
        ));
    }

    /**
     * Get single request details.
     *
     * @param array $args
     *
     * @return array|int
     */
    #[Route('get', 'api/repo/{username}/{repo_name}/request/{request.id}')]
    public function find(...$args)
    {
        list($username, $repo_name, $request_id) = $args;

        JWTController::checkByRepo(...$args);

        $result = Build::find((int) $request_id);

        if ($result) {
            return $result;
        }

        throw new Exception('Not Found', 404);
    }
}
