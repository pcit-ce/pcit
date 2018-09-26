<?php

declare(strict_types=1);

namespace App\Console\KhsCIDaemon;

use App\Console\Events\LogHandle;
use App\Console\Events\Subject;
use App\Console\Events\UpdateBuildStatus;
use App\Job;
use KhsCI\KhsCI;
use KhsCI\Support\CI;
use KhsCI\Support\Log;

/**
 * Class Agent.
 *
 * Agent run job
 */
class Agent
{
    /**
     * @param int $build_key_id
     * @param     $config
     *
     * @throws \Exception
     */
    public function handle(int $build_key_id, $config): void
    {
        Log::debug(__FILE__, __LINE__, 'Docker connect ...');

        (new KhsCI())->docker->system->ping(1);

        Log::debug(__FILE__, __LINE__, 'Docker build Start ...');

        $job_ids = Job::getByBuildKeyID($build_key_id, true);

        $subject = new Subject();

        foreach ($job_ids as $job_id) {
            $job_id = $job_id['id'];
            Log::debug(__FILE__, __LINE__, 'Handle build jobs', ['job_id' => $job_id], Log::EMERGENCY);

            $subject
                // update build status in progress
                ->register(new UpdateBuildStatus((int)$job_id, $config, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS))
                ->handle();

            try {
                (new KhsCI())->build_agent->handle((int)$job_id);
            } catch (\Throwable $e) {
                Log::debug(__FILE__, __LINE__, 'Handle job success', ['job_id' => $job_id, 'message' => $e->getMessage(),], Log::EMERGENCY);

                $subject
                    ->register(new LogHandle((int)$job_id))
                    ->register(new UpdateBuildStatus((int)$job_id, $config, $e->getMessage()))
                    ->handle();
            }
        }
    }
}
