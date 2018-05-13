<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Webhooks\Webhooks;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WebhooksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['webhooks'] = function ($app) {
            return new Webhooks();
        };
    }
}
