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
use PCIT\Runner\Events\Handler\EnvHandler;
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

    private $cache;

    private $env = [];

    private $path = [];

    private $output = [];

    private $mask_value_array = [];

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
        \Log::emergency("Run job $job_id step containers...", ['job_id' => $job_id]);

        try {
            // 运行 toolkit 容器
            $this->handleToolkit();

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

    public function handleToolkit(): void
    {
        \Log::emergency('run toolkit container');

        $this->docker_container
        ->setImage('pcit/toolkit')
        ->setBinds([
            'pcit_toolkit:/data',
        ])
        ->setLabels([
            'com.khs1994.ci' => 'toolkit',
        ])
        ->create()
        ->start(null);
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

        \Log::emergency('Handle job '.$job_id, ['job_id' => $job_id]);

        // create network
        \Log::emergency('Create Network '.$job_id, [$job_id]);
        $this->createNetwork();

        // git clone container
        \Log::emergency('Run git clone container', []);
        $this->gitClone();

        // download cache
        \Log::emergency('Run cache downloader container', []);
        $this->runCacheContainer($job_id);

        // run service
        $this->runService($job_id);

        // run step
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

        $job_id = $this->job_id;

        $retry = (int) env('CI_GIT_CLONE_STEP_RETRY', 1);

        retry($retry, function () use ($job_id,$git_container_config): void {
            $this->runStep($job_id, $git_container_config, 'clone');
        });
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
     * 将在 step 中设置的 env 注入到接下来的容器配置中.
     */
    public function insertEnv(string $container_config): string
    {
        if (!$this->env) {
            return $container_config;
        }

        $container_env = json_decode($container_config)->Env;

        $container_env = array_merge(
           $container_env,
           $this->env,
        );

        $container_config = json_decode($container_config);
        $container_config->Env = $container_env;

        return json_encode($container_config);
    }

    /**
     * 执行 step.
     *
     * @throws \Exception
     */
    public function runStep(int $job_id, string $container_config, string $step = null): void
    {
        \Log::emergency('Run step container', ['job_id' => $job_id,
                'container_config' => $container_config, ]);

        $container_config = $this->insertEnv($container_config);

        $container_config = $this->handleArtifact($job_id, $container_config);

        $container_id = $this->docker_container
            ->setCreateJson($container_config)
            ->create(false)
            ->start(null);

        [
            'env' => $env,
            'mask' => $mask_value_array
        ] = (new ContainerLog($job_id, $container_id, $step))
        ->handle($this->mask_value_array);

        \Log::emergency('Run step container success', ['job_id' => $job_id]);

        // env
        // var_dump($step,$env);
        $this->env = array_merge($this->env, $env);

        // output

        // path

        // mask
        $this->mask_value_array = array_merge(
            $this->mask_value_array,
            $mask_value_array
        );
    }

    public function handleArtifact(int $job_id, string $container_config): string
    {
        $container_config_object = json_decode($container_config);
        $image = $container_config_object->Image;

        if ('pcit/upload-artifact' !== $image) {
            return $container_config;
        }

        \Log::emergency('this step is artifact uploader');

        $preEnv = $container_config_object->Env;

        $name = (new EnvHandler())->array2obj($preEnv)['INPUT_NAME'];
        $path = (new EnvHandler())->array2obj($preEnv)['INPUT_PATH'];

        $git_type = Job::getGitType($job_id);
        $repo_full_name = Job::getRepoFullName($job_id);
        $s3_dir_root = "$git_type/$repo_full_name/$job_id";

        $env = array_merge($preEnv, [
                'INPUT_ENDPOINT='.env('CI_S3_ENDPOINT'),
                'INPUT_ACCESS_KEY_ID='.env('CI_S3_ACCESS_KEY_ID'),
                'INPUT_SECRET_ACCESS_KEY='.env('CI_S3_SECRET_ACCESS_KEY'),
                'INPUT_BUCKET='.env('CI_S3_ARTIFACT_BUCKET', 'pcit-artifact'),
                'INPUT_REGION='.env('CI_S3_REGION', 'us-east-1'),
                'INPUT_USE_PATH_STYLE_ENDPOINT='.
                (env('CI_S3_USE_PATH_STYLE_ENDPOINT', true) ? 'true' : 'false'),
                'INPUT_ARTIFACT_NAME='.$name,
                'INPUT_ARTIFACT_PATH='.$path,
                'INPUT_UPLOAD_DIR='.$s3_dir_root,
                // must latest key
                'INPUT_ARTIFACT_DOWNLOAD=',
        ]);

        $container_config_object->Image = 'pcit/s3';
        $container_config_object->Env = $env;

        $container_config = json_encode($container_config_object);

        \Log::emergency('run step artifact uploader', json_decode($container_config, true));

        return $container_config;
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
        \Log::emergency('Run job after container', ['job_id' => $job_id, 'status' => $status]);

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
        \Log::emergency('Run job services container', ['job_id' => $job_id]);

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
