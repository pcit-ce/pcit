<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

class Metrics
{
    public function __invoke()
    {
        $content = '# builds
pending_builds 1
success_builds 1
total_builds 1
average_queue_time 10

# jobs
pending_jobs 1
success_jobs 1
total_job_minutes 100

# user
user_number 1

# repo
active_repositories 100
';

        return \Response::make($content, 200, [
            'Content-Type' => 'text/plain; version=0.0.4; charset=utf-8',
        ]);
    }
}
