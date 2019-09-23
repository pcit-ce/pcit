<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;

class ActivityProvider extends \PCIT\Framework\Support\ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['activity_events'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Activity\EventsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_feeds'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Activity\FeedsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_notifications'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Activity\NotificationsClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_starring'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Activity\StarringClient';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['activity_watching'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Activity\\WatchingClient';

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
