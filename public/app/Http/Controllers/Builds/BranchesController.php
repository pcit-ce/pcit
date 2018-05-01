<?php

namespace App\Http\Controllers\Builds;


use KhsCI\Support\DB;

class BranchesController
{
    /**
     * @param mixed ...$args
     *
     * @return array
     * @throws \Exception
     */
    public function post(...$args)
    {
        list($git_type, $username, $repo) = $args;

        $sql = "SELECT rid FROM repo WHERE git_type=? AND repo_full_name=?";

        $ridArray = DB::select($sql, [$git_type, "$username/$repo"]);

        foreach ($ridArray as $r_id) {
            $rid = $r_id['rid'];
        }

        $sql = "SELECT DISTINCT branch FROM builds WHERE git_type=? AND rid=?";

        $branchArray = DB::select($sql, [$git_type, $rid]);

        $build_status_array = [];

        foreach ($branchArray as $branch) {
            $branch = $branch['branch'];

            if (null === $branch) {
                continue;
            }

            $sql = "SELECT id,build_status FROM builds WHERE git_type=? AND rid=? AND branch=? ORDER BY id DESC LIMIT 5 ";

            $outputArray = DB::select($sql, [$git_type, $rid, $branch]);

            foreach ($outputArray as $output) {
                $build_status = $output['build_status'];
                $build_id = (string)$output['id'];
                $build_status_array["$branch"]['k'."$build_id"] = $build_status;
            }
        }

        return $build_status_array;
    }
}
