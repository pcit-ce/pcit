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
     * 获取用户项目列表
     *
     * @param $accessToken
     * @return array
     *
     * @throws Exception
     */
    public function getProject($accessToken)
    {
        $type = static::TYPE;

        $array = [];

        $objClass = 'KhsCI\\Service\\OAuth\\'.ucfirst($type);

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
     * @param string $uid
     * @param string $username
     * @param string $email
     * @param string $pic
     * @param string|null $accessToken
     * @return array
     * @throws Exception
     */
    public function syncProject(string $uid,
                                string $username,
                                string $email,
                                string $pic,
                                string $accessToken = null)
    {
        $type = static::TYPE;
        $typeLower = strtolower($type);

        if (!$accessToken) {
            $accessToken = Session::get($typeLower.'.access_token');
        }

        $array = static::getProject($accessToken);

        $redis = Cache::connect();
        $redis->set($uid.'_uid', $uid);
        $redis->set($uid.'_username', $username);
        $redis->set($uid.'_email', $email);

        // 与 Git 同步时更新数据库

        $pdo = DB::connect();

        $sql = <<<EOF
DELETE FROM user WHERE 'username'=$username AND 'git_type'=$typeLower;

INSERT user VALUES(null,"$typeLower","$uid","$username","$email","$pic","$accessToken");

EOF;
        $pdo->exec($sql);

        foreach ($array as $id => $repoFullName) {
            $repoArray = explode('/', $repoFullName);

            $objClass = 'KhsCI\\Service\\OAuth\\'.ucfirst($type);

            $url = getenv('CI_HOST').'/webhooks/'.$typeLower;

            $repoPrefix = $repoArray[0];
            $repoName = $repoArray[1];

            //$status = $objClass::getWebhooksStatus($accessToken, $url, $repoPrefix, $repoName);

            $status = 0;

            $redis->hSet($uid.'_repo', $repoFullName, $status);

            $time = time();

            $sql = <<<EOF

DELETE FROM repo WHERE rid=$id;

INSERT repo VALUES(null,"$typeLower","$id","$username","$repoPrefix","$repoName","$repoFullName","$status",$time);

EOF;

            $pdo->exec($sql);
        }

        $array = [];

        $cacheArray = $redis->hGetAll($uid.'_repo');

        foreach ($cacheArray as $k => $status) {
            //$k = explode('/', $k, 2);
            $array[$k] = $status;
        }

        return $array;
    }

    /**
     * @param mixed ...$arg
     * @return array
     * @throws Exception
     */
    public function __invoke(...$arg)
    {
        $redis = Cache::connect();

        $type = static::TYPE;
        $typeLower = strtolower($type);

        $email = Session::get($typeLower.'.email');
        $uid = Session::get($typeLower.'.uid');
        $username = Session::get($typeLower.'.username');
        $arg[0] === $username && $username = $arg[0];
        $pic = Session::get($typeLower.'.pic');

        $redis->connect(getenv('REDIS_HOST'));

        $ajax = $_GET['ajax'] ?? false;

        // ?ajax=true $ajax=true
        // cache

        if (!($ajax)) {
            require __DIR__.'/../../../../public/profile/index.html';
            exit;
        }

        $sync = false;
        $cache = true;

        $array = [];

        if ($redis->get($uid.'_username')) {
            $cacheArray = $redis->hGetAll($uid.'_repo');
            foreach ($cacheArray as $k => $status) {
                // $k = explode('/', $k, 2);
                $array[$k] = $status;
            }
        } else {
            $sync = true;
        }

        if ($_GET['sync'] ?? false or $sync) {
            $array = $this->syncProject((string)$uid, (string)$username, (string)$email, (string)$pic);
            $cache = false;
        }

        return [
            'code' => 200,
            'git_type' => $typeLower,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'cache' => $cache,
            'repos' => $array,
        ];
    }
}
