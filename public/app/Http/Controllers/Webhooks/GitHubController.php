<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\GetAccessToken;
use Exception;
use KhsCI\KhsCI;

class GitHubController
{
    /**
     * @return array
     * @throws Exception
     */
    public function __invoke()
    {
        $khsci = new KhsCI();

        $webhooks = $khsci->webhooks_github;

        $output = $webhooks();

        if ($output['rid'] ?? false) {
            $access_token = GetAccessToken::byRid((int)$output['rid']);

            $array = $output[0];

            array_push($array, $access_token);

            return $status = $khsci->repo_status->create(...$array);
        }

        return $output;
    }
}
