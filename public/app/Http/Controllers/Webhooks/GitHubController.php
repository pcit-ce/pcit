<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

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

        if ($output['build_key_id'] ?? false) {
            return Cache::connect()->lPush('github_status', $output['build_key_id']);
        }

        return $output;
    }

    /**
     * @return array|bool|int
     *
     * @throws Exception
     */
    public function githubApp()
    {
        $khsci = new KhsCI();

        $output = $khsci->webhooks_github->github_app();

        if ($output['build_key_id'] ?? false) {
            return Cache::connect()->lPush('github_app_checks', $output['build_key_id']);
        }

        return $output;
    }
}
