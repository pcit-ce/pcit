<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

use PCIT\Framework\Support\HttpClient;
use PCIT\PCIT;
use PCIT\Runner\BuildData;
use PCIT\Runner\CIDefault\Commands;
use PCIT\Runner\CIDefault\Image;
use PCIT\Runner\CIDefault\Status as CIDefaultStatus;
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
use PCIT\Runner\Parser\TextHandler as TextParser;
use PCIT\Support\CacheKey;
use Symfony\Component\Yaml\Yaml;

class Pipeline
{
    private $pipeline;

    private $matrix_config;

    private $build;

    private $client;

    private $cache;

    private $language;

    private $pluginHandler;

    /**
     * Pipeline constructor.
     *
     * @param           $pipeline
     * @param BuildData $build
     * @param Runner    $client
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
            \Log::emergency('skip by platform check');

            return true;
        }

        if (!(new Event($when_event, $this->build->event_type))->handle()) {
            \Log::emergency('skip by event check');

            return true;
        }

        if (!(new Branch($when_branch, $this->build->branch))->handle(true)) {
            \Log::emergency('skip by branch check');

            return true;
        }

        if (!(new Tag($when_tag, $this->build->tag))->handle(true)) {
            \Log::emergency('skip by tag check');

            return true;
        }

        if (!(new Matrix($when_matrix, $this->matrix_config))->handle()) {
            \Log::emergency('skip by matrix check');

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
    public function handleEnv(array $pipelineEnv): array
    {
        $pre_env = [];

        foreach ($pipelineEnv as $env) {
            [$key,$value] = explode('=', $env);
            $pre_env[$key] = $value;
        }

        $pipelineEnv = (new EnvHandler())->handle($pre_env, array_merge(
            $this->client->system_env, $this->client->system_job_env
            )
        );

        $preEnv = array_merge($this->client->system_env,
            $this->client->system_job_env,
            $pipelineEnv
        );

        if (!$this->matrix_config) {
            return $preEnv;
        }

        $matrixEnv = [];

        foreach ($this->matrix_config as $k => $v) {
            $matrixEnv[] = $k.'='.$v;
        }

        return array_merge($preEnv, $matrixEnv);
    }

    public function handleCommands($pipeline, $pipelineContent): array
    {
        // 内容为字符串
        if (\is_string($pipelineContent)) {
            return [$pipelineContent];
        }

        // 判断内容是否为数组
        foreach (array_keys((array) $pipelineContent) as $key => $value) {
            if (\is_int($value)) {
                return $pipelineContent;
            }
        }

        $commands = $pipelineContent->run
            ?? Commands::get($this->language, $pipeline);

        return \is_string($commands) ? [$commands] : $commands;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $docker_container = app(PCIT::class)->docker->container;

        $jobId = $this->client->job_id;
        $workdir = $this->client->workdir;
        $language = $this->client->language ?? 'php';
        $hosts = $this->client->networks->hosts ?? [];

        // custome github.com hosts
        if (env('CI_GITHUB_HOST')) {
            $hosts = array_merge($hosts,
            ['github.com:'.env('CI_GITHUB_HOST')]
           );
        }

        $this->language = $language;

        foreach ($this->pipeline as $step => $pipelineContent) {
            \Log::emergency('Handle pipeline', ['pipeline' => $step]);

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

            // 预处理 env
            $preEnv = $this->handleEnv($env);

            // 处理插件
            if ($settings) {
                $preEnv = array_merge($preEnv, $this->pluginHandler->handleSettings($settings, $preEnv));
            }

            // 处理构建条件 true: skip
            if ($this->checkWhen($when)) {
                continue;
            }

            // 根据 pipeline 获取默认的构建条件
            $status = $when->status ?? CIDefaultStatus::get($step);
            $failure = (new Status($status, 'failure'))->handle();
            $success = (new Status($status, 'success'))->handle();
            $changed = (new Status($status, 'changed'))->handle();

            $no_status = $status ? false : true;

            // 处理 image
            $image = (new TextParser())->handle($image, $preEnv);

            if ('github://' === substr($image, 0, 9)) {
                try {
                    $commands = $this->actionsHandler($step, $image);
                    // 由于获取 action.yml 文件可能超时，捕获该错误
                } catch (\Throwable $e) {
                    \Log::emergency('handle pipeline use actions error'.$e->getMessage(), []);

                    continue;
                }

                $image = 'khs1994/node:git';

                $preEnv = array_merge($preEnv, $this->actionsEnvHandler($step, $workdir));
            }

            // 处理 commands
            $ci_script = CommandHandler::parse($shell, $step, $image, $commands);

            $env = array_merge(["CI_SCRIPT=$ci_script"], $preEnv);

            \Log::info(json_encode($env), []);

            $timeout = env('CI_STEP_TIMEOUT', 21600);

            if ('bash' === $shell || 'sh' === $shell) {
                $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' '.$shell.' -e'] : null;
            }

            if ('python' === $shell) {
                $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' python'] : null;
            }

            if ('pwsh' === $shell) {
                $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' pwsh -Command -'] : null;
            }

            if ('node' === $shell) {
                $cmd = $commands ? ['echo $CI_SCRIPT | base64 -d | timeout '.$timeout.' node -'] : null;
            }

            // 有 commands 指令则改为 ['/bin/sh', '-c'], 否则为默认值
            $entrypoint = $commands ? ['/bin/sh', '-c'] : null;

            $container_config = $docker_container
                ->setEnv($env)
                ->setBinds([
                    "pcit_$jobId:$workdir",
                    'pcit_cache:/tmp/pcit_cache',
                    'pcit_actions_'.$jobId.':'.'/var/run/actions',
                    '/var/run/docker.sock:/var/run/docker.sock',
                ])
                ->setEntrypoint($entrypoint)
                ->setLabels([
                    'com.khs1994.ci.pipeline' => "$jobId",
                    'com.khs1994.ci.pipeline.name' => $step,
                    'com.khs1994.ci.pipeline.status.no_status' => (string) $no_status,
                    'com.khs1994.ci.pipeline.status.failure' => (string) $failure,
                    'com.khs1994.ci.pipeline.status.success' => (string) $success,
                    'com.khs1994.ci.pipeline.status.changed' => (string) $changed,
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
                ->setCreateJson(null)
                ->getCreateJson();

            $this->storeCache($jobId, $step, $container_config, $failure, $success, $changed);
        }
    }

    public function generateDocker(): void
    {
    }

    public function storeCache($jobId,
    $step,
    $container_config,
    $failure = false,
    $success = false,
    $changed = false): void
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

    public function actionsHandler(string $step, string $image)
    {
        // github://
        $actions = substr($image, 9);

        // user/repo@ref
        // user/repo/path@ref
        $explode_array = explode('@', $actions);
        [$repo,] = $explode_array;

        $ref = 'master';
        if ($explode_array[1] ?? false) {
            $ref = $explode_array[1];
        }

        $explode_array = explode('/', $repo, 3);

        [$user,$repo] = $explode_array;
        $repo = $user.'/'.$repo;

        $path = null;
        if ($explode_array[2] ?? false) {
            $path = '/'.$explode_array[2];
        }

        \Log::info('this pipeline use actions', [
          'repo' => $repo,
          'path' => $path,
          'ref' => $ref,
        ]);

        // git clone
        $workdir = '/var/run/actions/'.$repo;
        $this->actionsGitHandler($step, $repo, $ref);

        // action.yml
        $action_yml = HttpClient::get(
            'https://raw.githubusercontent.com/'.$repo.'/'.$ref.$path.'/action.yml',
            null,
            [],
            20
        );

        $action_yml = Yaml::parse($action_yml);

        $using = $action_yml['runs']['using'];
        $main = $action_yml['runs']['main'] ?? 'index.js';
        $main = $workdir.$path.'/'.$main;

        if ('node' === substr($using, 0, 4)) {
            $using = 'node';
        }

        return [
          "$using $main",
      ];
    }

    public function actionsGitHandler($step, $repo, $ref): void
    {
        $step .= '_actions_downloader';
        $workdir = '/var/run/actions/'.$repo;
        $jobId = $this->client->job_id;
        $env = [
            'INPUT_REPO='.$repo,
            'INPUT_REF='.$ref,
        ];

        $config = (new Git(null, null, null))->generateDocker(
            $env,
            'pcit/actions-downloader',
            [],
            $jobId,
            $workdir,
            [
                'pcit_actions_'.$jobId.':'.'/var/run/actions',
            ]
        );

        $this->storeCache($jobId, $step, $config);
    }

    public function actionsEnvHandler($step, $workdir)
    {
        return [
        'GITHUB_WORKSPACE='.$workdir,
        'RUNNER_WORKSPACE'.$workdir,
        'GITHUB_REF=',
        'GITHUB_SHA='.$this->build->commit_id,
        'RUNNER_OS=Linux',
        'RUNNER_USER=',
        'RUNNER_TEMP=/home/runner/work/_temp',
        'GITHUB_REPOSITORY='.$this->build->repo_full_name,
        'GITHUB_EVENT_NAME='.$this->build->event_type,
        'GITHUB_WORKFLOW='.$step,
        'GITHUB_ACTIONS=true',
        'GITHUB_HEAD_REF=',
        'GITHUB_BASE_REF=',
        'GITHUB_ACTOR=',
        'GITHUB_ACTION=run9',
        'GITHUB_EVENT_PATH=/home/runner/work/_temp/_github_workflow/event.json',
      ];
    }
}
