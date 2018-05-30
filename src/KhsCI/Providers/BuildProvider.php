<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Build\Build;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class BuildProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['build'] = function ($app) {
            return new Build();
        };
    }
}
