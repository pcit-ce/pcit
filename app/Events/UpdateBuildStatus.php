<?php

declare(strict_types=1);

namespace App\Events;

use App\Build;
use App\Job;
use App\Notifications\GitHubChecksConclusion\Cancelled;
use App\Notifications\GitHubChecksConclusion\Failed;
use App\Notifications\GitHubChecksConclusion\InProgress;
use App\Notifications\GitHubChecksConclusion\Passed;
use PCIT\Support\CI;

/**
 * 更新状态
 */
class UpdateBuildStatus
{
    public $build_status;

    public $build_log;

    private $job_key_id;

    private $config;

    public function __construct(
        int $job_key_id,
        ?int $build_key_id,
        string $build_status,
        $build_log = null
    ) {
        $this->job_key_id = $job_key_id;

        $this->config = $config = Build::getConfig((int) $build_key_id) ?? '';

        $this->build_status = $build_status;

        $this->build_log = $build_log;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $job_key_id = $this->job_key_id;
        $config = $this->config;
        $build_log = $this->build_log;

        $is_github = false;
        if ('github' === Job::getGitType($job_key_id)) {
            $is_github = true;
        }

        switch ($this->build_status) {
            case 'inactive':
                $this->build_status = 'inactive';
                \App\Build::updateBuildStatus($job_key_id, 'skipped');

                break;
            case CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS:
                $this->build_status = CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS;
                $is_github && (new InProgress($job_key_id, $config, $build_log))->handle();

                break;
            case CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE;
                Job::updateBuildStatus($job_key_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE);
                $this->updateBuildStatus();
                $is_github && (new Failed($job_key_id, $config, $build_log))->handle();

                break;
            case CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS;
                Job::updateBuildStatus($job_key_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);
                $this->updateBuildStatus();
                $is_github && (new Passed($job_key_id, $config, $build_log))->handle();

                break;
            default:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED;
                Job::updateBuildStatus($job_key_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED);
                $this->updateBuildStatus();
                $is_github && (new Cancelled($job_key_id, $config, $build_log))->handle();
        }
    }

    public function updateBuildStatus(): void
    {
        $build_key_id = Job::getBuildKeyId($this->job_key_id);

        $status = Job::getBuildStatusByBuildKeyId($build_key_id);

        Build::updateBuildStatus($build_key_id, $status);
        Build::updateFinishedAt($build_key_id);
    }
}
