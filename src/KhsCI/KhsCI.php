<?php

declare(strict_types=1);

namespace KhsCI;

use Curl\Curl;
use Exception;
use KhsCI\Support\Config;
use Pimple\Container;
use TencentAI\TencentAI;
use WeChat\WeChat;

/**
 * 核心方法 注入类（依赖），之后通过调用属性或方法，获取类.
 *
 * $container->register();
 *
 * $container['a'] = new A();
 *
 * $a = $container['a'];
 *
 * @property Service\GitHubApp\Installations      $github_apps_installations
 * @property Service\OAuth\Coding                 $oauth_coding
 * @property Service\OAuth\GitHub                 $oauth_github
 * @property Service\OAuth\GitHubApp              $oauth_github_app
 * @property Service\OAuth\Gitee                  $oauth_gitee
 * @property Service\Issue\Assignees              $issue_assignees
 * @property Service\Issue\Comments               $issue_comments
 * @property Service\Issue\Events                 $issue_events
 * @property Service\Issue\Issues                 $issue
 * @property Service\Issue\Labels                 $issue_labels
 * @property Service\Issue\Milestones             $issue_milestones
 * @property Service\Issue\Timeline               $issue_timeline
 * @property Service\Organizations\GitHubClient   $github_orgs
 * @property Service\Repositories\Collaborators   $repo_collaborators
 * @property Service\Repositories\Status          $repo_status
 * @property Service\Repositories\Webhooks        $repo_webhooks
 * @property Service\PullRequest\GitHubClient     $github_pull_request
 * @property Service\Webhooks\Webhooks            $webhooks
 * @property Service\Build\Build                  $build
 * @property TencentAI                            $tencent_ai
 * @property Service\Users\GitHubClient           $user_basic_info
 * @property Service\Checks\Run                   $check_run
 * @property Service\Checks\Suites                $check_suites
 * @property Service\Checks\MarkDown              $check_md
 * @property Curl                                 $curl
 * @property WeChat                               $wechat
 * @property Service\WeChat\Template\WeChatClient $wechat_template_message
 */
class KhsCI extends Container
{
    /**
     * 服务提供器数组.
     */
    protected $providers = [
        Providers\ChecksProvider::class,
        Providers\GitHubAppProvider::class,
        Providers\IssueProvider::class,
        Providers\OAuthProvider::class,
        Providers\BuildProvider::class,
        Providers\OrganizationsProvider::class,
        Providers\RepositoriesProvider::class,
        Providers\PullRequestProvider::class,
        Providers\TencentAIProvider::class,
        Providers\UserProvider::class,
        Providers\WebhooksProvider::class,
        Providers\WeChatProvider::class,
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
     * @param array  $config
     * @param string $git_type
     *
     * @throws Exception
     */
    public function __construct(array $config = [], string $git_type = 'github')
    {
        parent::__construct($config);

        // 在容器中注入类

        $this['config'] = Config::config($config, $git_type);

        if ($this['config']['github']['access_token'] ?? false) {
            $this['curl'] = new Curl(
                null,
                false,
                [
                    'Authorization' => 'token '.$this['config']['github']['access_token'],
                    'Accept' => 'application/vnd.github.machine-man-preview+json',
                ]
            );
        } elseif ($this['config']['github_app']['access_token'] ?? false) {
            $this['curl'] = new Curl(
                null,
                false,
                [
                    'Authorization' => 'token '.$this['config']['github_app']['access_token'],
                    'Accept' => 'application/vnd.github.machine-man-preview+json',
                ]
            );
        } elseif ($this['config']['gitee']['access_token'] ?? false) {
            $this['curl'] = new Curl(
                null,
                false,
                [
                    'Authorization' => 'token '.$this['config']['gitee']['access_token'],
                ]
            );
        } else {
            $this['curl'] = new Curl();
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
