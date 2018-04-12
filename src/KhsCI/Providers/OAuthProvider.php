<?php

namespace KhsCI\Providers;

use Curl\Curl;
use KhsCI\Service\OAuth\Coding;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OauthProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['OAuthCoding'] = function ($app) {
            return new Coding([], new Curl());
        };

    }

}