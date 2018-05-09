<?php

namespace App;


use Exception;
use KhsCI\Support\DB;

class Repo
{
    /**
     * @param string $git_type
     * @param string $username
     * @param string $repo
     *
     * @return array|string
     * @throws Exception
     */
    public static function getRepoId(string $git_type, string $username, string $repo)
    {
        $sql = 'SELECT rid FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=?';

        $id = DB::select($sql, [$git_type, $username, $repo], true);

        return $id;
    }

    /**
     * @param string $git_type
     * @param string $username
     * @param string $repo
     *
     * @return array|string
     * @throws Exception
     */
    public static function getDefaultBranch(string $git_type, string $username, string $repo)
    {
        $sql = 'SELECT default_branch FROM repo WHERE git_type=? AND repo_prefix=? AND repo_name=?';

        $default_branch = DB::select($sql, [$git_type, $username, $repo], true);

        return $default_branch;
    }
}
