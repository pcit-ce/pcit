<?php

namespace App;

use Exception;
use KhsCI\Support\DB;

class GetAccessToken
{
    /**
     * @param int $rid
     *
     * @return null
     * @throws Exception
     */
    public static function byRid(int $rid)
    {
        return self::byRepoFullName(null, $rid);
    }

    /**
     * @param null|string $repo_full_name
     * @param int|null    $rid
     *
     * @return array|null|string
     * @throws Exception
     */
    public static function byRepoFullName(?string $repo_full_name, ?int $rid = null)
    {
        if ($rid) {
            $sql = 'SELECT repo_admin FROM repo WHERE rid=? AND git_type=?';
        } else {
            $sql = 'SELECT repo_admin FROM repo WHERE repo_full_name=? AND git_type=?';
        }

        $admin = DB::select($sql, [$repo_full_name ?? $rid, 'github'], true);

        $accessToken = null;

        foreach (json_decode($admin, true) as $k) {
            $sql = 'SELECT access_token FROM user WHERE uid=? AND git_type=?';
            $output = DB::select($sql, [$k, 'github'], true);

            if ($output) {
                $accessToken = $output;
                break;
            }
        }

        if ($accessToken) {
            return $accessToken;
        }

        throw new Exception('access_token not found', 500);
    }
}
