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
     * 获取 SQL 语句执行结果
     *
     * @param $sql
     * @return null
     */
    private function getDBOutput($sql)
    {
        $pdo = DB::connect();

        $stmt = $pdo->prepare($sql);

        $stmt->execute();

        $output = $stmt->fetchAll();

        if (!$output) {
            return null;
        }

        foreach ($output as $k) {
            $id = $k[0];
        }

        return $id;
    }

    /**
     * 查看用户是否已存在
     *
     * @param $username
     * @return null
     */
    private function getUserStatus($username)
    {
        $typeLower = strtolower(static::TYPE);

        $sql = <<<EOF
SELECT id FROM user WHERE username='$username' AND git_type='$typeLower';
EOF;
        return self::getDBOutput($sql);

    }

    /**
     * 查看 REPO 是否存在
     *
     * @param $repo
     * @return null
     */
    private function getRepoStatus($repo)
    {
        $typeLower = strtolower(static::TYPE);

        $sql = <<<EOF
SELECT id FROM repo WHERE git_type='$typeLower' AND repo_full_name='$repo';
EOF;

        return self::getDBOutput($sql);

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
     * 与 Git 同步
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

        // 先检查用户是否存在
        $output = self::getUserStatus($username);

        if ($output) {
            $sql = <<<EOF
UPDATE user set git_type=?,uid=?,username=?,email=?,pic=?,access_token=? WHERE id='$output';
EOF;
        } else {
            $sql = <<<EOF
INSERT user VALUES(null,?,?,?,?,?,?);
EOF;
        }

        $pdo = DB::connect();
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(1, $typeLower);
        $stmt->bindParam(2, $uid);
        $stmt->bindParam(3, $username);
        $stmt->bindParam(4, $email);
        $stmt->bindParam(5, $pic);
        $stmt->bindParam(6, $accessToken);

        $stmt->execute();

        foreach ($array as $rid => $repoFullName) {
            $repoArray = explode('/', $repoFullName);

            $repoPrefix = $repoArray[0];
            $repoName = $repoArray[1];

            $webhooksStatus = 0;
            $buildActivate = 0;

            $sql = <<<EOF
select webhooks_status from repo where rid='$rid' AND git_type='$typeLower';
EOF;
            $output = self::getDBOutput($sql);

            if (1 === $output) {
                $webhooksStatus = 1;
            }

            $sql = <<<EOF
select build_activate from repo where rid='$rid' AND git_type='$typeLower';
EOF;

            $output = self::getDBOutput($sql);

            if (1 === $output) {
                $buildActivate = 1;
            }

            $redis->hSet($uid.'_repo', $repoFullName, $webhooksStatus);

            $time = time();

            $sql = <<<EOF
SELECT id FROM repo WHERE git_type='$typeLower' AND repo_full_name='$repoFullName';
EOF;
            $output = self::getDBOutput($sql);

            if ($output) {
                $sql = <<<EOF
UPDATE repo set git_type=?,
                rid=?,
                username=?,
                repo_prefix=?,
                repo_name=?,
                repo_full_name=?,
                webhooks_status=?,
                build_activate=?,
                last_sync=? WHERE id='$output';
EOF;

            } else {
                $sql = <<<EOF
INSERT repo VALUES(null,?,?,?,?,?,?,?,?,?);

EOF;
            }

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(1, $typeLower);
            $stmt->bindParam(2, $rid);
            $stmt->bindParam(3, $username);
            $stmt->bindParam(4, $repoPrefix);
            $stmt->bindParam(5, $repoName);
            $stmt->bindParam(6, $repoFullName);
            $stmt->bindParam(7, $webhooksStatus);
            $stmt->bindParam(8, $buildActivate);
            $stmt->bindParam(9, $time);

            $stmt->execute();

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
     *
     * @return array
     *
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
        $code = 200;

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
            $code = 200;
        }

        return [
            'code' => $code,
            'git_type' => $typeLower,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'cache' => $cache,
            'repos' => $array,
        ];
    }
}
