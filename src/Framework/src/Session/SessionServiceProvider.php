<?php

declare(strict_types=1);

namespace PCIT\Framework\Session;

use PCIT\Framework\Support\ServiceProvider;
use Pimple\Container;

class SessionServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['session'] = function ($app) {
            return new Session();
        };
    }
}
