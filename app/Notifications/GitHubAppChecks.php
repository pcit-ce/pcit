<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Build;
use App\GetAccessToken;
use App\Job;
use App\Notifications\GitHubChecksConclusion\Queued;
use App\Repo;
use Exception;
use PCIT\GitHub\Service\Checks\RunData;
use PCIT\PCIT;
use PCIT\Support\CI;
use PCIT\Support\JSON;
use PCIT\Support\Log;

class GitHubAppChecks
{
    /**
     * @param int         $job_key_id
     * @param string|null $name
     * @param string      $status
     * @param int         $started_at
     * @param int         $completed_at
     * @param string      $conclusion
     * @param string|null $title
     * @param string      $summary
     * @param string      $text
     * @param array|null  $annotations  [$annotation, $annotation2]
     * @param array|null  $images       [$image, $image2]
     * @param array|null  $actions      [$action]
     * @param bool        $force_create 默认情况下若 check_run_id 已存在，则更新此 check_run_id
     *                                  若设为 true 则新建一个 check_run ,适用于第三方服务完成状态展示
     *                                  或是没有过程，直接完成的构建
     *
     * @throws Exception
     */
    public static function send(int $job_key_id,
                                string $name = null,
                                string $status = null,
                                int $started_at = null,
                                int $completed_at = null,
                                string $conclusion = null,
                                string $title = null,
                                string $summary = null,
                                string $text = null,
                                array $annotations = null,
                                array $images = null,
                                array $actions = null,
                                bool $force_create = false): void
    {
        Log::debug(__FILE__, __LINE__, 'Create GitHub App Check Run', [
            'job_key_id' => $job_key_id, ], Log::INFO);

        $rid = Job::getRid((int) $job_key_id);

        $build_key_id = (int) Job::getBuildKeyID($job_key_id);

        $repo_full_name = Repo::getRepoFullName((int) $rid);

        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        $pcit = new PCIT(['github_access_token' => $access_token], 'github');

        $output_array = Build::find((int) $build_key_id);

        $commit_id = $output_array['commit_id'];
        $event_type = $output_array['event_type'];

        $details_url = env('CI_HOST').'/github/'.$repo_full_name.'/jobs/'.$job_key_id;

        $config = JSON::beautiful(Build::getConfig((int) $build_key_id));

        $status = $status ?? CI::GITHUB_CHECK_SUITE_STATUS_QUEUED;

        $status_use_in_title = $status;

        if (CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS === $status) {
            $status_use_in_title = 'in Progress';
        }

        $name = $name ?? 'PCIT - '.ucfirst($event_type).' #'.$build_key_id.'-'.$job_key_id;

        $title = $title ??
            ucfirst($event_type).' - '.ucfirst($status_use_in_title).' #'.$build_key_id.'-'.$job_key_id;

        $summary = $summary ??
            'This Repository Build Powered By [PCIT](https://github.com/pcit-ce/pcit)';

        $text = $text ??
            (new Queued($build_key_id, $config, null, 'PHP', PHP_OS))
                ->markdown();

        $check_run_id = Job::getCheckRunId((int) $job_key_id);

        $run_data = new RunData(
            $repo_full_name,
            $name,
            $commit_id,
            $details_url,
            (string) $job_key_id,
            $status,
            $started_at,
            $completed_at,
            $conclusion,
            $title,
            $summary,
            $text,
            $annotations,
            $images,
            $actions
        );

        $run_data->check_run_id = $check_run_id;

        if ($check_run_id and !$force_create) {
            $result = $pcit->check_run->update($run_data);
        } else {
            $result = $pcit->check_run->create($run_data);
        }

        $check_run_id = json_decode($result)->id ?? null;

        $log_message = 'Create GitHub App Check run error';

        if ($check_run_id) {
            Job::updateCheckRunId(json_decode($result)->id ?? null, $job_key_id);

            $log_message = 'Create GitHub App Check Run success';

            $result = $check_run_id;
        }

        Log::debug(__FILE__, __LINE__, $log_message, [
            'job_key_id' => $job_key_id,
            'build_key_id' => $build_key_id,
            'result' => $result,
        ], Log::INFO);
    }

    /**
     * @param int $job_key_id
     *
     * @throws Exception
     */
    public static function notIncludeYaml(int $job_key_id): void
    {
        self::send($job_key_id,
            null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(),
            CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS, null,
            null,
            (new PCIT())
                ->check_md
                ->success(
                    'PHP',
                    PHP_OS,
                    null,
                    'This repo not include .pcit.yml file, please see https://docs.ci.khs1994.com/usage/'
                )
        );
    }
}
