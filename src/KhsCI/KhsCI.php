<?php

declare(strict_types=1);

namespace KhsCI;

use Curl\Curl;
use Docker\Docker;
use Exception;
use KhsCI\Support\Config;
use KhsCI\Support\Git;
use PHPMailer\PHPMailer\PHPMailer;
use Pimple\Container;
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
 * @property Service\Activity\EventsGitHubClient            $activity_events
 * @property Service\Activity\FeedsGitHubClient             $activity_feeds
 * @property Service\Activity\NotificationsGitHubClient     $activity_notifications
 * @property Service\Activity\StarringGitHubClient          $activity_starring
 * @property Service\Activity\WatchingGitHubClient          $activity_watching
 * @property Service\Data\GitHubClient                      $data
 * @property Service\Deployment\GitHubClient                $deployment
 * @property Service\Gist\GitHubClient                      $gist
 * @property Service\Gist\CommentsGitHubClient              $gist_comments
 * @property Service\GitHubApp\Client                       $github_apps_installations
 * @property Service\OAuth\GitHubClient                     $oauth
 * @property Service\Issue\AssigneesGitHubClient            $issue_assignees
 * @property Service\Issue\CommentsGitHubClient             $issue_comments
 * @property Service\Issue\EventsGitHubClient               $issue_events
 * @property Service\Issue\GitHubClient                     $issue
 * @property Service\Issue\LabelsGitHubClient               $issue_labels
 * @property Service\Issue\MilestonesGitHubClient           $issue_milestones
 * @property Service\Miscellaneous\GitHubClient             $miscellaneous
 * @property Service\Organizations\GitHubClient             $orgs
 * @property Service\Repositories\CollaboratorsGitHubClient $repo_collaborators
 * @property Service\Repositories\CommitsGitHubClient       $repo_commits
 * @property Service\Repositories\CommunityGitHubClient     $repo_community
 * @property Service\Repositories\ContentsGitHubClient      $repo_contents
 * @property Service\Repositories\MergingGitHubClient       $repo_merging
 * @property Service\Repositories\ReleasesGitHubClient      $repo_releases
 * @property Service\Repositories\StatusGitHubClient        $repo_status
 * @property Service\Repositories\WebhooksCodingClient      $repo_webhooks
 * @property PHPMailer                                      $mail
 * @property Service\PullRequest\GitHubClient               $pull_request
 * @property Service\Webhooks\GitHubClient                  $webhooks
 * @property Service\Build\Client                           $build
 * @property Service\Build\Cleanup                          $build_cleanup
 * @property \TencentAI\Application                         $tencent_ai
 * @property Service\Users\GitHubClient                     $user_basic_info
 * @property Service\Checks\Run                             $check_run
 * @property Service\Checks\Suites                          $check_suites
 * @property Service\Checks\MarkDown                        $check_md
 * @property Curl                                           $curl
 * @property Docker                                         $docker
 * @property WeChat                                         $wechat
 * @property Service\WeChat\Template\WeChatClient           $wechat_template_message
 */
class KhsCI extends Container
{
    // 服务提供器数组.
    protected $providers = [
        Providers\ActivityProvider::class,
        Providers\BuildProvider::class,
        Providers\ChecksProvider::class,
        Providers\CurlProvider::class,
        Providers\DataProvider::class,
        Providers\DeploymentProvider::class,
        Providers\DockerProvider::class,
        Providers\GistProvider::class,
        Providers\GitHubAppProvider::class,
        Providers\IssueProvider::class,
        Providers\MiscellaneousProvider::class,
        Providers\OAuthProvider::class,
        Providers\OrganizationsProvider::class,
        Providers\PHPMailerProvider::class,
        Providers\PullRequestProvider::class,
        Providers\RepositoriesProvider::class,
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
        // 取得服务提供器数组.
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
        $this['git_type'] = $git_type;

        $this['class_name'] = Git::getClassName($git_type).'Client';

        $this['config'] = Config::config($config, $git_type);

        if ($this['config']['github']['access_token'] ?? false) {
            $this['curl_config'] = [null, false,
                [
                    'Authorization' => 'token '.$this['config']['github']['access_token'],
                    'Accept' => 'application/vnd.github.machine-man-preview+json',
                ],
            ];
        } elseif ($this['config']['gitee']['access_token'] ?? false) {
            $this['curl_config'] = [null, false,
                [
                    'Authorization' => 'token '.$this['config']['gitee']['access_token'],
                ],
            ];
        } elseif ($this['config']['coding']['access_token'] ?? false) {
            $this['curl_config'] = [
                null, false,
                [
                    'Authorization' => 'access_token '.$this['config']['coding']['access_token'],
                ],
            ];
        } else {
            $this['curl_config'] = [];
        }

        set_time_limit(0);

        $this['curl_timeout'] = 100;

        // 注册服务提供器
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
        // $example->调用不存在属性时
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
