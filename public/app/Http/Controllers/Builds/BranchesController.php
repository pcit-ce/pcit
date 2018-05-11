<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Repo;
use Exception;
use KhsCI\Support\CI;
use KhsCI\Support\DB;
use KhsCI\Support\Git;

class BranchesController
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

        $base_url = Git::getUrl($git_type, $repo_full_name);

        $rid = Repo::getRepoId($git_type, $username, $repo);

        $sql = 'SELECT DISTINCT branch FROM builds WHERE git_type=? AND rid=?';

        $branchArray = DB::select($sql, [$git_type, $rid]);

        $build_status_array = [];

        foreach ($branchArray as $branch) {
            $branch = $branch['branch'];

            if (null === $branch) {
                continue;
            }

            $sql = <<<'EOF'
SELECT 

id,
build_status,
commit_id,
committer_name,
end_time

FROM builds WHERE
 
git_type=? AND rid=? AND branch=? AND event_type IN (?,?) ORDER BY id DESC LIMIT 5

EOF;
            $outputArray = DB::select($sql, [$git_type, $rid, $branch, CI::BUILD_EVENT_PUSH, CI::BUILD_EVENT_TAG]);

            foreach ($outputArray as $output) {
                $build_status = $output['build_status'];
                $build_id = (string) $output['id'];
                $commit_id = $output['commit_id'];
                $commit_url = $base_url.'/commit/'.$commit_id;
                $commit_id = substr($commit_id, 0, 7);
                $committer_name = $output['committer_name'];
                $end_time = $output['end_time'];

                $build_status_array[$branch]['k'."$build_id"] = [
                    $build_status, $commit_id, $committer_name, $end_time, $commit_url,
                ];
            }
        }

        return $build_status_array;
    }
}
