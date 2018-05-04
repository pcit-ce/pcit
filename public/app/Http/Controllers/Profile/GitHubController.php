<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use Error;
use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\DB;
use KhsCI\Support\Session;

class GitHubController
{
    const TYPE = 'gitHub';

    /**
     * 查看用户是否已存在.
     *
     * @param $username
     *
     * @return bool
     *
     * @throws Exception
     */
    private function getUserStatus($username)
    {
        $gitTypeLower = strtolower(static::TYPE);

        $sql = 'SELECT id FROM user WHERE username=? AND git_type=?';

        $array = DB::select($sql, [$username, $gitTypeLower]);

        if ($array) {
            foreach ($array as $id) {
                return $id['id'];
            }
        }

        return false;
    }

    /**
     * 查看 REPO 是否存在.
     *
     * @param $repo
     *
     * @return bool
     *
     * @throws Exception
     */
    private function getRepoStatus($repo)
    {
        $gitTypeLower = strtolower(static::TYPE);

        $sql = 'SELECT id FROM repo WHERE git_type=? AND repo_full_name=?';

        $array = DB::select($sql, [$gitTypeLower, $repo]);

        if ($array) {
            foreach ($array as $id) {
                return $id['id'];
            }
        }

        return false;
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
        $gitType = static::TYPE;

        $array = [];

        $objClass = 'KhsCI\\Service\\OAuth\\'.ucfirst($gitType);

        for ($page = 1; $page <= 100; ++$page) {
            try {
                $json = $objClass::getProjects((string) $accessToken, $page);
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

                    $id = $obj_repo->id;

                    $array[$id] = $full_name;
                }
            } else {
                break;
            }
        }

        return $array;
    }

    /**
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
        $gitTypeLower = strtolower(static::TYPE);

        $user_key_id = self::getUserStatus($username);

        if ($user_key_id) {
            $sql = 'UPDATE user set git_type=?,uid=?,username=?,email=?,pic=?,access_token=? WHERE id=?';
            DB::update($sql, [$gitTypeLower, $uid, $username, $email, $pic, $accessToken, $user_key_id]);
        } else {
            $sql = 'INSERT user VALUES(null,?,?,?,?,?,?)';
            DB::insert($sql, [$gitTypeLower, $uid, $username, $email, $pic, $accessToken]);
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
        $gitTypeLower = strtolower(static::TYPE);

        $array = static::getProject($accessToken);

        $redis = Cache::connect();
        $redis->set($uid.'_uid', $uid);
        $redis->set($uid.'_username', $username);
        $redis->set($uid.'_email', $email);

        /*
         * 用户相关.
         *
         * 先检查用户是否存在
         */
        self::updateUserInfo($uid, $username, $email, $pic, $accessToken);

        foreach ($array as $rid => $repoFullName) {
            $repoArray = explode('/', $repoFullName);

            list($repoPrefix, $repoName) = $repoArray;

            /**
             * repo 表是否存在 repo 数据.
             */
            $repo_key_id = self::getRepoStatus($repoFullName);

            $webhooksStatus = 0;
            $buildActivate = 0;
            $open_or_close = 0;
            $star = 0;
            $time = time();

            $repoDataArray = [
                $gitTypeLower, $rid, $username, $repoPrefix, $repoName, $repoFullName,
                $webhooksStatus, $buildActivate, $star, $time,
            ];

            if (!$repo_key_id) {
                $sql = 'INSERT repo VALUES(null,?,?,?,?,?,?,?,?,?,?)';
                DB::insert($sql, $repoDataArray);
                $redis->hSet($uid.'_repo', $repoFullName, $open_or_close);

                continue;
            }

            /**
             * repo 表中存在 repo 数据.
             */
            $sql = 'SELECT webhooks_status,build_activate FROM repo WHERE rid=? AND git_type=?';

            $output = DB::select($sql, [$rid, $gitTypeLower]);

            if ($output) {
                foreach ($output as $k) {
                    $webhooksStatus = $k['webhooks_status'];
                    $buildActivate = $k['build_activate'];
                }
            }

            if (1 === (int) $webhooksStatus && 1 === (int) $buildActivate) {
                $open_or_close = 1;
            }

            $sql = <<<'EOF'
UPDATE repo set git_type=?,rid=?,username=?,repo_prefix=?,repo_name=?,repo_full_name=?,last_sync=? WHERE id=?;
EOF;
            DB::update($sql, [
                $gitTypeLower, $rid, $username, $repoPrefix, $repoName, $repoFullName, $time, $repo_key_id,
            ]);

            $redis->hSet($uid.'_repo', $repoFullName, $open_or_close);
        }
    }

    /**
     * @param mixed ...$arg
     *
     * @return array
     *
     * @throws Exception
     */
    public function __invoke(...$arg)
    {
        $gitTypeLower = strtolower(static::TYPE);

        $email = Session::get($gitTypeLower.'.email');
        $uid = Session::get($gitTypeLower.'.uid');
        $username = Session::get($gitTypeLower.'.username');
        $pic = Session::get($gitTypeLower.'.pic');
        $accessToken = Session::get($gitTypeLower.'.access_token');

        $arg[0] === $username && $username = $arg[0];

        self::updateUserInfo($uid, $username, $email, $pic, $accessToken);

        $ajax = $_GET['ajax'] ?? false;

        // ?ajax=true $ajax=true
        // cache

        if (!($ajax)) {
            require __DIR__.'/../../../../public/profile/index.html';
            exit;
        }

        $sync = true;

        $redis = Cache::connect();

        if ($redis->get($uid.'_username')) {
            // Redis 已存在数据
            $sync = false;
        }

        if ($_GET['sync'] ?? false or $sync) {
            $this->syncProject((string) $uid, (string) $username, (string) $email, (string) $pic, (string) $accessToken);
            $sync = true;
        }

        $cacheArray = $redis->hGetAll($uid.'_repo');

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
            'git_type' => $gitTypeLower,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'cache' => false === $sync,
            'repos' => $array,
        ];
    }
}
