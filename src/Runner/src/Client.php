<?php

declare(strict_types=1);

namespace PCIT\Runner;

use App\Build;
use App\Job;
use Exception;
use PCIT\Framework\Support\Log;
use PCIT\Framework\Support\Subject;
use PCIT\Runner\Events\Cache;
use PCIT\Runner\Events\Git;
use PCIT\Runner\Events\Matrix;
use PCIT\Runner\Events\Notifications;
use PCIT\Runner\Events\Pipeline;
use PCIT\Runner\Events\Services;
use PCIT\Support\CacheKey;
use PCIT\Support\CI;

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

    public $buildID;

    public $language;

    public $git;

    public $services;

    public $cache;

    /**
     * @param int $job_id 处理 job 重新构建
     *
     * @throws Exception
     */
    public function handle(BuildData $build, int $job_id = 0): void
    {
        $this->build = $build;
        $this->buildID = (int) $this->build->build_key_id;

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
        $this->config($job_id);
    }

    /**
     * 生成 config.
     *
     * @throws Exception
     */
    private function config(int $job_id = 0): void
    {
        if (!$this->build->repo_full_name or !$this->build->config) {
            throw new Exception(CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
        }

        // 解析 .pcit.y(a)ml.
        $yaml_obj = json_decode($this->build->config);

        $this->language = $language = $yaml_obj->language ?? 'php';
        $this->git = $git = $yaml_obj->clone->git ?? null;
        $this->cache = $cache = $yaml_obj->cache ?? null;
        $workspace = $yaml_obj->workdir ?? $yaml_obj->workspace ?? null;
        $pipeline = $yaml_obj->steps ?? $yaml_obj->pipeline ?? null;
        $this->services = $services = $yaml_obj->services ?? null;
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

        if ($job_id) {
            Log::getMonolog()->emergency('Handle job restart');

            $this->handleJob($job_id, Job::getEnv($job_id));

            return;
        }

        // 解析构建矩阵
        $matrix = Matrix::parseMatrix((array) $matrix);

        // 不存在构建矩阵
        if (!$matrix) {
            Log::getMonolog()->emergency('This build is not matrix');

            $job_id = (int) (Job::getJobIDByBuildKeyID($this->buildID)[0] ?? 0);

            $this->handleJob($job_id, null);

            return;
        }

        Log::getMonolog()->emergency('This build include matrix');

        // 矩阵构建循环
        foreach ($matrix as $k => $matrix_config) {
            // 用户点击重新构建，必须重新生成 job
            // 获取已有的 job_id
            $job_id = Job::getJobIDByBuildKeyIDAndEnv(
                $this->buildID, json_encode($matrix_config));

            $this->handleJob($job_id, $matrix_config);
        }
    }

    /**
     * 生成 job 缓存.
     *
     * @throws Exception
     */
    private function handleJob(int $job_id, ?array $matrix_config): void
    {
        $this->job_id = $job_id = $job_id ?: Job::create($this->build->build_key_id);

        Log::getMonolog()->emergency(
            '=== Handle job Start ===', ['job_id' => $this->job_id]);
        // 清理缓存
        CacheKey::flush($job_id);

        Job::updateEnv($job_id, json_encode($matrix_config));

        $build_key_id = (int) $this->build->build_key_id;

        $gitType = Build::getGitType($build_key_id);
        $rid = (int) Build::getRid($build_key_id);
        $branch = Build::getBranch($build_key_id);

        (new Subject())
            // git
            ->register(new Git($this->git, $this->build, $this))
            // services
            ->register(new Services($this->services, (int) $this->job_id, $matrix_config))
            // pipeline
            ->register(new Pipeline($this->pipeline, $this->build, $this, $matrix_config))
            // cache
            ->register(new Cache((int) $this->job_id, $build_key_id, $this->workdir, $gitType,
                       $rid, $branch, $this->cache,
                       // pull_request 事件不上传缓存
                       'pull_request' === $this->build->event_type
            ))
            ->handle();

        Log::getMonolog()->emergency('=== Generate Job Success ===', ['job_id' => $this->job_id]);
    }
}
