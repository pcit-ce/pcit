<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Webhooks\Coding;
use KhsCI\Service\Webhooks\Gitee;
use KhsCI\Service\Webhooks\GitHub;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WebhooksProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['webhooks_coding'] = function ($app) {
            return new Coding();
        };

        $pimple['webhooks_gitee'] = function ($app) {
            return new Gitee();
        };

        $pimple['webhooks_github'] = function ($app) {
            return new GitHub($app['config']['github']['access_token']);
        };
    }
}
