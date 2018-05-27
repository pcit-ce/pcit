<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\DB;
use KhsCI\Support\Env;

class GetAccessToken
{
    /**
     * @param int $rid
     *
     * @return array|null|string
     *
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
     *
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

    /**
     * @param $rid
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function getGitHubAppAccessToken($rid)
    {
        $installation_id = Repo::getGitHubInstallationIdByRid((int) $rid);

        if (!$installation_id) {
            throw new Exception('installation_id is error', 500);
        }

        $khsci = new KhsCI();

        $access_token = $khsci->github_apps_installations->getAccessToken(
            (int) $installation_id,
            __DIR__.'/../private_key/'.Env::get('CI_GITHUB_APP_PRIVATE_FILE')
        );

        return $access_token;
    }

    /**
     * @param string $git_type
     * @param int    $uid
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getAccessTokenByUid(string $git_type, int $uid)
    {
        $sql = 'SELECT access_token FROM user WHERE git_type=? AND uid=?';

        return DB::select($sql, [$git_type, $uid], true);
    }
}
