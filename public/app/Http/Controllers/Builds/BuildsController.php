<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Http\Controllers\APITokenController;
use App\Repo;
use Exception;
use KhsCI\Support\CI;

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
        $before = (int) $_GET['before'] ?? null;

        $limit = (int) $_GET['limit'] ?? null;

        list('git_type' => $git_type, 'uid' => $uid) = (APITokenController::getGitTypeAndUid())[0];

        $array = Build::allByAdmin($git_type, (int) $uid, $before, $limit);

        return $array;
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
        list($git_type, $username, $repo_name) = $args;

        $before = $_GET['before'] ?? null;
        $limit = $_GET['limit'] ?? null;
        $pr = $_GET['type'] ?? null;

        $before && $before = (int) $before;
        $limit && $limit = (int) $before;

        $rid = Repo::getRid(...$args);

        $array = Build::allByRid($git_type, (int) $rid, $before, $limit, (bool) $pr);

        $return_array = [];

        foreach ($array as $k) {
            $return_array[] = $k;
        }

        return $return_array;
    }

    /**
     * @param mixed ...$args
     *
     * @return array|int
     *
     * @throws Exception
     */
    public function repoCurrent(...$args)
    {
        list($git_type, $username, $repo_name) = $args;

        $build_key_id = Build::getCurrentBuildKeyId($git_type, (int) Repo::getRid(...$args));

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

        $output = Build::find((int) $build_id);

        if ($output) {
            return $output;
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
        APITokenController::check((int) $build_id);

        Build::updateBuildStatus((int) $build_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
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
        APITokenController::check((int) $build_id);

        Build::updateBuildStatus((int) $build_id, 'pending');
        Build::updateStartAt((int) $build_id, 0);
        Build::updateStopAt((int) $build_id, 0);
    }
}
