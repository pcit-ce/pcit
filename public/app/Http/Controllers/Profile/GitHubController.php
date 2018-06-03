<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\User;
use Error;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\DB;
use KhsCI\Support\Env;
use KhsCI\Support\Response;
use KhsCI\Support\Session;

class GitHubController
{
    const GIT_TYPE = 'github';

    /**
     * 查看 REPO 是否存在.
     *
     * @param $repo
     *
     * @return int
     *
     * @throws Exception
     */
    private function repoExists($repo)
    {
        $sql = 'SELECT id FROM repo WHERE git_type=? AND repo_full_name=?';

        $repo_key_id = DB::select($sql, [static::GIT_TYPE, $repo], true) ?? false;

        return (int) $repo_key_id;
    }

    /**
     * 获取用户项目列表.
     *
     * @param $accessToken
     *
     * @return array
     *
     * @throws Exception
     */
    private function getProject($accessToken)
    {
        $array = [];

        $khsci = new KhsCI(['github_access_token' => $accessToken]);

        for ($page = 1; $page <= 100; ++$page) {
            try {
                $json = $khsci->user_basic_info->getRepos($page);
            } catch (Error | Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode());
            }

            if ($obj = json_decode($json)) {
                for ($i = 0; $i < 30; ++$i) {
                    $obj_repo = $obj[$i] ?? false;

                    if (false === $obj_repo) {
                        break;
                    }

                    $full_name = $obj_repo->full_name ?? false;

                    $default_branch = $obj_repo->default_branch;

                    /**
                     * 获取 repo 全名，默认分支，是否为管理员（拥有全部权限）.
                     *
                     * gitee permission
                     *
                     * github *s
                     */
                    $admin = $obj_repo->permissions->admin ?? $obj_repo->permission->admin ?? null;

                    $value = [$full_name, $default_branch, $admin];

                    $id = $obj_repo->id;

                    $array[$id] = $value;
                }
            } else {
                break;
            }
        }

        return $array;
    }

    /**
     * 用户表中已存在用户信息，则更新数据.
     *
     * @param $uid
     * @param $username
     * @param $email
     * @param $pic
     * @param $accessToken
     *
     * @throws Exception
     */
    private function updateUserInfo($uid, $username, $email, $pic, $accessToken): void
    {
        $git_type = static::GIT_TYPE;

        $user_key_id = User::exists($git_type, $username);

        if ($user_key_id) {
            $sql = 'UPDATE user SET git_type=?,uid=?,username=?,email=?,pic=?,access_token=? WHERE id=?';
            DB::update($sql, [
                    $git_type, $uid, $username, $email, $pic, $accessToken, $user_key_id,
                ]
            );
        } else {
            $sql = 'INSERT user VALUES(null,?,?,?,?,?,?)';
            DB::insert($sql, [$git_type, $uid, $username, $email, $pic, $accessToken]);
        }
    }

    /**
     * 与 Git 同步.
     *
     * @param string      $uid
     * @param string      $username
     * @param string      $email
     * @param string      $pic
     * @param string|null $accessToken
     *
     * @throws Exception
     */
    private function syncProject(string $uid,
                                 string $username,
                                 string $email,
                                 string $pic,
                                 string $accessToken): void
    {
        $git_type = static::GIT_TYPE;

        $array = static::getProject($accessToken);

        $redis = Cache::connect();
        $redis->set($git_type.'_'.$uid.'_uid', $uid);
        $redis->set($git_type.'_'.$uid.'_username', $username);
        $redis->set($git_type.'_'.$uid.'_email', $email);

        /*
         * 用户相关.
         *
         * 先检查用户是否存在
         */
        static::updateUserInfo($uid, $username, $email, $pic, $accessToken);

        foreach ($array as $rid => $k) {
            list($repo_full_name, $default_branch, $admin) = $k;

            list($repoPrefix, $repoName) = explode('/', $repo_full_name);

            /**
             * repo 表是否存在 repo 数据.
             */
            $repo_key_id = static::repoExists($repo_full_name);

            $webhooksStatus = 0;
            $buildActivate = 0;
            $open_or_close = 0;
            $time = time();

            $insert_admin = null;

            $insert_collaborators = null;

            if ($admin) {
                $insert_admin = $uid;
            } else {
                $insert_collaborators = $uid;
            }

            if (!$repo_key_id) {
                $sql = <<<EOF
INSERT INTO repo(
id,git_type, rid, repo_prefix, repo_name, repo_full_name, 
webhooks_status, build_activate, repo_admin, repo_collaborators, default_branch, 
last_sync
) VALUES(null,?,?,?,?,?,?,?,JSON_ARRAY(?),JSON_ARRAY(?),?,?)
EOF;
                DB::insert($sql, [
                    $git_type, $rid, $repoPrefix, $repoName, $repo_full_name,
                    $webhooksStatus, $buildActivate, $insert_admin, $insert_collaborators, $default_branch, $time,
                ]);

                $redis->hSet($git_type.'_repo', $repo_full_name, $open_or_close);

                if ($admin) {
                    $redis->hSet(
                        $git_type.'_'.$uid.'_repo_admin', $repo_full_name, $open_or_close
                    );
                } else {
                    $redis->hSet(
                        $git_type.'_'.$uid.'_collaborators', $repo_full_name, $open_or_close
                    );
                }

                continue;
            }

            /**
             * repo 表中存在 repo 数据.
             */
            $sql = 'SELECT webhooks_status,build_activate FROM repo WHERE rid=? AND git_type=?';

            $output = DB::select($sql, [$rid, $git_type]);

            if ($output) {
                foreach ($output as $status_k) {
                    $webhooksStatus = $status_k['webhooks_status'];
                    $buildActivate = $status_k['build_activate'];
                }
            }

            if (1 === (int) $webhooksStatus && 1 === (int) $buildActivate) {
                $open_or_close = 1;
            }

            /**
             * repo 表已存在数据，则更新表.
             */
            $sql = <<<'EOF'
UPDATE repo SET 

git_type=?,rid=?,repo_prefix=?,repo_name=?,repo_full_name=?,last_sync=? WHERE id=?;
EOF;
            DB::update($sql, [
                $git_type, $rid, $repoPrefix, $repoName,
                $repo_full_name, $time, $repo_key_id,
            ]);

            if ($admin) {
                $this->updateRepoAdmin($repo_key_id, $uid);
                $redis->hSet(
                    $git_type.'_'.$uid.'_repo_admin', $repo_full_name, $open_or_close
                );
            } else {
                $this->updateRepoCollaborators($repo_key_id, $uid);
                $redis->hSet(
                    $git_type.'_'.$uid.'_repo_collaborators', $repo_full_name, $open_or_close
                );
            }
        }
    }

    /**
     * 更新仓库管理员.
     *
     * @param int    $repo_key_id
     * @param string $uid
     *
     * @throws Exception
     */
    private function updateRepoAdmin(int $repo_key_id, string $uid): void
    {
        $sql = <<<EOF
UPDATE repo SET repo_admin=JSON_MERGE(repo_admin,?) WHERE id=? AND NOT JSON_CONTAINS(repo_admin,?);
EOF;

        DB::update($sql, ["[\"$uid\"]", $repo_key_id, "\"$uid\""]);
    }

    /**
     * @param int    $repo_key_id
     * @param string $uid
     *
     * @throws Exception
     */
    private function updateRepoCollaborators(int $repo_key_id, string $uid): void
    {
        $sql = <<<EOF
UPDATE repo set 

repo_admin=JSON_MERGE(repo_collaborators,?) where id=? AND NOT JSON_CONTAINS(repo_collaborators,?);
EOF;

        DB::update($sql, ["[\"$repo_key_id\"]", "\"$uid\"", $repo_key_id]);
    }

    /**
     * Profile 页主要展示用户仓库列表.
     *
     * @param mixed ...$arg
     *
     * @return array
     *
     * @throws Exception
     */
    public function __invoke(...$arg)
    {
        $git_type = static::GIT_TYPE;

        $username_from_web = $arg[0];

        $email = Session::get($git_type.'.email');
        $uid = Session::get($git_type.'.uid');
        $username = Session::get($git_type.'.username');
        $pic = Session::get($git_type.'.pic');
        $accessToken = Session::get($git_type.'.access_token');

        if (null === $username or null === $accessToken) {
            Response::redirect(Env::get('CI_HOST').'/login');
        }

        if ($username_from_web !== $username) {
            Response::redirect('/profile/'.$git_type.'/'.$username);
        }

        $arg[0] === $username && $username = $arg[0];

        static::updateUserInfo($uid, $username, $email, $pic, $accessToken);

        $ajax = $_GET['ajax'] ?? false;

        // ?ajax=true $ajax=true
        // cache

        if (!($ajax)) {
            // 非 ajax 请求返回静态 HTML 页面

            require __DIR__.'/../../../../public/profile/index.html';
            exit;
        }

        $sync = true;

        $redis = Cache::connect();

        if ($redis->get($git_type.'_'.$uid.'_username')) {
            // Redis 已存在数据
            $sync = false;
        }

        if ($_GET['sync'] ?? false or $sync) {
            $this->syncProject(
                (string) $uid, (string) $username, (string) $email, (string) $pic, (string) $accessToken
            );
            $sync = true;
        }

        $cacheArray = $redis->hGetAll($git_type.'_'.$uid.'_repo_admin');

        $array_active = [];

        $array = [];

        foreach ($cacheArray as $k => $status) {
            if (1 === (int) $status) {
                $array_active[$k] = $status;

                continue;
            }

            $array[$k] = $status;
        }

        $array = array_merge($array_active, $array);

        return [
            'code' => 200,
            'git_type' => $git_type,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'cache' => false === $sync,
            'repos' => $array,
        ];
    }
}
