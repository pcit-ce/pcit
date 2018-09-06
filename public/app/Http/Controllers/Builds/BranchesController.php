<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Repo;
use Exception;

class BranchesController
{
    /**
     * 某分支的构建列表.
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

        $rid = Repo::getRid($username, $repo, $git_type);

        $branchArray = Build::getBranches((int) $rid, $git_type);

        $return_array = [];

        foreach ($branchArray as $k) {
            $return_array[] = $k['branch'];
        }

        return $return_array;
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
        $before = $_GET['before'] ?? null;
        $limit = $_GET['limit'] ?? null;

        $before && $before = (int) $before;
        $limit && $limit = (int) $before;

        list($git_type, $username, $repo_name, $branch_name) = $args;

        $rid = Repo::getRid($username, $repo_name, $git_type);

        $output = Build::allByBranch((int) $rid, $branch_name, $before, $limit, $git_type);

        if ($output) {
            return $output;
        }

        throw new Exception('Not Found', 404);
    }
}
