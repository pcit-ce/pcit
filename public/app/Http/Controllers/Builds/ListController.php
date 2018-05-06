<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\CI;
use KhsCI\Support\DB;
use KhsCI\Support\Git;

class ListController
{
    public function __invoke(...$arg): void
    {
        require __DIR__.'/../../../../public/builds/index.html';
        exit;
    }

    /**
     * @param mixed ...$arg
     *
     * @return string|array
     *
     * @throws Exception
     */
    public function post(...$arg)
    {
        list($gitType, $username, $repo) = $arg;

        $sql = 'SELECT rid FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=?';

        $rid = DB::select($sql, [$gitType, $username, $repo], true);

        $sql = 'SELECT id FROM builds WHERE rid=? AND build_status NOT IN (?,?,?) ORDER BY id DESC LIMIT 1';

        $last_build_id = DB::select($sql, [
            $rid, CI::BUILD_STATUS_PENDING, CI::BUILD_STATUS_SKIP, CI::BUILD_STATUS_INACTIVE,
        ], true
        );

        if (!$last_build_id) {
            return [];
        }

        return $this->getBuildDetails(null, null, null, $last_build_id);
    }

    /**
     * List build Status, include commit tag, not include pull_request.
     *
     * @param mixed ...$args
     *
     * @return array
     *
     * @throws Exception
     */
    public function list(...$args)
    {
        list($gitType, $username, $repo) = $args;

        $repo_full_name = "$username/$repo";

        $base_url = Git::getUrl($gitType, $repo_full_name);

        $sql = <<<'EOF'
SELECT id,event_type,branch,committer_username,commit_message,commit_id,build_status,create_time,end_time

FROM builds WHERE event_type IN (?,?) AND rid=

(SELECT rid FROM repo WHERE git_type=? AND repo_full_name=?)

ORDER BY id DESC 

EOF;

        $output = DB::select($sql, [
                CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG, $gitType, $repo_full_name,
            ]
        );

        $array = [];

        foreach ($output as $k) {
            $merge_array = [
                'commit_url' => $base_url.'/commit/'.$k['commit_id'],
                'commit_id' => substr($k['commit_id'], 0, 7)
            ];

            $array[] = array_merge($k, $merge_array);
        }

        return $array;
    }

    /**
     * Show build details.
     *
     * @param mixed ...$args
     *
     * @return string|array
     *
     * @throws Exception
     */
    public function getBuildDetails(...$args)
    {
        list($gitType, $username, $repo, $build_key_id) = $args;

        $redis = Cache::connect();

        $build_log = $redis->hget('build_log', $build_key_id);

        $sql = <<<'EOF'
SELECT 

build_status,
commit_id,
branch,
committer_name,
end_time

FROM builds WHERE 

id=? 
EOF;
        $output = DB::select($sql, [$build_key_id]);

        $output = $output[0];

        return [
            'status' => $output['build_status'],
            'commit_id' => $output['commit_id'],
            'branch' => $output['branch'],
            'committer_name' => $output['committer_name'],
            'end_time' => $output['end_time'],
            'data' => $build_log
        ];
    }
}
