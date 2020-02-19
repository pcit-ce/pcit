<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Docker\Docker;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DockerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['docker'] = function () {
            if (env('CI_DOCKER_TLS_VERIFY', false)) {
                return Docker::docker(Docker::createOptionArray(env('CI_DOCKER_HOST'),
                    true,
                    base_path().'framework/storage/private_key',
                    null,
                    null,
                    null,
                    (int) env('CI_DOCKER_TIMEOUT', 60 * 5)
                ));
            }

            return Docker::docker(Docker::createOptionArray(env('CI_DOCKER_HOST'),
                false,
                null,
                null,
                null,
                null,
                (int) env('CI_DOCKER_TIMEOUT', 60 * 5)
            ));
        };
    }
}
