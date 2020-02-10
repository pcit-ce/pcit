<?php

declare(strict_types=1);

namespace PCIT\Runner\Agent\Docker;

use App\Build;
use App\Job;
use Docker\Container\Client as Container;
use Docker\Network\Client as Network;
use PCIT\Exception\PCITException;
use PCIT\PCIT;
use PCIT\Runner\Agent\Docker\Log as ContainerLog;
use PCIT\Runner\Agent\Interfaces\RunnerHandlerInterface;
use PCIT\Support\CacheKey;
use PCIT\Support\CI;

class DockerHandler implements RunnerHandlerInterface
{
    /**
     * @var Container
     */
    private $docker_container;

    /**
     * @var Network
     */
    private $docker_network;

    private $job_id;

    /**
     * RunContainer constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $docker = app(PCIT::class)->docker;
        $this->docker_container = $docker->container;
        $this->docker_network = $docker->network;
        $this->cache = \Cache::store();
    }

    /**
     * @throws PCITException
     * @throws \Exception
     */
    public function handle(int $job_id): void
    {
        \Log::emergency('Handle job start...', ['job_id' => $job_id]);

        try {
            // 运行一个 job
            Job::updateStartAt($job_id, time());
            self::handleJob($job_id);
        } catch (\Throwable $e) {
            if (CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE === $e->getMessage()) {
                // job 失败
                $this->after($job_id, 'failure');

                // 清理 job 的构建环境
                Cleanup::systemDelete((string) $job_id, true);

                throw new PCITException($e->getMessage(), $e->getCode());
            } elseif (CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS === $e->getMessage()) {
                // job success
                $this->after($job_id, 'success');
            } else {
                // 其他错误
                Job::updateFinishedAt($job_id, time());
                // 清理 job 的构建环境
                Cleanup::systemDelete((string) $job_id, true);

                throw new \Exception($e->__toString(), $e->getCode());
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
     * @throws \Exception
     */
    private function handleJob(int $job_id): void
    {
        $this->job_id = $job_id;
        $cache = $this->cache;

        // drop prev log
        $this->dropLog();

        \Log::emergency('Handle job', ['job_id' => $job_id]);

        // create network
        \Log::emergency('Create Network', [$job_id]);

        $this->createNetwork();

        // git clone container
        \Log::emergency('Run git clone container', []);

        $this->gitClone();

        // download cache
        \Log::emergency('', []);
        $this->runCacheContainer($job_id);

        // run service
        $this->runService($job_id);

        $this->handleSteps();

        throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
    }

    /**
     * Drop prev log.
     */
    public function dropLog(): void
    {
        ContainerLog::drop($this->job_id);
    }

    public function createNetwork(): void
    {
        $result = $this->docker_network->list(['name' => 'pcit_'.$this->job_id]);

        if ($result) {
            foreach (json_decode($result) as $network) {
                try {
                    $this->docker_network->remove($network->Id);
                } catch (\Throwable $e) {
                    \Log::emergency('Delete docker network error', [$e->getMessage()]);
                }
            }
        }

        $this->docker_network->create('pcit_'.$this->job_id);
    }

    public function gitClone(): void
    {
        $git_container_config = $this->cache->get(CacheKey::cloneKey($this->job_id));

        $this->runStep($this->job_id, $git_container_config, 'clone');
    }

    public function handleSteps(): void
    {
        $job_id = $this->job_id;
        $cache = $this->cache;
        // 复制原始 key
        $copyKey = CacheKey::pipelineListCopyKey($job_id, 'pipeline', 'runner');

        while (1) {
            $step = $cache->rpop($copyKey);

            if (!$step) {
                break;
            }

            $container_config = $cache->hget(CacheKey::pipelineHashKey($job_id), $step);

            if (!\is_string($container_config)) {
                \Log::emergency('Container config empty', []);
            }

            try {
                $this->runStep($job_id, $container_config, $step);
            } catch (\Throwable $e) {
                if (CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE === $e->getMessage()) {
                    throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);
                }

                \Log::emergency($e->getMessage());

                throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
            }
        }

        $cache->del($copyKey);
    }

    /**
     * 执行 step.
     *
     * @throws \Exception
     */
    public function runStep(int $job_id, string $container_config, string $step = null): void
    {
        \Log::emergency('Run job container', ['job_id' => $job_id,
                'container_config' => $container_config, ]);

        $container_id = $this->docker_container
            ->setCreateJson($container_config)
            ->create(false)
            ->start(null);

        (new ContainerLog($job_id, $container_id, $step))->handle();

        \Log::emergency('Run job container success', [
            'job_id' => $job_id, ]);
    }

    /**
     * @param $job_id
     *
     * @throws \Exception
     */
    public function runCacheContainer(int $job_id, bool $download = true): void
    {
        $type = $download ? 'download' : 'upload';

        $containerConfig = \Cache::store()->get(CacheKey::cacheKey($job_id, $type));

        if (!$containerConfig) {
            return;
        }

        try {
            $this->runStep($job_id, $containerConfig, 'cache_'.$type);
            'upload' === $type && $this->updateCacheInfo($containerConfig);
        } catch (\Throwable $e) {
            \Log::emergency(
                'upload or download cache error, please check s3(minio) server status',
                ['message' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    /**
     * 更新缓存信息，存入数据库.
     *
     * TODO
     */
    public function updateCacheInfo(string $containerConfig): void
    {
        $envs = json_decode($containerConfig)->Env;

        $envArray = preg_grep('/S3_CACHE_PREFIX=*/', $envs);

        $prefix = explode('=', array_values($envArray)[0])[1];

        [$gitType,$rid,$branch] = explode('_', $prefix);

        \App\Cache::insert($gitType, (int) $rid, $branch, $prefix);
        \App\Cache::update($gitType, (int) $rid, $branch);
    }

    /**
     * @throws \Exception
     */
    private function changed(int $job_id): void
    {
        // TODO 获取上一次 build 的状况
        $changed = Build::buildStatusIsChanged(Job::getRid($job_id), 'master');

        $changed_key = $job_id.'_'.\PCIT\Support\Job::JOB_STATUS_CHANGED;

        Job::updateFinishedAt($job_id, time());
    }

    /**
     * 运行 成功或失败之后的任务
     *
     * @param $status
     *
     * @throws \Exception
     */
    private function after(int $job_id, $status): void
    {
        \Log::emergency('Run job after', ['job_id' => $job_id, 'status' => $status]);

        // TODO 获取上一次 build 的状况
        if ('changed' === $status && !Build::buildStatusIsChanged(Job::getRid($job_id), 'master')) {
            return;
        }

        $cache = $this->cache;

        // 复制 key

        $copyKey = CacheKey::pipelineListCopyKey($job_id, $status, 'runner');

        while (1) {
            $step = $cache->rpop($copyKey);

            if (!$step) {
                break;
            }

            $container_config = $cache->hget(CacheKey::pipelineHashKey($job_id, $status), $step);

            try {
                $this->runStep($job_id, $container_config, $step);
            } catch (\Throwable $e) {
                \Log::emergency($e->__toString(), []);
            }
        }

        if ('changed' !== $status) {
            $this->after($job_id, 'changed');
        }

        $cache->del($copyKey);

        \Log::emergency('Run job after finished', ['status' => $status]);
    }

    /**
     * 运行依赖的外部服务
     *
     * @throws \Exception
     */
    private function runService(int $job_id): void
    {
        \Log::emergency('Run job services', ['job_id' => $job_id]);

        $container_configs = \Cache::store()->hgetall(CacheKey::serviceHashKey($job_id));

        foreach ($container_configs as $service => $container_config) {
            $container_id = $this->docker_container
                ->setCreateJson($container_config)
                ->create(false)
                ->start(null);

            \Log::emergency('Run Services success', [
                'job_id' => $job_id, 'container_id' => $container_id, ]);
        }
    }
}
