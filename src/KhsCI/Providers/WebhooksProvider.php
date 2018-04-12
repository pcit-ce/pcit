<?php

namespace KhsCI\Providers;

use KhsCI\Service\Webhooks\Coding;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WebhooksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['WebhooksCoding'] = function ($app) {
            return new Coding();
        };
    }

}