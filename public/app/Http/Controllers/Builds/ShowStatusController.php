<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use Exception;
use KhsCI\Support\DB;

class ShowStatusController
{
    /**
     * 获取 分支 commit tag pr 状态
     *
     * @param mixed ...$arg
     *
     * @throws Exception
     */
    public function __invoke(...$arg): void
    {
        list($gitType, $username, $repo, $branch) = $arg;

        $sql = 'SELECT build_status FROM builds WHERE git_type=? AND repo=? AND branch=?';

        DB::select($sql, [$gitType, $repo, $branch]);
    }
}
