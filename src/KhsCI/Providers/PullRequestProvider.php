<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\PullRequest\GitHubClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PullRequestProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['github_pull_request'] = function ($app) {
            return new GitHubClient($app['curl'], $app['config']['api_url']);
        };
    }
}
