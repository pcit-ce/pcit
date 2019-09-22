<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OrganizationsProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['orgs'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Organizations\Client';

            return new $class($app['curl'], $app['config']['api_url']);
        };
    }
}
