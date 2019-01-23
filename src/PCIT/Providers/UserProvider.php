<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['user_basic_info'] = function ($app) {
            $class = 'PCIT\Service\\'.$app->class_name.'\Users\Client';

            return new $class($app['curl'], $app['config']['api_url']);
        };
    }
}
