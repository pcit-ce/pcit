<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks\Admin;

use Exception;
use KhsCI\Support\Request;
use KhsCI\Support\Response;
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
     * @param mixed ...$arg
     *
     * @throws Exception
     */
    public static function list(...$arg): void
    {
        $raw = false;

        $gitType = $arg[0];

        unset($arg[0]);

        self::$gitType = $gitType;

        $access_token = self::checkAccessToken();

        $obj = self::getObj();

        $json = $obj::getWebhooks($access_token, $raw, ...$arg);

        Response::json(json_decode($json, true));
    }

    /**
     * @param mixed ...$arg
     * @return mixed
     * @throws Exception
     */
    public static function add(...$arg)
    {
        $data = file_get_contents('php://input');

        $gitType = $arg[0];

        unset($arg[0]);

        self::$gitType = $gitType;

        $access_token = self::checkAccessToken();

        $obj = self::getObj();

        return $obj::setWebhooks($access_token, $data, ...$arg);
    }

    /**
     * @param mixed ...$arg
     * @return mixed
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
}
