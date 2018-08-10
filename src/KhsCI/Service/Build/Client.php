<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use App\Job;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Service\Build\Events\GitClient;
use KhsCI\Service\Build\Events\PipelineClient;
use KhsCI\Service\Build\Events\ServicesClient;
use KhsCI\Service\Build\Events\Subject;
use KhsCI\Service\Build\Events\SystemEnv;
use KhsCI\Support\CI;
use KhsCI\Support\Log;

class Client
{
    /**
     * @var BuildData
     */
    public $build;

    public $system_env = [];

    public $pipeline;

    public $workdir;

    public $job_id;

    /**
     * @param $build
     *
     * @throws Exception
     */
    public function handle(BuildData $build): void
    {
        $this->build = $build;

        $this->system_env = array_merge($this->system_env, $this->build->env);

        Log::debug(__FILE__, __LINE__, 'Generate Container Config', [
            'build_key_id' => $this->build->build_key_id,
            'event_type' => $this->build->event_type,
            'commit_id' => $this->build->commit_id,
            'pull_request_id' => $this->build->pull_request_number,
            'tag' => $this->build->tag,
            'git_type' => $this->build->git_type, ], Log::EMERGENCY
        );

        // 生成容器配置
        $this->config();
    }

    /**
     * 生成 config.
     *
     * @throws Exception
     */
    public function config(): void
    {
        if (!$this->build->repo_full_name or !$this->build->config) {
            throw new Exception(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
        }

        // 解析 .khsci.yml.
        $yaml_obj = json_decode($this->build->config);

        $git = $yaml_obj->clone->git ?? null;
        $cache = $yaml_obj->cache ?? null;
        $workspace = $yaml_obj->workspace ?? null;
        $pipeline = $yaml_obj->pipeline ?? null;
        $services = $yaml_obj->services ?? null;
        $matrix = $yaml_obj->matrix ?? null;
        $config = $yaml_obj->config ?? null;

        $this->pipeline = $pipeline;

        //项目根目录
        $base_path = $workspace->base ?? null;

        $path = $workspace->path ?? $this->build->repo_full_name;

        if ('.' === $path) {
            $path = null;
        }

        $this->workdir = $workdir = $base_path.'/'.$path;

        // ci system env
        $this->system_env = (new SystemEnv($this->build, $this))->handle()->env;

        // 解析构建矩阵
        $matrix = MatrixClient::parseMatrix((array) $matrix);

        // 不存在构建矩阵
        if (!$matrix) {
            $this->job_id = Job::create($this->build->build_key_id);

            (new Subject())
                // git
                ->register(new GitClient($git, $this->build, $this))
                // services
                ->register(new ServicesClient($services, (int) $this->job_id, null))
                // pipeline
                ->register(new PipelineClient($pipeline, $this->build, $this, null))
                ->handle();

            return;
        }

        // 矩阵构建循环
        foreach ($matrix as $k => $matrix_config) {
            $this->job_id = Job::create($this->build->build_key_id);

            // set git config
            (new Subject())
                // git
                ->register(new GitClient($git, $this->build, $this))
                // services
                ->register(new ServicesClient($services, (int) $this->job_id, $matrix_config))
                // pipeline
                ->register(new PipelineClient($pipeline, $this->build, $this, $matrix_config))
                ->handle();
        }
    }
}
