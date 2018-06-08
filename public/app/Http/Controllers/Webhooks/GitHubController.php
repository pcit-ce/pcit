<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use KhsCI\KhsCI;

class GitHubController
{
    /**
     * @throws Exception
     */
    public function __invoke()
    {
        $khsci = new KhsCI([], 'github');

        $output = $khsci->webhooks->Server();

        return [$output];
    }
}
