<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Closure;
use Error;
use Exception;

/**
 * Class Route.
 *
 * @method static get($url, $action)
 * @method static post($url, $action)
 * @method static delete($url, $action)
 */
class Route
{
    public static $obj = [];

    public static $method = [];

    private static function return($response)
    {

        if (is_array($response)) {
            header('content-type: application/json');
            echo json_encode($response);
        } elseif (is_string($response) or is_int($response)) {
            echo $response;
        }
    }

    /**
     * @param $action
     * @param mixed ...$arg
     * @throws Exception
     */
    private static function make($action, ...$arg): void
    {
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
                    $response = $obj(...$arg);
                } else {
                    $response = $obj->$method(...$arg);
                }
                self::return($response);
            } catch (Error | Exception $e) {
                // 捕获类方法不存在错误
                $code = $e->getCode();

                if (0 === $code) {
                    $code = 404;
                }

                throw new Error($e->getMessage(), $code);
            }

            // 处理完毕，退出
            exit(0);
        } else {
            self::$obj[] = $obj;
            self::$method[] = $method;
        }
    }

    /**
     * @param $targetUrl
     * @param $action
     * @throws Exception
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

        $targetUrlArray = explode('/', $targetUrl);

        $offset = preg_grep('/{*}/', $targetUrlArray);

        $kArray = [];
        $array = [];

        if ($offset === []) {
            if ($targetUrl === $url) {// 传统 url
                self::make($action);
            } else {
                return;
            }
        } else { // 有 {id}
            $urlArray = explode('/', $url);

            if (count($targetUrlArray) === count($urlArray)) {
                foreach ($offset as $k => $v) {
                    $kArray[] = $k;
                    $array[] = $urlArray[$k];
                }

                foreach ($kArray as $k) {
                    unset($targetUrlArray[$k]);
                    unset($urlArray[$k]);
                }

                $targetUrlArray === $urlArray && self::make($action, ...$array);

                return;
            }
        }
    }

    private static function checkMethod($type)
    {
        return strtoupper($type) === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param $name
     * @param $arg
     * @throws Exception
     */
    public static function __callStatic($name, $arg): void
    {
        if (!self::checkMethod($name)) {
            return;
        }

        self::exec($arg[0], $arg[1]);
    }
}
