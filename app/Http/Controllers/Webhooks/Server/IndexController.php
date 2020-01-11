<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks\Server;

use PCIT\PCIT;

class IndexController
{
    public function __invoke($gitType)
    {
        $pcit = app(PCIT::class)->setGitType($gitType);

        $result = $pcit->webhooks->server();

        return [$result];
    }
}
