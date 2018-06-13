<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OAuthProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $curl = new Curl();

        $pimple['oauth'] = function ($app) use ($curl) {
            $class = 'KhsCI\Service\OAuth\\'.$app->class_name;
            $git_type = $app->git_type;

            return new $class($app['config'][$git_type], $curl);
        };
    }
}
