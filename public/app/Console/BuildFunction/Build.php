<?php

declare(strict_types=1);

namespace App\Console\BuildFunction;

use App\Repo;
use App\Setting;
use Exception;
use KhsCI\CIException;
use KhsCI\Support\CI;
use KhsCI\Support\DB;
use KhsCI\Support\JSON;
use KhsCI\Support\Log;

class Build
{
    public $commit_id;

    public $commit_message;

    public $unique_id;

    public $event_type;

    public $build_key_id;

    public $pull_request_number;

    public $tag;

    /**
     * @var int
     */
    public $rid;

    public $repo_full_name;

    /**
     * @var string
     */
    public $git_type;

    public $config;

    public $build_status;

    public $description;

    public $branch;

    // repo config

    public $build_pushes;

    public $build_pull_requests;

    public $maximum_number_of_builds;

    public $auto_cancel_branch_builds;

    public $auto_cancel_pull_request_builds;

    /**
     * @return Build
     *
     * @throws CIException
     * @throws Exception
     */
    public function handle()
    {
        $sql = <<<'EOF'
SELECT

id,git_type,rid,commit_id,commit_message,branch,event_type,
pull_request_number,tag,config

FROM

builds WHERE 1=(SELECT build_activate FROM repo WHERE repo.rid=builds.rid AND repo.git_type=builds.git_type LIMIT 1) 

AND build_status=? AND event_type IN (?,?,?) AND config !='[]' ORDER BY id DESC LIMIT 1;
EOF;

        $output = DB::select($sql, [
            'pending',
            CI::BUILD_EVENT_PUSH,
            CI::BUILD_EVENT_TAG,
            CI::BUILD_EVENT_PR,
        ]);

        $output = $output[0] ?? null;

        // 数据库没有结果，跳过构建，也就没有 build_key_id

        if (!$output) {
            throw new CIException('Build not Found, skip', 01404);
        }

        $output = array_values($output);

        list($build_key_id,
            $this->git_type,
            $rid,
            $this->commit_id,
            $this->commit_message,
            $this->branch,
            $this->event_type,
            $pull_request_number,
            $this->tag,
            $this->config) = $output;

        if (!$this->config) {
            throw new CIException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
        }

        $this->build_key_id = (int) $build_key_id;
        $this->rid = (int) $rid;
        $this->pull_request_number = (int) $pull_request_number;

        $this->unique_id = session_create_id();

        $this->getRepoConfig();

        if (!$this->build_pull_requests and CI::BUILD_EVENT_PR === $this->event_type) {
            // don't build pr
        }

        $this->config = JSON::beautiful($this->config);

        Log::connect()->emergency('====== '.$this->build_key_id.' Build Start Success ======');

        $this->getRepoConfig();

        $this->getEnv();

        $this->repo_full_name = Repo::getRepoFullName((int) $this->rid, $this->git_type);

        return $this;
    }

    /**
     * @throws Exception
     */
    private function getRepoConfig(): void
    {
        $array = Setting::list($this->rid, $this->git_type);

        $this->build_pushes = $array['build_pushes'];
        $this->build_pull_requests = $array['build_pull_requests'];
        $this->maximum_number_of_builds = $array['maximum_number_of_builds'];
        $this->auto_cancel_branch_builds = $array['auto_cancel_branch_builds'];
        $this->auto_cancel_pull_request_builds = $array['auto_cancel_pull_request_builds'];
    }

    /**
     * get user set build env.
     *
     * @return array
     *
     * @throws Exception
     */
    private function getEnv()
    {
        $env = [];

        $env_array = \App\Env::list($this->rid, $this->git_type);

        foreach ($env_array as $k) {
            $name = $k['name'];
            $value = $k['value'];

            $env[] = $name.'='.$value;
        }

        return $env;
    }
}
