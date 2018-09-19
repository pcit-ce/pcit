<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WebhooksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['webhooks'] = function ($app) {
            $class = 'KhsCI\Service\Webhooks\\'.$app->class_name;

            return new $class($app);
        };
    }
}
