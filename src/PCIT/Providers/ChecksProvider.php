<?php

declare(strict_types=1);

namespace PCIT\Providers;

use PCIT\GitHub\Service\Checks\Run;
use PCIT\GitHub\Service\Checks\Suites;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ChecksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['check_run'] = function ($app) {
            return new Run($app['curl'], $app['config']['api_url']);
        };

        $pimple['check_suites'] = function ($app) {
            return new Suites($app['curl'], $app['config']['api_url']);
        };
    }
}
