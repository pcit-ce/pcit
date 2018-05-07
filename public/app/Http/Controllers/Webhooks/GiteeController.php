<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use KhsCI\KhsCI;

class GiteeController
{
    /**
     * @throws Exception
     */
    public function __invoke()
    {
        $khsci = new KhsCI();

        $webhooks = $khsci->webhooks_gitee;

        return $webhooks();
    }
}
