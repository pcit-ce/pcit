<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Repo;
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
        $rid = Repo::getRid(...$arg);

        $last_build_id = Build::getLastBuildId($arg[0], (int) $rid);

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
        list($git_type, $username, $repo) = $args;

        $repo_full_name = "$username/$repo";

        $sql = <<<'EOF'
SELECT id,event_type,branch,committer_username,commit_message,commit_id,build_status,create_time,end_time

FROM builds WHERE git_type=? AND event_type IN (?,?) AND rid=

(SELECT rid FROM repo WHERE git_type=? AND repo_full_name=?)

ORDER BY id DESC 

EOF;

        $output = DB::select($sql, [
                $git_type, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG, $git_type, $repo_full_name,
            ]
        );

        $array = [];

        foreach ($output as $k) {
            $merge_array = [
                'commit_url' => Git::getCommitUrl($git_type, $repo_full_name, $k['commit_id']),
                'commit_id' => substr($k['commit_id'], 0, 7),
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
        $build_key_id = $args[3];

        $redis = Cache::connect();

        $build_log = $redis->hget('build_log', $build_key_id);

        $sql = <<<'EOF'
SELECT 

id,
build_status,
commit_id,
branch,
committer_name,
commit_message,
compare,
end_time

FROM builds WHERE 

id=? 
EOF;
        $output = DB::select($sql, [$build_key_id]);

        if (!$output) {
            return [];
        }

        $output = $output[0];

        return array_merge($output, ['data' => $build_log]);
    }
}
