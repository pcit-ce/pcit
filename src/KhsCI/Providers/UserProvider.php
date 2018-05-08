<?php

namespace KhsCI\Providers;


use KhsCI\Service\Users\BasicInfo;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['user_basic_info'] = function ($app) {
            return new BasicInfo($app['curl']);
        };
    }
}
