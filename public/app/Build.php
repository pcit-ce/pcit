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
     * @param int  $build_key_id
     * @param int  $time
     * @param bool $started_at
     * @param bool $finished_at
     * @param bool $created_at
     * @param bool $deleted_at
     *
     * @return int
     *
     * @throws Exception
     */
    private static function updateTime(int $build_key_id,
                                       int $time = null,
                                       bool $started_at = true,
                                       bool $finished_at = false,
                                       bool $created_at = false,
                                       bool $deleted_at = false)
    {
        $column = null;

        $started_at && $column = 'started_at';

        $finished_at && $column = '';

        $created_at && $column = '';

        $deleted_at && $column = '';

        if (!$column) {
            throw new Exception('500', 500);
        }

        $sql = "UPDATE builds SET $column = ? WHERE id=?";

        $time = $time ?? time();

        if (0 === $time) {
            $time = null;
        }

        return DB::update($sql, [$time, $build_key_id]);
    }

    /**
     * @param int $build_key_id
     * @param int $time
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateStartAt(int $build_key_id, int $time = null)
    {
        return self::updateTime($build_key_id, $time, true);
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
        $sql = 'SELECT started_at FROM builds WHERE id=? LIMIT 1';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int      $build_key_id
     * @param int|null $time
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateStopAt(int $build_key_id, int $time = null)
    {
        return self::updateTime($build_key_id, $time, false, true);
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
        $sql = 'SELECT finished_at FROM builds WHERE id=? LIMIT 1';

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
        $sql = 'SELECT git_type FROM builds WHERE id=? LIMIT 1';

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
        $sql = 'SELECT rid FROM builds WHERE id=? LIMIT 1';

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
    public static function updateBuildStatus(int $build_key_id, ?string $status)
    {
        $sql = 'UPDATE builds SET build_status=? WHERE id=?';

        return DB::update($sql, [$status, $build_key_id]);
    }

    /**
     * @param string $build_status
     * @param string $git_type
     * @param int    $rid
     * @param string $branch
     * @param string $commit_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateBuildStatusByCommitId(string $build_status,
                                                       string $git_type,
                                                       int $rid,
                                                       string $branch,
                                                       string $commit_id)
    {
        $sql = 'UPDATE builds SET build_status=? WHERE git_type=? AND rid=? AND commit_id=?';

        return DB::update($sql, [$build_status, $git_type, $rid, $commit_id]);
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
        $sql = 'SELECT build_status FROM builds WHERE rid=? AND branch=? AND build_status NOT IN ("skip") ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$rid, $branch], true);
    }

    /**
     * @param int    $rid
     * @param string $branch
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function buildStatusIsChanged(int $rid, string $branch)
    {
        $sql = 'SELECT build_status FROM builds WHERE rid=? AND branch=? AND build_status NOT IN ("skip") ORDER BY id DESC LIMIT 2';

        $output = DB::select($sql, [$rid, $branch]);

        if ($output[0] === $output[1] ?? null) {
            return false;
        }

        return true;
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
     * 某仓库最新的一次构建 ID PR 除外.
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
SELECT id FROM builds WHERE git_type=? AND rid=? AND build_status NOT IN (?,?,?) AND event_type NOT IN (?)
ORDER BY id DESC LIMIT 1
EOF;

        return DB::select($sql, [
            $git_type, $rid,
            'pending',
            'skip',
            'inactive',
            CI::BUILD_EVENT_PR,
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
        $sql = 'SELECT check_run_id FROM builds WHERE id=? LIMIT 1';

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
        $sql = 'UPDATE builds SET check_run_id=? WHERE id=? LIMIT 1';

        DB::update($sql, [$check_run_id, $build_key_id]);
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
        $sql = 'SELECT config FROM builds WHERE id=? LIMIT 1';

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
        $sql = 'SELECT build_status FROM builds WHERE id=? LIMIT 1';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * 某分支的构建列表.
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
SELECT id,branch,commit_id,tag,commit_message,
compare,committer_name,committer_username,created_at,started_at,finished_at,build_status,event_type
FROM builds WHERE
id<=$before AND git_type=? AND rid=? AND branch=? AND event_type IN(?,?) AND build_status NOT IN('skip')
 ORDER BY id DESC LIMIT $limit;
EOF;

        return DB::select($sql, [$git_type, $rid, $branch_name, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG]);
    }

    /**
     * 某仓库的构建列表.
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
SELECT id,branch,commit_id,tag,commit_message,compare,
committer_name,committer_username,created_at,started_at,finished_at,build_status,event_type,pull_request_number
FROM builds WHERE
id<=$before AND git_type=? AND rid=? AND event_type IN(?,?) AND build_status NOT IN('skip')
ORDER BY id DESC LIMIT $limit
EOF;
        if ($pr) {
            return DB::select($sql, [$git_type, $rid, CI::BUILD_EVENT_PR, null]);
        }

        return DB::select($sql, [$git_type, $rid, CI::BUILD_EVENT_TAG, CI::BUILD_EVENT_PUSH]);
    }

    /**
     * 某用户的构建列表.
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
SELECT id,branch,commit_id,tag,commit_message,compare,
committer_name,committer_username,created_at,started_at,finished_at,build_status,event_type,pull_request_number
FROM builds
WHERE id<=$before AND rid IN (select rid FROM repo WHERE JSON_CONTAINS(repo_admin,?) )
AND git_type=? AND event_type IN(?,?) AND build_status NOT IN('skip') ORDER BY id DESC LIMIT $limit;
EOF;

        return DB::select($sql, ["\"$uid\"", $git_type, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getCommitterEmail(int $build_key_id)
    {
        $sql = 'SELECT committer_email FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getCommitterName(int $build_key_id)
    {
        $sql = 'SELECT committer_name FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id]);
    }
}
