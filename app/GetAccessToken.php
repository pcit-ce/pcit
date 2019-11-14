<?php

declare(strict_types=1);

namespace App;

use Exception;
use PCIT\Framework\Support\DB;

class GetAccessToken
{
    /**
     * @return string
     *
     * @throws Exception
     */
    public static function byRid(int $rid)
    {
        return self::byRepoFullName(null, $rid);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public static function byRepoFullName(?string $repo_full_name, ?int $rid = null)
    {
        if ($rid) {
            $sql = 'SELECT repo_admin FROM repo WHERE rid=? AND git_type=? LIMIT 1';
        } else {
            $sql = 'SELECT repo_admin FROM repo WHERE repo_full_name=? AND git_type=? LIMIT 1';
        }

        $admin = DB::select($sql, [$repo_full_name ?? $rid, 'github'], true);

        $accessToken = null;

        foreach (json_decode($admin, true) as $k) {
            $sql = 'SELECT access_token FROM user WHERE uid=? AND git_type=? LIMIT 1';
            $result = DB::select($sql, [$k, 'github'], true);

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
     *
     * @throws Exception
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
        $access_token = pcit()->github_apps_installations->getAccessToken(
            (int) $installation_id,
            base_path().'framework/storage/private_key/private.key'
        );

        return $access_token;
    }

    /**
     * @param string $git_type
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getAccessTokenByUid(int $uid, $git_type = 'github')
    {
        $sql = 'SELECT access_token FROM user WHERE git_type=? AND uid=? LIMIT 1';

        return DB::select($sql, [$git_type, $uid], true);
    }
}
