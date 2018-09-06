<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class JobController
{
    public function __invoke(...$arg): void
    {
        require __DIR__.'/../../../../public/jobs/index.html';
        exit;
    }

    public function list(): void
    {
    }

    public function find($job_id): void
    {
    }

    public function cancel($job_id): void
    {
    }

    public function restart($job_id): void
    {
    }
}
