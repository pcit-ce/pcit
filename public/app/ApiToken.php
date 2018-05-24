<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;

class ApiToken extends DBModel
{
    protected static $table = 'api_token';

    /**
     * @param string $git_type
     * @param int    $uid
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function get(string $git_type, int $uid)
    {
        $sql = 'SELECT api_token FROM api_token WHERE git_type=? AND uid=?';

        return DB::select($sql, [$git_type, $uid], true);
    }

    /**
     * @param string $git_type
     * @param int    $uid
     * @param string $api_token
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function check(string $git_type, int $uid, string $api_token)
    {
        $sql = 'SELECT uid,created_at FROM api_token WHERE git_type=? AND api_token=?';

        DB::select($sql, [$git_type, $api_token]);

        $uid_from_db = '';

        $created_at = '';

        // 检查过期时间

        if ((int) $uid_from_db === $uid) {
            return true;
        }

        return false;
    }

    /**
     * @param string $api_token
     * @param string $git_type
     * @param int    $uid
     *
     * @throws Exception
     */
    public static function add(string $api_token, string $git_type, int $uid): void
    {
        $sql = 'INSERT INTO api_token VALUES(null,?,?,?,?)';

        DB::insert($sql, [$api_token, $git_type, $uid, time()]);
    }

    /**
     * @param string $api_token
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitTypeAndUid(string $api_token)
    {
        $sql = 'SELECT git_type,uid FROM api_token WHERE api_token=?';

        return DB::select($sql, [$api_token]);
    }
}
