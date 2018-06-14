<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OrganizationsProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['orgs'] = function ($app) {
            $class = 'KhsCI\\Service\\Organizations\\'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };
    }
}
