<?php

declare(strict_types=1);

namespace PCIT\Providers;

use PCIT\Service\Build\Agent\RunContainer;
use PCIT\Service\Build\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuildProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['build'] = function ($app) {
            return new Client();
        };

        $pimple['build_agent'] = function ($app) {
            return new RunContainer();
        };
    }
}
