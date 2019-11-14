<?php

declare(strict_types=1);

namespace App\Http\Controllers\WebhooksServer;

use PCIT\Support\Git;

class IndexController
{
    public function getClass(string $gitType)
    {
        $class = 'PCIT\\'.Git::getClassName($gitType).'\Webhooks\Server\IndexController';

        return $class;
    }

    public function __invoke($gitType)
    {
        $class = $this->getClass($gitType);

        return (new $class())();
    }
}
