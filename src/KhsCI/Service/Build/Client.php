<?php

declare(strict_types=1);

namespace KhsCI\Service\Build;

use App\Job;
use Docker\Docker;
use Exception;
use KhsCI\CIException;
use KhsCI\KhsCI;
use KhsCI\Support\CI;
use KhsCI\Support\Log;

class Client
{
    private $git_type;

    private $build_key_id;

    public $pull_number;

    public $tag;

    private $commit_id;

    private $commit_message;

    private $branch;

    private $event_type;

    private $config;

    private $rid;

    private $repo_full_name;

    private $system_env = [];

    private $pipeline;

    private $workdir;
    /**
     * @var Docker
     */
    private $docker;

    /**
     * @param int         $build_key_id
     * @param string      $git_type
     * @param int         $rid
     * @param string      $commit_id
     * @param string      $commit_message
     * @param string      $branch
     * @param string      $event_type
     * @param string      $pull_request_number
     * @param string      $tag
     * @param null|string $config
     * @param null|string $repo_full_name
     * @param array       $env_vars
     *
     * @throws CIException
     * @throws Exception
     */
    public function __invoke($build_key_id,
                             string $git_type,
                             $rid,
                             string $commit_id,
                             string $commit_message,
                             string $branch,
                             string $event_type,
                             $pull_request_number,
                             ?string $tag,
                             ?string $config,
                             ?string $repo_full_name,
                             array $env_vars): void
    {
        $this->build_key_id = (int) $build_key_id;
        $this->config = $config;
        $this->commit_id = $commit_id;

        // config 不存在，。khsci.yml 文件不存在
        if ('[]' === $config) {
            throw new CIException(CI::BUILD_STATUS_PASSED);
        }

        $this->commit_message = $commit_message;
        $this->branch = $branch;
        $this->event_type = $event_type;
        $this->pull_number = $pull_request_number;
        $this->tag = $tag;
        $this->git_type = $git_type;
        $this->rid = $rid;
        $this->repo_full_name = $repo_full_name;
        $this->system_env = array_merge($this->system_env, $env_vars ?? []);

        Log::debug(__FILE__, __LINE__, json_encode([
            'build_key_id' => $build_key_id,
            'event_type' => $event_type,
            'commit_id' => $commit_id,
            'pull_request_id' => $pull_request_number,
            'tag' => $tag,
            'git_type' => $git_type, [], Log::EMERGENCY,
        ]));

        try {
            // 生成容器配置
            $this->config();

            // 运行容器
            RunContainer::run($this->build_key_id);
        } catch (\Throwable $e) {
            throw new CIException($e->getMessage());
        }
    }

    /**
     * 生成 config
     *
     * @throws Exception
     */
    public function config(): void
    {
        $branch = $this->branch;
        $commit_id = $this->commit_id;
        $event_type = $this->event_type;

        if (!$this->repo_full_name or !$this->config) {
            throw new Exception(CI::BUILD_STATUS_ERRORED);
        }

        // 解析 .khsci.yml.
        $yaml_obj = (object) json_decode($this->config, true);

        $git = $yaml_obj->clone['git'] ?? null;
        $cache = $yaml_obj->cache ?? null;
        $workspace = $yaml_obj->workspace ?? null;
        $pipeline = $yaml_obj->pipeline ?? null;
        $services = $yaml_obj->services ?? null;
        $matrix = $yaml_obj->matrix ?? null;
        $config = $yaml_obj->config ?? null;

        $this->pipeline = $pipeline;

        //项目根目录
        $base_path = $workspace['base'] ?? null;

        $path = $workspace['path'] ?? $this->repo_full_name;

        if ('.' === $path) {
            $path = null;
        }

        // --workdir.
        $this->workdir = $workdir = $base_path.'/'.$path;

        // ci system env
        $system_env = [
            'CI=true',
            'KHSCI=true',
            'CONTINUOUS_INTEGRATION=true',
            'KHSCI_BRANCH='.$this->branch,
            'KHSCI_TAG='.$this->tag,
            'KHSCI_BUILD_DIR='.$workdir,
            'KHSCI_BUILD_ID='.$this->build_key_id,
            'KHSCI_COMMIT='.$this->commit_id,
            'KHSCI_COMMIT_MESSAGE='.$this->commit_message,
            'KHSCI_EVENT_TYPE='.$this->event_type,
            'KHSCI_PULL_REQUEST=false',
            'KHSCI_REPO_SLUG='.$this->repo_full_name,
        ];

        if ($this->pull_number) {
            array_merge($system_env,
                [
                    'KHSCI_PULL_REQUEST=true',
                    'KHSCI_PULL_REQUEST_BRANCH='.$this->branch,
                    'KHSCI_PULL_REQUEST_SHA='.$this->commit_id,
                ]
            );
        }

        $this->system_env = array_merge($system_env, $this->system_env);

        Log::debug(__FILE__, __LINE__, json_encode($this->system_env), [], Log::EMERGENCY);

        // get docker
        $docker = (new KhsCI())->docker;
        $this->docker = $docker;
        $docker_container = $docker->container;

        // set git config
        GitClient::config($git,
            $this->git_type,
            $event_type,
            $this->repo_full_name,
            $workdir,
            $commit_id,
            $branch,
            $docker_container,
            $this->build_key_id,
            $this
        );

        // 解析构建矩阵
        $matrix = MatrixClient::parseMatrix($matrix);

        // 不存在构建矩阵
        if (!$matrix) {
            $job_id = Job::create($this->build_key_id);

            ServicesClient::config($services, (int) $job_id, null, $docker);

            PipelineClient::config($pipeline,
                null,
                $this->event_type,
                $this->system_env,
                $workdir,
                $docker_container,
                (int) $job_id
            );
        }

        // 矩阵构建循环
        foreach ($matrix as $k => $config) {
            $job_id = Job::create($this->build_key_id);

            //启动服务
            ServicesClient::config($services, (int) $job_id, $config, $docker);

            // 构建步骤
            PipelineClient::config($pipeline,
                $config,
                $this->event_type,
                $this->system_env,
                $workdir,
                $docker_container,
                (int) $job_id
            );
        }
    }

    /**
     * @param string|array $pattern
     * @param string       $subject
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function check($pattern, string $subject)
    {
        if (is_string($pattern)) {
            return self::checkString($pattern, $subject);
        }

        if (is_array($pattern)) {
            foreach ($pattern as $k) {
                if (self::checkString($k, $subject)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @return bool
     */
    public static function checkString(string $pattern, string $subject)
    {
        if (preg_match('#'.$pattern.'#', $subject)) {
            return true;
        }

        return false;
    }
}
