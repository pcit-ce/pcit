<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks\Admin;

use Exception;
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
        $access_token = Session::get(self::$gitType.'.access_token') ?? false;

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
        $gitType = $arg[0];

        unset($arg[0]);

        self::$gitType = $gitType;

        $access_token = self::checkAccessToken();

        $obj = self::getObj();

        echo $obj::getWebhooks($access_token, ...$arg);
    }

    public static function add(...$arg): void
    {
    }

    /**
     * @param mixed ...$arg
     *
     * @throws Exception
     */
    public static function delete(...$arg): void
    {
        $gitType = $arg[0];

        unset($arg[0]);

        self::$gitType = $gitType;

        $access_token = self::checkAccessToken();

        $obj = self::getObj();
        var_dump(...$arg);
        echo $obj::unsetWebhooks($access_token, ...$arg);
    }
}
