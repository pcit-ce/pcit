<?php

declare(strict_types=1);

namespace App\Notifications\GitHubChecksConclusion;

use App\Build;
use App\Job;
use App\Notifications\GitHubAppChecks;
use PCIT\Framework\Support\JSON;
use PCIT\Support\CI;

abstract class Kernel
{
    protected static $header = <<<'EOF'
## About PCIT (PCIT is CI TOOLKIT Written by PHP)

**China First Support GitHub Checks API CI/CD System Powered By Container(Docker/Kubernetes)**

**Author** @khs1994

| [GitHub App](https://github.com/apps/pcit-ce) | [Official Website](https://ci.khs1994.com) | [Support Documents](https://github.com/pcit-ce/pcit/tree/master/docs) | [Community Support](https://github.com/pcit-ce/pcit/issues) |
| -- | -- | -- | -- |
| :octocat: | :computer: | :notebook: | :speech_balloon: |

## Try PCIT ?

Please See [PCIT Support Docs](https://github.com/pcit-ce/pcit/tree/master/docs)

EOF;

    public $job_key_id;

    public $config;

    public $language;

    public $os;

    public $build_log;

    public $git_type;

    /**
     * Passed constructor.
     *
     * @param string $config
     * @param string $language
     * @param string $git_type
     */
    public function __construct(
        int $job_key_id,
        string $config = null,
        string $build_log = null,
        string $language = null,
        string $os = null,
        $git_type = 'github'
    ) {
        $this->job_key_id = $job_key_id;

        $this->config = JSON::beautiful($config);

        $this->config = $this->config ??
            'This repo not include .pcit.yml file or build log is empty, please see https://docs.ci.khs1994.com/usage/';

        $this->language = $language ?? 'PHP';

        $this->os = $os ?? \PHP_OS;

        $this->build_log = $build_log ?? Job::getLog((int) $this->job_key_id) ??
            'This repo not include .pcit.yml file or build log is empty, please see https://docs.ci.khs1994.com/usage/';

        $this->git_type = $git_type;
    }

    public function handle(): void
    {
        if ('github' !== $this->git_type) {
            return;
        }

        $job_key_id = $this->job_key_id;

        // Build::updateBuildStatus($this->job_key_id, CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS);

        GitHubAppChecks::send(
            $job_key_id,
            null,
            CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            (int) Job::getStartAt($job_key_id),
            (int) Job::getFinishedAt($job_key_id),
            CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
            null,
            null,
            $this->markdown(),
            null,
            null
        );
    }

    /**
     * @return string
     */
    public function markdown()
    {
        return '';
    }
}
