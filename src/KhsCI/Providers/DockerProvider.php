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
            return Docker::docker(Docker::createOptionArray(Env::get('CI_DOCKER_HOST')));
        };
    }
}
