<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use PCIT\PCIT;

class Kernel
{
    protected static $git_type;

    /**
     * @throws Exception
     */
    public function __invoke()
    {
        $pcit = new PCIT([], static::$git_type);

        $output = $pcit->webhooks->server();

        return [$output];
    }
}
