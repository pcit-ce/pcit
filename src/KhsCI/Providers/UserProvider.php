<?php

declare(strict_types=1);

namespace KhsCI\Providers;

use KhsCI\Service\Users\GitHubClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UserProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['user_basic_info'] = function ($app) {
            $git_type = $app['git_type'];
            if ('github_app' === $git_type or 'github' === $git_type) {

                return new GitHubClient($app['curl'], $app['config']['api_url']);
            }

            $obj = 'KhsCI\Service\Users\\'.ucfirst($git_type).'Client';

            return new $obj($app['curl'], $app['config']['api_url']);
        };
    }
}
