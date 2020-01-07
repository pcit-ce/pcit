<?php

declare(strict_types=1);

namespace PCIT\Framework\Cache;

use PCIT\Framework\Contracts\Cache\Repository;
use PCIT\Framework\Support\ServiceProvider;
use Pimple\Container;

class CacheServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['cache'] = function ($app) {
            return new Cache();
        };

        $pimple[Repository::class] = $pimple['cache'];
    }
}
