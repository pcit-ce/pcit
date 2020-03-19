<?php

declare(strict_types=1);

namespace App\Events;

use App\Build as BuildDB;
use App\Repo;
use App\Setting;
use PCIT\Exception\PCITException;
use PCIT\Framework\Support\JSON;
use PCIT\Runner\BuildData;
use PCIT\Support\CI;

/**
 * 获取 build 数据.
 */
class Build extends BuildData
{
    /**
     * @throws PCITException
     */
    public function handle(int $buildId = 0): self
    {
        $result = \App\Build::getData($buildId);

        $result = array_values($result);

        list($build_key_id,
            $this->git_type,
            $rid,
            $this->commit_id,
            $this->commit_message,
            $this->branch,
            $this->event_type,
            $pull_request_number,
            $this->tag,
            $this->config) = $result;

        if (!$this->config or !json_decode($this->config)) {
            BuildDB::updateBuildStatus($buildId, 'misconfigured');

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

        \Log::emergency('====== Get Build '.$this->build_key_id.' Data Start ======');

        $this->getRepoConfig();

        $this->getEnv();

        $this->repo_full_name = Repo::getRepoFullName((int) $this->rid, $this->git_type);

        return $this;
    }

    /**
     * @throws \Exception
     */
    private function getRepoConfig(): void
    {
        $result = Setting::list($this->rid, $this->git_type);

        $this->build_pushes = $result['build_pushes'] ?? 1;
        $this->build_pull_requests = $result['build_pull_requests'] ?? 1;
        $this->maximum_number_of_builds = $result['maximum_number_of_builds'] ?? 1;
        $this->auto_cancel_branch_builds = $result['auto_cancel_branch_builds'] ?? 1;
        $this->auto_cancel_pull_request_builds = $result['auto_cancel_pull_request_builds'] ?? 1;
    }

    /**
     * get user set build env. ['k=v'].
     *
     * @throws \Exception
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
