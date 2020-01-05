<?php

declare(strict_types=1);

namespace PCIT\Framework\Http;

use PCIT\Framework\Http\Response\Response;
use PCIT\Framework\Support\ServiceProvider;
use Pimple\Container;

class HttpServiceProvider extends ServiceProvider
{
    public function register(Container $pimple): void
    {
        $pimple['response'] = function ($app) {
            return new Response();
        };
    }
}
