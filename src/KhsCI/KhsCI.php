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
 * @property Service\GitHubApps\Installations   $github_apps_installations
 * @property Service\OAuth\Coding               $oauth_coding
 * @property Service\OAuth\GitHub               $oauth_github
 * @property Service\OAuth\GitHubApp            $oauth_github_app
 * @property Service\OAuth\Gitee                $oauth_gitee
 * @property Service\Repositories\Collaborators $repo_collaborators
 * @property Service\Repositories\Status        $repo_status
 * @property Service\Repositories\Webhooks      $repo_webhooks
 * @property Service\Webhooks\Coding            $webhooks_coding
 * @property Service\Webhooks\Gitee             $webhooks_gitee
 * @property Service\Webhooks\GitHub            $webhooks_github
 * @property Service\Queue\Queue                $queue
 * @property Service\Users\BasicInfo            $user_basic_info
 */
class KhsCI extends Container
{
    /**
     * 服务提供器数组.
     */
    protected $providers = [
        Providers\GitHubAppsProvider::class,
        Providers\OAuthProvider::class,
        Providers\QueueProvider::class,
        Providers\RepositoriesProvider::class,
        Providers\WebhooksProvider::class,
        Providers\UserProvider::class,
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
            $this['curl'] = new Curl(
                null,
                false,
                [
                    'Authorization' => 'token '.$this['config']['github']['access_token'],
                    'Accept' => 'application/vnd.github.machine-man-preview+json',
                ]
            );
        }

        if ($this['config']['github_app']['access_token'] ?? false) {
            $this['curl'] = new Curl(
                null,
                false,
                [
                    'Authorization' => 'token '.$this['config']['github_app']['access_token'],
                    'Accept' => 'application/vnd.github.machine-man-preview+json',
                ]
            );
        }

        if ($this['config']['gitee_app']['access_token'] ?? false) {
            $this['curl'] = new Curl(
                null,
                false,
                [
                    'Authorization' => 'token '.$this['config']['github_ee']['access_token'],
                ]
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
