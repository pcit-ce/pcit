<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Console\Webhooks\GetConfig;
use App\Console\Webhooks\GitHub\Subject;
use App\GetAccessToken;
use App\Http\Controllers\Users\JWTController;
use App\Repo;
use Exception;
use PCIT\PCIT;
use Symfony\Component\Yaml\Yaml;

class RequestsController
{
    /**
     * Return a list of requests belonging to a repository.
     *
     * /repo/{repository.id}/requests
     *
     * @param array $args
     *
     * @return array|int
     *
     * @throws Exception
     */
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
            (int) $rid, (int) $before, (int) $limit, true, true, $git_type);

        if ($result) {
            return $result;
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * Create a request for an individual repository, triggering a build to run on CI.
     *
     * post
     *
     * /repo/{repository.id}/requests
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
     *
     * @throws Exception
     */
    public function create(...$args)
    {
        $request = app('request');

        list($username, $repo_name) = $args;

        list($rid) = JWTController::checkByRepo($username, $repo_name);

        $token = GetAccessToken::getGitHubAppAccessToken(
            null, $username.'/'.$repo_name);

        $app = new PCIT(['github_access_token' => $token], 'github');

        // $body = file_get_contents('php://input');

        $body = $request->getContent();

        $body_obj = json_decode($body);

        $config = $body_obj->request->config ?? '';
        $branch = $body_obj->request->branch ?? 'master';

        $result = $app->repo_branches->get($username, $repo_name, $branch);

        $result = json_decode($result);

        $compare = null;
        $commit = $result->commit;
        $commit_id = $commit->sha;
        $commit_message = $commit->commit->message;
        $committer = $commit->commit->committer;
        $author = $commit->commit->author;
        $event_time = time();

        if ($config) {
            $config = json_encode(Yaml::parse($config));
        } else {
            $subject = new Subject();
            $config_array = $subject->register(new GetConfig((int) $rid, $commit_id))->handle()->config_array;
            $config = json_encode($config_array);
        }
        $last_insert_id = Build::insert('push', $branch, $compare, $commit_id,
            $commit_message, $committer->name, $committer->email, $committer->name,
            $author->name, $author->email, $author->name,
            $rid, $event_time, $config, 'github', true);

        // trigger build 不检测是否跳过
        // 检查是否有配置文件 .pcit.yml

        $status = '[]' === $config ? 'misconfigured' : 'pending';

        Build::updateBuildStatus((int) $last_insert_id, $status);

        return ['build_id' => $last_insert_id];
    }

    /**
     * Get single request details.
     *
     * /repo/{repository.id}/request/{request.id}
     *
     * @param array $args
     *
     * @return array|int
     *
     * @throws Exception
     */
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
