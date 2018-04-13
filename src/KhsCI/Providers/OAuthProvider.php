<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use Pimple\Container;
use KhsCI\Service\OAuth\Gitee;
use KhsCI\Service\OAuth\Coding;
use KhsCI\Service\OAuth\GitHub;
use Pimple\ServiceProviderInterface;

class OAuthProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $curl = new Curl();

        $pimple['OAuthCoding'] = function ($app) use ($curl) {
            return new Coding($app['config']['coding'], $curl);
        };

        $pimple['OAuthGitee'] = function ($app) use ($curl) {
            return new Gitee($app['config']['gitee'], $curl);
        };

        $pimple['OAuthGitHub'] = function ($app) use ($curl) {
            return new GitHub($app['config']['github'], $curl);
        };
    }
}
