<?php

declare(strict_types=1);

namespace App\Console;

use App\Build;
use Error;
use Exception;
use KhsCI\CIException;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\DB;
use KhsCI\Support\Env;
use KhsCI\Support\JSON;
use KhsCI\Support\Log;

class Queue
{
    private static $commit_id;

    private static $unique_id;

    private static $event_type;

    private static $build_key_id;

    private static $git_type;

    private static $config;
    /**
     * @var KhsCI
     */
    private static $khsci;

    /**
     * @throws Exception
     */
    public static function queue(): void
    {
        self::$khsci = new KhsCI();

        $queue = self::$khsci->queue;

        try {
            $sql = <<<'EOF'
SELECT 

id,git_type,rid,commit_id,commit_message,branch,event_type,pull_request_id,tag_name,config,check_run_id

FROM 

builds WHERE build_status=? AND event_type IN (?,?,?) ORDER BY id DESC;
EOF;

            $output = DB::select($sql, [
                CI::BUILD_STATUS_PENDING,
                CI::BUILD_EVENT_PUSH,
                CI::BUILD_EVENT_TAG,
                CI::BUILD_EVENT_PR,
            ]);

            $output = $output[0] ?? null;

            // 数据库没有结果，跳过构建

            if (!$output) {
                Log::debug(__FILE__, __LINE__, 'Build Table output is empty skip');

                return;
            }

            $output = array_values($output);

            $build_key_id = $output[0];

            Build::updateStartAt((int) $build_key_id);

            unset($output[10]);

            self::$config = JSON::beautiful($output[9]);

            if ('github_app' === $output[1]) {
                Up::updateGitHubAppChecks((int) $build_key_id, null,
                    CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS,
                    time(),
                    null,
                    null,
                    null,
                    null,
                    self::$khsci->check_md->in_progress('PHP', PHP_OS, self::$config)
                );
            }

            $queue(...$output);
        } catch (CIException $e) {
            self::$commit_id = $e->getCommitId();
            self::$unique_id = $e->getUniqueId();
            self::$event_type = $e->getEventType();
            self::$build_key_id = $e->getCode();
            self::$git_type = Build::getGitType(self::$build_key_id);

            // $e->getCode() is build key id.
            Build::updateStopAt(self::$build_key_id);

            self::saveLog();

            switch ($e->getMessage()) {
                case CI::BUILD_STATUS_INACTIVE:
                    self::setBuildStatusInactive();

                    break;
                case CI::BUILD_STATUS_FAILED:
                    self::setBuildStatusFailed();

                    break;
                case CI::BUILD_STATUS_PASSED:
                    self::setBuildStatusPassed();

                    break;
                default:
                    self::setBuildStatusErrored();
            }

            Log::debug(__FILE__, __LINE__, $e->__toString());
        } catch (Exception | Error  $e) {
            Log::debug(__FILE__, __LINE__, $e->__toString());
        } finally {
            if (!self::$unique_id) {
                return;
            }

            $queue::systemDelete(self::$unique_id, true);
            Cache::connect()->set('khsci_up_status', 0);

            Log::connect()->debug('====== Build Stopped Success ======');

            self::$unique_id = null;
        }
    }

    /**
     * @throws Exception
     */
    private static function saveLog(): void
    {
        // 日志美化

        $output = Cache::connect()->hGet('build_log', (string) self::$build_key_id);

        if (!$output) {
            Log::debug(__FILE__, __LINE__, 'Build Log empty, skip');

            return;
        }

        file_put_contents(sys_get_temp_dir().'/'.self::$unique_id, $output);

        $fh = fopen(sys_get_temp_dir().'/'.self::$unique_id, 'r');

        Cache::connect()->del((string) self::$unique_id);

        while (!feof($fh)) {
            $one_line_content = fgets($fh);

            $one_line_content = substr("$one_line_content", 8);

            Cache::connect()->append((string) self::$unique_id, $one_line_content);
        }

        fclose($fh);

        $a = Cache::connect()->get((string) self::$unique_id);

        Build::updateLog(self::$build_key_id, $a);

        // cleanup
        unlink(sys_get_temp_dir().'/'.self::$unique_id);

        Cache::connect()->del((string) self::$unique_id);
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusInactive(): void
    {
        Build::updateBuildStatus(self::$build_key_id, CI::BUILD_STATUS_INACTIVE);

        if ('github' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_FAILURE,
                'This Repo is Inactive'
            );
        }

        if ('github_app' === self::$git_type) {
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt(self::$build_key_id),
                (int) Build::getStopAt(self::$build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED,
                null,
                null,
                self::$khsci->check_md->cancelled('PHP', PHP_OS, self::$config, null),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusErrored(): void
    {
        // 更新数据库状态
        Build::updateBuildStatus(self::$build_key_id, CI::BUILD_STATUS_ERRORED);

        // 通知 GitHub commit Status
        if ('github' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_ERROR,
                'The '.Env::get('CI_NAME').' build could not complete due to an error'
            );
        }
        // 微信通知

        // GitHub App checks API

        if ('github_app' === self::$git_type) {
            $build_log = Build::getLog((int) self::$build_key_id);

            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt(self::$build_key_id),
                (int) Build::getStopAt(self::$build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                self::$khsci->check_md->failure('PHP', PHP_OS, self::$config, $build_log),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusFailed(): void
    {
        Build::updateBuildStatus(self::$build_key_id, CI::BUILD_STATUS_FAILED);

        if ('github' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_FAILURE,
                'The '.Env::get('CI_NAME').' build is failed'
            );
        }

        if ('github_app' === self::$git_type) {
            $build_log = Build::getLog((int) self::$build_key_id);
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt(self::$build_key_id),
                (int) Build::getStopAt(self::$build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                self::$khsci->check_md->failure('PHP', PHP_OS, self::$config, $build_log),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusPassed(): void
    {
        Build::updateBuildStatus(self::$build_key_id, CI::BUILD_STATUS_PASSED);

        if ('github' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_SUCCESS,
                'The '.Env::get('CI_NAME').' build passed'
            );
        }

        if ('github_app' === self::$git_type) {
            $build_log = Build::getLog((int) self::$build_key_id);
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt(self::$build_key_id),
                (int) Build::getStopAt(self::$build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
                null,
                null,
                self::$khsci->check_md->success('PHP', PHP_OS, self::$config, $build_log),
                null,
                null
            );

            return;
        }
    }
}
