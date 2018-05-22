<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\ApiToken;
use App\Build;
use App\Http\Controllers\APITokenController;
use App\Repo;
use Exception;
use KhsCI\Support\CI;

class BuildsController
{
    /**
     * Returns a list of builds for the current user. The result is paginated.
     *
     * /builds
     *
     * @throws Exception
     */
    public function __invoke()
    {
        $array = APITokenController::getGitTypeAndUid();

        list('git_type' => $git_type, 'uid' => $uid) = $array[0];

        $output = Repo::allByAdmin($git_type, (int) $uid);

        $build_array = [];

        foreach ($output as $k) {
            $rid = $k['rid'];

            foreach (Build::allByRid((int) $rid) as $build_k) {
                $build_array [] = $build_k;
            }
        }

        return $build_array;
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
        APITokenController::check((int) $build_id);

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

        Build::updateBuildStatus((int) $build_id, CI::BUILD_STATUS_CANCELED);
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

        Build::updateBuildStatus((int) $build_id, CI::BUILD_STATUS_PENDING);
    }
}
