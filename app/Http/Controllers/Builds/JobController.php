<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Job;

class JobController
{
    /**
     * @param $build_key_id
     *
     * @return array
     *
     * @throws \Exception
     */
    public function list($build_key_id)
    {
        return Job::getByBuildKeyID((int) $build_key_id);
    }

    /**
     * @param $job_id
     *
     * @return array|int
     *
     * @throws \Exception
     */
    public function find($job_id)
    {
        return Job::find((int) $job_id);
    }

    /**
     * @param $job_id
     *
     * @throws \Exception
     */
    public function cancel($job_id): void
    {
        Job::updateBuildStatus((int) $job_id, 'cancelled');

        $this->updateBuildStatus((int) $job_id);
    }

    /**
     * @param $job_id
     *
     * @throws \Exception
     */
    public function restart($job_id): void
    {
        Job::updateBuildStatus((int) $job_id, 'queued');

        $this->updateBuildStatus((int) $job_id);
    }

    /**
     * 更新 job 的状态，同时更新 build 的状态
     *
     * @param int $job_id
     *
     * @throws \Exception
     */
    private function updateBuildStatus(int $job_id): void
    {
        $build_key_id = Job::getBuildKeyId($job_id);

        $status = Job::getBuildStatusByBuildKeyId($build_key_id);

        Build::updateBuildStatus($build_key_id, $status);
    }
}
