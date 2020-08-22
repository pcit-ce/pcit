<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\GetAccessToken;
use App\Http\Controllers\Users\JWTController;
use App\Repo;
use Exception;
use PCIT\GetConfig;
use PCIT\PCIT;
use PCIT\Subject;
use Symfony\Component\Yaml\Yaml;

class RequestsController
{
    /**
     * Return a list of requests belonging to a repository.
     *
     * @param array $args
     *
     * @throws \Exception
     *
     * @return array|int
     */
    @@\Route('get', 'api/repo/{git_type}/{username}/{repo_name}/requests')
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
     *
     * @throws \Exception
     */
    @@\Route('post', 'api/repo/{username}/{repo_name}/requests')
    public function create(...$args)
    {
        list($username, $repo_name) = $args;

        list($rid,$git_type) = JWTController::checkByRepo($username, $repo_name);

        $token = GetAccessToken::byRid($rid,$git_type);

        /** @var \PCIT\GPI\GPI */
        $pcit = app(PCIT::class)->git($git_type,$token);

        // $body = file_get_contents('php://input');

        $body = \Request::getContent();

        $body_obj = json_decode($body);

        $config = $body_obj->request->config ?? '';
        $branch = $body_obj->request->branch ?? 'master';

        $result = $pcit->repo_branches->get($username, $repo_name, $branch);

        $result = json_decode($result);

        $compare = null;
        $commit = $result->commit ?? null;

        if (!$commit) {
            throw new \Exception('git repo empty, please commit to git repo', 404);
        }

        $commit_id = $commit->sha;
        $commit_message = $commit->commit->message;
        $committer = $commit->commit->committer;
        $author = $commit->commit->author;
        $event_time = time();

        if ($config) {
            $config = json_encode(Yaml::parse($config));
        } else {
            try {
                $subject = new Subject();
                $config_array = $subject->register(new GetConfig((int) $rid, $commit_id))->handle()->config_array;
                $config = json_encode($config_array);
            } catch (\Throwable $e) {
                // throw new \Exception($e->getMessage());
                $config = '[]';
            }
        }
        // TODO: 判断是否为私有仓库
        $last_insert_id = Build::insert(
            'push',
            $branch,
            $compare,
            $commit_id,
            $commit_message,
            $committer->name,
            $committer->email,
            $committer->name,
            $author->name,
            $author->email,
            $author->name,
            $rid,
            $event_time,
            $config,
            false,
            'github',
            true
        );

        // trigger build 不检测是否跳过
        // 检查是否有配置文件 .pcit.yml

        $status = '[]' === $config ? 'misconfigured' : 'pending';

        Build::updateBuildStatus((int) $last_insert_id, $status);

        \Log::info('build '.$last_insert_id.' is '.$status);

        return ['build_id' => $last_insert_id];
    }

    /**
     * Get single request details.
     *
     * @param array $args
     *
     * @throws \Exception
     *
     * @return array|int
     */
    @@\Route('get', 'api/repo/{username}/{repo_name}/request/{request.id}')
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
