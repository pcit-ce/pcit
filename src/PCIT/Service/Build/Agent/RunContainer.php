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
     * RunContainer constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $docker = (new PCIT())->docker;
        $this->docker_container = $docker->container;
        $this->docker_network = $docker->network;
    }

    /**
     * @param int $job_id
     *
     * @throws PCITException
     * @throws \Exception
     */
    public function handle(int $job_id): void
    {
        LogSupport::debug(__FILE__, __LINE__, 'Handle job start...', ['job_id' => $job_id], LogSupport::EMERGENCY);

        try {
            // 运行一个 job
            Job::updateStartAt($job_id);
            self::handleJob($job_id);
        } catch (\Throwable $e) {
            if (CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE === $e->getMessage()) {
                // job 失败
                $this->after($job_id, 'failure');
                Job::updateBuildStatus($job_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);

                throw new PCITException($e->getMessage(), $e->getCode());
            } elseif (CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $e->getMessage()) {
                // job success
                $this->after($job_id, 'success');
                Job::updateBuildStatus($job_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
            } else {
                // 其他错误
                Job::updateBuildStatus($job_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
                Job::updateFinishedAt($job_id);
                // 清理 job 的构建环境
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

        LogSupport::debug(__FILE__, __LINE__, 'Handle job', ['job_id' => $job_id], LogSupport::EMERGENCY);

        // create network
        LogSupport::debug(__FILE__, __LINE__, 'Create Network', [$job_id], LogSupport::EMERGENCY);

        $this->docker_network->create((string) $job_id);

        // 处理缓存
        $this->cacheHandle((string) $job_id);
        $cache = Cache::store();

        // git container
        LogSupport::debug(__FILE__, __LINE__, 'Run git clone container', [], LogSupport::EMERGENCY);

        $git_container_config = $cache->rpoplpush((string) $job_id, (string) $job_id);

        $this->runPipeline($job_id, $git_container_config);

        // download cache
        LogSupport::debug(__FILE__, __LINE__, '', [], LogSupport::EMERGENCY);
        $this->runCacheContainer($job_id);

        // service
        $services_key = (string) $job_id.'_services';
        $result = $cache->rpoplpush($services_key, $services_key);

        if ('end' === $result) {
            // run service
            $this->runService($job_id);
        } else {
            LogSupport::debug(
                __FILE__, __LINE__,
                'this job not include services', [], LogSupport::EMERGENCY);
        }

        $pipeline_key = $job_id.'_pipeline';

        while (1) {
            $container_config = $cache->rpoplpush($pipeline_key, $pipeline_key);

            if ('end' === $container_config) {
                break;
            }

            if (!\is_string($container_config)) {
                LogSupport::debug(__FILE__, __LINE__, 'Container config empty', [], LogSupport::EMERGENCY);

                break;
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
     * @param string $job_id
     *
     * @throws PCITException
     * @throws \Exception
     */
    public function cacheHandle(string $job_id): void
    {
        $cache = Cache::store();
        $pipeline_key = $job_id.'_pipeline';
        $success_key = $job_id.'_success';
        $failure_key = $job_id.'_failure';
        $changed_key = $job_id.'_changed';

        $key_array = [$pipeline_key, $success_key, $failure_key, $changed_key];

        foreach ($key_array as $key) {
            $result = $cache->rpoplpush($key, $key);

            if ('end' !== $result) {
                LogSupport::debug(
                    __FILE__, __LINE__,
                    $key.' list handle failure', [], LogSupport::EMERGENCY);

                throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
            }
        }
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
     * @param int $job_id
     *
     * @throws \Exception
     */
    private function changed(int $job_id): void
    {
        // TODO 获取上一次 build 的状况
        $changed = Build::buildStatusIsChanged(Job::getRid($job_id), 'master');

        $changed_key = $job_id.'_'.\PCIT\Support\Job::JOB_STATUS_CHANGED;

        Job::updateFinishedAt($job_id);
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

        // TODO 获取上一次 build 的状况
        if ('changed' === $status && !Build::buildStatusIsChanged(Job::getRid($job_id), 'master')) {
            return;
        }

        $status_key = $job_id.'_'.$status;

        if (1 === Cache::store()->lLen($status_key)) {
            $this->after($job_id, 'changed');

            return;
        }

        while (1) {
            $container_config = Cache::store()->rPoplpush($status_key, $status_key);

            if ('end' === $container_config) {
                break;
            }

            try {
                $this->runPipeline($job_id, $container_config);
            } catch (\Throwable $e) {
                LogSupport::debug(__FILE__, __LINE__, $e->__toString(), [], LogSupport::EMERGENCY);
            }
        }

        if ('changed' === $status) {
            return;
        }

        $this->after($job_id, 'changed');

        LogSupport::debug(__FILE__, __LINE__, 'Run job after finished', ['status' => $status], LogSupport::EMERGENCY);
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
            $container_config = Cache::store()
                ->rPoplpush((string) $job_id.'_services', (string) $job_id.'_services');

            if ('end' === $container_config) {
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
