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
     * /repo/{git_type}/{repository.slug}/branches
     *
     * @param mixed ...$args
     *
     * @throws \Exception
     *
     * @return array
     */
    public function __invoke(...$args)
    {
        list($git_type, $username, $repo) = $args;

        $rid = Repo::getRid($username, $repo, $git_type);

        $branchArray = Build::getBranches((int) $rid, $git_type);

        $result = [];

        foreach ($branchArray as $k) {
            $result[] = $k['branch'];
        }

        return $result;
    }

    /**
     *  Return information about an individual branch.
     *
     * /repo/{repository.slug}/branch/{branch.name}
     *
     * @param array $args
     *
     * @throws \Exception
     *
     * @return array|string
     */
    public function find(...$args)
    {
        $request = app('request');
        // $before = $_GET['before'] ?? null;
        $before = $request->query->get('before');
        // $limit = $_GET['limit'] ?? null;
        $limit = $request->query->get('limit');

        $before && $before = (int) $before;
        $limit && $limit = (int) $before;

        list($git_type, $username, $repo_name, $branch_name) = $args;

        $rid = Repo::getRid($username, $repo_name, $git_type);

        $result = Build::allByBranch((int) $rid, $branch_name, $before, $limit, $git_type);

        if ($result) {
            return $result;
        }

        throw new Exception('Not Found', 404);
    }
}
