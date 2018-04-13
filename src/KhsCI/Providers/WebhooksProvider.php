<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Pimple\Container;
use KhsCI\Service\Webhooks\Coding;
use Pimple\ServiceProviderInterface;

class WebhooksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['WebhooksCoding'] = function ($app) {
            return new Coding();
        };
    }
}
