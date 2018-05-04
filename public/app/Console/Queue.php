<?php

declare(strict_types=1);

namespace App\Console;

use Exception;
use KhsCI\Service\Queue\Queue as QueueService;
use KhsCI\Support\CIConst;
use KhsCI\Support\DB;
use KhsCI\Support\Log;

class Queue
{
    /**
     * @throws Exception
     */
    public static function queue(): void
    {
        try {
            $queue = new QueueService();
            $queue();
        } catch (Exception $e) {
            $commit_id = '1';

            switch ($e->getMessage()) {
                case CIConst::BUILD_STATUS_SKIP:
                    self::setBuildStatusSkip($e->getCode(), $commit_id);
                    break;
                case CIConst::BUILD_STATUS_INACTIVE:
                    self::setBuildStatusInactive($e->getCode(), $commit_id);
                    break;
                case CIConst::BUILD_STATUS_ERRORED:
                    self::setBuildStatusErrored($e->getCode(), $commit_id);
                    break;
                case CIConst::BUILD_STATUS_FAILED:
                    self::setBuildStatusFailed($e->getCode(), $commit_id);
                    break;
                case CIConst::BUILD_STATUS_PASSED:
                    self::setBuildStatusPassed($e->getCode(), $commit_id);
                    break;
                default:
                    throw new Exception($e->getMessage(), 500);
            }

            Log::connect()->debug($e->getCode().$e->getMessage());
        }
    }

    /**
     * @param     $rid
     * @param int $lastId
     *
     * @throws Exception
     */
    private static function setBuildStatusInactive(string $rid, int $lastId = 0): void
    {
        $sql = 'UPDATE builds SET build_status=? WHERE git_type=? AND rid=? AND id>?';

        DB::update($sql, [CIConst::BUILD_STATUS_INACTIVE, self::$gitType, $rid, $lastId]);
    }

    /**
     * @param int    $build_key_id
     * @param string $commit_id
     *
     * @throws Exception
     */
    private static function setBuildStatusSkip(int $build_key_id, string $commit_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CIConst::BUILD_STATUS_SKIP, $build_key_id]);
    }

    /**
     * @param int    $build_key_id
     * @param string $commit_id
     *
     * @throws Exception
     */
    private static function setBuildStatusPending(int $build_key_id, string $commit_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CIConst::BUILD_STATUS_PENDING, $build_key_id]);
    }

    /**
     * @param int    $build_key_id
     * @param string $commit_id
     *
     * @throws Exception
     */
    private static function setBuildStatusErrored(int $build_key_id, string $commit_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        /*
         * 更新数据库状态
         */
        DB::update($sql, [CIConst::BUILD_STATUS_ERRORED, $build_key_id]);
        /*
         * 通知 GitHub commit Status
         */

        /*
         * 微信通知
         */
    }

    /**
     * @param int    $build_key_id
     * @param string $commit_id
     *
     * @throws Exception
     */
    private static function setBuildStatusFailed(int $build_key_id, string $commit_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CIConst::BUILD_STATUS_FAILED, $build_key_id]);
    }

    /**
     * @param int    $build_key_id
     * @param string $commit_id
     *
     * @throws Exception
     */
    private static function setBuildStatusPassed(int $build_key_id, string $commit_id): void
    {
        $sql = 'UPDATE builds SET build_status =? WHERE id=?';

        DB::update($sql, [CIConst::BUILD_STATUS_PASSED, $build_key_id]);
    }
}
