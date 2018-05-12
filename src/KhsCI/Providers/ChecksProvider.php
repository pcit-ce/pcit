<?php

namespace KhsCI\Providers;


use KhsCI\Service\Checks\Run;
use KhsCI\Service\Checks\Suites;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ChecksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['check_run'] = function ($app) {
            return new Run($app['curl'], $app['config']['api_url']);
        };

        $pimple['check_suites'] = function ($app) {
            return new Suites($app['curl'], $app['config']['api_url']);
        };
    }
}
