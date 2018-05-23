<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Http\Controllers\APITokenController;
use Exception;

class LogController
{
    /**
     * Returns a single log.
     *
     * /builds/{build_id}/log
     *
     * @param $build_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function __invoke($build_id)
    {
        $log = Build::getLog((int) $build_id);

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
     * /builds/{build_id}/log
     *
     * @param $build_id
     *
     * @throws Exception
     */
    public function delete($build_id): void
    {
        APITokenController::check((int) $build_id);

        Build::updateLog((int) $build_id, 'Log removed at '.date('c'));
    }
}
