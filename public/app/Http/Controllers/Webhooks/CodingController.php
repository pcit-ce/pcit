<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use KhsCI\KhsCI;

class CodingController
{
    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $khsci = new KhsCI();

        $khsci->webhooks->coding();
    }
}
