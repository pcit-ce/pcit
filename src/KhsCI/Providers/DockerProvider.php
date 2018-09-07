<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Docker\Docker;
use KhsCI\Support\Env;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DockerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['docker'] = function () {
            if (Env::get('CI_DOCKER_TLS_VERIFY', false)) {
                return Docker::docker(Docker::createOptionArray(Env::get('CI_DOCKER_HOST'),
                    true,
                    __DIR__.'/../../../public/storage/private_key',
                    null,
                    null,
                    null,
                    (int) Env::get('CI_DOCKER_TIMEOUT', 100)
                ));
            }

            return Docker::docker(Docker::createOptionArray(Env::get('CI_DOCKER_HOST'),
                false,
                null,
                null,
                null,
                null,
                (int) Env::get('CI_DOCKER_TIMEOUT', 100)
            ));
        };
    }
}
