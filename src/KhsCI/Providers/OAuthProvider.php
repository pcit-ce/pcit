<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use Curl\Curl;
use KhsCI\Service\OAuth\CodingClient;
use KhsCI\Service\OAuth\GiteeClient;
use KhsCI\Service\OAuth\GitHubAppClient;
use KhsCI\Service\OAuth\GitHubClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OAuthProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $curl = new Curl();

        $pimple['oauth_coding'] = function ($app) use ($curl) {
            return new CodingClient($app['config']['coding'], $curl);
        };

        $pimple['oauth_gitee'] = function ($app) use ($curl) {
            return new GiteeClient($app['config']['gitee'], $curl);
        };

        $pimple['oauth_github'] = function ($app) use ($curl) {
            return new GitHubClient($app['config']['github'], $curl);
        };

        $pimple['oauth_github_app'] = function ($app) use ($curl) {
            return new GitHubAppClient($app['config']['github_app'], $curl);
        };
    }
}
