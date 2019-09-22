<?php

declare(strict_types=1);

namespace PCIT\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class GistProvider implements ServiceProviderInterface
{
    public function register(Container $pimple): void
    {
        $pimple['gist'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Gist\Client';

            return new $class($app->curl, $app->config['api_url']);
        };

        $pimple['gist_comments'] = function ($app) {
            $class = 'PCIT\\'.$app->class_name.'\Service\Gist\CommentsClient';

            return new $class($app->curl, $app->config['api_url']);
        };
    }
}
