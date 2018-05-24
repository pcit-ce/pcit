<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\CI;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;

class Build extends DBModel
{
    /**
     * @param int $build_key_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateStartAt(int $build_key_id)
    {
        $sql = 'UPDATE builds SET started_at = ? WHERE id=?';

        return DB::update($sql, [time(), $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getStartAt(int $build_key_id)
    {
        $sql = 'SELECT started_at FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int $build_key_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateStopAt(int $build_key_id)
    {
        $sql = 'UPDATE builds SET finished_at = ? WHERE id=?';

        return DB::update($sql, [time(), $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getStopAt(int $build_key_id)
    {
        $sql = 'SELECT finished_at FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitType(int $build_key_id)
    {
        $sql = 'SELECT git_type FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getRid(int $build_key_id)
    {
        $sql = 'SELECT rid FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int    $build_key_id
     * @param string $status
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateBuildStatus(int $build_key_id, string $status)
    {
        $sql = 'UPDATE builds SET build_status=? WHERE id=?';

        return DB::update($sql, [$status, $build_key_id]);
    }

    /**
     * @param int    $rid
     * @param string $branch
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getBuildStatus(int $rid, string $branch)
    {
        $sql = 'SELECT build_status FROM builds WHERE rid=? AND branch=? ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$rid, $branch], true);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getBranches(string $git_type, int $rid)
    {
        $sql = 'SELECT DISTINCT branch FROM builds WHERE git_type=? AND rid=?';

        return $branches = DB::select($sql, [$git_type, $rid]);
    }

    /**
     * 某仓库最新的一次构建 ID PR 除外
     *
     * @param string $git_type
     * @param int    $rid
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getCurrentBuildKeyId(string $git_type, int $rid)
    {
        $sql = <<<EOF
SELECT id FROM builds WHERE git_type=? AND rid=? AND build_status NOT IN (?,?,?) ORDER BY id DESC LIMIT 1
EOF;

        return DB::select($sql, [
            $git_type, $rid,
            CI::BUILD_STATUS_PENDING,
            CI::BUILD_STATUS_SKIP,
            CI::BUILD_STATUS_INACTIVE,
        ], true);
    }

    /**
     * @param int  $build_key_id
     * @param bool $throw
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getCheckRunId(int $build_key_id, bool $throw = false)
    {
        $sql = 'SELECT check_run_id FROM builds WHERE id=?';

        $output = DB::select($sql, [$build_key_id], true);

        if (!$output and $throw) {
            throw new Exception('Check Run Id is null');
        }

        return $output;
    }

    /**
     * @param int $check_run_id
     * @param int $build_key_id
     *
     * @throws Exception
     */
    public static function updateCheckRunId(?int $check_run_id, int $build_key_id): void
    {
        $sql = 'UPDATE builds SET check_run_id=? WHERE id=?';

        DB::update($sql, [$check_run_id, $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getLog(int $build_key_id)
    {
        $sql = 'SELECT build_log FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int    $build_key_id
     * @param string $build_log
     *
     * @throws Exception
     */
    public static function updateLog(int $build_key_id, string $build_log): void
    {
        $sql = 'UPDATE builds SET build_log=? WHERE id=?';

        DB::update($sql, [$build_log, $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getConfig(int $build_key_id)
    {
        $sql = 'SELECT config FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getBuildStatusByBuildKeyId(int $build_key_id)
    {
        $sql = 'SELECT build_status FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * 某分支的构建列表
     *
     * @param string   $git_type
     * @param int      $rid
     * @param string   $branch_name
     * @param int|null $before
     * @param int|null $limit
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function allByBranch(string $git_type,
                                       int $rid,
                                       string $branch_name,
                                       ?int $before,
                                       ?int $limit)
    {
        $before = $before ?? self::getLastKeyId();

        $limit = $limit ?? 25;

        $sql = <<<EOF
SELECT id,branch,commit_id,tag_name,commit_message,
compare,committer_name,committer_username,created_at,started_at,finished_at,build_status,event_type
FROM builds WHERE 
id<$before AND git_type=? AND rid=? AND branch=? AND event_type IN(?,?) AND build_status NOT IN('skip')
 ORDER BY id DESC LIMIT $limit;
EOF;
        return DB::select($sql, [$git_type, $rid, $branch_name, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG]);
    }

    /**
     * 某仓库的构建列表
     *
     * @param string   $git_type
     * @param int      $rid
     * @param int|null $before
     * @param int|null $limit
     * @param bool     $pr
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function allByRid(string $git_type, int $rid, ?int $before, ?int $limit, bool $pr)
    {
        $before = $before ?? self::getLastKeyId();

        $limit = $limit ?? 25;

        $sql = <<<EOF
SELECT id,branch,commit_id,tag_name,commit_message,compare,
committer_name,committer_username,created_at,started_at,finished_at,build_status,event_type,pull_request_id
FROM builds WHERE 
id<$before AND git_type=? AND rid=? AND event_type IN(?,?) AND build_status NOT IN('skip') 
ORDER BY id DESC LIMIT $limit
EOF;
        if ($pr) {

            return DB::select($sql, [$git_type, $rid, CI::BUILD_EVENT_PR, null]);
        }

        return DB::select($sql, [$git_type, $rid, CI::BUILD_EVENT_TAG, CI::BUILD_EVENT_PUSH]);
    }

    /**
     * 某用户的构建列表
     *
     * @param string   $git_type
     * @param int      $uid
     * @param int|null $before
     * @param int|null $limit
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function allByAdmin(string $git_type, int $uid, ?int $before, ?int $limit)
    {
        $before = $before ?? self::getLastKeyId();

        $limit = $limit ?? 25;

        $sql = <<<EOF
SELECT id,branch,commit_id,tag_name,commit_message,compare,
committer_name,committer_username,created_at,started_at,finished_at,build_status,event_type,pull_request_id
FROM builds 
WHERE id<$before AND rid IN (select rid FROM repo WHERE JSON_CONTAINS(repo_admin,?) ) 
AND git_type=? AND event_type IN(?,?) AND build_status NOT IN('skip') ORDER BY id DESC LIMIT $limit;
EOF;

        return DB::select($sql, ["\"$uid\"", $git_type, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG]);
    }
}
