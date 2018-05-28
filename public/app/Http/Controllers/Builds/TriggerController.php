<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

class TriggerController
{
    /**
     * 用户指定分支构建.
     *
     * @param mixed ...$args
     */
    public function __invoke(...$args): void
    {
        list($username, $repo_name, $branch) = $args;
    }
}
