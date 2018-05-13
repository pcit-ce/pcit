<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use KhsCI\KhsCI;

class CodingController
{
    /**
     * @return array
     *
     * @throws Exception
     */
    public function __invoke()
    {
        $khsci = new KhsCI();

        $output = $khsci->webhooks->startCodingServer();

        return [$output];
    }
}
