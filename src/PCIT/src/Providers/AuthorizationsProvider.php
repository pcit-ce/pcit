<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AuthorizationsProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['authorizations'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Authorizations\Client';

            return new $class($app);
        };
    }
}
