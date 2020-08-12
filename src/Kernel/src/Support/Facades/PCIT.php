<?php

declare(strict_types=1);

namespace PCIT\Support\Facades;

use PCIT\Framework\Support\Facades\Facade;

/**
 * @method static \PCIT\GitHub\Service\Activity\EventsClient            activity_events()
 * @method static \PCIT\GitHub\Service\Activity\FeedsClient             activity_feeds()
 * @method static \PCIT\GitHub\Service\Activity\NotificationsClient     activity_notifications()
 * @method static \PCIT\GitHub\Service\Activity\StarringClient          activity_starring()
 * @method static \PCIT\GitHub\Service\Activity\WatchingClient          activity_watching()
 * @method static \PCIT\GitHub\Service\Data\Client                      data()
 * @method static \PCIT\GitHub\Service\Deployment\Client                deployment()
 * @method static \PCIT\GitHub\Service\Gist\Client                      gist()
 * @method static \PCIT\GitHub\Service\Gist\CommentsClient              gist_comments()
 * @method static \PCIT\GitHub\Service\GitHubApp\Client                 github_apps()
 * @method static \PCIT\GitHub\Service\GitHubApp\InstallationsClient    github_apps_installations()
 * @method static \PCIT\GitHub\Service\GitHubApp\AccessTokenClient      github_apps_access_token()
 * @method static \PCIT\GitHub\Service\OAuth\Client                     oauth()
 * @method static \PCIT\GitHub\Service\Issue\AssigneesClient            issue_assignees()
 * @method static \PCIT\GitHub\Service\Issue\CommentsClient             issue_comments()
 * @method static \PCIT\GitHub\Service\Issue\EventsClient               issue_events()
 * @method static \PCIT\GitHub\Service\Issue\Client                     issue()
 * @method static \PCIT\GitHub\Service\Issue\LabelsClient               issue_labels()
 * @method static \PCIT\GitHub\Service\Issue\MilestonesClient           issue_milestones()
 * @method static \PCIT\GitHub\Service\Miscellaneous\Client             miscellaneous()
 * @method static \PCIT\GitHub\Service\Organizations\Client             orgs()
 * @method static \PCIT\GitHub\Service\Repositories\BranchesClient      repo_branches()
 * @method static \PCIT\GitHub\Service\Repositories\CollaboratorsClient repo_collaborators()
 * @method static \PCIT\GitHub\Service\Repositories\CommitsClient       repo_commits()
 * @method static \PCIT\GitHub\Service\Repositories\CommunityClient     repo_community()
 * @method static \PCIT\GitHub\Service\Repositories\ContentsClient      repo_contents()
 * @method static \PCIT\GitHub\Service\Repositories\MergingClient       repo_merging()
 * @method static \PCIT\GitHub\Service\Repositories\ReleasesClient      repo_releases()
 * @method static \PCIT\GitHub\Service\Repositories\StatusClient        repo_status()
 * @method static \PCIT\GitHub\Service\Repositories\WebhooksClient      repo_webhooks()
 * @method static \PCIT\GitHub\Service\PullRequest\Client               pull_request()
 * @method static \PCIT\GitHub\Service\Webhooks\Server                  webhooks()
 * @method static \PCIT\GitHub\Service\Users\Client                     user_basic_info()
 * @method static \PCIT\GitHub\Service\Checks\Run                       check_run()
 * @method static \PCIT\GitHub\Service\Checks\Suites                    check_suites()
 * @method static Curl                                            curl()
 * @method static Docker                                          docker()
 * @method static WeChat                                          wechat()
 * @method static Service\Kernel\WeChat\Template\WeChatClient     wechat_template_message()
 * @method static PHPMailer                                       mail()
 * @method static \PCIT\Runner\JobGenerator                       runner_job_generator()
 * @method static \PCIT\Runner\Agent\Docker\DockerHandler         runner_agent_docker()
 * @method static \TencentAI\TencentAI                            tencent_ai()
 * @method static \PCIT\GPI\GPI git()
 */
class PCIT extends Facade
{
    public static function getfacadeaccessor(): string
    {
        return 'pcit';
    }
}
