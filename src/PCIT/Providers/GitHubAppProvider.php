<?php

declare(strict_types=1);

namespace PCIT\Providers;

use PCIT\Service\GitHubApp\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class GitHubAppProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['github_apps_installations'] = function ($app) {
            return new Client($app['curl'], $app['config']['api_url']);
        };
    }
}
