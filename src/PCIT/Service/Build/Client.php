<?php

declare(strict_types=1);

namespace PCIT\Service\Build;

use App\Job;
use Exception;
use PCIT\Service\Build\Events\Cache;
use PCIT\Service\Build\Events\Git;
use PCIT\Service\Build\Events\Matrix;
use PCIT\Service\Build\Events\Notifications;
use PCIT\Service\Build\Events\Pipeline;
use PCIT\Service\Build\Events\Services;
use PCIT\Service\Build\Events\Subject;
use PCIT\Support\CI;
use PCIT\Support\Log;

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

        Log::debug(__FILE__, __LINE__, 'This build property', [
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
    private function config(): void
    {
        if (!$this->build->repo_full_name or !$this->build->config) {
            throw new Exception(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
        }

        // 解析 .pcit.yml.
        $yaml_obj = json_decode($this->build->config);

        $git = $yaml_obj->clone->git ?? null;
        $cache = $yaml_obj->cache ?? null;
        $workspace = $yaml_obj->workspace ?? null;
        $pipeline = $yaml_obj->pipeline ?? null;
        $services = $yaml_obj->services ?? null;
        $matrix = $yaml_obj->matrix ?? null;
        $notifications = $yaml_obj->notifications ?? null;

        $this->pipeline = $pipeline;

        //项目根目录
        $base_path = $workspace->base ?? null;

        $path = $workspace->path ?? $this->build->repo_full_name;

        $path = '.' === $path ? null : $path;

        $this->workdir = $workdir = $base_path.'/'.$path;

        (new Subject())
            // notification
            ->register(new Notifications($this->build->build_key_id, $notifications))
            ->handle();

        // ci system env
        $this->system_env = (new SystemEnv($this->build, $this))->handle()->env;

        // 解析构建矩阵
        $matrix = Matrix::parseMatrix((array) $matrix);

        // 不存在构建矩阵
        if (!$matrix) {
            Log::getMonolog()->emergency('This build is not matrix');

            $this->job_id = Job::create($this->build->build_key_id);

            Log::getMonolog()->emergency(
                '=== Handle job Start', ['job_id' => $this->job_id]);

            (new Subject())
                // git
                ->register(new Git($git, $this->build, $this))
                // services
                ->register(new Services($services, (int) $this->job_id, null))
                // pipeline
                ->register(new Pipeline($pipeline, $this->build, $this, null))
                // cache
                ->register(new Cache((int) $this->job_id, $this->build->build_key_id, $workdir, $cache))
                ->handle();

            Log::getMonolog()->emergency('=== Generate job success ===', ['job_id' => $this->job_id]);

            return;
        }

        Log::getMonolog()->emergency('This build include matrix');

        // 矩阵构建循环
        foreach ($matrix as $k => $matrix_config) {
            $this->job_id = Job::create($this->build->build_key_id);

            Log::getMonolog()->emergency(
                '=== Handle job Start ===', ['job_id' => $this->job_id]);

            (new Subject())
                // git
                ->register(new Git($git, $this->build, $this))
                // services
                ->register(new Services($services, (int) $this->job_id, $matrix_config))
                // pipeline
                ->register(new Pipeline($pipeline, $this->build, $this, $matrix_config))
                // cache
                ->register(new Cache((int) $this->job_id, $this->build->build_key_id, $workdir, $cache))
                ->handle();

            Log::getMonolog()->emergency('=== Generate job success ===', ['job_id' => $this->job_id]);
        }
    }
}
