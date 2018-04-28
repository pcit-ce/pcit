<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use KhsCI\Support\DB;

class ListController
{
    public function __invoke(...$arg): void
    {
        require(__DIR__.'/../../../../public/builds/index.html');
        exit;
    }

    public function post(...$arg)
    {
        list($gitType, $username, $repo) = $arg;

        $sql = <<<EOF
SELECT id FROM builds WHERE git_type='$gitType' AND username='$username';
EOF;

        $pdo = DB::connect();

        $stmt = $pdo->query($sql);
       // var_dump($gitType);
        //var_dump($username);
        foreach ($stmt as $k) {
            var_dump($k);
        }

        return "<pre>latest build log<pre>";
    }
}
