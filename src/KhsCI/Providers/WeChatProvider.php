<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WeChat\Wechat;

class WeChatProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['wechat'] = function ($app) {
            return new Wechat($app['config']['wechat'], new Curl(), $app['data']);
        };
    }
}
