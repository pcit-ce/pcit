<?php

declare(strict_types=1);

namespace PCIT\GPI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RepositoriesProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $namespace = 'PCIT\\Service\\Repositories\\';

        $pimple['repo_branches'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\BranchesClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_collaborators'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\CollaboratorsClient';

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_comments'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\CommentsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_commits'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\CommitsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_community'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\CommunityClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_contents'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\ContentsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['repo_status'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\StatusClient';

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_webhooks'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\WebhooksClient';

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_releases'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\ReleasesClient';

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['repo_merging'] = function ($app) use ($namespace) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Repositories\MergingClient';

            return new $class($app['curl'], $app['config']['api_url']);
        };
    }
}
