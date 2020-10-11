<?php

declare(strict_types=1);

namespace App;

use Exception;
use PCIT\Framework\Support\DB;

class GetAccessToken
{
    /**
     * @return string
     */
    public static function byRid(int $rid, string $git_type = 'github')
    {
        return self::byRepoFullName(null, $rid, $git_type);
    }

    /**
     * @return string
     */
    public static function byRepoFullName(?string $repo_full_name, ?int $rid = null, string $git_type = 'github')
    {
        if ('github' === $git_type) {
            return self::getGitHubAppAccessToken($rid, $repo_full_name);
        }

        if ($rid) {
            $sql = 'SELECT repo_admin FROM repo WHERE rid=? AND git_type=? LIMIT 1';
        } else {
            $sql = 'SELECT repo_admin FROM repo WHERE repo_full_name=? AND git_type=? LIMIT 1';
        }

        $admin = DB::select($sql, [$repo_full_name ?? $rid, $git_type], true);

        $accessToken = null;

        foreach (json_decode($admin, true) as $k) {
            $sql = 'SELECT access_token FROM user WHERE uid=? AND git_type=? LIMIT 1';
            $result = DB::select($sql, [$k, $git_type], true);

            if ($result) {
                $accessToken = $result;

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
     */
    public static function getGitHubAppAccessToken($rid = null, string $repo_full_name = null, int $installation_id = null)
    {
        if (!$installation_id) {
            $installation_id = $rid ? Repo::getGitHubInstallationIdByRid((int) $rid)
            : Repo::getGitHubInstallationIdByRepoFullName($repo_full_name);

            if (!$installation_id) {
                throw new Exception('installation_id is error', 500);
            }
        }

        return \PCIT::github_apps_access_token()->getAccessToken(
            (int) $installation_id,
            config('git.github.app.private_key_path')
        );
    }

    /**
     * @param string $git_type
     *
     * @return string
     */
    public static function getAccessTokenByUid(int $uid, $git_type = 'github')
    {
        $sql = 'SELECT access_token FROM user WHERE git_type=? AND uid=? LIMIT 1';

        return DB::select($sql, [$git_type, $uid], true);
    }

    /**
     * @return string
     */
    public static function getRefreshTokenByUid(int $uid, string $git_type = 'github')
    {
        $sql = 'SELECT refresh_token FROM user WHERE git_type=? AND uid=? LIMIT 1';

        return DB::select($sql, [$git_type, $uid], true);
    }
}
