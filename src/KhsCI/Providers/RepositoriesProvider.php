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

        $pimple['repo_comments'] = function ($app) use ($namespace) {
            $class = $namespace.'Comments'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_commits'] = function ($app) use ($namespace) {
            $class = $namespace.'Commits'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_community'] = function ($app) use ($namespace) {
            $class = $namespace.'Community'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_contents'] = function ($app) use ($namespace) {
            $class = $namespace.'Contents'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
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
