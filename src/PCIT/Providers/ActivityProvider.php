<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;

class ActivityProvider extends \PCIT\Support\ServiceProvider
{
    public function register(Container $pimple): void
    {
        $namespace = 'PCIT\\Service\\Activity\\';

        $pimple['activity_events'] = function ($app) use ($namespace) {
            $class = $namespace.'Events'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_feeds'] = function ($app) use ($namespace) {
            $class = $namespace.'Feeds'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_notifications'] = function ($app) use ($namespace) {
            $class = $namespace.'Notifications'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_starring'] = function ($app) use ($namespace) {
            $class = $namespace.'Starring'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_watching'] = function ($app) use ($namespace) {
            $class = $namespace.'Watching'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
