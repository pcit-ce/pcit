<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RepositoriesProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $namespace = 'KhsCI\\Service\\Repositories\\';

        $pimple['repo_branches'] = function ($app) use ($namespace) {
            $class = $namespace.'Branches'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_collaborators'] = function ($app) use ($namespace) {
            $class = $namespace.'Collaborators'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_status'] = function ($app) use ($namespace) {
            $class = $namespace.'Status'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_webhooks'] = function ($app) use ($namespace) {
            $class = $namespace.'Webhooks'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_releases'] = function ($app) use ($namespace) {
            $class = $namespace.'Releases'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_merging'] = function ($app) use ($namespace) {
            $class = $namespace.'Merging'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };
    }
}
