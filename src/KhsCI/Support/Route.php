<?php

namespace KhsCI\Support;

use Exception;

class Route
{
    public static $obj = [];

    public static $method = [];

    private static function getUrl()
    {
        $queryString = $_SERVER['QUERY_STRING'];

        if ((bool)$queryString) {
            $url = $_SERVER['REQUEST_URI'];
            $url = (explode('?', $url))[0];

        } else {
            $url = $_SERVER['REQUEST_URI'];
        }

        return $url = trim($url, '/');
    }

    private static function getMethond($type)
    {
        return strtoupper($type) === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param $url
     * @param $action
     * @return string
     * @throws Exception
     */
    public static function get($url, $action)
    {
        // 请求方法不匹配

        if (!self::getMethond('get')) {
            return 'not found';
        }

        if ($url === self::getUrl()) {
            // url 一致

            $array = explode('@', $action);

            $obj = 'App\\Http\\Controllers'.'\\'.$array[0];

            $method = $array[1] ?? false;

            if (true === class_exists($obj) && method_exists($obj, $method)) {

                $obj = new $obj;

                if (!$method) {
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

    public static function post()
    {

    }
}