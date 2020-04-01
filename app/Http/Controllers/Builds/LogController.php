<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Http\Controllers\Users\JWTController;
use App\Job;

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
     * @throws \Exception
     */
    public function __invoke($job_id)
    {
        $log = Job::getLog((int) $job_id);

        try {
            $log_array = json_decode($log, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return \Response::make('not found', 404);
        }

        // return json
        if ($log_array && \in_array('application/json', \Request::getAcceptableContentTypes())) {
            return $log_array;
        }

        // return text/plain (default)
        if ($log_array) {
            $text_plain_log = '';

            foreach ($log_array as $step => $log) {
                $start_time = substr(explode("\n", $log)[0], 0, 30);
                $text_plain_log .= $start_time.' ##[step]'.$step."\n".$log."\n";
            }

            return \Response::make($text_plain_log, 200, [
                'Content-type' => 'text/plain',
                ]);
        }

        return \Response::make('not found', 404);
    }

    /**
     * Removes the contents of a log. It gets replace with the message: Log removed at 2017-02-13 16:00:00 UTC.
     *
     * Delete
     *
     * /job/{job_id}/log
     *
     * @param $job_id
     *
     * @throws \Exception
     */
    public function delete($job_id)
    {
        JWTController::check(Job::getBuildKeyId((int) $job_id));

        Job::updateLog((int) $job_id, 'Log removed at '.date('c'));

        return \Response::make('', 204);
    }
}
