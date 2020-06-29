<?php

declare(strict_types=1);

namespace PCIT\Framework\Routing;

use Closure;
use Exception;
use PCIT\Framework\Foundation\Http\SuccessException;
use PCIT\Support\Git;
use Throwable;

/**
 * @method get(string $url, Closure|string $action)
 * @method post(string $url, Closure|string $action)
 * @method put(string $url, Closure|string $action)
 * @method patch(string $url, Closure|string $action)
 * @method delete(string $url, Closure|string $action)
 * @method options(string $url, Closure|string $action)
 */
class Router
{
    public $obj = [];

    public $method = [];

    public $output = null;

    /**
     * @param string|Closure $action
     * @param mixed          ...$arg
     *
     * @throws \Exception
     */
    private function make($action, ...$arg): void
    {
        if ($action instanceof Closure) {
            $arg = $this->getParameters(null, $action, $arg);
            $this->output = \call_user_func($action, ...$arg);

            throw new SuccessException();
        }

        $array = explode('@', $action);

        $obj = 'App\\Http\\Controllers'.'\\'.$array[0];

        // 没有 @ 说明是 __invoke() 方法
        $method = $array[1] ?? '__invoke';

        if (!class_exists($obj)) {
            // 类不存在，返回
            $this->obj[] = $obj;
            $this->method[] = $method;

            return;
        }

        // 获取方法参数
        $args = $this->getParameters($obj, $method, $arg);
        // 获取类构造函数参数
        $construct_args = $this->getParameters($obj, '__construct');

        // var_dump($args);
        // var_dump($construct_args);

        $instance = new $obj(...$construct_args);

        try {
            $response = '__invoke' === $method ?
                $instance(...$args) : $instance->$method(...$args);

            $this->output = $response;
        } catch (\Throwable $e) {
            // 捕获类方法不存在错误
            $code = $e->getCode();

            0 === $code && $code = 500;

            throw new Exception($e->getMessage(), $code, $e);
        }

        // 处理完毕，退出
        throw new SuccessException();
    }

    /**
     * 获取方法参数列表.
     */
    private function getParameters($obj = null, $method = null, $arg = [])
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
                $this->obj[] = $obj;
                $this->method[] = $method;
                throw new Exception("$obj::$method is deprecated", 500);
            }
        }

        $args = [];

        // 遍历
        foreach ($method_parameters as $key => $parameter) {
            // 获取参数类型
            $parameter_class = null;

            if ($parameter->getType()) {
                $parameter_class = $parameter->getType()->getName();
            }

            // 可变参数列表 function demo(...$args){}
            if ($parameter->isVariadic()) {
                $args = array_merge($args, $arg);

                break;
            }

            if ($parameter_class and class_exists($parameter_class)) {
                try {
                    $args[$key] = app($parameter_class);
                } catch (Throwable $e) {
                    // 参数提示为类，获取构造函数参数
                    $construct_args = $this->getParameters($parameter_class, '__construct');
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
     * @param $targetUrl route 定义的 URL
     * @param $action
     *
     * @throws \Exception
     */
    private function handle($targetUrl, $action): void
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

        if ([] === $offset) {
            if ($targetUrl === $url) {// 传统 url
                $this->make($action);
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

                $targetUrlArray === $urlArray && $this->make($action, ...$array);

                return;
            }
        }
    }

    private function checkMethod($type)
    {
        // return strtoupper($type) === $_SERVER['REQUEST_METHOD'];
        return strtoupper($type) === app('request')->server->get('REQUEST_METHOD');
    }

    /**
     * @param $name
     * @param $arg
     *
     * @throws \Exception
     */
    public function __call($name, $arg): void
    {
        if ('match' === $name) {
            $methods = $arg[0];

            array_shift($arg);

            if (\is_string($methods)) {
                return;
            }

            foreach ($methods as $key) {
                if ($this->checkMethod($key)) {
                    $this->handle(...$arg);

                    return;
                }
            }

            return;
        }

        if ('any' !== $name && !$this->checkMethod($name)) {
            return;
        }

        $this->handle(...$arg);
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getObj()
    {
        return $this->obj;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
