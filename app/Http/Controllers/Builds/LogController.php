<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;
use App\Job;
use Exception;

class LogController
{
    /**
     * Returns a single log.
     *
     * /job/{job_id}/log
     *
     * @param $job_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function __invoke($job_id)
    {
        $log = Job::getLog((int) $job_id);

        if ($log) {
            return $log;
        }

        throw new Exception('Not Found', 404);
    }

    /**
     * Removes the contents of a log. It gets replace with the message: Log removed at 2017-02-13 16:00:00 UTC.
     *
     * delete
     *
     * /job/{job_id}/log
     *
     * @param $job_id
     *
     * @throws Exception
     */
    public function delete($job_id): void
    {
        JWTController::check((int) $job_id);

        Job::updateLog((int) $job_id, 'Log removed at '.date('c'));
    }
}
