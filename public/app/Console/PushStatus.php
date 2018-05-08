<?php

declare(strict_types=1);

namespace App\Console;

use App\Http\Controllers\Status\GitHubController;
use Exception;

class PushStatus
{
    /**
     * @param string $login_username
     * @param string $repo_full_name
     * @param string $commit_id
     * @param string $state
     * @param string $target_url
     * @param string $description
     * @param string $context
     *
     * @throws Exception
     */
    public static function push(string $login_username,
                                string $repo_full_name,
                                string $commit_id,
                                string $state,
                                string $target_url,
                                string $description,
                                string $context): void
    {
        $status = new GitHubController();
        $status->create($login_username, $repo_full_name, $commit_id, $state, $target_url, $description, $context);
    }
}
