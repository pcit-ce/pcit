<?php

declare(strict_types=1);

namespace App;

use Exception;
use PCIT\Support\CI;
use PCIT\Support\DB;
use PCIT\Support\Model;

class Build extends Model
{
    /**
     * @param int $build_key_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getStartAt(int $build_key_id)
    {
        $sql = 'SELECT created_at FROM jobs WHERE build_id=? ORDER BY id LIMIT 1';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int      $build_key_id
     * @param int|null $time
     *
     * @throws Exception
     */
    public static function updateStartAt(int $build_key_id, ?int $time): void
    {
        $time = null === $time ? time() : $time;
        $sql = 'UPDATE builds SET created_at=? WHERE id=?';

        DB::update($sql, [$time, $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getStopAt(int $build_key_id)
    {
        $sql = 'SELECT finished_at FROM jobs WHERE build_id=? ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param int $buildId
     *
     * @throws Exception
     */
    public static function updateFinishedAt(int $buildId): void
    {
        $finished_at = Job::getFinishedAtByBuildId($buildId);

        $sql = 'UPDATE builds set finished_at=? WHERE id=?';

        DB::update($sql, [$finished_at, $buildId]);
    }

    /**
     * @param int $build_key_id
     *
     * @return string
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
     * @return int
     *
     * @throws Exception
     */
    public static function getRid(int $build_key_id)
    {
        $sql = 'SELECT rid FROM builds WHERE id=? LIMIT 1';

        $rid = DB::select($sql, [$build_key_id], true);

        if ($rid) {
            return (int) $rid;
        }

        throw new Exception('', 404);
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

        if (null === $status) {
            $status = self::getBuildStatus($build_key_id);
        }

        return DB::update($sql, [$status, $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getBuildStatus(int $build_key_id)
    {
        $sql = 'SELECT state FROM jobs WHERE build_id=? group by state';

        $result = DB::select($sql, [$build_key_id]);

        if (1 === \count($result)) {
            return $result[0]['state'];
        }

        foreach ($result as $k) {
            $state[] = $k['state'];
        }

        $conclusion = [
            'cancelled',
            'errored',
            'failure',
            'in_progress',
            // 'success',
            'pending',
        ];

        foreach ($conclusion as $k) {
            if (\in_array('$k', $result)) {
                return $k;
            }
        }

        return 'errored';
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
                                                       int $rid,
                                                       string $branch,
                                                       string $commit_id,
                                                       $git_type = 'github')
    {
        $sql = 'UPDATE builds SET build_status=? WHERE git_type=? AND rid=? AND commit_id=?';

        return DB::update($sql, [$build_status, $git_type, $rid, $commit_id]);
    }

    /**
     * @param int    $rid
     * @param string $branch
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getLastBuildStatus(int $rid, string $branch)
    {
        $sql = 'SELECT build_status FROM builds WHERE rid=? AND branch=? AND build_status NOT IN ("skip") ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$rid, $branch], true);
    }

    /**
     * @param int    $rid
     * @param string $branch
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function buildStatusIsChanged(int $rid, string $branch)
    {
        $sql = 'SELECT build_status FROM builds WHERE rid=? AND branch=? AND build_status NOT IN ("skip") ORDER BY id DESC LIMIT 2';

        $result = DB::select($sql, [$rid, $branch]);

        if (($result[1] ?? null) === $result[0]) {
            return false;
        }

        return true;
    }

    /**
     * @param string $git_type
     * @param int    $rid
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getBranches(int $rid, $git_type = 'github')
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
     * @return string
     *
     * @throws Exception
     */
    public static function getCurrentBuildKeyId(int $rid, $git_type = 'github')
    {
        $sql = <<<'EOF'
SELECT id FROM builds WHERE git_type=? AND rid=? AND build_status NOT IN (?,?) AND event_type NOT IN (?)
ORDER BY id DESC LIMIT 1
EOF;

        return DB::select($sql, [
            $git_type, $rid,
            // 'pending',
            'skip',
            'inactive',
            CI::BUILD_EVENT_PR,
        ], true);
    }

    /**
     * @param int $build_key_id
     *
     * @return string
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
     * @return string
     *
     * @throws Exception
     */
    public static function getBuildStatusByBuildKeyId(int $build_key_id)
    {
        $sql = 'SELECT build_status FROM builds WHERE id=? LIMIT 1';

        return DB::select($sql, [$build_key_id], true);
    }

    /**
     * @param $build_key_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getBranch($build_key_id)
    {
        $sql = 'SELECT branch FROM builds WHERE id=? LIMIT 1';

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
     * @return array
     *
     * @throws Exception
     */
    public static function allByBranch(int $rid,
                                       string $branch_name,
                                       ?int $before,
                                       ?int $limit,
                                       $git_type = 'github')
    {
        $before = 0 === $before ? null : $before;

        $before = $before ?? self::getLastKeyId();

        $limit = 0 === $limit ? null : $limit;

        $limit = $limit ?? 25;

        $limit = $limit <= 25 ? $limit : 25;

        $sql = <<<EOF
SELECT id,branch,commit_id,tag,commit_message,
compare,committer_name,committer_username,build_status,event_type
FROM builds WHERE
id<=$before AND git_type=? AND rid=? AND branch=? AND event_type IN(?,?) AND build_status NOT IN('skip')
 ORDER BY id DESC LIMIT $limit;
EOF;

        return DB::select($sql, [$git_type, $rid, $branch_name, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG]);
    }

    /**
     * 某仓库的构建列表.
     *
     * @param int      $rid
     * @param int|null $before
     * @param int|null $limit
     * @param bool     $pr
     * @param string   $git_type
     * @param bool     $all
     *
     * @return array
     *
     * @throws Exception
     */
    public static function allByRid(int $rid,
                                    ?int $before,
                                    ?int $limit,
                                    bool $pr,
                                    $all = false,
                                    $git_type = 'github')
    {
        $before = 0 === $before ? null : $before;

        $before = $before ?? self::getLastKeyId();

        $limit = 0 === $limit ? null : $limit;

        $limit = $limit ?? 25;

        $limit = $limit <= 25 ? $limit : 25;

        $exclude = $all ? 'null' : 'skip';

        $sql = <<<EOF
SELECT id,branch,commit_id,tag,commit_message,compare,
committer_name,committer_username,build_status,event_type,pull_request_number,created_at,finished_at
FROM builds WHERE
id<=$before AND git_type=? AND rid=? AND event_type IN(?,?,?) AND build_status NOT IN('$exclude')
ORDER BY id DESC LIMIT $limit
EOF;
        if ($all) {
            return DB::select($sql, [
                $git_type, $rid, CI::BUILD_EVENT_PR, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG,
            ]);
        }

        if ($pr) {
            return DB::select($sql, [$git_type, $rid, CI::BUILD_EVENT_PR, null, null]);
        }

        return DB::select($sql, [$git_type, $rid, CI::BUILD_EVENT_TAG, CI::BUILD_EVENT_PUSH, null]);
    }

    /**
     * 某用户的构建列表.
     *
     * @param string   $git_type
     * @param int      $uid
     * @param int|null $before
     * @param int|null $limit
     *
     * @return array
     *
     * @throws Exception
     */
    public static function allByAdmin(int $uid, ?int $before, ?int $limit, $git_type = 'github')
    {
        $before = 0 === $before ? null : $before;

        $before = $before ?? self::getLastKeyId();

        $limit = 0 === $limit ? null : $limit;

        $limit = $limit ?? 25;

        $limit = $limit <= 25 ? $limit : 25;

        $sql = <<<EOF
SELECT id,branch,commit_id,tag,commit_message,compare,
committer_name,committer_username,build_status,event_type,pull_request_number
FROM builds
WHERE id<=$before AND rid IN (select rid FROM repo WHERE JSON_CONTAINS(repo_admin,?) )
AND git_type=? AND event_type IN(?,?) AND build_status NOT IN('skip') ORDER BY id DESC LIMIT $limit;
EOF;

        return DB::select($sql, ["\"$uid\"", $git_type, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array
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
     * @return array
     *
     * @throws Exception
     */
    public static function getCommitterName(int $build_key_id)
    {
        $sql = 'SELECT committer_name FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id]);
    }

    /**
     * @param $git_type
     * @param $branch
     * @param $tag
     * @param $commit_id
     * @param $commit_message
     * @param $committer_name
     * @param $committer_email
     * @param $committer_username
     * @param $author_name
     * @param $author_email
     * @param $author_username
     * @param $rid
     * @param $event_time
     * @param $config
     *
     * @return int
     *
     * @throws Exception
     */
    public static function insertTag($branch,
                                     $tag,
                                     $commit_id,
                                     $commit_message,
                                     $committer_name,
                                     $committer_email,
                                     $committer_username,
                                     $author_name,
                                     $author_email,
                                     $author_username,
                                     $rid,
                                     $event_time,
                                     $config,
                                     $git_type = 'github')
    {
        $sql = <<<'EOF'
INSERT INTO builds(

git_type,event_type,branch,tag,
commit_id,commit_message,
committer_name,committer_email,committer_username,
author_name,author_email,author_username,
rid,created_at,config

) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
EOF;

        $last_insert_id = DB::insert($sql, [
            $git_type, 'tag', $branch, $tag,
            $commit_id, $commit_message,
            $committer_name, $committer_email, $committer_username,
            $author_name, $author_email, $author_username,
            $rid, $event_time, $config,
        ]);

        return $last_insert_id;
    }

    /**
     * @param $git_type
     * @param $event_type
     * @param $branch
     * @param $compare
     * @param $commit_id
     * @param $commit_message
     * @param $committer_name
     * @param $committer_email
     * @param $committer_username
     * @param $author_name
     * @param $author_email
     * @param $author_username
     * @param $rid
     * @param $event_time
     * @param $config
     * @param $unique
     *
     * @return int
     *
     * @throws Exception
     */
    public static function insert($event_type,
                                  $branch,
                                  $compare,
                                  $commit_id,
                                  $commit_message,
                                  $committer_name,
                                  $committer_email,
                                  $committer_username,
                                  $author_name,
                                  $author_email,
                                  $author_username,
                                  $rid,
                                  $event_time,
                                  $config,
                                  $git_type = 'github',
                                  bool $unique = false)
    {
        $sql = <<<'EOF'
INSERT INTO builds(

git_type,event_type,branch,compare,
commit_id,commit_message,
committer_name,committer_email,committer_username,
author_name,author_email,author_username,
rid,created_at,config,unique_key

) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);
EOF;

        $unique_key = $unique ? time() : 0;

        $last_insert_id = DB::insert($sql, [
            $git_type, $event_type, $branch, $compare,
            $commit_id, $commit_message,
            $committer_name, $committer_email, $committer_username,
            $author_name, $author_email, $author_username,
            $rid, $event_time, $config, $unique_key,
        ]);

        return $last_insert_id;
    }

    /**
     * @param $git_type
     * @param $rid
     * @param $created_at
     *
     * @return int
     *
     * @throws Exception
     */
    public static function insertPing($rid, $created_at, $git_type = 'github')
    {
        $sql = <<<'EOF'
INSERT INTO builds(

git_type,event_type,rid,created_at

) VALUES(?,?,?,?);
EOF;
        $data = [
            $git_type, 'ping', $rid, $created_at,
        ];

        return DB::insert($sql, $data);
    }

    /**
     * @param        $event_time
     * @param        $action
     * @param        $commit_id
     * @param        $commit_message
     * @param int    $committer_uid
     * @param        $committer_username
     * @param        $pull_request_number
     * @param        $branch
     * @param        $rid
     * @param        $config
     * @param        $internal
     * @param        $pull_request_source
     * @param string $git_type
     *
     * @return int
     *
     * @throws Exception
     */
    public static function insertPullRequest($event_time,
                                             string $action,
                                             string $commit_id,
                                             string $commit_message,
                                             int $committer_uid,
                                             string $committer_username,
                                             $pull_request_number,
                                             string $branch,
                                             $rid,
                                             string $config,
                                             $internal,
                                             $pull_request_source,
                                             $git_type = 'github')
    {
        $sql = <<<'EOF'
INSERT INTO builds(

git_type,event_type,created_at,action,
commit_id,commit_message,pull_request_number,
committer_uid,committer_username,
branch,rid,config,internal,pull_request_source

) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?);

EOF;

        $last_insert_id = DB::insert($sql, [
                $git_type, 'pull_request', $event_time, $action,
                $commit_id, $commit_message, $pull_request_number,
                $committer_uid, $committer_username,
                $branch, $rid, $config, $internal, $pull_request_source,
            ]
        );

        return $last_insert_id;
    }

    /**
     * @param int    $rid
     * @param string $commit_id
     * @param int    $check_suite_id
     * @param string $git_type
     *
     * @throws Exception
     */
    public static function updateCheckSuiteId(int $rid,
                                              string $commit_id,
                                              int $check_suite_id,
                                              string $git_type = 'github'): void
    {
        $sql = 'UPDATE builds SET check_suites_id=? WHERE rid=? AND commit_id=? AND git_type=? ';

        DB::update($sql, [
            $check_suite_id, $rid, $commit_id, $git_type,
        ]);
    }
}
