<?php

declare(strict_types=1);

namespace KhsCI\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['queue'] = function ($app): void {
        };
    }
}
