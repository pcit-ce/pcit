<?php

namespace KhsCI\Providers;

use Curl\Curl;
use KhsCI\Service\IM\Wechat;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class IMProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['wechat'] = function ($app) {
            return new Wechat($app['config']['wechat'], new Curl(), $app['data']);
        };
    }
}