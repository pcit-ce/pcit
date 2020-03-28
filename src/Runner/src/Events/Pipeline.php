<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use PCIT\PCIT;
use PCIT\Runner\BuildData;
use PCIT\Runner\CIDefault\Commands;
use PCIT\Runner\CIDefault\Image;
use PCIT\Runner\Client as Runner;
use PCIT\Runner\Conditional\Branch;
use PCIT\Runner\Conditional\Event;
use PCIT\Runner\Conditional\Matrix;
use PCIT\Runner\Conditional\Platform;
use PCIT\Runner\Conditional\Status;
use PCIT\Runner\Conditional\Tag;
use PCIT\Runner\Events\Handler\CommandHandler;
use PCIT\Runner\Events\Handler\EnvHandler;
use PCIT\Runner\Events\Handler\PluginHandler;
use PCIT\Runner\Events\Handler\ShellHandler;
use PCIT\Runner\Events\Handler\TextHandler;
use PCIT\Support\CacheKey;

class Pipeline
{
    private $pipeline;
    /**
     * @var array ['k'=>'v','k2'=>'v2']
     */
    private $matrix_config;

    public $build;

    public $client;

    private $cache;

    private $language;

    private $pluginHandler;

    /**
     * Pipeline constructor.
     *
     * @param            $pipeline
     * @param BuildData  $build
     * @param Runner     $client
     * @param array|null $matrix_config ['k'=>'v']
     *
     * @throws \Exception
     */
    public function __construct($pipeline, ?BuildData $build, ?Runner $client, ?array $matrix_config)
    {
        $this->pipeline = $pipeline;
        $this->matrix_config = $matrix_config;
        $this->build = $build;
        $this->client = $client;
        $this->cache = \Cache::store();
        $this->pluginHandler = new PluginHandler();
    }

    /**
     * @param $when
     *
     * @return bool true: skip
     *
     * @throws \Exception
     */
    public function checkWhen($when): bool
    {
        if (!$when) {
            return false;
        }

        $when_platform = $when->platform ?? null;
        $when_event = $when->event ?? null; // tag pull_request
        $when_branch = $when->branch ?? null;
        $when_tag = $when->tag ?? null;
        $when_matrix = $when->jobs ?? $when->matrix ?? null;

        if (!(new Platform($when_platform, 'linux/amd64'))->handle(true)) {
            \Log::emergency('🛑skip by platform check');

            return true;
        }

        if (!(new Event($when_event, $this->build->event_type))->handle()) {
            \Log::emergency('🛑skip by event check');

            return true;
        }

        if (!(new Branch($when_branch, $this->build->branch))->handle(true)) {
            \Log::emergency('🛑skip by branch check');

            return true;
        }

        if (!(new Tag($when_tag, $this->build->tag))->handle(true)) {
            \Log::emergency('🛑skip by tag check');

            return true;
        }

        if (!(new Matrix($when_matrix, $this->matrix_config))->handle()) {
            \Log::emergency('🛑skip by matrix check');

            return true;
        }

        return false;
    }

    /**
     * 整合 pipelineEnv systemEnv matrixEnv.
     *
     * @param array $pipelineEnv ['k=v']
     *
     * @return array ['k=v']
     */
    public function handleEnv(array $pipelineEnv, string $step_name): array
    {
        $step_system_env = [
            'PCIT_STEP_NAME='.$step_name,
        ];

        $envHandler = new EnvHandler();
        $pipelineEnv = $envHandler->handle($pipelineEnv, array_merge(
            $step_system_env,
            $this->client->system_env,
            $this->client->system_job_env,
            )
        );

        $preEnv = array_merge(
            $step_system_env,
            $this->client->system_env,
            $this->client->system_job_env,
            $pipelineEnv
        );

        if (!$this->matrix_config) {
            return $preEnv;
        }

        return array_merge($preEnv, $envHandler->obj2array($this->matrix_config));
    }

    public function handleCommands($pipeline, $pipelineContent): array
    {
        // 内容为字符串
        if (\is_string($pipelineContent)) {
            return [$pipelineContent];
        }

        // 判断内容是否为数组
        foreach (array_keys((array) $pipelineContent) as $key => $value) {
            if (0 === $value) {
                return $pipelineContent;
            }

            break;
        }

        $commands = $pipelineContent->run ?? Commands::get($this->language, $pipeline);

        return \is_string($commands) ? [$commands] : $commands;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        /**
         * @var \Docker\Container\Client
         */
        $docker_container = app(PCIT::class)->docker->container;

        $jobId = $this->client->job_id;
        $workdir = $this->client->workdir;
        $this->language = $language = $this->client->language ?? 'php';
        $hosts = $this->client->networks->hosts ?? [];

        // custome github.com hosts
        if (env('CI_GITHUB_HOST')) {
            $hosts = array_merge($hosts,
            ['github.com:'.env('CI_GITHUB_HOST')]
           );
        }

        foreach ($this->pipeline as $step => $pipelineContent) {
            \Log::emergency("🔄Handle step $step ...");

            $image = $pipelineContent->image
                ?? $this->client->image
                ?? Image::get($language);
            $commands = $this->handleCommands($step, $pipelineContent);
            $env = $pipelineContent->env ?? [];
            $shell = $pipelineContent->shell ?? 'sh';
            $privileged = $pipelineContent->privileged ?? false;
            $pull = $pipelineContent->pull ?? false;
            $settings = $pipelineContent->with ?? new \stdClass();
            $settings = (array) $settings;
            $when = $pipelineContent->if ?? null;
            $read_only = $pipelineContent->read_only ?? false;

            // 预处理 env
            $preEnv = $this->handleEnv($env, $step);

            // 处理插件
            if ($settings) {
                $preEnv = array_merge($preEnv, $this->pluginHandler->handleSettings($settings, $preEnv));
            }

            // 处理构建条件 true: skip
            if ($this->checkWhen($when)) {
                continue;
            }

            // 解析运行条件
            $status = $when->status ?? null;
            $failure = (new Status($status, 'failure'))->handle();
            $success = (new Status($status, 'success'))->handle();
            $changed = (new Status($status, 'changed'))->handle();

            $no_status = $status ? false : true;

            // 处理 image
            $image = (new TextHandler())->handle($image, $preEnv);

            if ('github://' === substr($image, 0, 9)) {
                $actionHandler = new ActionHandler($this);

                try {
                    $commands = $actionHandler->handle($step, $image);
                    // 由于获取 action.yml 文件可能超时，捕获该错误
                } catch (\Throwable $e) {
                    \Log::emergency('🛑handle pipeline use actions error'.$e->getMessage(), []);

                    continue;
                }

                $image = 'khs1994/node:git';

                $preEnv = array_merge($preEnv, $actionHandler->handleEnv($step, $workdir));
            }

            // 处理 commands
            $ci_script = CommandHandler::parse($shell, $step, $image, $commands);

            $env = array_merge(["CI_SCRIPT=$ci_script"], $preEnv);
            \Log::info(json_encode($env), []);

            [$entrypoint,$cmd] = (new ShellHandler())->handle($shell, $commands);

            $container_config = $docker_container
                ->setEnv($env)
                ->setBinds([
                    "pcit_$jobId:$workdir",
                    'pcit_cache:/tmp/pcit_cache',
                    'pcit_toolkit:/opt/pcit/toolkit',
                    'pcit_actions_'.$jobId.':'.'/var/run/actions',
                    '/var/run/docker.sock:/var/run/docker.sock',
                ])
                ->setEntrypoint($entrypoint)
                ->setLabels([
                    'com.khs1994.ci.pipeline' => "$jobId",
                    'com.khs1994.ci.pipeline.name' => $step,
                    'com.khs1994.ci.pipeline.if_status.no_status' => (string) $no_status,
                    'com.khs1994.ci.pipeline.if_status.failure' => (string) $failure,
                    'com.khs1994.ci.pipeline.if_status.success' => (string) $success,
                    'com.khs1994.ci.pipeline.if_status.changed' => (string) $changed,
                    'com.khs1994.ci' => (string) $jobId,
                ])
                ->setPrivileged($privileged)
                ->setWorkingDir($workdir)
                ->setCmd($cmd)
                ->setImage($image)
                ->setExtraHosts($hosts)
                ->setNetworkingConfig([
                    'EndpointsConfig' => [
                        "pcit_$jobId" => [
                            'Aliases' => [
                                $step,
                            ],
                        ],
                    ],
                ])
                ->setReadonlyRootfs($read_only)
                ->setCreateJson(null)
                ->getCreateJson();

            $this->storeCache((int) $jobId, $step, $container_config, $failure, $success, $changed);
        }
    }

    public function generateDocker(): void
    {
    }

    public function storeCache(int $jobId,
    string $step,
    string $container_config,
    bool $failure = false,
    bool $success = false,
    bool $changed = false): void
    {
        $cache = $this->cache;

        $is_status = false;

        if ($failure) {
            $is_status = true;
            $cache->lpush(CacheKey::pipelineListKey($jobId, 'failure'), $step);
            $cache->hset(CacheKey::pipelineHashKey($jobId, 'failure'), $step, $container_config);
        }

        if ($success) {
            $is_status = true;
            $cache->lpush(CacheKey::pipelineListKey($jobId, 'success'), $step);
            $cache->hset(CacheKey::pipelineHashKey($jobId, 'success'), $step, $container_config);
        }

        if ($changed) {
            $is_status = true;
            $cache->lpush(CacheKey::pipelineListKey($jobId, 'changed'), $step);
            $cache->hset(CacheKey::pipelineHashKey($jobId, 'changed'), $step, $container_config);
        }

        if (true === $is_status) {
            return;
        }

        $cache->lpush(CacheKey::pipelineListKey($jobId), $step);
        $cache->hset(CacheKey::pipelineHashKey($jobId), $step, $container_config);
    }
}
