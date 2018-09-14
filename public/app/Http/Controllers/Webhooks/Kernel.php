<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use KhsCI\KhsCI;

class Kernel
{
    protected static $git_type;

    /**
     * @throws Exception
     */
    public function __invoke()
    {
        $khsci = new KhsCI([], static::$git_type);

        $output = $khsci->webhooks->Server();

        return [$output];
    }
}
