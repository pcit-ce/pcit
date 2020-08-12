<?php

declare(strict_types=1);

namespace PCIT\GPI;

use Pimple\Container;
use Pimple\Exception\UnknownIdentifierException;

/**
 * @property \PCIT\GitHub\Service\Activity\EventsClient            $activity_events
 * @property \PCIT\GitHub\Service\Activity\FeedsClient             $activity_feeds
 * @property \PCIT\GitHub\Service\Activity\NotificationsClient     $activity_notifications
 * @property \PCIT\GitHub\Service\Activity\StarringClient          $activity_starring
 * @property \PCIT\GitHub\Service\Activity\WatchingClient          $activity_watching
 * @property \PCIT\GitHub\Service\Data\Client                      $data
 * @property \PCIT\GitHub\Service\Deployment\Client                $deployment
 * @property \PCIT\GitHub\Service\Gist\Client                      $gist
 * @property \PCIT\GitHub\Service\Gist\CommentsClient              $gist_comments
 * @property \PCIT\GitHub\Service\GitHubApp\Client                 $github_apps
 * @property \PCIT\GitHub\Service\GitHubApp\InstallationsClient    $github_apps_installations
 * @property \PCIT\GitHub\Service\GitHubApp\AccessTokenClient      $github_apps_access_token
 * @property \PCIT\GitHub\Service\OAuth\Client                     $oauth
 * @property \PCIT\GitHub\Service\Issue\AssigneesClient            $issue_assignees
 * @property \PCIT\GitHub\Service\Issue\CommentsClient             $issue_comments
 * @property \PCIT\GitHub\Service\Issue\EventsClient               $issue_events
 * @property \PCIT\GitHub\Service\Issue\Client                     $issue
 * @property \PCIT\GitHub\Service\Issue\LabelsClient               $issue_labels
 * @property \PCIT\GitHub\Service\Issue\MilestonesClient           $issue_milestones
 * @property \PCIT\GitHub\Service\Miscellaneous\Client             $miscellaneous
 * @property \PCIT\GitHub\Service\Organizations\Client             $orgs
 * @property \PCIT\GitHub\Service\Repositories\BranchesClient      $repo_branches
 * @property \PCIT\GitHub\Service\Repositories\CollaboratorsClient $repo_collaborators
 * @property \PCIT\GitHub\Service\Repositories\CommitsClient       $repo_commits
 * @property \PCIT\GitHub\Service\Repositories\CommunityClient     $repo_community
 * @property \PCIT\GitHub\Service\Repositories\ContentsClient      $repo_contents
 * @property \PCIT\GitHub\Service\Repositories\MergingClient       $repo_merging
 * @property \PCIT\GitHub\Service\Repositories\ReleasesClient      $repo_releases
 * @property \PCIT\GitHub\Service\Repositories\StatusClient        $repo_status
 * @property \PCIT\GitHub\Service\Repositories\WebhooksClient      $repo_webhooks
 * @property \PCIT\GitHub\Service\PullRequest\Client               $pull_request
 * @property \PCIT\GitHub\Service\Webhooks\Server                  $webhooks
 * @property \PCIT\GitHub\Service\Users\Client                     $user_basic_info
 * @property \PCIT\GitHub\Service\Checks\Run                       $check_run
 * @property \PCIT\GitHub\Service\Checks\Suites                    $check_suites
 */
abstract class GPI extends Container
{
    protected $providers = [
        \PCIT\GPI\Providers\ActivityProvider::class,
        \PCIT\GPI\Providers\ChecksProvider::class,
        \PCIT\GPI\Providers\DataProvider::class,
        \PCIT\GPI\Providers\DeploymentProvider::class,
        \PCIT\GPI\Providers\GistProvider::class,
        \PCIT\GPI\Providers\GitHubAppProvider::class,
        \PCIT\GPI\Providers\IssueProvider::class,
        \PCIT\GPI\Providers\MiscellaneousProvider::class,
        \PCIT\GPI\Providers\OAuthProvider::class,
        \PCIT\GPI\Providers\OrganizationsProvider::class,
        \PCIT\GPI\Providers\PullRequestProvider::class,
        \PCIT\GPI\Providers\RepositoriesProvider::class,
        \PCIT\GPI\Providers\UserProvider::class,
        \PCIT\GPI\Providers\WebhooksProvider::class,
    ];

    public function __get(string $name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        throw new UnknownIdentifierException($name);
    }

    public function __call(string $method, array $arguments)
    {
        if (isset($this[$method])) {
            return $this[$method];
        }

        throw new UnknownIdentifierException($method);
    }
}
