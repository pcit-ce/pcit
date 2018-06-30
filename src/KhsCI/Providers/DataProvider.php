<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['data'] = function ($app) {
            $class = 'KhsCI\\Service\\Data'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
