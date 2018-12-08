<?php

declare(strict_types=1);

namespace App\Http\Controllers\Builds;

use App\Build;
use App\Repo;
use Exception;
use PCIT\Support\Env;

class ShowStatusController
{
    /**
     * @param mixed ...$arg
     *
     * @throws Exception
     */
    public function __invoke(...$arg): void
    {
        $branch = $_GET['branch'] ?? null;

        list($git_type, $username, $repo) = $arg;

        $arg = [$username, $repo, $git_type];

        if (!$branch) {
            $branch = Repo::getDefaultBranch(...$arg) ?? 'master';
        }

        $rid = Repo::getRid(...$arg);

        $status = Build::getLastBuildStatus((int) $rid, $branch);

        if (null === $status) {
            header('Content-Type: image/svg+xml;charset=utf-8');
            require __DIR__.'/../../../../public/ico/unknown.svg';
            exit;
        }

        header('Content-Type: image/svg+xml;charset=utf-8');

        require __DIR__.'/../../../../public/ico/'.$status.'.svg';
    }

    /**
     * @param mixed ...$arg
     *
     * @return string
     */
    public function getStatus(...$arg)
    {
        list($git_type, $username, $repo) = $arg;
        $host = env('CI_HOST');

        return <<<EOF
<pre>

<h1>IMAGE</h1>

$host/$git_type/$username/$repo/status?branch=master

<h1>MARKDOWN</h1>

[![Build Status]($host/$git_type/$username/$repo/status?branch=master)]($host/$git_type/$username/$repo)

</pre>
EOF;
    }
}
