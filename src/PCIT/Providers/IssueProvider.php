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
            $class = 'PCIT\Service\Issue\\'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url'], $app['tencent_ai']);
        };

        $pimple['issue_assignees'] = function ($app) {
            $class = 'PCIT\Service\Issue\Assignees'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };

        $pimple['issue_comments'] = function ($app) {
            $class = 'PCIT\Service\Issue\Comments'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url'], $app['tencent_ai']);
        };

        $pimple['issue_events'] = function ($app) {
            $class = 'PCIT\Service\Issue\Events'.$app->class_name;

            return new $class();
        };

        $pimple['issue_labels'] = function ($app) {
            $class = 'PCIT\Service\Issue\Labels'.$app->class_name;

            return new $class();
        };

        $pimple['issue_milestones'] = function ($app) {
            $class = 'PCIT\Service\Issue\Milestones'.$app->class_name;

            return new $class();
        };
    }
}
