<?php

declare(strict_types=1);

namespace PCIT\Providers;

use PCIT\Runner\Agent\Docker\DockerHandler;
use PCIT\Runner\Agent\Exec\ExecHandler;
use PCIT\Runner\Client as JobGenerator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RunnerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['runner_job_generator'] = function ($app) {
            return new JobGenerator();
        };

        $pimple['runner_agent_docker'] = function ($app) {
            return new DockerHandler();
        };

        $pimple['runner_agent_exec'] = function ($app) {
            return new ExecHandler();
        };
    }
}
