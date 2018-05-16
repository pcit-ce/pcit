<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use Exception;
use KhsCI\Support\CI;
use KhsCI\Support\DB;
use KhsCI\Support\Git;

class PullRequestsController
{
    /**
     * @param mixed ...$args
     *
     * @return array
     *
     * @throws Exception
     */
    public function post(...$args)
    {
        list($git_type, $username, $repo) = $args;

        $repo_full_name = "$username/$repo";

        $sql = <<<'EOF'
SELECT id,branch,pull_request_id,committer_username,commit_message,commit_id,build_status,started_at,stopped_at

FROM builds WHERE git_type=? AND event_type IN (?) AND rid=

(SELECT rid FROM repo WHERE git_type=? AND repo_full_name=?)

AND action IN (?,?)

ORDER BY id DESC 

EOF;

        $output = DB::select($sql, [
               $git_type, CI::BUILD_EVENT_PR, $git_type, $repo_full_name, 'synchronize', 'opened',
            ]
        );

        $array = [];

        foreach ($output as $k) {
            $merge_array = [
                'commit_url' => Git::getCommitUrl($git_type, $repo_full_name, $k['commit_id']),
                'commit_id' => substr($k['commit_id'], 0, 7),
                'compare' => Git::getPullRequestUrl($git_type, $repo_full_name, (int) $k['pull_request_id']),
            ];

            $array[] = array_merge($k, $merge_array);
        }

        return $array;
    }
}
