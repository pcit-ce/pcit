<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Baidu\Baidu;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SEOProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['seo_baidu'] = function ($app) {
            return new Baidu($app['baidu']);
        };
    }
}
