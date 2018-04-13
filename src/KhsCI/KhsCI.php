<?php

declare(strict_types=1);

namespace KhsCI;

use Exception;
use Pimple\Container;
use KhsCI\Support\Config;

/**
 * 核心方法 注入类（依赖），之后通过调用属性或方法，获取类.
 *
 * $container->register();
 *
 * $container['a'] = new A();
 *
 * $a = $container['a'];
 *
 * @property \KhsCI\Service\OAuth\Coding $OAuthCoding
 * @property \KhsCI\Service\OAuth\GitHub $OAuthGitHub
 * @property \KhsCI\Service\OAuth\Gitee  $OAuthGitee
 */
class KhsCI extends Container
{
    /**
     * 服务提供器数组.
     */
    protected $providers = [
        Providers\OAuthProvider::class,
    ];

    /**
     * 注册服务提供器.
     */
    private function registerProviders(): void
    {
        /*
         * 取得服务提供器数组.
         */
        foreach ($this->providers as $k) {
            $this->register(new $k());
        }
    }

    public function __construct()
    {
        parent::__construct();
        /*
         * 在容器中注入类
         */
        $this['config'] = Config::config();

        /*
         * 注册服务提供器
         */
        $this->registerProviders();
    }

    /**
     * 通过调用属性，获取对象
     *
     * @param $name
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function __get($name)
    {
        /*
         * $example->调用不存在属性时
         */
        if (isset($this[$name])) {
            return $this[$name];
        }

        throw new Exception('Not found');
    }

    /**
     * 通过调用方法，获取对象
     *
     * @param $name
     * @param $arguments
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        throw new Exception('Not found');
    }
}
