<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;

class Repo extends DBModel
{
    protected static $table = 'repo';

    /**
     * @param string $git_type
     * @param string $username
     * @param string $repo
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getRid(string $git_type, string $username, string $repo)
    {
        $sql = 'SELECT rid FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=?';

        $id = DB::select($sql, [$git_type, $username, $repo], true);

        return $id;
    }

    /**
     * @param string $git_type
     * @param string $username
     * @param string $repo
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getDefaultBranch(string $git_type, string $username, string $repo)
    {
        $sql = 'SELECT default_branch FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=?';

        $default_branch = DB::select($sql, [$git_type, $username, $repo], true);

        return $default_branch;
    }

    /**
     * @param string $git_type
     * @param int    $rid
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getRepoFullName(string $git_type, int $rid)
    {
        $sql = 'SELECT repo_full_name FROM repo WHERE rid=? AND git_type=?';

        return DB::select($sql, [$rid, $git_type], true);
    }

    /**
     * @param string $repo_full_name
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitHubInstallationIdByRepoFullName(string $repo_full_name)
    {
        $sql = 'SELECT installation_id FROM repo WHERE repo_full_name=? AND git_type=?';

        return DB::select($sql, [$repo_full_name, 'github_app'], true);
    }

    /**
     * @param int $rid
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitHubInstallationIdByRid(int $rid)
    {
        $sql = 'SELECT installation_id FROM repo WHERE rid=? AND git_type=?';

        return DB::select($sql, [$rid, 'github_app'], true);
    }

    /**
     * @param int $rid
     * @param int $installation_id
     *
     * @throws Exception
     */
    public static function updateGitHubInstallationIdByRid(int $rid, ?int $installation_id): void
    {
        if (null === $installation_id) {
            return;
        }

        $installation_id_in_db = self::getGitHubInstallationIdByRid($rid);

        if ((int) $installation_id_in_db !== $installation_id) {
            $sql = 'UPDATE repo SET installation_id=? WHERE rid=?';

            DB::update($sql, [$installation_id, $rid]);
        }
    }

    /**
     * @param string $git_type
     * @param int    $rid
     *
     * @return array|string
     * @throws Exception
     */
    public static function getAdmin(string $git_type, int $rid)
    {
        $sql = 'SELECT repo_admin FROM repo WHERE git_type=? AND rid=?';

        return DB::select($sql, [$git_type, $rid], true);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param int    $uid
     *
     * @return array|string
     * @throws Exception
     */
    public static function checkAdmin(string $git_type, int $rid, int $uid)
    {
        $sql = 'SELECT id FROM repo WHERE git_type=? AND rid=? AND JSON_CONTAINS(repo_admin,?)';

        return DB::select($sql, [$git_type, $rid, "\"$uid\""]);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param int    $uid
     *
     * @throws Exception
     */
    public static function updateAdmin(string $git_type, int $rid, int $uid)
    {
        $sql = <<<EOF
UPDATE repo SET repo_admin=JSON_MERGE(repo_admin,?) WHERE git_type=? AND rid=? AND NOT JSON_CONTAINS(repo_admin,?)
EOF;

        DB::update($sql, ["[\"$uid\"]", $git_type, $rid, "\"$uid\""]);
    }

    /**
     * @param string $git_type
     * @param int    $uid
     *
     * @return array|string
     * @throws Exception
     */
    public static function allByAdmin(string $git_type, int $uid)
    {
        $sql = 'SELECT rid,repo_full_name FROM repo WHERE git_type=? AND JSON_CONTAINS(repo_admin,?)';

        return DB::select($sql, [$git_type, "\"$uid\""]);
    }

    /**
     * @param int $uid
     *
     * @return array|string
     * @throws Exception
     */
    public static function getActiveByAdmin(int $uid)
    {
        $sql = 'SELECT rid FROM repo WHERE JSON_CONTAINS(repo_admin,"?") AND build_activate=1 AND webhooks_status=1';

        return DB::select($sql, [$uid]);
    }
}
