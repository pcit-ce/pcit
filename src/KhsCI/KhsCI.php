<?php

declare(strict_types=1);

namespace KhsCI;

use Curl\Curl;
use Exception;
use KhsCI\Support\Config;
use Pimple\Container;

/**
 * 核心方法 注入类（依赖），之后通过调用属性或方法，获取类.
 *
 * $container->register();
 *
 * $container['a'] = new A();
 *
 * $a = $container['a'];
 *
 * @property Service\OAuth\Coding               $OAuthCoding
 * @property Service\OAuth\GitHub               $OAuthGitHub
 * @property Service\OAuth\Gitee                $OAuthGitee
 * @property Service\Repositories\Collaborators $repo_collaborators
 * @property Service\Repositories\Status        $repo_status
 */
class KhsCI extends Container
{
    /**
     * 服务提供器数组.
     */
    protected $providers = [
        Providers\OAuthProvider::class,
        Providers\RepositoriesProvider::class,
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

    /**
     * KhsCI constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        /*
         * 在容器中注入类
         */
        $this['config'] = Config::config($config);

        $this['curl'] = new Curl();

        if ($this['config']['github']['access_token'] ?? false) {
            echo 1;
            $this['curl'] = new Curl(
                null,
                false,
                ['Authorization' => 'token '.$this['config']['github']['access_token']]
            );
        }

        set_time_limit(0);

        $this['curl']->setTimeout(100);

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
