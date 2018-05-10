<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\GitHubApps\Installations;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class GitHubAppsProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['github_apps_installations'] = function ($app) {
            return new Installations($app['curl'], $app['config']['github_app']['api_url']);
        };
    }
}
