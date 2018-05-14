<?php

namespace KhsCI\Providers;

use KhsCI\Service\Issue\Comments;
use KhsCI\Service\Issue\Issues;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class IssueProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['issue'] = function ($app) {
            return new Issues($app['curl'], $app['config']['api_url'], $app['tencent_ai']);
        };

        $pimple['issue_comments'] = function ($app) {
            return new Comments($app['curl'], $app['config']['api_url'], $app['tencent_ai']);
        };
    }
}
