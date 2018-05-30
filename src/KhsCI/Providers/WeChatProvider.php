<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\WeChat\Template\WeChatClient;
use KhsCI\Support\Cache;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WeChat\Wechat;

class WeChatProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['wechat'] = function ($app) {
            return new Wechat(
                $app['config']['wechat']['app_id'],
                $app['config']['wechat']['app_secret'],
                $app['config']['wechat']['token'],
                Cache::connect()
            );
        };

        $pimple['wechat_template_message'] = function ($app) {
            return new WeChatClient($app);
        };
    }
}
