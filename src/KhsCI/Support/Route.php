<?php

declare(strict_types=1);

namespace KhsCI\Support;

/**
 * Class Route.
 *
 * @method static get($url, $action)
 * @method static post($url, $action)
 */
class Route
{
    public static $obj = [];

    public static $method = [];

    private static function getUrl($targetUrl, $action)
    {
        $queryString = $_SERVER['QUERY_STRING'];

        if ((bool) $queryString) {
            $url = $_SERVER['REQUEST_URI'];
            $url = (explode('?', $url))[0];
        } else {
            $url = $_SERVER['REQUEST_URI'];
        }

        $url = trim($url, '/');

        if ($targetUrl === $url) {
            // url 一致

            $array = explode('@', $action);

            $obj = 'App\\Http\\Controllers'.'\\'.$array[0];

            $method = $array[1] ?? false;

            if (true === class_exists($obj) && method_exists($obj, $method)) {
                $obj = new $obj();

                if ($method) {
                    $obj->$method();
                }
                // 处理完毕，退出
                exit(0);
            } else {
                self::$obj[] = $obj;
                self::$method[] = $method;
            }
        }// url 不一致
        return null;
    }

    private static function getMethod($type)
    {
        return strtoupper($type) === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param $name
     * @param $arg
     *
     * @return string
     */
    public static function __callStatic($name, $arg)
    {
        // 请求方法不匹配

        if (!self::getMethod($name)) {
            return 'not found';
        }

        return self::getUrl($arg[0], $arg[1]);
    }
}
