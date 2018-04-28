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
     * @return null
     * @throws Exception
     */
    private function getUserStatus($username)
    {
        $gitTypeLower = strtolower(static::TYPE);

        $sql = "SELECT id FROM user WHERE username=? AND git_type=?";

        $array = DB::select($sql, [$username, $gitTypeLower]);

        if ($array) {
            foreach ($array as $id) {
                return $id['id'];
            }
        }

        return null;
    }

    /**
     * 查看 REPO 是否存在.
     *
     * @param $repo
     * @return null
     * @throws Exception
     */
    private function getRepoStatus($repo)
    {
        $gitTypeLower = strtolower(static::TYPE);

        $sql = "SELECT id FROM repo WHERE git_type=? AND repo_full_name=?";

        $array = DB::select($sql, [$gitTypeLower, $repo]);

        if ($array) {
            foreach ($array as $id) {
                return $id['id'];
            }
        }

        return null;
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
                $json = $objClass::getProjects((string)$accessToken, $page);
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
     * 与 Git 同步.
     *
     * @param string $uid
     * @param string $username
     * @param string $email
     * @param string $pic
     * @param string|null $accessToken
     *
     * @return array
     *
     * @throws Exception
     */
    private function syncProject(string $uid,
                                 string $username,
                                 string $email,
                                 string $pic,
                                 string $accessToken = null)
    {
        $typeLower = strtolower(static::TYPE);

        $accessToken = $accessToken ?? Session::get($typeLower.'.access_token');

        $array = static::getProject($accessToken);

        $redis = Cache::connect();
        $redis->set($uid.'_uid', $uid);
        $redis->set($uid.'_username', $username);
        $redis->set($uid.'_email', $email);

        /**
         * 用户相关
         *
         * 先检查用户是否存在
         */
        $id = self::getUserStatus($username);

        if ($id) {

            $sql = "UPDATE user set git_type=?,uid=?,username=?,email=?,pic=?,access_token=? WHERE id=?";
            DB::update($sql, [$typeLower, $uid, $username, $email, $pic, $accessToken, $id]);

        } else {

            $sql = "INSERT user VALUES(null,?,?,?,?,?,?)";
            DB::insert($sql, [$typeLower, $uid, $username, $email, $pic, $accessToken]);

        }

        foreach ($array as $rid => $repoFullName) {
            $repoArray = explode('/', $repoFullName);

            list($repoPrefix, $repoName) = $repoArray;

            $webhooksStatus = 0;
            $buildActivate = 0;

            $sql = "SELECT webhooks_status FROM repo WHERE rid=? AND git_type=?";

            $output = DB::select($sql, [$rid, $typeLower]);

            if ($output) {
                foreach ($output as $k) {
                    $webhooksStatus = $k['webhooks_status'];
                }
            }

            $sql = "SELECT build_activate FROM repo WHERE rid=? AND git_type=?";

            $output = DB::select($sql, [$rid, $typeLower]);

            if ($output) {
                foreach ($output as $k) {
                    $buildActivate = $k['build_activate'];
                }
            }

            $redis->hSet($uid.'_repo', $repoFullName, $webhooksStatus);

            $time = time();

            $id = self::getRepoStatus($repoFullName);

            $star = 0;

            $array = [
                $typeLower, $rid, $username, $repoPrefix, $repoName, $repoFullName,
                $webhooksStatus, $buildActivate, $star, $time
            ];

            if ($id) {
                $sql = <<<EOF
UPDATE repo set git_type=?,
                rid=?,
                username=?,
                repo_prefix=?,
                repo_name=?,
                repo_full_name=?,
                webhooks_status=?,
                build_activate=?,
                star=?,
                last_sync=? WHERE id='$id';
EOF;
                DB::update($sql, $array);
            } else {
                $sql = "INSERT repo VALUES(null,?,?,?,?,?,?,?,?,?,?)";
                DB::insert($sql, $array);
            }
        }

        $array = [];

        $cacheArray = $redis->hGetAll($uid.'_repo');

        foreach ($cacheArray as $k => $status) {
            $array[$k] = $status;
        }

        return $array;
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

        $arg[0] === $username && $username = $arg[0];

        $ajax = $_GET['ajax'] ?? false;

        // ?ajax=true $ajax=true
        // cache

        if (!($ajax)) {
            require __DIR__.'/../../../../public/profile/index.html';
            exit;
        }

        $sync = true;
        $array = [];

        $redis = Cache::connect();

        if ($redis->get($uid.'_username')) {
            $cacheArray = $redis->hGetAll($uid.'_repo');
            foreach ($cacheArray as $k => $status) {
                $array[$k] = $status;
            }
            $sync = false;
        }

        if ($_GET['sync'] ?? false or $sync) {
            $array = $this->syncProject((string)$uid, (string)$username, (string)$email, (string)$pic);
            $sync = true;
        }

        return [
            'code' => 200,
            'git_type' => $gitTypeLower,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'cache' => $sync === false,
            'repos' => $array,
        ];
    }
}
