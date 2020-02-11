<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Http\Controllers\Users\JWTController;
use App\Job;
use App\Repo;
use Exception;
use PCIT\Framework\Support\DB;
use PCIT\Support\CI;

class BuildsController
{
    /**
     * 某用户的构建列表.
     *
     * Returns a list of builds for the current user. The result is paginated.
     *
     * /builds
     *
     * @throws Exception
     */
    public function __invoke()
    {
        // $before = (int) $_GET['before'] ?? null;
        $before = app('request')->query->get('before');
        // $limit = (int) $_GET['limit'] ?? null;
        $limit = app('request')->query->get('limit');
        list($git_type, $uid) = JWTController::getUser();

        $result = Build::allByAdmin((int) $uid, (int) $before, (int) $limit, $git_type);

        return $result;
    }

    /**
     * 某仓库的构建列表.
     *
     * This returns a list of builds for an individual repository. The result is paginated. Each request will return 25
     * results.
     *
     * /repo/{git_type}/{username}/{repo.name}/builds
     *
     * @param mixed ...$args
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function listByRepo(...$args)
    {
        $request = app('request');

        list($git_type, $username, $repo_name) = $args;

        // $before = $_GET['before'] ?? null;
        $before = $request->query->get('before');
        // $limit = $_GET['limit'] ?? null;
        $limit = $request->query->get('limit');
        // $pr = $_GET['type'] ?? null;
        $pr = $request->query->get('type');

        $before && $before = (int) $before;
        $limit && $limit = (int) $before;

        $rid = Repo::getRid($username, $repo_name, $git_type);

        $preResult = Build::allByRid((int) $rid, (int) $before, (int) $limit, (bool) $pr, false, $git_type);

        $result = [];

        foreach ($preResult as $k) {
            $result[] = $k;
        }

        return $result;
    }

    /**
     * GET.
     *
     * /repo/{git_type}/{username}/{repo.name}/build/current
     *
     * @param mixed ...$args
     *
     * @return array|int
     *
     * @throws Exception
     */
    public function repoCurrent(...$args)
    {
        list($git_type, $username, $repo_name) = $args;

        $build_key_id = Build::getCurrentBuildKeyId(
            (int) Repo::getRid($username, $repo_name, $git_type), $git_type);

        return self::find($build_key_id);
    }

    /**
     * Returns a single build.
     *
     * /build/{build.id}
     *
     * @param $build_id
     *
     * @return array|int
     *
     * @throws Exception
     */
    public function find($build_id)
    {
        // APITokenController::check((int) $build_id);
        $result = Build::find((int) $build_id);

        if ($result) {
            $result['jobs'] = Job::allByBuildKeyID((int) $build_id);

            return $result;
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * This cancels a currently running build. It will set the build and associated jobs to "state": "canceled".
     *
     * /build/{build.id}/cancel
     *
     * @param $build_id
     *
     * @throws Exception
     */
    public function cancel($build_id): void
    {
        $build_id = (int) $build_id;

        JWTController::check($build_id);

        if (\function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        Build::updateBuildStatus($build_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);

        $this->updateJobStatus($build_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);

        Build::updateFinishedAt($build_id);
    }

    /**
     * Restarts a build that has completed or been canceled.
     *
     * /build/{build.id}/restart
     *
     * @param $build_id
     *
     * @throws Exception
     */
    public function restart($build_id): void
    {
        $build_id = (int) $build_id;
        JWTController::check($build_id);

        if ('misconfigured' === Build::getBuildStatus($build_id)) {
            throw new Exception('.pcit.yml not found', 500);
        }

        if (!Build::getConfig($build_id)) {
            Build::updateBuildStatus($build_id, 'misconfigured');
            throw new Exception('.pcit.yml not found', 500);
        }

        Build::updateBuildStatus($build_id, 'pending');
        Build::updateStartAt($build_id, null);
        $this->updateJobStatus($build_id, 'queued');
        // 更新 build 状态
        Build::updateFinishedAt($build_id);
        Build::deleteLog($build_id);
    }

    /**
     * 更新 build 的状态同时更新 job 的状态
     *
     * @throws Exception
     */
    private function updateJobStatus(int $build_id, string $status): void
    {
        DB::beginTransaction();
        $jobs = Job::getByBuildKeyID($build_id);

        foreach ($jobs as $job) {
            $job_id = (int) $job['id'];

            Job::updateBuildStatus($job_id, $status);

            if ('queued' === $status) {
                Job::updateCreatedAT($job_id, time());
                Job::updateStartAt($job_id, null);
                Job::updateFinishedAt($job_id, null);
            }

            'cancelled' === $status && (new JobController())->handleCancel($job_id);
        }
        DB::commit();
    }
}
