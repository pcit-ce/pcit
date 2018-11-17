<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Repo;
use App\Setting;
use Exception;
use PCIT\Exception\PCITException;
use PCIT\Service\Build\BuildData;
use PCIT\Support\CI;
use PCIT\Support\DB;
use PCIT\Support\JSON;
use PCIT\Support\Log;

class Build extends BuildData
{
    /**
     * @return Build
     *
     * @throws PCITException
     * @throws Exception
     */
    public function handle()
    {
        $sql = <<<'EOF'
SELECT

id,git_type,rid,commit_id,commit_message,branch,event_type,
pull_request_number,tag,config

FROM

builds WHERE build_status=? AND event_type IN (?,?,?) AND config !='[]' ORDER BY id ASC LIMIT 1;
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
            throw new PCITException('Build not Found, skip', 01404);
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
            throw new PCITException(CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
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

        Log::connect()->emergency('====== Get Build '.$this->build_key_id.' Data Start ======');

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

        $this->build_pushes = $array['build_pushes'] ?? 1;
        $this->build_pull_requests = $array['build_pull_requests'] ?? 1;
        $this->maximum_number_of_builds = $array['maximum_number_of_builds'] ?? 1;
        $this->auto_cancel_branch_builds = $array['auto_cancel_branch_builds'] ?? 1;
        $this->auto_cancel_pull_request_builds = $array['auto_cancel_pull_request_builds'] ?? 1;
    }

    /**
     * get user set build env.
     *
     * @throws Exception
     */
    private function getEnv(): void
    {
        $env = [];

        $env_array = \App\Env::list($this->rid, $this->git_type);

        foreach ($env_array as $k) {
            $name = $k['name'];
            $value = $k['value'];

            $env[] = $name.'='.$value;
        }

        $this->env = $env;
    }
}
