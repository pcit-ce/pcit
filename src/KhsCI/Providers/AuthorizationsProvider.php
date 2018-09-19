<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Authorizations\GitHubClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AuthorizationsProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['authorizations'] = function ($app) {
            return new GitHubClient($app);
        };
    }
}
