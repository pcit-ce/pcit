<?php

namespace KhsCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TencentAI\TencentAI;

class TencentAIProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['tencent_ai'] = function ($app) {
            return TencentAI::tencentAI(
                $app['config']['tencent_ai']['app_id'], $app['config']['tencent_ai']['app_key']
            );

        };
    }
}
