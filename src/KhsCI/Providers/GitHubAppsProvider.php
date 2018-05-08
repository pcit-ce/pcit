<?php

namespace KhsCI\Providers;

use KhsCI\Service\GitHubApps\Installations;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class GitHubAppsProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['github_apps_installations'] = function ($app) {
            return new Installations($app['curl']);
        };
    }
}
