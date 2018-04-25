<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use Error;
use Exception;
use KhsCI\Support\Session;

class GitHubController
{
    const TYPE = 'gitHub';

    /**
     * 获取 Webhooks 状态
     *
     * @param $accessToken
     * @param $username
     * @param $repo
     * @return mixed
     */
    public function getStatus($accessToken, $username, $repo)
    {
        $objClass = 'KhsCI\\Service\\OAuth\\'.ucfirst(static::TYPE);

        $array = json_decode($objClass::getWebhooks($accessToken, false, $username, $repo));

        if ($array) {
            foreach ($array as $k) {
                if ($k->url === getenv('CI_HOST').'/webhooks/'.strtolower(static::TYPE)) {
                    return 1;
                    break;
                }
            }
            return 0;
        } else {
            return 0;
        }
    }

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
     * @param mixed ...$arg
     * @return array
     * @throws Exception
     */
    public function __invoke(...$arg)
    {
        $redis = new \Redis();

        $type = static::TYPE;
        $typeLower = strtolower($type);

        $accessToken = Session::get($typeLower.'.access_token');

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
                $k = explode('/', $k, 2);
                $array[$k[1]] = $status;
            }
        } else {
            $sync = false;
        }

        if ($_GET['sync'] ?? false or $sync) {
            $array = static::getProject($accessToken);
            $redis->set($uid.'_uid', $uid);
            $redis->set($uid.'_username', $username);

            foreach ($array as $id => $repo) {
                $repoArray = explode('/', $repo);
                $status = $this->getStatus($accessToken, $repoArray[0], $repoArray[1]);
                $redis->hSet($uid.'_repo', $id.'/'.$repo, $status);
            }

            $array = [];

            $cache = false;

            $cacheArray = $redis->hGetAll($uid.'_repo');

            foreach ($cacheArray as $k => $status) {
                $k = explode('/', $k, 2);
                $array[$k[1]] = $status;
            }
        }

        return [
            'code' => 200,
            'uid' => $uid,
            'username' => $arg[0],
            'pic' => $pic,
            'cache' => $cache,
            'repos' => $array,
        ];
    }
}
