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

            /**
             * $e->getCode() is build key id.
             */

            switch ($e->getMessage()) {
                case CI::BUILD_STATUS_SKIP:
                    self::setBuildStatusSkip($e->getCode());

                    break;
                case CI::BUILD_STATUS_INACTIVE:

                    self::setBuildStatusInactive($e->getCode());

                    break;
                case CI::BUILD_STATUS_FAILED:

                    self::setBuildStatusFailed($e->getCode());

                    break;
                case CI::BUILD_STATUS_PASSED:

                    self::setBuildStatusPassed($e->getCode());

                    break;
                default:

                    self::setBuildStatusErrored($e->getCode());
            }

            Log::connect()->debug($e->getCode().$e->getMessage());
        } catch (Exception | Error $e) {
            echo $e->getMessage();
            echo $e->getFile();
            echo $e->getLine();
        }
    }

    /**
     * @param int $build_key_id
     * @param int $lastId
     *
     * @throws Exception
     */
    private static function setBuildStatusInactive(int $build_key_id, int $lastId = 0): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CI::BUILD_STATUS_INACTIVE, $build_key_id]);

        self::updateGitHubCommitStatus(
            $build_key_id,
            CI::GITHUB_STATUS_FAILURE,
            'This Repo is Inactive',
            'continuous-integration/khsci/'.CI::BUILD_EVENT_PUSH
        );
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    private static function setBuildStatusSkip(int $build_key_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CI::BUILD_STATUS_SKIP, $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    private static function setBuildStatusErrored(int $build_key_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        /*
         * 更新数据库状态
         */
        DB::update($sql, [CI::BUILD_STATUS_ERRORED, $build_key_id]);
        /*
         * 通知 GitHub commit Status
         */

        /*
         * 微信通知
         */
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    private static function setBuildStatusFailed(int $build_key_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CI::BUILD_STATUS_FAILED, $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @throws Exception
     */
    private static function setBuildStatusPassed(int $build_key_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CI::BUILD_STATUS_PASSED, $build_key_id]);
    }

    /**
     * @param int    $build_key_id
     *
     * @param string $state
     *
     * @param string $description
     *
     * @param string $context
     *
     * @throws Exception
     */
    private static function updateGitHubCommitStatus(int $build_key_id,
                                                     string $state,
                                                     string $description,
                                                     string $context)
    {
        $status = new GitHub();

        $accessToken = '';

        $sql = <<<EOF
SELECT

username,repo_name
FROM repo WHERE 

rid=( SELECT rid FROM builds WHERE id=? )
EOF;
        $output = DB::select($sql, []);

        $status->create(
            $username = $output[0]['username'],
            $repo = $output[0]['repo_name'],
            self::$commit_id,
            $accessToken,
            $state,
            $target_url = Env::get('CI_HOST').$username.'/'.$repo.'/builds/'.$build_key_id,
            $description,
            $context
        );
    }

    /**
     * Remove all Docker Resource
     */
    private function systemDelete()
    {

    }
}
