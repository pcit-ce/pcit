<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DeploymentProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['deployment'] = function ($app) {
            $class = 'PCIT\\Service\\Deployment\\'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
