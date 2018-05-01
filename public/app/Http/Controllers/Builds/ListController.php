<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use KhsCI\Support\Cache;
use KhsCI\Support\DB;

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
     * @return string
     * @throws \Exception
     */
    public function post(...$arg)
    {
        list($gitType, $username, $repo) = $arg;

        $sql = "SELECT rid FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=?";

        $outputArray = DB::select($sql, [$gitType, $username, $repo]);

        foreach ($outputArray as $k) {
            $rid = $k['rid'];
        }

        $sql = "SELECT id FROM builds WHERE rid=? ORDER BY id DESC LIMIT 1";

        $output = DB::select($sql, [$rid]);

        foreach ($output as $k) {
            $last_build_id = $k['id'];
        }

        return $this->getBuildDetails(null, null, null, $last_build_id);
    }

    /**
     * List build Status
     *
     * @param mixed ...$args
     *
     * @return array
     * @throws \Exception
     */
    public function list(...$args)
    {
        list($gitType, $username, $repo) = $args;

        /**
         * id
         * branch
         * committer
         * commit_message
         * build_status
         * commit_id
         * build_time = end_time - create_time
         * now_time - end_time
         */

        $sql = <<<EOF
SELECT id,branch,committer_username,commit_message,commit_id,build_status,create_time,end_time

FROM builds WHERE git_type=? AND rid=

(SELECT rid FROM repo WHERE git_type=? AND repo_full_name=?)

ORDER BY id DESC 

EOF;

        $output = DB::select($sql, [$gitType, $gitType, "$username/$repo"]);

        return $output;
    }

    /**
     * Show build details
     *
     * @param mixed ...$args
     *
     * @return string
     * @throws \Exception
     */
    public function getBuildDetails(...$args)
    {
        list($gitType, $username, $repo, $buildId) = $args;

        $redis = Cache::connect();

        $output = $redis->hget('build_log', $buildId);

        return $output;
    }
}
