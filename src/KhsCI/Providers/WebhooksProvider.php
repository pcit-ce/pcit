<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Webhooks\GitHubClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WebhooksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['webhooks'] = function ($app) {
            if ('github' === $app['git_type']) {
                return new GitHubClient();
            } else {
                $class = 'KhsCI\Service\Webhooks\\'.ucfirst($app['git_type']);
                return new $class();
            }
        };
    }
}
