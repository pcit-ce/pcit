<?php

namespace App\Http\Controllers\Builds;

class TriggerController
{
    /**
     * 用户指定分支构建
     *
     * @param mixed ...$args
     */
    public function __invoke(...$args)
    {
        list($git_type, $username, $repo_name, $branch) = $args;
    }
}
