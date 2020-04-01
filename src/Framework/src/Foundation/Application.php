<?php

declare(strict_types=1);

namespace PCIT\Framework\Foundation;

use PCIT\Framework\Dotenv\Dotenv;
use PCIT\Framework\Support\Env;
use Pimple\Container;

class Application extends Container
{
    public static $instance;

    public $basePath;

    public $serviceProviders;

    public $environment;

    public $environmentPath;

    public $environmentFile;

    public $resolve = [];

    public $resolves = [];

    /**
     * @var bool
     */
    public $isDebug;

    /**
     * 返回当前 ENV.
     *
     * 传入 env, 判断是否与当前环境匹配
     *
     * @param string|array|null $env
     *
     * @return false|string
     */
    public function environment($env = null)
    {
        $current_env = $this->environment ?: Env::get('APP_ENV');

        if (null === $env) {
            return $current_env;
        }

        if (\is_array($env)) {
            return \in_array($current_env, $env, true);
        }

        return $env === $current_env;
    }

    private function resolveEnv(): void
    {
        $app_env = $this->environment();

        $env_file = Dotenv::load($app_env);

        $this->environmentFile = $env_file;
        $this->environmentPath = $this->basePath.\DIRECTORY_SEPARATOR.$this->environmentFile;
        $this->environment = config('app.env');
    }

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        // 设置单例
        static::$instance = $this;

        $this['app'] = $this;

        $this->basePath = $this['base_path'];

        $this->resolveEnv();

        $this->registerProviders();

        $this->isDebug = (bool) config('app.debug');
    }

    public function registerProviders(): void
    {
        $this->serviceProviders = config('app.providers');

        foreach ($this->serviceProviders as $provider) {
            $this->register(new $provider());
        }
    }

    // 解析
    public function make($abstract)
    {
        return $this[$abstract];
    }

    // 绑定单例
    public function singleton(string $abstract, $concrete = null): void
    {
        if (null === $concrete) {
            $concrete = $abstract;
        }

        $closure = $concrete;

        if (\is_string($concrete)) {
            $closure = function ($app) use ($concrete) {
                return new $concrete();
            };
        }

        $this[$abstract] = $closure;
    }

    // 简单绑定
    public function bind(string $abstract, $concrete): void
    {
        if (null === $concrete) {
            $concrete = $abstract;
        }

        $closure = $concrete;

        if (\is_string($concrete)) {
            $closure = function ($app) use ($concrete) {
                return new $concrete();
            };
        }

        $this[$abstract] = $this->factory($closure);
    }

    // 绑定实例
    public function instance($abstract, $instance): void
    {
        if (\is_string($instance)) {
            $instance = new $instance();
        }

        $this[$abstract] = $instance;
    }

    // 获取 app 实例
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function resolving($object, $closure = null): void
    {
        if (\is_callable($object)) {
            $this->resolve[] = $object;

            return;
        }

        $this->resolves[$object] = $closure;
    }

    public function offsetGet($id)
    {
        if ($this->resolve) {
            foreach ($this->resolve as $resolve) {
                $resolve($id, $this);
            }
        }

        if ($resolve = $this->resolves[$id] ?? false) {
            $resolve($id, $this);
        }

        return parent::offsetGet($id);
    }
}
