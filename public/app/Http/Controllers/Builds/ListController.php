<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use Exception;
use KhsCI\Support\Cache;
use KhsCI\Support\CIConst;
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
            $rid, CIConst::BUILD_STATUS_PENDING, CIConst::BUILD_STATUS_SKIP, CIConst::BUILD_STATUS_INACTIVE,
        ], true
        );

        if (!$last_build_id) {
            return [];
        }

        return $this->getBuildDetails(null, null, null, $last_build_id);
    }

    /**
     * List build Status.
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

        $sql = <<<'EOF'
SELECT id,event_type,branch,committer_username,commit_message,commit_id,build_status,create_time,end_time

FROM builds WHERE git_type=? AND event_type IN (?,?) AND rid=

(SELECT rid FROM repo WHERE git_type=? AND repo_full_name=?)

ORDER BY id DESC 

EOF;

        $output = DB::select($sql, [
                $gitType, CIConst::BUILD_EVENT_PUSH, CIConst::BUILD_EVENT_TAG, $gitType, "$username/$repo",
            ]
        );

        return $output;
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
        list($gitType, $username, $repo, $buildId) = $args;

        $redis = Cache::connect();

        $build_log = $redis->hget('build_log', $buildId);

        $sql = 'SELECT build_status FROM builds WHERE id=?';

        $output = DB::select($sql, [$buildId], true);

        if ($build_log) {
            return [
                'status' => $output, 'data' => $build_log,
            ];
        }

        return ['status' => $output, 'data' => null];
    }
}
