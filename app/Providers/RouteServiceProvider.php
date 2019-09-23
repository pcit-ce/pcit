<?php

declare(strict_types=1);

namespace App\Providers;

use PCIT\Framework\Support\ServiceProvider;
use Pimple\Container;

class RouteServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['route'] = null;
    }
}
