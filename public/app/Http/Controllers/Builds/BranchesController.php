<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Repo;
use Exception;
use KhsCI\Support\Git;

class BranchesController
{
    /**
     * 某分支的构建列表
     *
     * Return a list of branches a repository has on Git.
     *
     * /repo/{repository.id}/branches
     *
     * @param mixed ...$args
     *
     * @return array
     *
     * @throws Exception
     */
    public function __invoke(...$args)
    {
        list($git_type, $username, $repo) = $args;

        $repo_full_name = "$username/$repo";

        $base_url = Git::getUrl($git_type, $repo_full_name);

        $rid = Repo::getRid($git_type, $username, $repo);

        $branchArray = Build::getBranches($git_type, (int) $rid);

        $build_status_array = [];

        foreach ($branchArray as $branch) {
            $branch = $branch['branch'];

            if (null === $branch) {
                continue;
            }

            $outputArray = Build::getPushAndTagEvent($git_type, (int) $rid, $branch);

            foreach ($outputArray as $output) {
                $build_status = $output['build_status'];
                $build_id = (string) $output['id'];
                $commit_id = $output['commit_id'];
                $commit_url = $base_url.'/commit/'.$commit_id;
                $commit_id = substr($commit_id, 0, 7);
                $committer_name = $output['committer_name'];
                $end_time = $output['stopped_at'];

                $build_status_array[$branch]['k'."$build_id"] = [
                    $build_status, $commit_id, $committer_name, $end_time, $commit_url,
                ];
            }
        }

        return $build_status_array;
    }

    /**
     *  Return information about an individual branch.
     *
     * /repo/{repository.id}/branch/{branch.name}
     *
     * @param array $args
     *
     * @return array|string
     *
     * @throws Exception
     */
    public function find(...$args)
    {
        list($git_type, $username, $repo_name, $branch_name) = $args;

        $rid = Repo::getRid($git_type, $username, $repo_name);

        $output = Build::allByBranch((int) $rid, $branch_name);

        if ($output) {
            return $output;
        }

        throw new Exception('Not Found', 4040);
    }
}
