<?php

declare(strict_types=1);

namespace App;

use Exception;
use PCIT\Support\DB;
use PCIT\Support\Model;

class Job extends Model
{
    /**
     * @param int $jobs_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getLog(int $jobs_id)
    {
        $sql = 'SELECT build_log FROM jobs WHERE id=? LIMIT 1';

        return DB::select($sql, [$jobs_id], true);
    }

    /**
     * @param int    $job_id
     * @param string $build_log
     *
     * @throws Exception
     */
    public static function updateLog(int $job_id, string $build_log): void
    {
        $sql = 'UPDATE jobs SET build_log=? WHERE id=?';

        DB::update($sql, [$build_log, $job_id]);
    }

    /**
     * @param int $build_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function create(int $build_id)
    {
        $sql = <<<'EOF'
INSERT INTO jobs(id,allow_failure,state,created_at,build_id)

values(null,?,?,?,?)
EOF;

        return DB::insert($sql, [0, 'pending', time(), $build_id]);
    }

    /**
     * @param int  $build_key_id
     * @param bool $pending
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getByBuildKeyID(int $build_key_id, bool $pending = false)
    {
        if ($pending) {
            $sql = 'SELECT id FROM jobs WHERE build_id=? AND state=?';

            return DB::select($sql, [$build_key_id, 'pending']);
        }

        $sql = 'SELECT id FROM jobs WHERE build_id=?';

        return DB::select($sql, [$build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function allByBuildKeyID(int $build_key_id)
    {
        $sql = 'SELECT * FROM jobs WHERE build_id=?';

        return DB::select($sql, [$build_key_id]);
    }

    /**
     * @param int $job_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function getRid(int $job_id)
    {
        $sql = 'SELECT builds.rid FROM jobs RIGHT JOIN builds ON jobs.build_id=builds.id WHERE jobs.id=? LIMIT 1';

        $rid = DB::select($sql, [$job_id], true);

        if ($rid) {
            return (int) $rid;
        }

        throw new Exception('', 404);
    }

    /**
     * @param int  $job_id
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
    private static function updateTime(int $job_id,
                                       int $time = null,
                                       bool $started_at = true,
                                       bool $finished_at = false,
                                       bool $created_at = false,
                                       bool $deleted_at = false)
    {
        $column = null;

        $started_at && $column = 'started_at';

        $finished_at && $column = 'finished_at';

        $created_at && $column = 'created_at';

        $deleted_at && $column = 'deleted_at';

        if (!$column) {
            throw new Exception('500', 500);
        }

        $sql = "UPDATE jobs SET $column = ? WHERE id=?";

        $time = $time ?? time();

        if (0 === $time) {
            $time = null;
        }

        return DB::update($sql, [$time, $job_id]);
    }

    /**
     * @param int $job_id
     * @param int $time
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateStartAt(int $job_id, int $time = null)
    {
        return self::updateTime($job_id, $time, true);
    }

    /**
     * @param int $job_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getStartAt(int $job_id)
    {
        $sql = 'SELECT started_at FROM jobs WHERE id=? LIMIT 1';

        return DB::select($sql, [$job_id], true);
    }

    /**
     * @param int      $job_id
     * @param int|null $time
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateFinishedAt(int $job_id, int $time = null)
    {
        return self::updateTime($job_id, $time, false, true);
    }

    /**
     * @param int $job_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function getFinishedAt(int $job_id)
    {
        $sql = 'SELECT finished_at FROM jobs WHERE id=? LIMIT 1';

        return DB::select($sql, [$job_id], true);
    }

    /**
     * @param int    $job_key_id
     * @param string $status
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateBuildStatus(int $job_key_id, ?string $status)
    {
        $sql = 'UPDATE jobs SET state=? WHERE id=?';

        return DB::update($sql, [$status, $job_key_id]);
    }

    /**
     * @param int $job_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getBuildStatus(int $job_key_id)
    {
        $sql = 'SELECT state FROM jobs WHERE id=?';

        return DB::select($sql, [$job_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function getCheckRunId(int $build_key_id)
    {
        $sql = 'SELECT check_run_id FROM jobs WHERE id=? LIMIT 1';

        $output = DB::select($sql, [$build_key_id], true);

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
        $sql = 'UPDATE jobs SET check_run_id=? WHERE id=?';

        DB::update($sql, [$check_run_id, $build_key_id]);
    }

    /**
     * @param int $job_key_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getGitType(int $job_key_id)
    {
        $sql = 'SELECT builds.git_type FROM jobs JOIN builds ON jobs.build_id = builds.id WHERE jobs.id = ? LIMIT 1';

        return DB::select($sql, [$job_key_id], true);
    }

    /**
     * @param int $job_key_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function getBuildKeyId(int $job_key_id)
    {
        $sql = 'SELECT build_id FROM jobs WHERE id =?';

        return (int) DB::select($sql, [$job_key_id], true);
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
        $status = DB::select(
            'SELECT state FROM jobs WHERE build_id=? GROUP BY state', [$build_key_id]);

        if (1 === \count($status)) {
            return $status[0]['state'];
        }

        // 有一个 error failure 均返回对应值
        foreach ($status as $state) {
            if ('cancelled' === $state['state']) {
                return 'cancelled';
            }

            if ('error' === $state['state']) {
                return 'error';
            }

            if ('failure' === $state['state']) {
                return 'failure';
            }

            if ('skip' === $state['state']) {
                return 'skip';
            }
        }

        return 'pending';
    }
}
