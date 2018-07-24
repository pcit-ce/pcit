<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;

class Job extends DBModel
{
    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getLog(int $build_key_id)
    {
        $sql = 'SELECT build_log FROM jobs WHERE id=? LIMIT 1';

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
        $sql = 'UPDATE jobs SET build_log=? WHERE id=?';

        DB::update($sql, [$build_log, $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return string
     *
     * @throws Exception
     */
    public static function create(int $build_key_id)
    {
        $sql = <<<EOF
INSERT INTO jobs(id,allow_failure,state,created_at,build,private) 

values(null,?,?,?,?,?)
EOF;

        return DB::insert($sql, [0, 'pending', time(), $build_key_id, 0]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getByBuildKeyID(int $build_key_id)
    {
        $sql = 'SELECT id FROM jobs WHERE build=?';

        return DB::select($sql, [$build_key_id]);
    }
}
