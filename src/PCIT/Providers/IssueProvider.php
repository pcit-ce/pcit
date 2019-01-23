<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class IssueProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['issue'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Issue\Client';

            return new $class($app['curl'], $app['config']['api_url'], $app['tencent_ai']);
        };

        $pimple['issue_assignees'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Issue\AssigneesClient';

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['issue_comments'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Issue\CommentsClient';

            return new $class($app['curl'], $app['config']['api_url'], $app['tencent_ai']);
        };

        $pimple['issue_events'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Issue\EventsClient';

            return new $class();
        };

        $pimple['issue_labels'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Issue\LabelsClient';

            return new $class();
        };

        $pimple['issue_milestones'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Issue\MilestonesClient';

            return new $class();
        };
    }
}
