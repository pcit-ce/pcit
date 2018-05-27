<?php

namespace KhsCI\Providers;

use KhsCI\Service\Users\GitHubClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OrganizationsProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['github_orgs'] = function ($app) {
            return new GitHubClient($app['curl'], $app['config']['api_url']);
        };
    }
}
