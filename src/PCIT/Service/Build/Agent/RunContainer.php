<?php

declare(strict_types=1);

namespace PCIT\Service\Build\Agent;

use App\Build;
use App\Job;
use Docker\Container\Client as Container;
use Docker\Network\Client as Network;
use PCIT\Exception\PCITException;
use PCIT\PCIT as PCIT;
use PCIT\Service\Build\Cleanup;
use PCIT\Service\Build\Events\Log;
use PCIT\Support\Cache;
use PCIT\Support\CI;
use PCIT\Support\Log as LogSupport;

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
     * @throws PCITException
     * @throws \Exception
     */
    public function handle(int $job_id): void
    {
        $docker = (new PCIT())->docker;
        $this->docker_container = $docker->container;
        $this->docker_network = $docker->network;

        LogSupport::debug(__FILE__, __LINE__, 'Handle job start...', ['job_id' => $job_id], LogSupport::EMERGENCY);

        try {
            // 运行一个 job
            Job::updateStartAt($job_id);
            self::handleJob($job_id);
        } catch (\Throwable $e) {
            if (CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE === $e->getMessage()) {
                // 某一 job 失败
                $this->after($job_id, 'failure');
                Job::updateBuildStatus($job_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);

                throw new PCITException($e->getMessage(), $e->getCode());
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

        // upload cache
        $this->runCacheContainer($job_id, false);

        Cleanup::systemDelete((string) $job_id, true);

        throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
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
        Log::drop($job_id);

        LogSupport::debug(__FILE__, __LINE__, 'Handle job by type', ['job_id' => $job_id], LogSupport::EMERGENCY);

        // create network
        LogSupport::debug(__FILE__, __LINE__, 'Create Network', [$job_id], LogSupport::EMERGENCY);

        $this->docker_network->create((string) $job_id);

        // git container
        LogSupport::debug(__FILE__, __LINE__, 'Run git clone container', [], LogSupport::EMERGENCY);

        $git_container_config = Cache::store()->rPop((string) $job_id);
        $this->runPipeline($job_id, $git_container_config);

        // download cache
        LogSupport::debug(__FILE__, __LINE__, '', [], LogSupport::EMERGENCY);
        $this->runCacheContainer($job_id);

        // run service
        $this->runService($job_id);

        while (1) {
            $container_config = Cache::store()->rPop((string) $job_id.'_pipeline');

            if (!\is_string($container_config)) {
                LogSupport::debug(__FILE__, __LINE__, 'Container config empty', [], LogSupport::EMERGENCY);

                break;
            }

            $labels = (json_decode($container_config, true))['Labels'];

            $success = $labels['com.khs1994.ci.pipeline.status.success'] ?? false;
            $failure = $labels['com.khs1994.ci.pipeline.status.failure'] ?? false;
            $changed = $labels['com.khs1994.ci.pipeline.status.changed'] ?? false;

            // 将依赖于结果运行的 pipeline 放入缓存队列，只执行正常任务
            if ($success) {
                LogSupport::debug('This pipeline is a success after job');
                Cache::store()->lPush($job_id.'_success', $container_config);

                continue;
            }

            if ($failure) {
                LogSupport::debug('This pipeline is a failure after job');
                Cache::store()->lPush($job_id.'_failure', $container_config);

                continue;
            }

            if ($changed) {
                LogSupport::debug('This pipeline is a changed after job');
                Cache::store()->lPush($job_id.'_changed', $container_config);

                continue;
            }

            try {
                $this->runPipeline($job_id, $container_config);
            } catch (\Throwable $e) {
                if (CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE === $e->getMessage()) {
                    throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);
                }

                LogSupport::getMonolog()->emergency($e->getMessage());

                throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
            }
        }

        throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
    }

    /**
     * @param      $job_id
     * @param bool $download
     *
     * @throws \Exception
     */
    public function runCacheContainer(int $job_id, bool $download = true): void
    {
        $cache_hash_key = $download ? 'cache_download' : 'cache_upload';

        $container_config = Cache::store()->hGet($cache_hash_key, (string) $job_id);

        if (!$container_config) {
            return;
        }

        try {
            $this->runPipeline($job_id, $container_config);
        } catch (\Throwable $e) {
            LogSupport::debug(__FILE__, __LINE__, 'fetch cache error',
                ['message' => $e->getMessage(), 'code' => $e->getCode()],
                LogSupport::EMERGENCY);
        }
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
        LogSupport::debug(__FILE__, __LINE__,
            'Run job container', ['job_id' => $job_id,
                'container_config' => $container_config, ], LogSupport::EMERGENCY);

        $container_id = $this->docker_container
            ->setCreateJson($container_config)
            ->create(false)
            ->start(null);

        (new Log($job_id, $container_id))->handle();

        LogSupport::debug(__FILE__, __LINE__, 'Run job container success', [
            'job_id' => $job_id, ], LogSupport::EMERGENCY);
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
        LogSupport::debug(__FILE__, __LINE__,
            'Run job after', ['job_id' => $job_id, 'status' => $status], LogSupport::EMERGENCY);

        // TODO
        // 获取上一次 build 的状况
        $changed = Build::buildStatusIsChanged(Job::getRid($job_id), 'master');

        while (1) {
            $container_config = Cache::store()->rPop($job_id.'_'.$status);

            if (!$container_config) {
                $container_config = Cache::store()->rPop($job_id.'_'.\PCIT\Support\Job::JOB_STATUS_CHANGED);

                if (!$container_config && $changed) {
                    break;
                }

                break;
            }

            try {
                $this->runPipeline($job_id, $container_config);
            } catch (\Throwable $e) {
                LogSupport::debug(__FILE__, __LINE__, $e->__toString(), [], LogSupport::EMERGENCY);
            }
        }
        LogSupport::debug(__FILE__, __LINE__, 'Run job after finished', ['status' => $status], LogSupport::EMERGENCY);

        Job::updateFinishedAt($job_id);
    }

    /**
     * 运行依赖的外部服务
     *
     * @param int $job_id
     *
     * @throws \Exception
     */
    private function runService(int $job_id): void
    {
        LogSupport::debug(__FILE__, __LINE__, 'Run job services', ['job_id' => $job_id], LogSupport::EMERGENCY);

        while (1) {
            $container_config = Cache::store()->rPop((string) $job_id.'_services');

            if (!$container_config) {
                break;
            }

            $container_id = $this->docker_container
                ->setCreateJson($container_config)
                ->create(false)
                ->start(null);

            LogSupport::debug(__FILE__, __LINE__, 'Run Services success', [
                'job_id' => $job_id, 'container_id' => $container_id, ], LogSupport::EMERGENCY);
        }
    }
}
