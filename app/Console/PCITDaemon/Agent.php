<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use App\Build;
use App\Console\Events\LogHandle;
use App\Console\Events\Subject;
use App\Console\Events\UpdateBuildStatus;
use App\Job;
use PCIT\PCIT;
use PCIT\Support\Cache;
use PCIT\Support\CI;
use PCIT\Support\Log;

/**
 * Agent run job, need docker.
 */
class Agent extends Kernel
{
    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        Log::debug(__FILE__, __LINE__, 'Docker connect ...');

        (new PCIT())->docker->system->ping(1);

        Log::debug(__FILE__, __LINE__, 'Docker build Start ...');

        // 取出一个 job,包括 job config, build key id
        $job_data = Job::getPendingJob()[0] ?? null;

        if (!$job_data) {
            return;
        }

        ['id' => $job_id, 'build_id' => $build_key_id] = $job_data;

        echo $job_id;

        $config = Build::getConfig((int) $build_key_id) ?? '';

        $subject = new Subject();

        Log::debug(__FILE__, __LINE__, 'Handle build jobs', ['job_id' => $job_id], Log::EMERGENCY);

        $subject
            // update build status in progress
            ->register(new UpdateBuildStatus((int) $job_id, $config, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS))
            ->handle();

        try {
            (new PCIT())->build_agent->handle((int) $job_id);
        } catch (\Throwable $e) {
            Log::debug(__FILE__, __LINE__, 'Handle job success', ['job_id' => $job_id, 'message' => $e->getMessage()], Log::EMERGENCY);

            $subject
                ->register(new LogHandle((int) $job_id))
                ->register(new UpdateBuildStatus((int) $job_id, $config, $e->getMessage()))
                ->handle();
        }

        // 恢复缓存队列
        $services_key = $job_id.'_services';
        $pipeline_key = $job_id.'_pipeline';
        $this->cacheHandler($services_key);
        $this->cacheHandler($pipeline_key);

        // 运行一个 job 之后更新 build 状态
        $status = Job::getBuildStatusByBuildKeyId((int) $build_key_id);

        Build::updateBuildStatus((int) $build_key_id, $status);
    }

    /**
     * @param $key
     *
     * @throws \Exception
     */
    public function cacheHandler($key): void
    {
        $cache = Cache::store();

        if ($cache->lLen($key) <= 1) {
            return;
        }

        $result = $cache->lpop($key);

        if ('end' === $result) {
            $cache->rpush($key, 'end');
        } else {
            $length = $cache->lLen($key);

            for ($i = 0; $i > $length; ++$i) {
                $result = $cache->rpoplpush($key, $key);

                if ('end' === $result) {
                    $cache->lpop($key);
                    $cache->rpush($key, 'end');
                }
            }
        }
    }
}
