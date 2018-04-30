<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

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
     * @return string
     * @throws \Exception
     */
    public function post(...$arg)
    {
        list($gitType, $username, $repo) = $arg;

        $sql = "SELECT id FROM builds WHERE git_type=? AND username=";

        $outputArray = DB::select($sql, [$gitType, $username]);

        var_dump($outputArray);

        foreach ($outputArray as $k) {
            $last_build_id = $k['id'];
        }

        return $last_build_id;
    }
}
