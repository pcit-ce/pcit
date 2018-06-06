<?php

namespace KhsCI\Providers;

use Docker\Docker;
use KhsCI\Support\Env;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DockerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['docker'] = function () {
            if (Env::get('CI_DOCKER_TLS_VERIFY', false)) {

                return Docker::docker(Docker::createOptionArray(Env::get('CI_DOCKER_HOST'),
                    1,
                    __DIR__.'/../../../public/private_key'
                ));
            }

            return Docker::docker(Docker::createOptionArray(Env::get('CI_DOCKER_HOST')));
        };
    }
}
