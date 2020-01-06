<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use App\Build;
use App\Events\LogHandle;
use App\Events\UpdateBuildStatus;
use App\Job;
use PCIT\Framework\Support\Subject;
use PCIT\Support\CI;

/**
 * TODO.
 *
 * 与数据库交互的操作全部移到 Server 节点，Agent 节点严禁与数据库直接交互
 *
 * Agent run job, need docker.
 * 1. 取出一个 job,包括 job config, build key id
 */
class Agent extends Kernel
{
    private $subject;

    public function __construct()
    {
        $this->subject = new Subject();

        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        \Log::debug('Docker connect ...');

        try {
            $this->pcit->docker->system->ping(1);
        } catch (\Throwable $e) {
            \Log::debug($e->getMessage());

            return;
        }

        \Log::debug('Docker build Start ...');

        // 取出一个 job,包括 job config, build key id
        $job_data = $this->getJob();

        if (!$job_data) {
            return;
        }

        ['id' => $job_id, 'build_id' => $build_key_id] = $job_data;

        \Log::emergency('Handle build jobs', ['job_id' => $job_id]);

        $this->subject
            // TODO update build status in progress
            ->register(new UpdateBuildStatus(
                (int) $job_id, (int) $build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS))
            ->handle();

        try {
            $this->pcit->runner_agent->handle((int) $job_id);
        } catch (\Throwable $e) {
            \Log::emergency('Handle job success', ['job_id' => $job_id, 'message' => $e->getMessage()]);

            Job::updateFinishedAt((int) $job_id, time());

            try {
                // TODO
                $this->subject
                    ->register(new LogHandle((int) $job_id))
                    ->register(new UpdateBuildStatus((int) $job_id, (int) $build_key_id, $e->getMessage()))
                    ->handle();
            } catch (\Throwable $e) {
                // catch curl error (timeout,etc)
                \Log::emergency($e->getMessage(), []);
            }
        }

        $this->updateBuildStatus((int) $build_key_id);
    }

    /**
     * TODO 从服务端获取待执行 job.
     */
    public function getJob()
    {
        return Job::getQueuedJob()[0] ?? null;
    }

    /**
     * TODO.
     *
     * 更新 job 对应的 build 状态
     */
    public function updateBuildStatus(int $build_key_id): void
    {
        $status = Job::getBuildStatusByBuildKeyId($build_key_id);

        Build::updateBuildStatus($build_key_id, $status);
        Build::updateFinishedAt($build_key_id);
    }
}
