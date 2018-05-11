<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\GetAccessToken;
use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;

class GitHubController
{
    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function __invoke()
    {
        $khsci = new KhsCI();

        $webhooks = $khsci->webhooks_github;

        $output = $webhooks();

        if ($output['rid'] ?? false) {
            $access_token = GetAccessToken::byRid((int) $output['rid']);

            $array = $output[0];

            array_push($array, $access_token);

            return Cache::connect()->lPush('github_status', json_encode($array));
        }

        return $output;
    }

    /**
     * @return array|bool|int
     * @throws Exception
     */
    public function githubApp()
    {
        $khsci = new KhsCI();

        $output = $khsci->webhooks_github->github_app();

        if ($output['build_key_id'] ?? false) {
            return Cache::connect()->lPush('github_app_checks_api', $output['build_key_id']);
        }

        return $output;
    }
}
