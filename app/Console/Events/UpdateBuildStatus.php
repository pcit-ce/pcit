<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Job;
use App\Notifications\GitHubChecksConclusion\Cancelled;
use App\Notifications\GitHubChecksConclusion\Failed;
use App\Notifications\GitHubChecksConclusion\InProgress;
use App\Notifications\GitHubChecksConclusion\Passed;
use PCIT\Support\CI;

class UpdateBuildStatus
{
    public $build_status;

    public $build_log;

    private $job_key_id;

    private $config;

    public function __construct(int $job_key_id,
                                string $config,
                                string $build_status,
                                $build_log = null)
    {
        $this->job_key_id = $job_key_id;

        $this->config = $config;

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

        Job::updateFinishedAt($job_key_id, time());

        switch ($this->build_status) {
            case 'inactive':
                $this->build_status = 'inactive';
                \App\Build::updateBuildStatus($job_key_id, 'skip');

                break;
            case CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS:
                $this->build_status = CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS;
                (new InProgress($job_key_id, $config, $build_log))->handle();

                break;
            case CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE;
                (new Failed($job_key_id, $config, $build_log))->handle();

                break;
            case CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS;
                (new Passed($job_key_id, $config, $build_log))->handle();

                break;
            default:
                $this->build_status = CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED;
                (new Cancelled($job_key_id, $config, $build_log))->handle();
        }
    }
}
