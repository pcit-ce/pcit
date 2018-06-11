<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PullRequestProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['pull_request'] = function ($app) {
            $class = "KhsCI\Service\PullRequest\\".$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };
    }
}
