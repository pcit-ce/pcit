<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

abstract class ServiceProvider implements ServiceProviderInterface
{
    abstract public function register(Container $pimple);
}
