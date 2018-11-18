<?php

declare(strict_types=1);

namespace App;

use Exception;
use PCIT\Support\DB;
use PCIT\Support\Model;

class Repo extends Model
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
    public static function getRid(string $username, string $repo, $git_type = 'github')
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
     * @return string
     *
     * @throws Exception
     */
    public static function getDefaultBranch(string $username, string $repo, $git_type = 'github')
    {
        $sql = 'SELECT default_branch FROM repo WHERE git_type=? AND repo_full_name=CONCAT_WS("/",?,?) ORDER BY id DESC LIMIT 1';

        $default_branch = DB::select($sql, [$git_type, $username, $repo], true);

        return $default_branch;
    }

    /**
     * @param string $git_type
     * @param int    $rid
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getRepoFullName(int $rid, $git_type = 'github')
    {
        $sql = 'SELECT repo_full_name FROM repo WHERE rid=? AND git_type=? ORDER BY id DESC LIMIT 1';

        return DB::select($sql, [$rid, $git_type], true);
    }

    /**
     * @param string $repo_full_name
     * @param string $git_type
     *
     * @return string
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
     * @return string
     *
     * @throws Exception
     */
    public static function getGitHubInstallationIdByRid(int $rid, $git_type = 'github')
    {
        $repo_full_name = self::getRepoFullName($rid, $git_type);

        return self::getGitHubInstallationIdByRepoFullName($repo_full_name, $git_type);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     * @param bool   $collaborators
     *
     * @return string
     *
     * @throws Exception
     */
    public static function getAdmin(int $rid, bool $collaborators = false, $git_type = 'github')
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
     * @return array
     *
     * @throws Exception
     */
    public static function checkAdmin(int $rid, int $uid, bool $collaborators = false, $git_type = 'github')
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
    public static function updateAdmin(int $rid, int $uid, $git_type = 'github', bool $collaborators = false): void
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
    public static function deleteAdmin(int $rid, int $uid, bool $collaborators = false, $git_type = 'github'): void
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
     * @return array
     *
     * @throws Exception
     */
    public static function allByAdmin(int $uid, bool $collaborators = false, $git_type = 'github')
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
     * @return array
     *
     * @throws Exception
     */
    public static function allByUsername(string $username, $git_type = 'github')
    {
        $sql = "SELECT rid FROM repo WHERE git_type=? AND repo_full_name LIKE \"$username/%\"";

        $result = DB::select($sql, [$git_type]);

        $repos = [];

        foreach ($result as $rids) {
            $rid = $rids['rid'];

            $sql = <<<EOF

select repo.rid,
       repo.repo_full_name,
       repo.default_branch,
       repo.git_type,
       repo.webhooks_status,
       builds.id as build_id,
       builds.build_status,
       builds.commit_id
from repo
       left join builds on repo.default_branch = builds.branch 
       and repo.rid = builds.rid and builds.event_type='push' 
                             and repo.repo_full_name LIKE "$username/%" 
where repo.git_type = "$git_type"
    and repo.rid = $rid order by build_id DESC limit 1;

EOF;

            $result = DB::select($sql, [])[0];

            if ($result) {
                if (null === $result['commit_id']) {
                    $result['commit_id'] = '0';
                }

                if ('github' === $result['git_type']) {
                    $result['webhooks_status'] = 1;
                }
            }

            array_push($repos, $result);
        }

        return $repos;
    }

    /**
     * @param string $git_type
     * @param string $username
     * @param string $repo_name
     *
     * @return array
     *
     * @throws Exception
     */
    public static function findByRepoFullName(string $username, string $repo_name, $git_type = 'github')
    {
        $sql = 'SELECT * FROM repo WHERE git_type=? AND repo_full_name=CONCAT_WS("/",?,?)';

        return DB::select($sql, [$git_type, $username, $repo_name]);
    }

    /**
     * @param int    $uid
     * @param bool   $collaborators
     * @param string $git_type
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getActiveByAdmin(int $uid, bool $collaborators = false, $git_type = 'github')
    {
        $type = 'repo_admin';

        if ($collaborators) {
            $type = 'repo_collaborators';
        }

        $sql = "SELECT rid FROM repo WHERE JSON_CONTAINS($type,JSON_QUOTE(?)) AND git_type=? AND build_activate=1 AND webhooks_status=1";

        return DB::select($sql, [$uid, $git_type]);
    }

    /**
     * @param string $git_type
     * @param int    $rid
     *
     * @return string
     *
     * @throws Exception
     */
    public static function exists(int $rid, $git_type = 'github')
    {
        $sql = 'SELECT id FROM repo WHERE git_type=? AND rid=?';

        return DB::select($sql, [$git_type, $rid], true);
    }

    /**
     * @param int      $rid
     * @param string   $repo_full_name
     * @param int|null $insert_admin
     * @param int|null $insert_collaborators
     * @param string   $default_branch
     * @param string   $git_type
     *
     * @throws Exception
     */
    public static function updateRepoInfo(int $rid,
                                          string $repo_full_name,
                                          ?int $insert_admin,
                                          ?int $insert_collaborators,
                                          string $default_branch = 'master',
                                          $git_type = 'github'): void
    {
        if ($repo_key_id = self::exists($rid, $git_type)) {
            $sql = <<<'EOF'
UPDATE repo SET

git_type=?,rid=?,repo_full_name=?,last_sync=?

WHERE id=?;
EOF;
            DB::update($sql, [
                $git_type, $rid, $repo_full_name, time(), $repo_key_id,
            ]);

            goto a;
        }

        $sql = <<<'EOF'
INSERT INTO repo(
id,git_type, rid, repo_full_name,default_branch,
last_sync
) VALUES(null,?,?,?,?,?)
EOF;

        DB::insert($sql, [
            $git_type, $rid, $repo_full_name,
            $default_branch, time(),
        ]);

        a:

        if ($insert_admin) {
            self::updateAdmin($rid, $insert_admin, $git_type);
        }

        if ($insert_collaborators) {
            self::updateAdmin($rid, $insert_collaborators, $git_type, true);
        }
    }

    /**
     * 用户卸载了 GitHub App.
     *
     * 将 repo 表中 user_full_name 与 user 表 installation_id 匹配的记录删除
     *
     * @param string $git_type
     * @param        $installation_id
     *
     * @return int
     *
     * @throws Exception
     */
    public static function deleteByInstallationId(int $installation_id, string $git_type = 'github')
    {
        $sql = <<<'EOF'
            DELETE repo FROM repo LEFT JOIN user ON repo.repo_full_name LIKE CONCAT(user.username,"/%")
            where user.installation_id = ?
EOF;

        return DB::delete($sql, [$installation_id]);
    }

    /**
     * @param int    $rid
     * @param string $git_type
     *
     * @return int
     *
     * @throws Exception
     */
    public static function deleteByRid(int $rid, $git_type = 'github')
    {
        $sql = 'DELETE FROM repo WHERE rid =? AND git_type=?';

        return DB::delete($sql, [$rid, $git_type]);
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
    public static function updateBuildActive(int $build_active, string $git_type, string $repo_full_name)
    {
        $sql = 'UPDATE repo SET build_activate=? WHERE git_type=? AND repo_full_name=?';

        return DB::update($sql, [$build_active, $git_type, $repo_full_name]);
    }

    /**
     * @param int    $webhooks_status
     * @param string $git_type
     * @param string $repo_full_name
     *
     * @return int
     *
     * @throws Exception
     */
    public static function updateWebhookStatus(int $webhooks_status, string $git_type, string $repo_full_name)
    {
        $sql = 'UPDATE repo SET webhooks_status = ? WHERE git_type=? AND repo_full_name=?';

        return DB::update($sql, [$webhooks_status, $git_type, $repo_full_name]);
    }

    /**
     * @param string $repo_full_name
     * @param string $git_type
     *
     * @throws Exception
     */
    public static function active(string $repo_full_name, string $git_type = 'gitee'): void
    {
        DB::beginTransaction();
        self::updateWebhookStatus(1, $git_type, $repo_full_name);
        self::updateBuildActive(1, $git_type, $repo_full_name);
        DB::commit();
    }

    /**
     * @param string $repo_full_name
     * @param string $git_type
     *
     * @throws Exception
     */
    public static function deactive(string $repo_full_name, string $git_type = 'gitee'): void
    {
        DB::beginTransaction();
        self::updateWebhookStatus(0, $git_type, $repo_full_name);
        self::updateBuildActive(0, $git_type, $repo_full_name);
        DB::commit();
    }

    /**
     * @param string $repo_full_name
     * @param string $git_type
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function canBuild(string $repo_full_name, string $git_type)
    {
        $sql = 'SELECT webhooks_status,build_activate FROM repo WHERE repo_full_name=? AND git_type=?';

        $result = DB::select($sql, [$repo_full_name, $git_type]);

        if (1 === $result[0]['webhooks_status'] and 1 === $result[0]['build_activate']) {
            return true;
        }

        return false;
    }
}
