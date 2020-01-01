<?php

declare(strict_types=1);

namespace PCIT\Providers;

use PCIT\Runner\Agent\RunContainer;
use PCIT\Runner\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RunnerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['runner'] = function ($app) {
            return new Client();
        };

        $pimple['runner_agent'] = function ($app) {
            return new RunContainer();
        };
    }
}
