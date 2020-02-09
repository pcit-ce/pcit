<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks\Server;

use PCIT\Framework\Support\StringSupport;
use PCIT\PCIT;

class IndexController
{
    public function __invoke($gitType)
    {
        // custom_provider
        $provider = StringSupport::camelize($gitType);

        // CustomProvider
        $class = 'PCIT\Provider\\'.ucfirst($provider).'\\WebhooksServer';

        if (class_exists($class)) {
            return (new $class())->server();
        }

        $pcit = app(PCIT::class)->setGitType($gitType);

        $result = $pcit->webhooks->server();

        return [$result];
    }
}
