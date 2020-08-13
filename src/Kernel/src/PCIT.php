<?php

declare(strict_types=1);

namespace PCIT;

use Curl\Curl;
use Docker\Docker;
use PCIT\Gitee\Gitee;
use PCIT\GitHub\GitHub;
use PCIT\Support\Config;
use PHPMailer\PHPMailer\PHPMailer;
use Pimple\Container;
use WeChat\WeChat;

/**
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
 * @property GitHub\Service\PullRequest\Client               $pull_request
 * @property GitHub\Service\Webhooks\Server                  $webhooks
 * @property GitHub\Service\Users\Client                     $user_basic_info
 * @property GitHub\Service\Checks\Run                       $check_run
 * @property GitHub\Service\Checks\Suites                    $check_suites
 * @property Curl                                            $curl
 * @property Docker                                          $docker
 * @property WeChat                                          $wechat
 * @property Service\Kernel\WeChat\Template\WeChatClient     $wechat_template_message
 * @property PHPMailer                                       $mail
 * @property \PCIT\Runner\JobGenerator                       $runner_job_generator
 * @property \PCIT\Runner\Agent\Docker\DockerHandler         $runner_agent_docker
 * @property \TencentAI\TencentAI                            $tencent_ai
 */
class PCIT extends Container
{
    /** @var array */
    private $gits;

    protected $providers = [
        // Providers\CurlProvider::class,
        Providers\PHPMailerProvider::class,
        Providers\DockerProvider::class,
        Providers\RunnerProvider::class,
        Providers\TencentAIProvider::class,
        Providers\WeChatProvider::class,
    ];

    private function registerProviders(): void
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * PCIT constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this['config'] = [
            'tencent_ai' => [
                'app_id' => config('ai.tencent.app.id'),
                'app_key' => config('ai.tencent.app.key'),
            ],
            'wechat' => [
                'app_id' => config('wechat.app.id'),
                'app_secret' => config('wechat.app.secret'),
                'token' => config('wechat.app.token'),
                'template_id' => config('wechat.template_id'),
                'open_id' => config('wechat.user_openid'),
            ],
        ];

        set_time_limit(0);

        $this['curl_timeout'] = $this->curl_timeout = 60 * 5;

        $curl = new Curl();
        $curl->setTimeout($this['curl_timeout']);

        $this['curl'] = $curl;

        // 注册服务提供器
        $this->registerProviders();
    }

    public function __get(string $name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        return $this->git()->$name;
    }

    public function __call(string $method, array $arguments)
    {
        if (isset($this[$method])) {
            return $this[$method];
        }

        return $this->git()->$method();
    }

    /**
     * @return \PCIT\GPI\GPI
     */
    public function git(?string $name = null, ?string $access_token = null)
    {
        $name = $name ?: 'github';

        if ((!$access_token) and ($git = $this->gits[$name] ?? false)) {
            return $git;
        }

        if ('github' === $name) {
            $git = new GitHub($this['tencent_ai'], $access_token);
        } elseif ('gitee' === $name) {
            $git = new Gitee($this['tencent_ai'], $access_token);
        } else {
            try {
                $class_name = config('git.'.$name.'.class_name');

                $git_class_name = 'PCIT\\'.$class_name.'\\'.$class_name;

                $git = new $git_class_name($this['tencent_ai'], $access_token);
            } catch (\Throwable $e) {
                throw new \Exception('can\'t find '.$name.' git providers or init meet error');
            }
        }

        return $this->gits[$name] = $git;
    }
}
