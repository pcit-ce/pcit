<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use Pimple\Container;
use KhsCI\Service\IM\Wechat;
use Pimple\ServiceProviderInterface;

class IMProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['wechat'] = function ($app) {
            return new Wechat($app['config']['wechat'], new Curl(), $app['data']);
        };
    }
}
