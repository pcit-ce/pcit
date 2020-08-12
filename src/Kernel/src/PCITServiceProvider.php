<?php

declare(strict_types=1);

namespace PCIT;

use PCIT\Framework\Support\ServiceProvider;
use Pimple\Container;

class PCITServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple->app->singleton(PCIT::class, function ($app) {
            return new PCIT();
        });

        $pimple['pcit'] = $pimple[PCIT::class];
    }
}
