<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

abstract class ServiceProvider implements ServiceProviderInterface
{
    /** @var \PCIT\Framework\Foundation\Application */
    public $app;

    public function __construct()
    {
        $this->app = app();
    }

    abstract public function register(Container $pimple);
}
