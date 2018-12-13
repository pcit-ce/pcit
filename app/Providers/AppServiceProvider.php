<?php

declare(strict_types=1);

namespace App\Providers;

use PCIT\PCIT;
use PCIT\Support\ServiceProvider;
use Pimple\Container;

class AppServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple[PCIT::class] = new PCIT();
    }
}
