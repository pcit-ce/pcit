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
     * @return string
     *
     * @throws Exception
     */
    public static function getRid(string $git_type, string $username, string $repo)
    {
        $sql = 'SELECT rid FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=? ORDER BY id DESC LIMIT 1';

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
        $sql = 'SELECT default_branch FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=? ORDER BY id DESC LIMIT 1';

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
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function getGitHubInstallationIdByRepoFullName(string $repo_full_name)
    {
        $sql = 'SELECT installation_id FROM repo WHERE repo_full_name=? AND git_type=? ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$repo_full_name, 'github'], true);
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
        $sql = 'SELECT installation_id FROM repo WHERE rid=? AND git_type=? ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$rid, 'github'], true);
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
     *
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
     *
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
     *
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
     *
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
    public static function allByRepoPrefix(string $git_type, string $username)
    {
        $sql = 'SELECT * FROM repo WHERE git_type=? AND repo_prefix=?';

        return DB::select($sql, [$git_type, $username]);
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
        $sql = 'SELECT * FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=?';

        return DB::select($sql, [$git_type, $username, $repo_name]);
    }

    /**
     * @param int  $uid
     *
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
     * @param string   $repo_prefix
     * @param string   $repo_name
     * @param string   $repo_full_name
     * @param int|null $insert_admin
     * @param int|null $insert_collaborators
     * @param string   $default_branch
     *
     * @throws Exception
     */
    public static function updateRepoInfo(string $git_type,
                                          int $rid,
                                          string $repo_prefix,
                                          string $repo_name,
                                          string $repo_full_name,
                                          ?int $insert_admin,
                                          ?int $insert_collaborators,
                                          string $default_branch)
    {
        if ($repo_key_id = self::exists($git_type, $rid)) {
            $sql = <<<'EOF'
UPDATE repo SET

git_type=?,rid=?,repo_prefix=?,repo_name=?,repo_full_name=?,last_sync=? WHERE id=?;
EOF;
            DB::update($sql, [
                $git_type, $rid, $repo_prefix, $repo_name,
                $repo_full_name, time(), $repo_key_id,
            ]);

            return;
        }

        $sql = <<<EOF
INSERT INTO repo(
id,git_type, rid, repo_prefix, repo_name, repo_full_name,default_branch,
last_sync
) VALUES(null,?,?,?,?,?,?,?)
EOF;

        DB::insert($sql, [
            $git_type, $rid, $repo_prefix, $repo_name, $repo_full_name,
            $default_branch, time(),
        ]);

        if ($insert_admin) {
            self::updateAdmin($git_type, $rid, $insert_admin);
        }

        if ($insert_collaborators) {
            self::updateAdmin($git_type, $rid, $insert_collaborators, true);
        }
    }

    /**
     * 用户卸载了 GitHub App
     *
     * @param string $git_type
     * @param        $installation_id
     *
     * @return int
     * @throws Exception
     */
    public static function deleteByInstallationId(string $git_type, int $installation_id)
    {
        $sql = 'DELETE FROM repo WHERE git_type=? AND installation_id=?';

        return DB::delete($sql, [$git_type, $installation_id,]);
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
}
