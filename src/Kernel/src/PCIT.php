<?php

declare(strict_types=1);

namespace PCIT;

use Curl\Curl;
use Docker\Docker;
use PCIT\Support\Config;
use PCIT\Support\Git;
use PHPMailer\PHPMailer\PHPMailer;
use Pimple\Container;
use Pimple\Exception\UnknownIdentifierException;
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
 * @property GitHub\Service\Activity\EventsClient            $activity_events
 * @property GitHub\Service\Activity\FeedsClient             $activity_feeds
 * @property GitHub\Service\Activity\NotificationsClient     $activity_notifications
 * @property GitHub\Service\Activity\StarringClient          $activity_starring
 * @property GitHub\Service\Activity\WatchingClient          $activity_watching
 * @property GitHub\Service\Data\Client                      $data
 * @property GitHub\Service\Deployment\Client                $deployment
 * @property GitHub\Service\Gist\Client                      $gist
 * @property GitHub\Service\Gist\CommentsClient              $gist_comments
 * @property GitHub\Service\GitHubApp\Client                 $github_apps
 * @property GitHub\Service\GitHubApp\InstallationsClient    $github_apps_installations
 * @property GitHub\Service\GitHubApp\AccessTokenClient      $github_apps_access_token
 * @property GitHub\Service\OAuth\Client                     $oauth
 * @property GitHub\Service\Issue\AssigneesClient            $issue_assignees
 * @property GitHub\Service\Issue\CommentsClient             $issue_comments
 * @property GitHub\Service\Issue\EventsClient               $issue_events
 * @property GitHub\Service\Issue\Client                     $issue
 * @property GitHub\Service\Issue\LabelsClient               $issue_labels
 * @property GitHub\Service\Issue\MilestonesClient           $issue_milestones
 * @property GitHub\Service\Miscellaneous\Client             $miscellaneous
 * @property GitHub\Service\Organizations\Client             $orgs
 * @property GitHub\Service\Repositories\BranchesClient      $repo_branches
 * @property GitHub\Service\Repositories\CollaboratorsClient $repo_collaborators
 * @property GitHub\Service\Repositories\CommitsClient       $repo_commits
 * @property GitHub\Service\Repositories\CommunityClient     $repo_community
 * @property GitHub\Service\Repositories\ContentsClient      $repo_contents
 * @property GitHub\Service\Repositories\MergingClient       $repo_merging
 * @property GitHub\Service\Repositories\ReleasesClient      $repo_releases
 * @property GitHub\Service\Repositories\StatusClient        $repo_status
 * @property GitHub\Service\Repositories\WebhooksClient      $repo_webhooks
 * @property PHPMailer                                       $mail
 * @property GitHub\Service\PullRequest\Client               $pull_request
 * @property GitHub\Service\Webhooks\Server                  $webhooks
 * @property \PCIT\Runner\Client                             $runner_job_generator
 * @property \PCIT\Runner\Agent\Docker\DockerHandler         $runner_agent_docker
 * @property \TencentAI\TencentAI                            $tencent_ai
 * @property GitHub\Service\Users\Client                     $user_basic_info
 * @property GitHub\Service\Checks\Run                       $check_run
 * @property GitHub\Service\Checks\Suites                    $check_suites
 * @property Curl                                            $curl
 * @property Docker                                          $docker
 * @property WeChat                                          $wechat
 * @property Service\Kernel\WeChat\Template\WeChatClient     $wechat_template_message
 */
class PCIT extends Container
{
    /**
     * 服务提供器数组.
     */
    protected $providers = [
        Providers\ActivityProvider::class,
        Providers\ChecksProvider::class,
        // Providers\CurlProvider::class,
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
        Providers\RunnerProvider::class,
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
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * PCIT constructor.
     *
     * @throws \Exception
     */
    public function __construct(array $config = [],
                                string $git_type = 'github',
                                string $accessToken = null)
    {
        parent::__construct($config);

        set_time_limit(0);

        $this['curl_timeout'] = $this->curl_timeout = 60 * 5;

        $this->setGitType($git_type);

        $this->setConfig($config, $git_type);

        $this->setAccessToken($accessToken);

        // 注册服务提供器
        $this->registerProviders();
    }

    public function setConfig($config, $git_type): void
    {
        $this['config'] = Config::config($config, $git_type);
    }

    public function setGitType($git_type = 'github')
    {
        $this['git_type'] = $this->git_type = $git_type;

        $this['class_name'] = $this->class_name = Git::getClassName($git_type);

        return $this;
    }

    public function setAccessToken(?string $accessToken = null)
    {
        if ($accessToken) {
            $this->setConfig(
            [$this['git_type'].'_access_token' => $accessToken], $this['git_type']);
        }

        if ($this['config']['github']['access_token'] ?? false) {
            $this['curl_config'] = [null, false,
                [
                    'Authorization' => 'token '.$this['config']['github']['access_token'],
                    'Accept' => 'application/vnd.github.machine-man-preview+json,application/vnd.github.speedy-preview+json',
                    'Content-Type' => 'application/json',
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
                    'x-coding-token' => 'access_token '.$this['config']['coding']['access_token'],
                ],
            ];
        } else {
            $this['curl_config'] = [];
        }

        $curl = new Curl(...$this['curl_config']);
        $curl->setTimeout($this['curl_timeout']);

        $this['curl'] = $curl;

        return $this;
    }

    /**
     * 通过调用属性，获取对象
     *
     * @param $name
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __get($name)
    {
        // $example->调用不存在属性时
        if (isset($this[$name])) {
            return $this[$name];
        }

        throw new UnknownIdentifierException($name);
    }

    /**
     * 通过调用方法，获取对象
     *
     * @param $name
     * @param $arguments
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        throw new UnknownIdentifierException($name);
    }
}
