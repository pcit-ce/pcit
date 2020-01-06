<?php

declare(strict_types=1);

namespace PCIT\Framework\Log;

use PCIT\Framework\Support\ServiceProvider;
use Pimple\Container;

class LogServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['log'] = function ($app) {
            return new Log();
        };
    }
}
