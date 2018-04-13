<?php

namespace KhsCI\Providers;

use Curl\Curl;
use KhsCI\Service\SEO\Baidu;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SEOProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['SEOBaidu'] = function ($app) {
            return new Baidu($app['baidu'], new Curl());
        };
    }
}