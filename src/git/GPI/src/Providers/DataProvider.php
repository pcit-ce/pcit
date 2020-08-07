<?php

declare(strict_types=1);

namespace PCIT\GPI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['data'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Data\Client';

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
