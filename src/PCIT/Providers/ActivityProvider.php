<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;

class ActivityProvider extends \PCIT\Support\ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['activity_events'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Activity\EventsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_feeds'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Activity\FeedsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_notifications'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Activity\NotificationsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_starring'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Activity\StarringClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_watching'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Activity\\WatchingClient';

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
