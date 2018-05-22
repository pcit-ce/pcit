<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use Exception;
use KhsCI\Support\CI;

class BuildsController
{
    /**
     * Returns a list of builds for the current user. The result is paginated.
     *
     * /builds
     */
    public function __invoke(): void
    {
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
        Build::updateBuildStatus((int) $build_id, CI::BUILD_STATUS_PENDING);
    }
}
