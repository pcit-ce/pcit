<?php

namespace KhsCI\Providers;

use KhsCI\Service\Repositories\Collaborators;
use KhsCI\Service\Repositories\Status;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RepositoriesProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['repo_collaborators'] = function ($app) {
            return new Collaborators($app['curl']);
        };

        $pimple['repo_status'] = function ($app) {
            return new Status($app['curl']);
        };
    }
}
