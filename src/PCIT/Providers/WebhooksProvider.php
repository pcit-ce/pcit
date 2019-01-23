<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WebhooksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['webhooks'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Webhooks\Client';

            return new $class($app);
        };
    }
}
