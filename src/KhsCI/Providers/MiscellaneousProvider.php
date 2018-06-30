<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MiscellaneousProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['miscellaneous'] = function ($app) {
            $class = 'KhsCI\\Service\\Miscellaneous\\'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
