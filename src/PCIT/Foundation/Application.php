<?php

declare(strict_types=1);

namespace PCIT\Foundation;

use Pimple\Container;

class Application extends Container
{
    public static $instance;

    public $providers;

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        // 设置单例
        static::$instance = $this;

        $this['app'] = $this;

        $config_app = require base_path().'framework/config/app.php';
        $this->providers = $config_app['providers'];
        $this->registerProviders();
    }

    public function registerProviders(): void
    {
        foreach ($this->providers as $provider) {
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

    public function resolving(): void
    {
    }
}
