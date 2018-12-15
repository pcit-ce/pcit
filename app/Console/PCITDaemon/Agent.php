<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use App\Build;
use App\Console\Events\LogHandle;
use App\Console\Events\Subject;
use App\Console\Events\UpdateBuildStatus;
use App\Job;
use PCIT\Support\CI;
use PCIT\Support\Log;

/**
 * Agent run job, need docker.
 */
class Agent extends Kernel
{
    /**
     * TODO 从服务端获取待执行 job.
     */
    public function getJob()
    {
        return Job::getQueuedJob()[0] ?? null;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        Log::debug(__FILE__, __LINE__, 'Docker connect ...');

        try {
            $this->pcit->docker->system->ping(1);
        } catch (\Throwable $e) {
            return;
        }

        Log::debug(__FILE__, __LINE__, 'Docker build Start ...');

        // 取出一个 job,包括 job config, build key id
        $job_data = $this->getJob();

        if (!$job_data) {
            return;
        }

        ['id' => $job_id, 'build_id' => $build_key_id] = $job_data;

        Log::debug(__FILE__, __LINE__, 'Handle build jobs',
            ['job_id' => $job_id], Log::EMERGENCY);

        $subject = new Subject();

        $subject
            // update build status in progress
            ->register(new UpdateBuildStatus(
                (int) $job_id, (int) $build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS))
            ->handle();

        try {
            $this->pcit->build_agent->handle((int) $job_id);
        } catch (\Throwable $e) {
            Log::debug(__FILE__, __LINE__, 'Handle job success', ['job_id' => $job_id, 'message' => $e->getMessage()], Log::EMERGENCY);

            Job::updateFinishedAt((int) $job_id, time());

            try {
                $subject
                    ->register(new LogHandle((int) $job_id))
                    ->register(new UpdateBuildStatus((int) $job_id, (int) $build_key_id, $e->getMessage()))
                    ->handle();
            } catch (\Throwable $e) {
                // catch curl error (timeout,etc)
                Log::debug(__FILE__, __LINE__,
                    $e->getMessage(), [], LOG::EMERGENCY);
            }
        }

        // 运行一个 job 之后更新 build 状态
        $this->updateBuildStatus((int) $build_key_id);
    }

    public function updateBuildStatus(int $build_key_id): void
    {
        $status = Job::getBuildStatusByBuildKeyId($build_key_id);

        Build::updateBuildStatus($build_key_id, $status);
    }
}
