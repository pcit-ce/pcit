<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CurlProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['curl'] = function ($app) {
            $curl = new Curl(...$app['curl_config']);
            $curl->setTimeout($app['curl_timeout']);

            return $curl;
        };
    }
}
