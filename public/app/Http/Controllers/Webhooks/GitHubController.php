<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use Exception;
use KhsCI\KhsCI;

class GitHubController
{
    /**
     * @return array
     *
     * @throws Exception
     */
    public function __invoke()
    {
        $access_token = '';

        $khsci = new KhsCI(['github_access_token' => $access_token]);

        $webhooks = $khsci->webhooks_github;

        return $webhooks();
    }
}
