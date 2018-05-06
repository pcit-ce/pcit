<?php

declare(strict_types=1);

namespace App\Console;

use Error;
use Exception;
use KhsCI\CIException;
use KhsCI\Service\Queue\Queue as QueueService;
use KhsCI\Service\Status\GitHub;
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

    /**
     * @throws Exception
     */
    public static function queue(): void
    {
        try {
            $queue = new QueueService();
            $queue();
        } catch (CIException $e) {
            self::$commit_id = $e->getCommitId();
            self::$unique_id = $e->getUniqueId();
            self::$event_type = $e->getEventType();
            self::$build_key_id = $e->getCode();

            /**
             * $e->getCode() is build key id.
             */
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

            Log::connect()->debug($e->getCode().$e->getMessage());
        } catch (Exception | Error $e) {
            echo $e->getMessage();
            echo $e->getFile();
            echo $e->getLine();
        }
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusInactive(): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CI::BUILD_STATUS_INACTIVE, self::$build_key_id]);

        self::updateGitHubCommitStatus(CI::GITHUB_STATUS_FAILURE, 'This Repo is Inactive');
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusSkip(): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CI::BUILD_STATUS_SKIP, self::$build_key_id]);

        self::updateGitHubCommitStatus(CI::GITHUB_STATUS_SUCCESS, 'The build is skip');
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusErrored(): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        /*
         * 更新数据库状态
         */
        DB::update($sql, [CI::BUILD_STATUS_ERRORED, self::$build_key_id]);

        /*
         * 通知 GitHub commit Status
         */
        self::updateGitHubCommitStatus(
            CI::GITHUB_STATUS_ERROR,
            'The '.Env::get('CI_NAME').' build could not complete due to an error'
        );

        /*
         * 微信通知
         */
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusFailed(): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CI::BUILD_STATUS_FAILED, self::$build_key_id]);

        self::updateGitHubCommitStatus(
            CI::GITHUB_STATUS_FAILURE,
            'The '.Env::get('CI_NAME').' build is failed'
        );
    }

    /**
     * @throws Exception
     */
    private static function setBuildStatusPassed(): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CI::BUILD_STATUS_PASSED, self::$build_key_id]);

        self::updateGitHubCommitStatus(
            CI::GITHUB_STATUS_SUCCESS,
            'The '.Env::get('CI_NAME').' build passed'
        );
    }

    /**
     * @param string $state
     * @param string $description
     *
     * @throws Exception
     */
    private static function updateGitHubCommitStatus(string $state, string $description): void
    {
        $status = new GitHub();

        $accessToken = '';

        $sql = <<<EOF
SELECT

username,repo_name
FROM repo WHERE 

rid=( SELECT rid FROM builds WHERE id=? )
EOF;
        $output = DB::select($sql, [self::$build_key_id]);

        $status->create(
            $username = $output[0]['username'],
            $repo = $output[0]['repo_name'],
            self::$commit_id,
            $accessToken,
            $state,
            $target_url = Env::get('CI_HOST').$username.'/'.$repo.'/builds/'.self::$build_key_id,
            $description,
            'continuous-integration/'.Env::get('CI_NAME').'/'.self::$event_type
        );
    }

    /**
     * Remove all Docker Resource.
     */
    private function systemDelete(): void
    {
    }
}
