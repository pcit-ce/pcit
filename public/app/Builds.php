<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;

class Builds
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
        $sql = 'UPDATE builds SET create_time = ? WHERE id=?';

        return DB::update($sql, [time(), $build_key_id]);
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
        $sql = 'UPDATE builds SET end_time = ? WHERE id=?';

        return DB::update($sql, [time(), $build_key_id]);
    }

    /**
     * @param int $build_key_id
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitTypeByBuildKeyId(int $build_key_id)
    {
        $sql = 'SELECT git_type FROM builds WHERE id=?';

        return DB::select($sql, [$build_key_id]);
    }
}
