<?php

declare(strict_types=1);

namespace PCIT\Framework\Routing;

use PCIT\Framework\Support\ServiceProvider;
use Pimple\Container;

class RoutingServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['router'] = function ($app) {
            return new Router();
        };
    }
}
