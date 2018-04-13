<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use KhsCI\Service\OAuth\Coding;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OAuthProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $curl = new Curl;

        $pimple['OAuthCoding'] = function ($app) use ($curl) {
            return new Coding($app['config']['coding'], $curl);
        };

        $pimple['OAuthGitee'] = function ($app) use ($curl) {
            return new Coding($app['config']['gitee'], $curl);
        };

        $pimple['OAuthGitHub'] = function ($app) use ($curl) {
            return new Coding($app['config']['github'], $curl);
        };
    }
}
