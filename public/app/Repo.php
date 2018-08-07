<?php

declare(strict_types=1);

namespace App;

use Exception;
use KhsCI\Support\DB;
use KhsCI\Support\DBModel;
use KhsCI\Support\Log;

class Repo extends DBModel
{
    protected static $table = 'repo';

    /**
     * @param string $git_type
     * @param string $username
     * @param string $repo
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getRid(string $git_type, string $username, string $repo)
    {
        $sql = 'SELECT rid FROM repo WHERE git_type=? AND repo_full_name=CONCAT_WS("/",?,?) ORDER BY id DESC LIMIT 1';

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
        $sql = 'SELECT default_branch FROM repo WHERE git_type=? AND repo_full_name=CONCAT_WS("/",?,?) ORDER BY id DESC LIMIT 1';

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
        $sql = 'SELECT repo_full_name FROM repo WHERE rid=? AND git_type=? ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$rid, $git_type], true);
    }

    /**
     * @param string $repo_full_name
     * @param string $git_type
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitHubInstallationIdByRepoFullName(string $repo_full_name, $git_type = 'github')
    {
        $username = (explode('/', $repo_full_name))[0];

        $sql = 'SELECT installation_id FROM user WHERE username=? AND git_type=? ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$username, $git_type], true);
    }

    /**
     * @param int    $rid
     * @param string $git_type
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitHubInstallationIdByRid(int $rid, $git_type = 'github')
    {
        $repo_full_name = self::getRepoFullName($git_type, $rid);

        return self::getGitHubInstallationIdByRepoFullName($repo_full_name, $git_type);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param string $repo_full_name
     * @param int    $installation_id
     *
     * @throws Exception
     */
    public static function updateGitHubInstallationIdByRid(string $git_type,
                                                           int $rid,
                                                           string $repo_full_name,
                                                           ?int $installation_id): void
    {
        if (null === $installation_id) {
            return;
        }

        $username = explode('/', $repo_full_name)[0];

        $installation_id_in_db = self::getGitHubInstallationIdByRid($rid);

        // user table not found installation_id
        if (!$installation_id_in_db) {
            Log::debug(null, null, 'repo not found, insert...', [], Log::INFO);

            self::updateRepoInfo($git_type, $rid, $repo_full_name, null, null);
        }

        if ((int) $installation_id_in_db !== $installation_id) {
            $sql = 'UPDATE repo SET installation_id=?,repo_full_name=? WHERE rid=?';

            DB::update($sql, [$installation_id, $repo_full_name, $rid]);
        }

        User::updateInstallationId($git_type, $installation_id, $username);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param bool   $collaborators
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getAdmin(string $git_type, int $rid, bool $collaborators = false)
    {
        $sql = 'SELECT repo_admin FROM repo WHERE git_type=? AND rid=? ORDER BY id DESC LIMIT 1';

        if ($collaborators) {
            $sql = 'SELECT repo_collaborators FROM repo WHERE git_type=? AND rid=? ORDER BY id DESC LIMIT 1';
        }

        return DB::select($sql, [$git_type, $rid], true);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param int    $uid
     * @param bool   $collaborators
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function checkAdmin(string $git_type, int $rid, int $uid, bool $collaborators = false)
    {
        $sql = 'SELECT id FROM repo WHERE git_type=? AND rid=? AND JSON_CONTAINS(repo_admin,json_quote(?))';

        if ($collaborators) {
            $sql = 'SELECT id FROM repo WHERE git_type=? AND rid=? AND JSON_CONTAINS(repo_collaborators,json_quote(?))';
        }

        return DB::select($sql, [$git_type, $rid, $uid]);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param int    $uid
     * @param bool   $collaborators
     *
     * @throws Exception
     */
    public static function updateAdmin(string $git_type, int $rid, int $uid, bool $collaborators = false): void
    {
        $type = 'repo_admin';

        if ($collaborators) {
            $type = 'repo_collaborators';
        }

        $sql = <<<EOF
UPDATE repo SET $type=?

WHERE git_type=? AND rid=? AND JSON_VALID($type) IS NULL
EOF;

        DB::update($sql, ['[]', $git_type, $rid]);

        $sql = <<<EOF
UPDATE repo SET $type=JSON_MERGE_PRESERVE($type,?) 

WHERE git_type=? AND rid=? AND NOT JSON_CONTAINS($type,JSON_QUOTE(?))
EOF;

        DB::update($sql, ["[\"$uid\"]", $git_type, $rid, $uid]);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param int    $uid
     * @param bool   $collaborators
     *
     * @throws Exception
     */
    public static function deleteAdmin(string $git_type, int $rid, int $uid, bool $collaborators = false): void
    {
        $type = 'repo_admin';

        if ($collaborators) {
            $type = 'repo_collaborators';
        }

        $sql = <<<EOF
UPDATE repo SET $type=JSON_REMOVE($type,JSON_UNQUOTE(JSON_SEARCH($type,'one',?)))

WHERE git_type=? AND rid=? AND JSON_CONTAINS($type,JSON_QUOTE(?))

EOF;

        DB::update($sql, [$uid, $git_type, $rid, $uid]);
    }

    /**
     * @param string $git_type
     * @param int    $uid
     * @param bool   $collaborators
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function allByAdmin(string $git_type, int $uid, bool $collaborators = false)
    {
        $type = 'repo_admin';

        if ($collaborators) {
            $type = 'repo_collaborators';
        }

        $sql = "SELECT rid,repo_full_name FROM repo WHERE git_type=? AND JSON_CONTAINS($type,JSON_QUOTE(?))";

        return DB::select($sql, [$git_type, $uid]);
    }

    /**
     * @param string $git_type
     * @param string $username
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function allByUsername(string $git_type, string $username)
    {
        $sql = "SELECT * FROM repo WHERE git_type=? AND repo_full_name LIKE \"$username/%\"";

        return DB::select($sql, [$git_type]);
    }

    /**
     * @param string $git_type
     * @param string $username
     * @param string $repo_name
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function findByRepoFullName(string $git_type, string $username, string $repo_name)
    {
        $sql = 'SELECT * FROM repo WHERE git_type=? AND repo_full_name=CONCAT_WS("/",?,?)';

        return DB::select($sql, [$git_type, $username, $repo_name]);
    }

    /**
     * @param int  $uid
     * @param bool $collaborators
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getActiveByAdmin(int $uid, bool $collaborators = false)
    {
        $type = 'repo_admin';

        if ($collaborators) {
            $type = 'repo_collaborators';
        }

        $sql = "SELECT rid FROM repo WHERE JSON_CONTAINS($type,JSON_QUOTE(?)) AND build_activate=1 AND webhooks_status=1";

        return DB::select($sql, [$uid]);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function exists(string $git_type, int $rid)
    {
        $sql = 'SELECT id FROM repo WHERE git_type=? AND rid=?';

        return DB::select($sql, [$git_type, $rid], true);
    }

    /**
     * @param string   $git_type
     * @param int      $rid
     * @param string   $repo_full_name
     * @param int|null $insert_admin
     * @param int|null $insert_collaborators
     * @param string   $default_branch
     * @param int      $build_active
     * @param int      $webhooks_status
     *
     * @throws Exception
     */
    public static function updateRepoInfo(string $git_type,
                                          int $rid,
                                          string $repo_full_name,
                                          ?int $insert_admin,
                                          ?int $insert_collaborators,
                                          string $default_branch = 'master',
                                          int $build_active = 1,
                                          int $webhooks_status = 1): void
    {
        if ($repo_key_id = self::exists($git_type, $rid)) {
            $sql = <<<'EOF'
UPDATE repo SET

git_type=?,rid=?,repo_full_name=?,last_sync=?,build_activate=?,webhooks_status=? 

WHERE id=?;
EOF;
            DB::update($sql, [
                $git_type, $rid, $repo_full_name, time(), $build_active, $webhooks_status, $repo_key_id,
            ]);

            return;
        }

        $sql = <<<EOF
INSERT INTO repo(
id,git_type, rid, repo_full_name,default_branch,
last_sync,build_activate,webhooks_status
) VALUES(null,?,?,?,?,?,?,?)
EOF;

        DB::insert($sql, [
            $git_type, $rid, $repo_full_name,
            $default_branch, time(), $build_active, $webhooks_status,
        ]);

        if ($insert_admin) {
            self::updateAdmin($git_type, $rid, $insert_admin);
        }

        if ($insert_collaborators) {
            self::updateAdmin($git_type, $rid, $insert_collaborators, true);
        }
    }

    /**
     * 用户卸载了 GitHub App.
     *
     * @param string $git_type
     * @param        $installation_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function deleteByInstallationId(string $git_type, int $installation_id)
    {
        $sql = 'DELETE FROM repo WHERE git_type=? AND installation_id=?';

        return DB::delete($sql, [$git_type, $installation_id]);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param int    $installation_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function deleteByRid(string $git_type, int $rid, int $installation_id)
    {
        $sql = 'DELETE FROM repo WHERE git_type=? AND rid=? AND installation_id=?';

        return DB::delete($sql, [$git_type, $rid, $installation_id]);
    }

    /**
     * @param $webhooks_status
     * @param $git_type
     * @param $repo_full_name
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateBuildActive(int $webhooks_status, string $git_type, string $repo_full_name)
    {
        $sql = 'UPDATE repo SET webhooks_status=? WHERE git_type=? AND repo_full_name=?';

        return DB::update($sql, [$webhooks_status, $git_type, $repo_full_name]);
    }

    /**
     * @param int    $build_active
     * @param string $git_type
     * @param string $repo_full_name
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateWebhookStatus(int $build_active, string $git_type, string $repo_full_name)
    {
        $sql = 'UPDATE repo SET build_activate = ? WHERE git_type=? AND repo_full_name=?';

        return DB::update($sql, [$build_active, $git_type, $repo_full_name]);
    }
}
