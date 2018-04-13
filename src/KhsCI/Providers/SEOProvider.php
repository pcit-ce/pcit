<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use Pimple\Container;
use KhsCI\Service\SEO\Baidu;
use Pimple\ServiceProviderInterface;

class SEOProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['SEOBaidu'] = function ($app) {
            return new Baidu($app['baidu'], new Curl());
        };
    }
}
