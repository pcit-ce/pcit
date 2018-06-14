<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['user_basic_info'] = function ($app) {
            $class = 'KhsCI\Service\Users\\'.$app->class_name;

            return new $class($app['curl'], $app['config']['api_url']);
        };
    }
}
