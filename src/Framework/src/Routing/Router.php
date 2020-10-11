<?php

declare(strict_types=1);

namespace PCIT\Framework\Routing;

use Closure;
use Exception;
use PCIT\Framework\Http\Request;
use PCIT\Framework\Routing\Exceptions\SkipThisRouteException;
use PCIT\Framework\Routing\Exceptions\SuccessHandleRouteException;
use PCIT\GPI\Support\Git;
use Throwable;

/**
 * @method void get(string $url, array|callable|string $action)
 * @method void post(string $url, array|callable|string $action)
 * @method void put(string $url, array|callable|string $action)
 * @method void patch(string $url, array|callable|string $action)
 * @method void delete(string $url, array|callable|string $action)
 * @method void options(string $url, array|callable|string $action)
 */
class Router
{
    public $obj = [];

    public $method = [];

    public $response;

    /**
     * @param array|callable|string $action e.g.
     *                                      'Controller@method'
     *                                      [\App\Http\Controllers\Controller::class,'method']
     *                                      fn() => 1
     * @param mixed                 ...$arg e.g. 'user/{id}' => 'user/1' => result [1]
     */
    private function make($action, ...$arg): void
    {
        if ($action instanceof Closure) {
            // 闭包
            $arg = $this->getParameters(null, $action, $arg);
            $this->matchVersion(null, $action);
            $this->response = \call_user_func_array($action, $arg);

            throw new SuccessHandleRouteException();
        }

        if (\is_array($action)) {
            $array = $action;

            $obj = $array[0];
        } else {
            $array = explode('@', $action);

            $obj = 'App\\Http\\Controllers'.'\\'.$array[0];
        }
        // 没有 @ 说明是 __invoke() 方法
        $method = $array[1] ?? '__invoke';

        $action = $obj.'@'.$method;

        if (!class_exists($obj)) {
            // 类不存在，返回
            $this->obj[] = $obj;
            $this->method[] = $method;

            return;
        }

        $this->matchVersion($obj, $method);
        $rc = new \ReflectionClass($obj);

        // 获取类构造函数参数
        $construct_args = $this->getParameters($rc->getName());

        // var_dump($args);
        // var_dump($construct_args);

        // 构造函数
        $instance = $rc->newInstanceArgs($construct_args);

        try {
            if ($rc->hasMethod($method)) {
                $rm = new \ReflectionMethod($instance, $method);

                // 获取方法参数
                $args = $this->getParameters($rc->getName(), $rm->getName(), $arg);

                $this->response = $rm->invokeArgs($instance, $args);
            } elseif ($rc->hasMethod('__call')) {
                // 方法不存在，尝试调用 __call
                $rm = new \ReflectionMethod($instance, '__call');

                $this->response = $rm->invokeArgs($instance, [
                    $method, $arg,
                ]);
            } else {
                // 方法 以及 __call 方法均不存在
                throw new \Exception('Controller '.$action.'not Found', 404);
            }
        } catch (\ReflectionException $e) {
            throw new Exception($e->getMessage(), 404, $e);
        } catch (SuccessHandleRouteException $e) {
            // 请求成功
            throw $e;
        } catch (\Throwable $e) {
            // 捕获异常
            throw $e;
        }

        // 处理完毕，退出
        throw new SuccessHandleRouteException();
    }

    /**
     * @param \ReflectionFunction|\ReflectionMethod $reflection
     */
    private function isDeprecated($reflection): void
    {
        // 是否废弃
        if ($reflection->isDeprecated()) {
            echo '已废弃';
        }

        $attrs = $reflection->getAttributes();

        foreach ($attrs as $attr) {
            if (\PCIT\Framework\Attributes\Deprecated::class === $attr->getName()) {
                throw new \Exception('deprecated by attributes', 403);
            }
        }

        // 通过检查注释，查看是否被废弃.
        if ($reflection->getDocComment()) {
            if (strpos($reflection->getDocComment(), '@deprecated')) {
                //$this->obj[] = $reflection->getDeclaringClass();
                //$this->method[] = $reflection->getName();

                throw new Exception('deprecated by phpdoc', 403);
            }
        }
    }

    /**
     * @param \ReflectionFunction|\ReflectionMethod $reflection
     */
    public function handleMiddleware($reflection): void
    {
        $attrs = $reflection->getAttributes();

        foreach ($attrs as $attr) {
            // var_dump($attr->getName());
            // continue;
            if (\PCIT\Framework\Attributes\Middleware::class === $attr->getName()) {
                $result = $attr->newInstance()
                    ->middleware->handle(
                        app(Request::class),
                        function (Request $request) {
                            return $request;
                        },
                        null
                    );

                if ($result instanceof Request) {
                    return;
                }

                $this->response = $result;

                throw new SuccessHandleRouteException();
            }
        }
    }

    /**
     * 获取方法参数列表.
     *
     * @param null|mixed|object   $obj
     * @param null|Closure|string $method
     * @param mixed               $arg
     */
    public function getParameters($obj = null, $method = null, $arg = []): array
    {
        try {
            $reflection = $obj ?
                new \ReflectionMethod($obj, $method ?? '__construct') : new \ReflectionFunction($method);
        } catch (Throwable $e) {
            return [];
        }

        // 是否废弃
        $this->isDeprecated($reflection);

        $this->handleMiddleware($reflection);

        // 获取方法的参数列表
        $method_parameters = $reflection->getParameters();

        // var_dump($method_parameters);

        $args = [];

        // 遍历
        foreach ($method_parameters as $key => $parameter) {
            // 获取参数类型
            $parameter_class = null;

            if ($parameter->hasType()) {
                $parameter_class = $parameter->getType()->getName();
            }

            // 可变参数列表 function demo(...$args){}
            if ($parameter->isVariadic()) {
                $args = array_merge($args, $arg);

                break;
            }

            if ($parameter_class and !$parameter->getType()->isBuiltin()) {
                // 不是内置类
                try {
                    // 首先尝试从容器中解析
                    $args[$key] = app($parameter_class);
                } catch (Throwable $e) {
                    // 未注入到容器中
                    // 参数提示为类，获取构造函数参数
                    $construct_args = $this->getParameters($parameter_class);
                    $args[$key] = (new \ReflectionClass($parameter_class))
                        ->newInstanceArgs($construct_args);
                }
            } else {
                // 参数类型不是类型实例
                // 参数类型是内置类型
                $args[$key] = array_shift($arg);
            }
        }

        return $args;
    }

    public function matchVersion($obj, $method): void
    {
        $reflection = $obj ?
            new \ReflectionMethod($obj, $method ?? '__construct') : new \ReflectionFunction($method);

        $attrs = $reflection->getAttributes();

        $accept = \Request::headers()->get('Accept');

        if (!$accept) {
            return;
        }

        $attrExists = false;
        $versionHeader = false;

        foreach ($attrs as $attr) {
            if (\PCIT\Framework\Attributes\APIVersion::class === $attr->getName()) {
                $attrExists = true;

                foreach (explode(',', $accept) as $item) {
                    if (preg_match('/^(application\/vnd.pcit.v).*.(\+json$)/', $item)) {
                        $versionHeader = true;
                    }
                    if (
                        'application/vnd.pcit.'.$attr->getArguments()[0].'+json'
                        === $item
                    ) {
                        // 版本匹配
                        return;
                    }
                }
            }
        }

        if (!$attrExists) {
            return;
        }

        if ($attrExists and $versionHeader) {
            throw new SkipThisRouteException();
        }

        if (!$versionHeader) {
        }

        // 版本属性
        // 版本请求头

        // attr + header    next
        // !attr + header   skip
        // !attr + !header  skip
        // attr + !header
    }

    /**
     * @param string                $targetUrl route 定义的 URL
     * @param array|\Closure|string $action
     */
    private function handle(string $targetUrl, $action): void
    {
        // ?a=1&b=2
        // $queryString = $_SERVER['QUERY_STRING'];
        /** @var \PCIT\Framework\Http\Request */
        $request = app('request');
        $queryString = $request->query->all();

        //$url = $_SERVER['REQUEST_URI'];
        $url = $request->getPathInfo();

        // if(explode('/',$url)[1] === 'api'){
        //     $targetUrl = 'api/'.$targetUrl;
        // }

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
            if ($targetUrl === $url) { // 传统 url
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
                    unset($targetUrlArray[$k], $urlArray[$k]);
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
     * @param string $name e.g. get
     * @param array  $arg  e.g. 'path/{id}' 'Controller@method'
     */
    public function __call(string $name, array $arg): void
    {
        try {
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
        } catch (SkipThisRouteException $e) {
            return;
        }
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

    public function getResponse()
    {
        return $this->response;
    }
}
