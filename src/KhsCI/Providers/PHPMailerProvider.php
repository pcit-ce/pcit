<?php

namespace KhsCI\Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PHPMailerProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['email'] = function ($app) {

        };
    }
}
