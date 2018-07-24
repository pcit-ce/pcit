<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;

class Job extends DBModel
{
    /**
     * @param int $jobs_id
     *
     * @return array|string
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
     * @param int $job_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function create(int $job_id)
    {
        $sql = <<<EOF
INSERT INTO jobs(id,allow_failure,state,created_at,build,private) 

values(null,?,?,?,?,?)
EOF;

        return DB::insert($sql, [0, 'pending', time(), $job_id, 0]);
    }

    /**
     * @param int $job_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getByBuildKeyID(int $job_id)
    {
        $sql = 'SELECT id FROM jobs WHERE build=?';

        return DB::select($sql, [$job_id]);
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

        $finished_at && $column = '';

        $created_at && $column = '';

        $deleted_at && $column = '';

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
     * @return array|string
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
    public static function updateStopAt(int $job_id, int $time = null)
    {
        return self::updateTime($job_id, $time, false, true);
    }

    /**
     * @param int $job_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getStopAt(int $job_id)
    {
        $sql = 'SELECT finished_at FROM jobs WHERE id=? LIMIT 1';

        return DB::select($sql, [$job_id], true);
    }
}
