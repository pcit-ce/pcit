<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Job;

class JobController
{
    public function __invoke(...$arg): void
    {
        require __DIR__.'/../../../../public/jobs/index.html';
        exit;
    }

    /**
     * @param $build_key_id
     *
     * @throws \Exception
     */
    public function list($build_key_id): void
    {
        Job::getByBuildKeyID((int) $build_key_id);
    }

    /**
     * @param $job_id
     *
     * @throws \Exception
     */
    public function find($job_id): void
    {
        Job::find((int) $job_id);
    }

    /**
     * @param $job_id
     */
    public function cancel($job_id): void
    {
    }

    /**
     * @param $job_id
     *
     * @throws \Exception
     */
    public function restart($job_id): void
    {
        Job::updateBuildStatus($job_id, 'pending');
    }
}
