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
use KhsCI\Support\Log;

class Queue
{
    private static $commit_id;

    private static $unique_id;

    private static $event_type;

    private static $build_key_id;

    private static $git_type;

    /**
     * @throws Exception
     */
    public static function queue(): void
    {
        $khsci = new KhsCI();

        $queue = $khsci->queue;

        try {
            $sql = <<<'EOF'
SELECT 

id,git_type,rid,commit_id,commit_message,branch,event_type,pull_request_id,tag_name

FROM 

builds WHERE build_status=? AND event_type IN (?,?,?) ORDER BY id DESC;
EOF;

            $output = DB::select($sql, [
                CI::BUILD_STATUS_PENDING,
                CI::BUILD_EVENT_PUSH,
                CI::BUILD_EVENT_TAG,
                CI::BUILD_EVENT_PR,
            ]);

            $output = array_values($output[0]);

            $queue(...$output);
        } catch (CIException $e) {
            self::$commit_id = $e->getCommitId();
            self::$unique_id = $e->getUniqueId();
            self::$event_type = $e->getEventType();
            self::$build_key_id = $e->getCode();
            self::$git_type = Build::getGitType(self::$build_key_id);

            // $e->getCode() is build key id.

            switch ($e->getMessage()) {
                case CI::BUILD_STATUS_SKIP:
                    self::setBuildStatusSkip();

                    break;
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

            Log::debug(__FILE__, __LINE__, $e->__toString(), $e->getCode());
        } catch (Exception | Error $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        } finally {
            $queue::systemDelete(self::$unique_id, true);
            Build::updateStopAt(self::$build_key_id);
            Cache::connect()->set('khsci_up_status', 0);
        }
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
                time() - 10,
                time(),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED,
                null,
                null,
                null,
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusSkip(): void
    {
        Build::updateBuildStatus(self::$build_key_id, CI::BUILD_STATUS_SKIP);

        if ('githuub' === static::$git_type) {
            Up::updateGitHubStatus(
                self::$build_key_id,
                CI::GITHUB_STATUS_SUCCESS,
                'The '.Env::get('CI_NAME').' build is skip'
            );
        }

        if ('github_app' === self::$git_type) {
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                time() - 10,
                time(),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_CANCELLED,
                null,
                null,
                null,
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
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                time() - 10,
                time(),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                null,
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
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                time() - 10,
                time(),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                null,
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
            Up::updateGitHubAppChecks(
                self::$build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                time() - 10,
                time(),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
                null,
                null,
                null,
                null,
                null
            );

            return;
        }
    }
}
