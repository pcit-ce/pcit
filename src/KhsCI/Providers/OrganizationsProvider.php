<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Organizations\GitHubClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OrganizationsProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['github_orgs'] = function ($app) {
            return new GitHubClient($app['curl'], $app['config']['api_url']);
        };
    }
}
