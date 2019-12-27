<?php

declare(strict_types=1);

namespace PCIT\Framework\Foundation;

use Pimple\Container;

class Application extends Container
{
    public static $instance;

    public $basePath;

    public $serviceProviders;

    public $environmentPath;

    public $environmentFile;

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        // 设置单例
        static::$instance = $this;

        $this['app'] = $this;

        $this->basePath = $this['base_path'];

        $this->registerProviders();
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
