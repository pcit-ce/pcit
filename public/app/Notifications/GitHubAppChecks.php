<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Build;
use App\GetAccessToken;
use App\Repo;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\CI;
use KhsCI\Support\Env;
use KhsCI\Support\JSON;
use KhsCI\Support\Log;

class GitHubAppChecks
{
    /**
     * @param int         $build_key_id
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
     * @param bool        $force_create 默认请款下若 check_run_id 已存在，则更新此 check_run_id
     *                                  若设为 true 则新建一个 check_run ,适用于第三方服务完成状态展示
     *                                  或是没有过程，直接完成的构建
     *
     * @throws Exception
     */
    public static function send(int $build_key_id,
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
        Log::debug(__FILE__, __LINE__, 'Create GitHub App Check Run '.$build_key_id.' ...', [], Log::INFO);

        $rid = Build::getRid((int) $build_key_id);

        $repo_full_name = Repo::getRepoFullName((int) $rid);

        $access_token = GetAccessToken::getGitHubAppAccessToken($rid);

        $khsci = new KhsCI(['github_access_token' => $access_token], 'github');

        $output_array = Build::find((int) $build_key_id);

        $branch = $output_array['branch'];
        $commit_id = $output_array['commit_id'];
        $event_type = $output_array['event_type'];

        $details_url = Env::get('CI_HOST').'/github/'.$repo_full_name.'/builds/'.$build_key_id;

        $config = JSON::beautiful(Build::getConfig((int) $build_key_id));

        $status = $status ?? CI::GITHUB_CHECK_SUITE_STATUS_QUEUED;

        $status_use_in_title = $status;

        if (CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS === $status) {
            $status_use_in_title = 'in Progress';
        }

        $name = $name ?? 'Build Event is '.ucfirst($event_type).' '.ucfirst($status_use_in_title);

        $title = $title ?? Env::get('CI_NAME').' Build is '.ucfirst($status_use_in_title);

        $summary = $summary ??
            'This Repository Build Powered By [KhsCI](https://github.com/khs1994-php/khsci)';

        $text = $text ??
            (new Queued($build_key_id, $config, null, 'PHP', PHP_OS))
                ->markdown();

        $check_run_id = Build::getCheckRunId((int) $build_key_id);

        if ($check_run_id and !$force_create) {
            $output = $khsci->check_run->update(
                $repo_full_name, $check_run_id, $name, $branch, $commit_id, $details_url,
                (string) $build_key_id, $status, $started_at ?? time(),
                $completed_at, $conclusion, $title, $summary, $text, $annotations, $images, $actions
            );
        } else {
            $output = $khsci->check_run->create(
                $repo_full_name, $name, $branch, $commit_id, $details_url, (string) $build_key_id, $status,
                $started_at ?? time(),
                $completed_at, $conclusion, $title, $summary, $text, $annotations, $images, $actions
            );
        }

        Build::updateCheckRunId(json_decode($output)->id ?? null, $build_key_id);

        $log_message = 'Create GitHub App Check Run '.$build_key_id.' success';

        Log::debug(__FILE__, __LINE__, $log_message, [], Log::INFO);
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    public static function notIncludeYaml(int $build_key_id): void
    {
        self::send($build_key_id,
            null, CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            time(), time(),
            CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS, null,
            null,
            (new KhsCI())
                ->check_md
                ->success(
                    'PHP',
                    PHP_OS,
                    null,
                    'This repo not include .khsci.yml file, please see https://docs.ci.khs1994.com/usage/'
                )
        );
    }
}
