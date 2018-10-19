<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['data'] = function ($app) {
            $class = 'PCIT\\Service\\Data\\'.$app->class_name;

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
