<?php

namespace KhsCI\Providers;

use KhsCI\Service\Repositories\Collaborators;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RepositoriesProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['repositoriesCollaborators'] = function ($app) {
            return new Collaborators($app['curl']);
        };
    }
}
