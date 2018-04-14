<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Error;
use Closure;
use Exception;

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

    /**
     * @param $targetUrl
     * @param $action
     */
    private static function exec($targetUrl, $action): void
    {
        // ?a=1&b=2
        $queryString = $_SERVER['QUERY_STRING'];

        if ((bool)$queryString) {
            $url = $_SERVER['REQUEST_URI'];
            // 使用 ? 分隔 url
            $url = (explode('?', $url))[0];
        } else {
            $url = $_SERVER['REQUEST_URI'];
        }

        $url = trim($url, '/');

        if ($targetUrl === $url) {
            if ($action instanceof Closure) {
                echo $action();
                exit(0);
            }

            $array = explode('@', $action);

            $obj = 'App\\Http\\Controllers'.'\\'.$array[0];
            // 没有 @ 说明是 __invoke() 方法
            $method = $array[1] ?? '__invoke';

            if (true === class_exists($obj)) {
                $obj = new $obj();

                try {
                    if ('__invoke' === $method) {
                        $obj();
                    } elseif ($method) {
                        $obj->$method();
                    }
                } catch (Error $e) {
                    // 捕获类方法不存在错误
                    echo $e->getMessage();
                }

                // 处理完毕，退出
                exit(0);
            } else {
                self::$obj[] = $obj;
                self::$method[] = $method;
            }
        }// url 不一致
    }

    private static function getMethod($type)
    {
        return strtoupper($type) === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param $name
     * @param $arg
     *
     * @throws Exception
     */
    public static function __callStatic($name, $arg): void
    {
        //echo '1<hr>';
        // 请求方法不匹配
        // var_dump($name);
        if (!self::getMethod($name)) {
            throw new Exception('not found');
        }

        self::exec($arg[0], $arg[1]);
    }
}
