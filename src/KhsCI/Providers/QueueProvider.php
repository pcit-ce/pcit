<?php

namespace KhsCI\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['Queue'] = function ($app) {

        };
    }
}