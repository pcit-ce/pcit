<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Repositories\Collaborators;
use KhsCI\Service\Repositories\Status;
use KhsCI\Service\Repositories\Webhooks;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RepositoriesProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['repo_collaborators'] = function ($app) {
            return new Collaborators($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_status'] = function ($app) {
            return new Status($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_webhooks'] = function ($app) {
            return new Webhooks($app['curl'], $app['config']['api_url']);
        };
    }
}
