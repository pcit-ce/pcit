<?php

declare(strict_types=1);

namespace KhsCI\Service\Build\Agent;

use App\Build;
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
     * @param int $job_id
     *
     * @throws CIException
     * @throws \Exception
     */
    public function handle(int $job_id): void
    {
        $docker = (new KhsCI())->docker;
        $this->docker_container = $docker->container;
        $this->docker_network = $docker->network;

        Log::debug(__FILE__, __LINE__, 'Handle job start...', ['job_id' => $job_id], Log::EMERGENCY);

        try {
            // 运行一个 job
            Job::updateStartAt($job_id);
            self::handleJob($job_id);
        } catch (\Throwable $e) {
            if (CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE === $e->getMessage()) {
                // 某一 job 失败
                $this->after($job_id, 'failure');
                Job::updateBuildStatus($job_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);

                throw new CIException($e->getMessage(), $e->getCode());
            } elseif (CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $e->getMessage()) {
                // 某一 job success
                $this->after($job_id, 'success');
                Job::updateBuildStatus($job_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
            } else {
                // 其他错误
                Job::updateBuildStatus($job_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
                Job::updateFinishedAt($job_id);
                // 清理某一 job 的构建环境
                Cleanup::systemDelete((string) $job_id, true);

                throw new \Exception($e->getMessage(), $e->getCode());
            }
        }

        throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
    }

    /**
     * 判断 job 类型.
     *
     * @param int $job_id
     *
     * @throws \Exception
     */
    private function handleJob(int $job_id): void
    {
        LogClient::drop($job_id);

        Log::debug(__FILE__, __LINE__, 'Handle job by type', ['job_id' => $job_id], LOg::EMERGENCY);

        // create network
        Log::debug(__FILE__, __LINE__, 'Create Network', [$job_id], Log::EMERGENCY);

        $this->docker_network->create((string) $job_id);

        // git container
        Log::debug(__FILE__, __LINE__, 'Run git clone container', [], Log::EMERGENCY);

        $git_container_config = Cache::store()->rPop((string) $job_id);
        $this->runPipeline($job_id, $git_container_config);

        // run service
        $this->runService($job_id);

        while (1) {
            $container_config = Cache::store()->rPop((string) $job_id.'_pipeline');

            if (!\is_string($container_config)) {
                Log::debug(__FILE__, __LINE__, 'Container config empty', [], Log::EMERGENCY);

                break;
            }

            $labels = (json_decode($container_config, true))['Labels'];

            $success = $labels['com.khs1994.ci.pipeline.status.success'] ?? false;
            $failure = $labels['com.khs1994.ci.pipeline.status.failure'] ?? false;
            $changed = $labels['com.khs1994.ci.pipeline.status.changed'] ?? false;

            // 将依赖于结果运行的 pipeline 放入缓存队列，只执行正常任务
            if ($success) {
                Log::debug('This pipeline is a success after job');
                Cache::store()->lPush($job_id.'_success', $container_config);

                continue;
            }

            if ($failure) {
                Log::debug('This pipeline is a failure after job');
                Cache::store()->lPush($job_id.'_failure', $container_config);

                continue;
            }

            if ($changed) {
                Log::debug('This pipeline is a changed after job');
                Cache::store()->lPush($job_id.'_changed', $container_config);

                continue;
            }

            try {
                $this->runPipeline($job_id, $container_config);
            } catch (\Throwable $e) {
                if (CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE === $e->getMessage()) {
                    throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);
                }

                Log::getMonolog()->emergency($e->getMessage());

                throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
            }
        }

        throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
    }

    /**
     * 启动容器.
     *
     * @param int    $job_id
     * @param string $container_config
     *
     * @throws \Exception
     */
    public function runPipeline(int $job_id, string $container_config): void
    {
        Log::debug(__FILE__, __LINE__,
            'Run job container', ['job_id' => $job_id,
                'container_config' => $container_config, ], Log::EMERGENCY);

        $container_id = $this->docker_container
            ->setCreateJson($container_config)
            ->create(false)
            ->start(null);

        (new LogClient($job_id, $container_id))->handle();

        Log::debug(__FILE__, __LINE__, 'Run job container success', [
            'job_id' => $job_id, ], Log::EMERGENCY);
    }

    /**
     * 运行 成功或失败之后的任务
     *
     * @param int $job_id
     * @param     $status
     *
     * @throws \Exception
     */
    private function after(int $job_id, $status): void
    {
        Log::debug(__FILE__, __LINE__,
            'Run job after', ['job_id' => $job_id, 'status' => $status], LOG::EMERGENCY);

        // TODO
        // 获取上一次 build 的状况
        $changed = Build::buildStatusIsChanged(Job::getRid($job_id), 'master');

        while (1) {
            $container_config = Cache::store()->rPop($job_id.'_'.$status);

            if (!$container_config) {
                $container_config = Cache::store()->rPop($job_id.'_'.\KhsCI\Support\Job::JOB_STATUS_CHANGED);

                if (!$container_config && $changed) {
                    break;
                }

                break;
            }

            try {
                $this->runPipeline($job_id, $container_config);
            } catch (\Throwable $e) {
                Log::debug(__FILE__, __LINE__, $e->__toString(), [], Log::EMERGENCY);
            }
        }
        Log::debug(__FILE__, __LINE__, 'Run job after finished', ['status' => $status], Log::EMERGENCY);

        Job::updateFinishedAt($job_id);

        Cleanup::systemDelete((string) $job_id, true);
    }

    /**
     * 运行依赖的外部服务
     *
     * @param $job_id
     *
     * @throws \Exception
     */
    private function runService($job_id): void
    {
        Log::debug(__FILE__, __LINE__, 'Run job services', ['job_id' => $job_id], Log::EMERGENCY);

        while (1) {
            $container_config = Cache::store()->rPop((string) $job_id.'_services');

            if (!$container_config) {
                break;
            }

            $container_id = $this->docker_container
                ->setCreateJson($container_config)
                ->create(false)
                ->start(null);

            Log::debug(__FILE__, __LINE__, 'Run Services success', [
                'job_id' => $job_id, 'container_id' => $container_id, ], LOG::EMERGENCY);
        }
    }
}
