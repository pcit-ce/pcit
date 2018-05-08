<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use KhsCI\Service\OAuth\Coding;
use KhsCI\Service\OAuth\Gitee;
use KhsCI\Service\OAuth\GitHub;
use KhsCI\Service\OAuth\GitHubApp;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OAuthProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $curl = new Curl();

        $pimple['oauth_coding'] = function ($app) use ($curl) {
            return new Coding($app['config']['coding'], $curl);
        };

        $pimple['oauth_gitee'] = function ($app) use ($curl) {
            return new Gitee($app['config']['gitee'], $curl);
        };

        $pimple['oauth_github'] = function ($app) use ($curl) {
            return new GitHub($app['config']['github'], $curl);
        };

        $pimple['oauth_github_app'] = function ($app) use ($curl) {
            return new GitHubApp($app['config']['github_app'], $curl);
        };
    }
}
