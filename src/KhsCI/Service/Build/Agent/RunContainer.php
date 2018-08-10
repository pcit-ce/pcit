<?php

declare(strict_types=1);

namespace KhsCI\Service\Build\Agent;

use App\Job;
use Docker\Container\Client as Container;
use Docker\Network\Client as Network;
use KhsCI\CIException;
use KhsCI\KhsCI;
use KhsCI\Service\Build\Cleanup;
use KhsCI\Service\Build\Events\LogClient;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\Log;

class RunContainer
{
    /**
     * @var Container
     */
    private $docker_container;

    /**
     * @var Network
     */
    private $docker_network;

    /**
     * @param int $build_key_id
     *
     * @throws CIException
     * @throws \Exception
     */
    public function run(int $build_key_id): void
    {
        $docker = (new KhsCI())->docker;

        $this->docker_container = $docker->container;
        $this->docker_network = $docker->network;

        $jobs = Job::getByBuildKeyID($build_key_id);

        // 遍历所有 jobs
        foreach ($jobs as $job_id) {
            try {
                self::job((int) $job_id);
            } catch (\Throwable $e) {
                // 某一 job 失败
                if (CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE === $e->getMessage()) {
                    $this->after($job_id, 'failure');
                    $this->after($job_id, 'changed');
                }

                // 某一 job success
                if (CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $e->getMessage()) {
                    $this->after($job_id, 'success');
                    $this->after($job_id, 'changed');
                }

                // 清理某一 job 的构建环境
                Cleanup::systemDelete((string) $job_id, true);
                Job::updateStopAt($job_id);

                throw new CIException($e->getMessage());
            }
        }

        // 所有 job 执行完毕
        throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
    }

    /**
     * 执行某一具体的 job.
     *
     * @param int $job_id
     *
     * @throws \Exception
     */
    private function job(int $job_id): void
    {
        LogClient::drop($job_id);

        $this->runService($job_id);

        $this->docker_network->create((string) $job_id);

        Log::debug(__FILE__, __LINE__, 'Create Network '.$job_id, [], Log::EMERGENCY);

        while (1) {
            $container_config = Cache::store()->rPop((string) $job_id);

            if (!$container_config) {
                break;
            }

            $labels = (json_decode($container_config, true))['Labels'];

            $success = $labels['com.khs1994.ci.pipeline.status.success'] ?? false;
            $failure = $labels['com.khs1994.ci.pipeline.status.failure'] ?? false;
            $changed = $labels['com.khs1994.ci.pipeline.status.changed'] ?? false;

            if ($success) {
                Cache::store()->lPush($job_id.'_success', $container_config);
                continue;
            }

            if ($failure) {
                Cache::store()->lPush($job_id.'_failure', $container_config);
                continue;
            }

            if ($changed) {
                Cache::store()->lPush($job_id.'_changed', $container_config);
                continue;
            }

            $this->runJob($job_id, $container_config);
        }

        throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
    }

    /**
     * @param int    $job_id
     * @param string $container_config
     *
     * @throws \Exception
     */
    public function runJob(int $job_id, string $container_config): void
    {
        $container_id = $this->docker_container
            ->setCreateJson($container_config)
            ->create(false)
            ->start(null);

        (new LogClient($job_id, $container_id))->handle();
    }

    /**
     * @param int $job_id
     * @param     $status
     *
     * @throws \Exception
     */
    private function after(int $job_id, $status): void
    {
        // 获取上一次 build 的状况

        while (1) {
            $container_config = Cache::store()->rPop($job_id.'_'.$status);

            if (!$container_config) {
                break;
            }

            $this->runJob($job_id, $container_config);
        }
    }

    /**
     * @param $job_id
     *
     * @throws \Exception
     */
    private function runService($job_id): void
    {
        while (1) {
            $container_config = Cache::store()->rPop((string) $job_id.'_services');

            if (!$container_config) {
                break;
            }

            $this->docker_container
                ->setCreateJson($container_config)
                ->create(false)
                ->start(null);

            Log::debug(__FILE__, __LINE__, 'Run Services '.$job_id, [], LOG::EMERGENCY);
        }
    }
}
