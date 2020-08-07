<?php

declare(strict_types=1);

namespace PCIT\GPI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PullRequestProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['pull_request'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\PullRequest\Client';

            return new $class($app['curl'], $app['config']['api_url']);
        };
    }
}
