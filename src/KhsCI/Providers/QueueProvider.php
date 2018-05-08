<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Queue\Queue;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['queue'] = function ($app) {
            return new Queue();
        };
    }
}
