<?php

declare(strict_types=1);

namespace PCIT\Support;

use Closure;
use Exception;
use Throwable;

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
            $arg = self::getParameters(null, $action, $arg);
            self::$output = \call_user_func($action, ...$arg);

            throw new Exception('Finish', 200);
        }

        $array = explode('@', $action);

        $obj = 'App\\Http\\Controllers'.'\\'.$array[0];

        // 没有 @ 说明是 __invoke() 方法
        $method = $array[1] ?? '__invoke';

        if (!class_exists($obj)) {
            // 类不存在，返回
            self::$obj[] = $obj;
            self::$method[] = $method;

            return;
        }

        // 获取方法参数
        $args = self::getParameters($obj, $method, $arg);
        // 获取类构造函数参数
        $construct_args = self::getParameters($obj, '__construct');

        // var_dump($args);
        // var_dump($construct_args);

        $instance = new $obj(...$construct_args);

        try {
            $response = '__invoke' === $method ?
                $instance(...$args) : $instance->$method(...$args);

            self::$output = $response;
        } catch (\Throwable $e) {
            // 捕获类方法不存在错误
            $code = $e->getCode();

            0 === $code && $code = 500;

            throw new Exception($e->getMessage(), $code, $e);
        }

        // 处理完毕，退出
        throw new Exception('Finish', 200);
    }

    /**
     * 获取方法参数列表.
     */
    private static function getParameters($obj = null, $method, $arg = [])
    {
        try {
            $reflection = $obj ?
                new \ReflectionMethod($obj, $method) : new \ReflectionFunction($method);
        } catch (Throwable $e) {
            return [];
        }

        // 获取方法的参数列表
        $method_parameters = $reflection->getParameters();

        // var_dump($method_parameters);

        // 是否废弃
        if ($reflection->isDeprecated()) {
            echo '已废弃';
        }

        // 通过检查注释，查看是否被废弃.
        if ($reflection->getDocComment()) {
            if (strpos($reflection->getDocComment(), '@deprecated')) {
                self::$obj[] = $obj;
                self::$method[] = $method;
                throw new Exception("$obj::$method is deprecated", 500);
            }
        }

        $args = [];

        // 遍历
        foreach ($method_parameters as $key => $parameter) {
            // 获取参数类型
            $parameter_class = $parameter->getClass()->name ?? null;

            // 可变参数列表 function demo(...$args){}
            if ($parameter->isVariadic()) {
                $args = array_merge($args, $arg);

                break;
            }

            if ($parameter_class) {
                try {
                    $args[$key] = app($parameter_class);
                } catch (Throwable $e) {
                    // 参数提示为类，获取构造函数参数
                    $construct_args = self::getParameters($parameter_class, '__construct');
                    $args[$key] = new $parameter_class(...$construct_args);
                }
            } else {
                // 参数类型不是类型实例
                $args[$key] = array_shift($arg);
            }
        }

        return $args;
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
        // $queryString = $_SERVER['QUERY_STRING'];
        $queryString = app('request')->query->all();

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
        // return strtoupper($type) === $_SERVER['REQUEST_METHOD'];
        return strtoupper($type) === app('request')->server->get('REQUEST_METHOD');
    }

    /**
     * @param $name
     * @param $arg
     *
     * @throws Exception
     */
    public static function __callStatic($name, $arg): void
    {
        if ('match' === $name) {
            $methods = $arg[0];

            array_shift($arg);

            if (\is_string($methods)) {
                return;
            }

            foreach ($methods as $key) {
                if (self::checkMethod($key)) {
                    self::handle(...$arg);

                    return;
                }
            }

            return;
        }

        if ('any' !== $name && !self::checkMethod($name)) {
            return;
        }

        self::handle(...$arg);
    }
}
