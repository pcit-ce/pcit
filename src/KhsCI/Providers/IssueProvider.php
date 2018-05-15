<?php

namespace KhsCI\Providers;

use KhsCI\Service\Issue\Assignees;
use KhsCI\Service\Issue\Comments;
use KhsCI\Service\Issue\Events;
use KhsCI\Service\Issue\Issues;
use KhsCI\Service\Issue\Labels;
use KhsCI\Service\Issue\Milestones;
use KhsCI\Service\Issue\Timeline;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class IssueProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['issue'] = function ($app) {
            return new Issues($app['curl'], $app['config']['api_url'], $app['tencent_ai']);
        };

        $pimple['issue_assignees'] = function ($app) {
            return new Assignees($app['curl'], $app['config']['api_url']);
        };

        $pimple['issue_comments'] = function ($app) {
            return new Comments($app['curl'], $app['config']['api_url'], $app['tencent_ai']);
        };

        $pimple['issue_events'] = function ($app) {
            return new Events();
        };

        $pimple['issue_labels'] = function ($app) {
            return new Labels();
        };

        $pimple['issue_milestones'] = function ($app) {
            return new Milestones();
        };

        $pimple['issue_timeline'] = function ($app) {
            return new Timeline($app['curl'], $app['config']['api_url']);
        };
    }
}
