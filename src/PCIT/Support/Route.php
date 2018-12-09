<?php

declare(strict_types=1);

namespace PCIT\Support;

use Closure;
use Exception;

/**
 * @method static get(string $url, Closure|string $action)
 * @method static post(string $url, Closure|string $action)
 * @method static put(string $url, Closure|string $action)
 * @method static patch(string $url, Closure|string $action)
 * @method static delete(string $url, Closure|string $action)
 * @method static options(string $url, Closure|string $action)
 */
class Route
{
    public static $obj = [];

    public static $method = [];

    public static $output = null;

    /**
     * @param string|Closure $action
     * @param mixed          ...$arg
     *
     * @throws Exception
     */
    private static function make($action, ...$arg): void
    {
        if ($action instanceof Closure) {
            self::$output = \call_user_func($action, ...$arg);

            throw new Exception('Finish', 200);
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
                self::$output = $response;
            } catch (\Throwable $e) {
                // 捕获类方法不存在错误
                $code = $e->getCode();

                0 === $code && $code = 500;

                throw new Exception($e->getMessage(), $code, $e);
            }

            // 处理完毕，退出
            throw new Exception('Finish', 200);
        } else {
            self::$obj[] = $obj;
            self::$method[] = $method;
        }
    }

    /**
     * @param $targetUrl
     * @param $action
     *
     * @throws Exception
     */
    private static function handle($targetUrl, $action): void
    {
        // ?a=1&b=2
        $queryString = $_SERVER['QUERY_STRING'];

        //$url = $_SERVER['REQUEST_URI'];
        $url = app('request')->getPathInfo();

        if ((bool) $queryString) {
            // 使用 ? 分隔 url
            $url = (explode('?', $url))[0];
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

            if (\count($targetUrlArray) === \count($urlArray)) {
                if (!(false === ($int = array_search('{git_type}', $targetUrlArray, true)))) {
                    if (!\in_array($urlArray[$int], Git::SUPPORT_GIT_ARRAY, true)) {
                        return;
                    }
                }

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
     *
     * @throws Exception
     */
    public static function __callStatic($name, $arg): void
    {
        if (!self::checkMethod($name)) {
            return;
        }

        self::handle(...$arg);
    }
}
