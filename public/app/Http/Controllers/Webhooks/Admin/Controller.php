<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks\Admin;

use Exception;
use KhsCI\Support\Request;
use KhsCI\Support\Session;

class Controller
{
    private static $gitType;

    /**
     * @return bool|null
     *
     * @throws Exception
     */
    private static function checkAccessToken()
    {
        $header = Request::header('Authorization') ?? '';

        $access_token = (explode(' ', $header))[1]
            ?? Session::get(self::$gitType.'.access_token')
            ?? false;

        if (false === $access_token) {
            throw new Exception('access_token not found || Requires authentication || 401 Unauthorized', 401);
        }

        return $access_token;
    }

    /**
     * @return string
     */
    private static function getObj()
    {
        if ('github' === self::$gitType) {
            $obj = 'KhsCI\\Service\\OAuth\\GitHub';
        } else {
            $obj = 'KhsCI\\Service\\OAuth\\'.ucfirst(self::$gitType);
        }

        return $obj;
    }

    /**
     * 获取 Webhooks 列表
     *
     * @param mixed ...$arg
     *
     * @return mixed
     * @throws Exception
     */
    public static function list(...$arg)
    {
        $raw = false;

        $gitType = $arg[0];

        unset($arg[0]);

        self::$gitType = $gitType;

        $access_token = self::checkAccessToken();

        $obj = self::getObj();

        $json = $obj::getWebhooks($access_token, $raw, ...$arg);

        return json_decode($json, true);
    }

    /**
     * 增加 Webhooks，增加之前必须先判断是否已存在
     *
     * @param mixed ...$arg
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function add(...$arg)
    {
        $data = file_get_contents('php://input');

        $obj = json_decode($data);

        if ((!$data) or (!is_object($obj)) or 0 !== json_last_error()) {
            throw new Exception('Invalid request, must include JSON', 422);
        }

        $webhooksUrl = $obj->url;

        $gitType = $arg[0];

        unset($arg[0]);

        self::$gitType = $gitType;

        $access_token = self::checkAccessToken();

        $obj = self::getObj();

        $getWebhooksStatus = $obj::getWebhooksStatus($access_token, $webhooksUrl, ...$arg);

        if (1 === $getWebhooksStatus) {
            $redis = new \Redis();

            $redis->connect(getenv('REDIS_HOST'));

            $uid = Session::get($gitType.'.uid');

            $redis->hSet($uid.'_repo', $arg[1].'/'.$arg[2], 1);

            $redis->close();

            throw new Exception('Webhooks already exists', 304);
        }

        return $obj::setWebhooks($access_token, $data, ...$arg);
    }

    /**
     * 删除 Webhooks
     *
     * @param mixed ...$arg
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function delete(...$arg)
    {
        $gitType = $arg[0];

        unset($arg[0]);

        self::$gitType = $gitType;

        $access_token = self::checkAccessToken();

        $obj = self::getObj();

        return $obj::unsetWebhooks($access_token, ...$arg);
    }

    /**
     * 停止构建，暂时不主动删除 Webhooks
     *
     * @param array $arg
     * @return array
     */
    public static function close(...$arg)
    {
        $gitType = $arg[0];

        unset($arg[0]);

        self::$gitType = $gitType;

        $redis = new \Redis();

        $redis->connect(getenv('REDIS_HOST'));

        $uid = Session::get($gitType.'.uid');

        $redis->hSet($uid.'_repo', $arg[1].'/'.$arg[2], 0);

        $redis->close();

        return [
            "code" => 200,
        ];
    }
}
