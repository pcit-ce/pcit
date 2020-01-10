<?php

declare(strict_types=1);

namespace PCIT\Providers;

use PCIT\Runner\Agent\Docker\Dockerhandler;
use PCIT\Runner\Agent\Exec\ExecHandler;
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

        $pimple['runner_agent_docker'] = function ($app) {
            return new Dockerhandler();
        };

        $pimple['runner_agent_exec'] = function ($app) {
            return new ExecHandler();
        };
    }
}
